<?php
declare(strict_types=1);

class PaymentModel extends BaseModel {
    private function generateId(string $prefix): string {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

    public function getOrder(string $orderId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Orders WHERE Order_Id = :id");
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetch() ?: null;
    }

    public function simulatePayment(string $orderId, string $method, float $amount, string $region): string {
        $this->db->beginTransaction();
        try {
            $order = $this->getOrder($orderId);
            if (!$order) {
                throw new RuntimeException('Order not found.');
            }
            if (!in_array($order['Order_Status'], ['Confirmed', 'Paid'], true)) {
                throw new RuntimeException('Order must be confirmed before payment.');
            }

            if ($method === 'COD') {
                $cap = strtolower($region) === 'metro manila' ? 50000.00 : 30000.00;
                if ($amount > $cap) {
                    throw new RuntimeException('COD amount exceeds allowed cap for selected region.');
                }
            }

            $payId = $this->generateId('P');
            $stmt = $this->db->prepare(
                "INSERT INTO Payment
                (Pay_Id, Pay_OrderID, Pay_Method, Pay_PaidAt, Pay_Amount, Pay_Status, Pay_Details)
                VALUES (:id, :oid, :method, NOW(), :amount, 'Verified', :details)"
            );
            $stmt->execute([
                ':id' => $payId,
                ':oid' => $orderId,
                ':method' => $method,
                ':amount' => $amount,
                ':details' => 'Simulation - ' . $region,
            ]);

            $this->db->prepare("UPDATE Orders SET Order_Status = 'Paid' WHERE Order_Id = :id AND Order_Status = 'Confirmed'")
                ->execute([':id' => $orderId]);
            $this->db->commit();
            return $payId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function listAllWithOrders(): array {
        $sql = "SELECT p.*, o.Order_InvoiceNo, o.Order_Status, o.Order_CusId
                FROM Payment p
                INNER JOIN Orders o ON o.Order_Id = p.Pay_OrderID
                ORDER BY p.Pay_PaidAt DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
