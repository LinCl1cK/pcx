<?php
declare(strict_types=1);

class OrderModel extends BaseModel {
    private function generateId(string $prefix): string {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

    public function getCustomerForCheckout(string $customerId): array {
        $stmt = $this->db->prepare(
            "SELECT Cus_Id, Cus_Fname, Cus_Lname, Cus_Email, Cus_ContactNo, Cus_Address, Cus_IdAttachment
             FROM Customer
             WHERE Cus_Id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $customerId]);
        return $stmt->fetch() ?: [];
    }

    public function updateCustomerIdAttachment(string $customerId, string $path): void {
        $stmt = $this->db->prepare("UPDATE Customer SET Cus_IdAttachment = :path WHERE Cus_Id = :id");
        $stmt->execute([':path' => $path, ':id' => $customerId]);
    }

    public function calculateCartTotal(string $customerId): float {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(Cait_Price * Cait_Quantity), 0) AS subtotal
             FROM Cart c
             INNER JOIN Cart_Item ci ON ci.Cait_CartId = c.Cart_Id
             WHERE c.Cart_CusId = :cid"
        );
        $stmt->execute([':cid' => $customerId]);
        $subtotal = (float) $stmt->fetchColumn();
        if ($subtotal <= 0) {
            throw new RuntimeException('Cart is empty.');
        }
        return round($subtotal * 1.12, 2);
    }

    public function placeOrderFromCart(string $customerId, string $shipping, string $destinationAddress, ?string $contactNo): string {
        $this->db->beginTransaction();
        try {
            $cartIdStmt = $this->db->prepare("SELECT Cart_Id FROM Cart WHERE Cart_CusId = :cid LIMIT 1");
            $cartIdStmt->execute([':cid' => $customerId]);
            $cartId = (string) ($cartIdStmt->fetchColumn() ?: '');
            if ($cartId === '') {
                throw new RuntimeException('No cart found.');
            }

            $itemsStmt = $this->db->prepare("SELECT * FROM Cart_Item WHERE Cait_CartId = :cart");
            $itemsStmt->execute([':cart' => $cartId]);
            $items = $itemsStmt->fetchAll();
            if (empty($items)) {
                throw new RuntimeException('Cart is empty.');
            }

            $subtotal = 0.0;
            foreach ($items as $item) {
                $subtotal += (float) $item['Cait_Price'] * (int) $item['Cait_Quantity'];
            }
            $vat = round($subtotal * 0.12, 2);
            $total = round($subtotal + $vat, 2);

            $orderId = $this->generateId('O');
            $invoiceNo = 'INV-' . date('Ymd') . '-' . substr($orderId, -4);
            $stmtOrder = $this->db->prepare(
                "INSERT INTO Orders
                (Order_Id, Order_Date, Order_Status, Order_Shipping, Order_DestinationAddress, Order_ContactNo, Order_CusId, Order_InvoiceNo, Order_InvoiceDate, Order_VAT, Order_TotalAmount)
                VALUES
                (:id, NOW(), 'Pending', :ship, :address, :contact, :cid, :invoice, NOW(), :vat, :total)"
            );

            $stmtOrder->execute([
                ':id' => $orderId,
                ':ship' => $shipping,
                ':address' => $destinationAddress,
                ':contact' => $contactNo,
                ':cid' => $customerId,
                ':invoice' => $invoiceNo,
                ':vat' => $vat,
                ':total' => $total,
            ]);

            $stmtItem = $this->db->prepare(
                "INSERT INTO Order_Item (Item_Id, Item_OrderID, Item_ProdId, Item_Quantity, Item_Price)
                 VALUES (:id, :orderId, :prodId, :qty, :price)"
            );
            foreach ($items as $item) {
                $stmtItem->execute([
                    ':id' => $this->generateId('I'),
                    ':orderId' => $orderId,
                    ':prodId' => $item['Cait_ProdId'],
                    ':qty' => $item['Cait_Quantity'],
                    ':price' => $item['Cait_Price'],
                ]);
            }

            $this->db->prepare("DELETE FROM Cart_Item WHERE Cait_CartId = :cart")->execute([':cart' => $cartId]);
            $this->db->commit();
            return $orderId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function getOrderById(string $orderId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Orders WHERE Order_Id = :id");
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }

        $itemsStmt = $this->db->prepare(
            "SELECT oi.*, p.Prod_Name, p.Prod_Image
             FROM Order_Item oi
             INNER JOIN Product p ON p.Prod_Id = oi.Item_ProdId
             WHERE oi.Item_OrderID = :id"
        );
        $itemsStmt->execute([':id' => $orderId]);
        $order['items'] = $itemsStmt->fetchAll();
        return $order;
    }

    public function getCustomerOrders(string $customerId): array {
        $stmt = $this->db->prepare("SELECT * FROM Orders WHERE Order_CusId = :cid ORDER BY Order_Date DESC");
        $stmt->execute([':cid' => $customerId]);
        return $stmt->fetchAll();
    }

    public function listAllOrders(): array {
        $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                ORDER BY o.Order_Date DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function listOrdersForSalesRep(string $empId): array {
        $stmt = $this->db->prepare(
            "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email
             FROM Orders o
             INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
             WHERE o.Order_Status = 'Pending' OR o.Order_VerifiedBy = :emp
             ORDER BY o.Order_Date DESC"
        );
        $stmt->execute([':emp' => $empId]);
        return $stmt->fetchAll();
    }
}
