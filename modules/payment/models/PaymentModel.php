<?php
declare(strict_types=1);

class PaymentModel extends BaseModel {
    private function generateId(string $prefix): string {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

    public function getOrder(string $orderId): ?array {
        $stmt = $this->db->prepare(
            "SELECT o.*, c.Cus_IdAttachment
             FROM Orders o
             INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
             WHERE o.Order_Id = :id"
        );
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
            if ($order['Order_Status'] !== 'Confirmed') {
                throw new RuntimeException('Order must be confirmed before payment.');
            }
            $existing = $this->db->prepare("SELECT 1 FROM Payment WHERE Pay_OrderID = :oid AND Pay_Status = 'Verified' LIMIT 1");
            $existing->execute([':oid' => $orderId]);
            if ($existing->fetchColumn()) {
                throw new RuntimeException('This order already has a verified payment.');
            }
            if (!in_array($method, ['COD', 'GCash', 'Maya', 'Bank Transfer'], true)) {
                throw new RuntimeException('Invalid payment method.');
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
                ':details' => $method === 'COD' ? 'COD - ' . $region : 'Simulated ' . $method,
            ]);

            $orderUpdate = $this->db->prepare("UPDATE Orders SET Order_Status = 'Paid' WHERE Order_Id = :id AND Order_Status = 'Confirmed'");
            $orderUpdate->execute([':id' => $orderId]);
            if ($orderUpdate->rowCount() !== 1) {
                $statusStmt = $this->db->prepare("SELECT Order_Status FROM Orders WHERE Order_Id = :id");
                $statusStmt->execute([':id' => $orderId]);
                if ($statusStmt->fetchColumn() !== 'Paid') {
                    throw new RuntimeException('Order could not be marked as paid.');
                }
            }
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
