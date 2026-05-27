<?php

declare(strict_types=1);



class VerificationModel extends BaseModel
{
    private function getPickupBranchId(array $order): ?string
    {
        if (($order['Order_Shipping'] ?? '') !== 'Pickup') {
            return null;
        }
        if (preg_match('/^Pickup at \[([^\]]+)\]/', (string) ($order['Order_DestinationAddress'] ?? ''), $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getPendingOrders(?string $branchId = null): array
    {
        // If a branch employee is logged in, show their local orders + unassigned Open Pool deliveries
        if ($branchId !== null) {
            $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email, c.Cus_IdAttachment
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                WHERE o.Order_Status = 'Pending' 
                  AND (o.Order_BranchId = :branchId OR (o.Order_Shipping = 'Delivery' AND o.Order_BranchId IS NULL))
                ORDER BY o.Order_Date ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':branchId' => $branchId]);
            return $stmt->fetchAll();
        }

        // Fallback for General Admins (Global View)
        $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email, c.Cus_IdAttachment
            FROM Orders o
            INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
            WHERE o.Order_Status = 'Pending'
            ORDER BY o.Order_Date ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function confirmOrder(string $orderId, string $employeeId, bool $idChecked): void
    {
        $this->db->beginTransaction();
        try {
            // Fetch order with row locking
            $orderStmt = $this->db->prepare("SELECT * FROM Orders WHERE Order_Id = :id AND Order_Status = 'Pending' LIMIT 1 FOR UPDATE");
            $orderStmt->execute([':id' => $orderId]);
            $order = $orderStmt->fetch();

            if (!$order) {
                throw new RuntimeException('Pending order not found or already processed.');
            }

            // ID Validation for High-Value Orders
            if ((float) $order['Order_TotalAmount'] >= 50000) {
                if (!$idChecked) {
                    throw new RuntimeException('Government ID verification is required for high-value transactions.');
                }
                $idStmt = $this->db->prepare("SELECT Cus_IdAttachment FROM Customer WHERE Cus_Id = :cid LIMIT 1");
                $idStmt->execute([':cid' => $order['Order_CusId']]);
                if (!$idStmt->fetchColumn()) {
                    throw new RuntimeException('Customer must submit a valid ID attachment.');
                }
            }

            // 1. Identify the Verifying Employee's Branch
            $branchStmt = $this->db->prepare("SELECT Emp_BranchId FROM Employee WHERE Emp_Id = :id LIMIT 1");
            $branchStmt->execute([':id' => $employeeId]);

            // Clean values safely to combat database CHAR() spacing issues
            $branchId = trim((string) ($branchStmt->fetchColumn() ?: ''));

            // Safely trim, but preserve NULL if the branch doesn't exist (e.g., Open Pool Delivery)
            $rawOrderBranch = $order['Order_BranchId'] ?? null;
            $orderBranchId = ($rawOrderBranch !== null && trim((string)$rawOrderBranch) !== '') ? trim((string)$rawOrderBranch) : null;

            // 2. Handle Global Staff (General Admins)
            if ($branchId === '') {
                if ($orderBranchId !== null) {
                    $branchId = $orderBranchId;
                } else {
                    throw new RuntimeException('Global Administrators cannot verify Open Pool delivery orders. Please assign to a branch-level Sales Representative.');
                }
            }

            // 3. Enforce Restrictions
            if ($orderBranchId !== null && $orderBranchId !== $branchId) {
                throw new RuntimeException('Orders assigned to a specific branch must be verified by local staff.');
            }

            // Inventory Validation — fetch all rows first so we can validate then deduct in one pass
            $invStmt = $this->db->prepare(
                "SELECT oi.Item_ProdId, oi.Item_Quantity, i.Inv_Id, i.Inv_StockQty AS stock
                 FROM Order_Item oi
                 LEFT JOIN Inventory i ON i.Inv_ProdId = oi.Item_ProdId AND i.Inv_BranchId = :branch
                 WHERE oi.Item_OrderID = :orderId
                 FOR UPDATE"
            );
            $invStmt->execute([':branch' => $branchId, ':orderId' => $orderId]);
            $invRows = $invStmt->fetchAll();
            foreach ($invRows as $row) {
                if (empty($row['Inv_Id'])) {
                    throw new RuntimeException('No branch inventory row for product ' . $row['Item_ProdId']);
                }
                if ((int) $row['stock'] < (int) $row['Item_Quantity']) {
                    throw new RuntimeException('Insufficient branch inventory for product ' . $row['Item_ProdId']);
                }
            }

            // 4. Deduct inventory (B-6 fix: COD stock must be deducted here at confirm time;
            //    cashless deduction happens later in PaymentModel::confirmPayment)
            $deductStmt = $this->db->prepare(
                "UPDATE Inventory
                 SET Inv_StockQty = Inv_StockQty - :deductQty, Inv_LastUpdated = NOW()
                 WHERE Inv_ProdId = :pid AND Inv_BranchId = :bid AND Inv_StockQty >= :minQty"
            );
            
            foreach ($invRows as $row) {
                $deductStmt->execute([
                    ':deductQty' => (int) $row['Item_Quantity'],
                    ':minQty'    => (int) $row['Item_Quantity'], // Bind the value a second time here
                    ':pid'       => $row['Item_ProdId'],
                    ':bid'       => $branchId,
                ]);
                
                if ($deductStmt->rowCount() !== 1) {
                    throw new RuntimeException('Inventory deduction failed for product ' . $row['Item_ProdId']);
                }
            }

            // 5. ATOMIC CLAIM: Update Status AND lock the Order_BranchId to the claiming branch
            $stmt = $this->db->prepare(
                "UPDATE Orders 
                 SET Order_Status='Confirmed', Order_VerifiedBy=:emp, Order_BranchId=:branch 
                 WHERE Order_Id=:id AND Order_Status='Pending'"
            );
            $stmt->execute([':emp' => $employeeId, ':branch' => $branchId, ':id' => $orderId]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Order could not be confirmed or was already claimed by another branch.');
            }

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function rejectOrder(string $orderId): void
    {
        $stmt = $this->db->prepare("UPDATE Orders SET Order_Status='Cancelled' WHERE Order_Id=:id AND Order_Status='Pending'");
        $stmt->execute([':id' => $orderId]);
    }

    public function getOrderForVerification(string $orderId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT Order_BranchId, Order_Shipping FROM Orders WHERE Order_Id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        // Normalize Branch ID: trim CHAR padding and convert empty strings to strict null
        $rawBranch = trim((string) ($order['Order_BranchId'] ?? ''));
        $order['Order_BranchId'] = $rawBranch !== '' ? $rawBranch : null;

        // Normalize Shipping (just to be safe against padding)
        $order['Order_Shipping'] = trim((string) ($order['Order_Shipping'] ?? ''));

        return $order;
    }
}
