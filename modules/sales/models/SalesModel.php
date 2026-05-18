<?php
declare(strict_types=1);

class SalesModel extends BaseModel {
    private function getPickupBranchId(array $order): ?string {
        if (($order['Order_Shipping'] ?? '') !== 'Pickup') {
            return null;
        }
        if (preg_match('/^Pickup at \[([^\]]+)\]/', (string) ($order['Order_DestinationAddress'] ?? ''), $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function dashboardSummary(string $empId): array {
        $pending = (int) $this->db->query("SELECT COUNT(*) FROM Orders WHERE Order_Status = 'Pending'")->fetchColumn();

        $verifiedStmt = $this->db->prepare("SELECT COUNT(*) FROM Orders WHERE Order_VerifiedBy = :emp");
        $verifiedStmt->execute([':emp' => $empId]);

        $paid = (int) $this->db->query("SELECT COUNT(*) FROM Orders WHERE Order_Status = 'Paid'")->fetchColumn();
        $lowStock = (int) $this->db->query("SELECT COUNT(*) FROM Inventory WHERE Inv_StockQty <= Inv_ReorderLevel")->fetchColumn();

        return [
            'pending_orders' => $pending,
            'verified_orders' => (int) $verifiedStmt->fetchColumn(),
            'paid_orders' => $paid,
            'low_stock' => $lowStock,
        ];
    }

    public function getPendingOrders(int $limit = 8): array {
        $stmt = $this->db->prepare(
            "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email, c.Cus_IdAttachment
             FROM Orders o
             INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
             WHERE o.Order_Status = 'Pending'
             ORDER BY o.Order_Date ASC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getOrdersForSalesRep(string $empId): array {
        $stmt = $this->db->prepare(
            "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email, c.Cus_IdAttachment, e.Emp_Fname, e.Emp_Lname
             FROM Orders o
             INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
             LEFT JOIN Employee e ON e.Emp_Id = o.Order_VerifiedBy
             WHERE o.Order_Status = 'Pending' OR o.Order_VerifiedBy = :emp
             ORDER BY o.Order_Date DESC"
        );
        $stmt->execute([':emp' => $empId]);
        return $stmt->fetchAll();
    }

    public function getPayments(): array {
        $sql = "SELECT p.Pay_Id, p.Pay_OrderID, p.Pay_CusId, p.Pay_Method, p.Pay_Amount, p.Pay_Status,
                       p.Pay_GatewayRef, p.Pay_PaidAt, o.Order_InvoiceNo, o.Order_Status
                FROM Payment p
                INNER JOIN Orders o ON o.Order_Id = p.Pay_OrderID
                ORDER BY p.Pay_PaidAt DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getPaidOrders(): array {
        $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, p.Pay_Id, p.Pay_Method, p.Pay_Status
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                LEFT JOIN Payment p ON p.Pay_OrderID = o.Order_Id
                WHERE o.Order_Status = 'Paid'
                   OR (o.Order_Status = 'Completed' AND p.Pay_Method = 'COD' AND p.Pay_Status = 'Pending')
                ORDER BY o.Order_Date ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getInventory(): array {
        $sql = "SELECT i.*, p.Prod_Name, p.Prod_Brand, b.Branch_Name
                FROM Inventory i
                INNER JOIN Product p ON p.Prod_Id = i.Inv_ProdId
                INNER JOIN Branch b ON b.Branch_Id = i.Inv_BranchId
                ORDER BY b.Branch_Name, p.Prod_Name";
        return $this->db->query($sql)->fetchAll();
    }

    public function confirmPendingOrder(string $orderId, string $employeeId, bool $idChecked): void {
        $this->db->beginTransaction();
        try {
            $orderStmt = $this->db->prepare("SELECT * FROM Orders WHERE Order_Id = :id AND Order_Status = 'Pending' LIMIT 1 FOR UPDATE");
            $orderStmt->execute([':id' => $orderId]);
            $order = $orderStmt->fetch();
            if (!$order) {
                throw new RuntimeException('Pending order not found.');
            }

            if ((float) $order['Order_TotalAmount'] >= 50000 && !$idChecked) {
                throw new RuntimeException('Government ID verification is required for high-value transactions.');
            }
            if ((float) $order['Order_TotalAmount'] >= 50000) {
                $idStmt = $this->db->prepare(
                    "SELECT c.Cus_IdAttachment
                     FROM Orders o
                     INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                     WHERE o.Order_Id = :id
                     LIMIT 1"
                );
                $idStmt->execute([':id' => $orderId]);
                if (!$idStmt->fetchColumn()) {
                    throw new RuntimeException('Customer must submit a valid ID attachment before high-value order confirmation.');
                }
            }

            $branchStmt = $this->db->prepare("SELECT Emp_BranchId FROM Employee WHERE Emp_Id = :id LIMIT 1");
            $branchStmt->execute([':id' => $employeeId]);
            $branchId = (string) ($branchStmt->fetchColumn() ?: '');
            if ($branchId === '') {
                throw new RuntimeException('Sales representative branch not found.');
            }
            $pickupBranchId = $this->getPickupBranchId($order);
            if ($pickupBranchId !== null && $pickupBranchId !== $branchId) {
                throw new RuntimeException('Pickup orders must be verified by a sales representative from the selected branch.');
            }

            $invStmt = $this->db->prepare(
                "SELECT oi.Item_ProdId, oi.Item_Quantity, i.Inv_Id, i.Inv_StockQty AS stock
                 FROM Order_Item oi
                 LEFT JOIN Inventory i ON i.Inv_ProdId = oi.Item_ProdId AND i.Inv_BranchId = :branch
                 WHERE oi.Item_OrderID = :orderId
                 FOR UPDATE"
            );
            $invStmt->execute([':branch' => $branchId, ':orderId' => $orderId]);
            foreach ($invStmt->fetchAll() as $row) {
                if (empty($row['Inv_Id'])) {
                    throw new RuntimeException('No branch inventory row for product ' . $row['Item_ProdId']);
                }
                if ((int) $row['stock'] < (int) $row['Item_Quantity']) {
                    throw new RuntimeException('Insufficient branch inventory for product ' . $row['Item_ProdId']);
                }
            }

            $stmt = $this->db->prepare("UPDATE Orders SET Order_Status = 'Confirmed', Order_VerifiedBy = :emp WHERE Order_Id = :id AND Order_Status = 'Pending'");
            $stmt->execute([':emp' => $employeeId, ':id' => $orderId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Order could not be confirmed.');
            }
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function cancelSalesOrder(string $orderId, string $employeeId): void {
        $stmt = $this->db->prepare(
            "UPDATE Orders
             SET Order_Status = 'Cancelled'
             WHERE Order_Id = :id
               AND (
                    Order_Status = 'Pending'
                    OR (Order_Status = 'Confirmed' AND Order_VerifiedBy = :emp)
               )"
        );
        $stmt->execute([':id' => $orderId, ':emp' => $employeeId]);
        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Sales can cancel only pending orders or confirmed orders they verified.');
        }
    }
}
