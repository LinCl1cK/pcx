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

    public function hasAnyPayment(string $orderId): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM Payment WHERE Pay_OrderID = :oid AND Pay_Status IN ('Pending', 'Verified') LIMIT 1");
        $stmt->execute([':oid' => $orderId]);
        return (bool) $stmt->fetchColumn();
    }

    public function createPendingPayment(string $orderId, string $customerId, string $method, float $amount, ?string $gatewayRef = null): string {
        $this->db->beginTransaction();
        try {
            $order = $this->getOrder($orderId);
            if (!$order) {
                throw new RuntimeException('Order not found.');
            }
            if (!in_array($order['Order_Status'], ['Pending', 'Confirmed'], true)) {
                throw new RuntimeException('Payment can be submitted only for pending or confirmed orders.');
            }
            if ($order['Order_CusId'] !== $customerId) {
                throw new RuntimeException('Payment customer does not match the order.');
            }
            $existing = $this->db->prepare("SELECT 1 FROM Payment WHERE Pay_OrderID = :oid AND Pay_Status IN ('Pending', 'Verified') LIMIT 1");
            $existing->execute([':oid' => $orderId]);
            if ($existing->fetchColumn()) {
                throw new RuntimeException('This order already has a payment submitted.');
            }
            if (!in_array($method, ['COD', 'Cashless'], true)) {
                throw new RuntimeException('Invalid payment method.');
            }

            if ($method === 'Cashless' && !$gatewayRef) {
                throw new RuntimeException('Cashless payment requires a transaction reference.');
            }

            $payId = $this->generateId('P');
            $stmt = $this->db->prepare(
                "INSERT INTO Payment
                (Pay_Id, Pay_OrderID, Pay_CusId, Pay_Method, Pay_PaidAt, Pay_Amount, Pay_Status, Pay_GatewayRef)
                VALUES (:id, :oid, :cid, :method, NOW(), :amount, 'Pending', :gatewayRef)"
            );
            $stmt->execute([
                ':id' => $payId,
                ':oid' => $orderId,
                ':cid' => $customerId,
                ':method' => $method,
                ':amount' => $amount,
                ':gatewayRef' => $gatewayRef,
            ]);

            $this->db->commit();
            return $payId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function confirmPayment(string $paymentId, bool $allowCod = true): array {
        $this->db->beginTransaction();
        try {
            $paymentStmt = $this->db->prepare(
                "SELECT p.Pay_Id, p.Pay_Method, p.Pay_Status, o.Order_Id, o.Order_Status
                 FROM Payment p
                 INNER JOIN Orders o ON o.Order_Id = p.Pay_OrderID
                 WHERE p.Pay_Id = :id
                 LIMIT 1
                 FOR UPDATE"
            );
            $paymentStmt->execute([':id' => $paymentId]);
            $payment = $paymentStmt->fetch();
            if (!$payment || $payment['Pay_Status'] !== 'Pending') {
                throw new RuntimeException('Only pending payments can be confirmed.');
            }

            if ($payment['Pay_Method'] === 'COD') {
                if (!$allowCod) {
                    throw new RuntimeException('COD payment must be confirmed by a sales representative.');
                }
                if ($payment['Order_Status'] !== 'Completed') {
                    throw new RuntimeException('COD payment can be confirmed only after admin marks fulfillment completed.');
                }
            }
            if ($payment['Pay_Method'] !== 'COD' && $payment['Order_Status'] !== 'Confirmed') {
                throw new RuntimeException('Cashless payment can be confirmed only for confirmed orders.');
            }

            $stmt = $this->db->prepare(
                "UPDATE Payment
                 SET Pay_Status = 'Verified'
                 WHERE Pay_Id = :id AND Pay_Status = 'Pending'"
            );
            $stmt->execute([':id' => $paymentId]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('Only pending payments can be confirmed.');
            }

            if ($payment['Pay_Method'] !== 'COD') {
                $orderStmt = $this->db->prepare(
                    "UPDATE Orders o
                     INNER JOIN Payment p ON p.Pay_OrderID = o.Order_Id
                     SET o.Order_Status = 'Paid'
                     WHERE p.Pay_Id = :id AND o.Order_Status = 'Confirmed'"
                );
                $orderStmt->execute([':id' => $paymentId]);
                if ($orderStmt->rowCount() !== 1) {
                    $check = $this->db->prepare(
                        "SELECT o.Order_Status
                         FROM Orders o
                         INNER JOIN Payment p ON p.Pay_OrderID = o.Order_Id
                         WHERE p.Pay_Id = :id"
                    );
                    $check->execute([':id' => $paymentId]);
                    if ($check->fetchColumn() !== 'Paid') {
                        throw new RuntimeException('Payment verified, but order could not be marked paid.');
                    }
                }
            }

            $this->db->commit();
            return [
                'method' => (string) $payment['Pay_Method'],
                'order_status' => $payment['Pay_Method'] === 'COD' ? 'Completed' : 'Paid',
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function listAllWithOrders(): array {
        $sql = "SELECT p.Pay_Id, p.Pay_OrderID, p.Pay_CusId, p.Pay_Method, p.Pay_Amount, p.Pay_Status,
                       p.Pay_GatewayRef, p.Pay_PaidAt, o.Order_InvoiceNo, o.Order_Status
                FROM Payment p
                INNER JOIN Orders o ON o.Order_Id = p.Pay_OrderID
                ORDER BY p.Pay_PaidAt DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
