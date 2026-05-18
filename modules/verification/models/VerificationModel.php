<?php
declare(strict_types=1);

class VerificationModel extends BaseModel {
    private function getPickupBranchId(array $order): ?string {
        if (($order['Order_Shipping'] ?? '') !== 'Pickup') {
            return null;
        }
        if (preg_match('/^Pickup at \[([^\]]+)\]/', (string) ($order['Order_DestinationAddress'] ?? ''), $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function getPendingOrders(): array {
        $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email, c.Cus_IdAttachment
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                WHERE o.Order_Status = 'Pending'
                ORDER BY o.Order_Date ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function confirmOrder(string $orderId, string $employeeId, bool $idChecked): void {
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
                throw new RuntimeException('Verifier branch not found.');
            }
            $pickupBranchId = $this->getPickupBranchId($order);
            if ($pickupBranchId !== null && $pickupBranchId !== $branchId) {
                throw new RuntimeException('Pickup orders must be verified by staff from the selected branch.');
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
            $stmt = $this->db->prepare("UPDATE Orders SET Order_Status='Confirmed', Order_VerifiedBy=:emp WHERE Order_Id=:id AND Order_Status='Pending'");
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

    public function rejectOrder(string $orderId): void {
        $stmt = $this->db->prepare("UPDATE Orders SET Order_Status='Cancelled' WHERE Order_Id=:id AND Order_Status='Pending'");
        $stmt->execute([':id' => $orderId]);
    }
}
