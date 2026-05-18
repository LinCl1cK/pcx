<?php
declare(strict_types=1);

class FulfillmentModel extends BaseModel {
    public function getPaidOrders(): array {
        $stmt = $this->db->query(
            "SELECT o.*, p.Pay_Id, p.Pay_Method, p.Pay_Status
             FROM Orders o
             LEFT JOIN Payment p ON p.Pay_OrderID = o.Order_Id
             WHERE o.Order_Status = 'Paid'
                OR (o.Order_Status = 'Confirmed' AND p.Pay_Method = 'COD' AND p.Pay_Status = 'Pending')
             ORDER BY o.Order_Date ASC"
        );
        return $stmt->fetchAll();
    }

    public function completeOrder(string $orderId): void {
        $stmt = $this->db->prepare(
            "UPDATE Orders o
             LEFT JOIN Payment p ON p.Pay_OrderID = o.Order_Id
             SET o.Order_Status = 'Completed'
             WHERE o.Order_Id = :id
               AND (
                    o.Order_Status = 'Paid'
                    OR (o.Order_Status = 'Confirmed' AND p.Pay_Method = 'COD' AND p.Pay_Status = 'Pending')
               )"
        );
        $stmt->execute([':id' => $orderId]);
        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Only paid orders or confirmed COD orders can be completed.');
        }
    }
}
