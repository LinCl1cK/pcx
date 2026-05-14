<?php
declare(strict_types=1);

class VerificationModel extends BaseModel {
    public function getPendingOrders(): array {
        $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                WHERE o.Order_Status = 'Pending'
                ORDER BY o.Order_Date ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function confirmOrder(string $orderId, string $employeeId, bool $idChecked): void {
        $orderStmt = $this->db->prepare("SELECT * FROM Orders WHERE Order_Id = :id AND Order_Status = 'Pending' LIMIT 1");
        $orderStmt->execute([':id' => $orderId]);
        $order = $orderStmt->fetch();
        if (!$order) {
            throw new RuntimeException('Pending order not found.');
        }

        if ((float) $order['Order_TotalAmount'] >= 50000 && !$idChecked) {
            throw new RuntimeException('Government ID verification is required for high-value transactions.');
        }

        $branchStmt = $this->db->prepare("SELECT Emp_BranchId FROM Employee WHERE Emp_Id = :id LIMIT 1");
        $branchStmt->execute([':id' => $employeeId]);
        $branchId = (string) ($branchStmt->fetchColumn() ?: '');
        if ($branchId === '') {
            throw new RuntimeException('Verifier branch not found.');
        }

        $invStmt = $this->db->prepare(
            "SELECT oi.Item_ProdId, oi.Item_Quantity, IFNULL(i.Inv_StockQty, 0) AS stock
             FROM Order_Item oi
             LEFT JOIN Inventory i ON i.Inv_ProdId = oi.Item_ProdId AND i.Inv_BranchId = :branch
             WHERE oi.Item_OrderID = :orderId"
        );
        $invStmt->execute([':branch' => $branchId, ':orderId' => $orderId]);
        foreach ($invStmt->fetchAll() as $row) {
            if ((int) $row['stock'] < (int) $row['Item_Quantity']) {
                throw new RuntimeException('Insufficient branch inventory for product ' . $row['Item_ProdId']);
            }
        }
        $stmt = $this->db->prepare("UPDATE Orders SET Order_Status='Confirmed', Order_VerifiedBy=:emp WHERE Order_Id=:id");
        $stmt->execute([':emp' => $employeeId, ':id' => $orderId]);
    }

    public function rejectOrder(string $orderId): void {
        $stmt = $this->db->prepare("UPDATE Orders SET Order_Status='Cancelled' WHERE Order_Id=:id AND Order_Status='Pending'");
        $stmt->execute([':id' => $orderId]);
    }
}
