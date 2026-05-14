<?php
declare(strict_types=1);

class FulfillmentModel extends BaseModel {
    public function getPaidOrders(): array {
        $stmt = $this->db->query("SELECT * FROM Orders WHERE Order_Status = 'Paid' ORDER BY Order_Date ASC");
        return $stmt->fetchAll();
    }

    public function completeOrder(string $orderId): void {
        $stmt = $this->db->prepare("UPDATE Orders SET Order_Status = 'Completed' WHERE Order_Id = :id AND Order_Status = 'Paid'");
        $stmt->execute([':id' => $orderId]);
    }
}
