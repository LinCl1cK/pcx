<?php

declare(strict_types=1);

class OrderModel extends BaseModel
{
    private function generateId(string $prefix): string
    {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

    // ----------------------------------------------------------------
    // MIGRATION: trg_Order_StatusFlow + trg_Order_CancelCheck
    //
    // The legacy SQL triggers enforced that:
    //   - Orders may only advance through valid state transitions
    //   - 'Paid' and 'Completed' orders are locked against cancellation
    //
    // These rules are now enforced in this central PHP method. Every
    // controller that changes order status MUST call this method first.
    // ----------------------------------------------------------------

    /**
     * Valid state machine transitions (mirrors trg_Order_StatusFlow).
     *
     * Allowed paths:
     *   Pending   → Confirmed | Cancelled
     *   Confirmed → Paid | Cancelled
     *   Paid      → Completed
     *   Completed → (terminal — no further transitions)
     *   Cancelled → (terminal — no further transitions)
     */
    private const STATUS_TRANSITIONS = [
        'Pending'   => ['Confirmed', 'Cancelled'],
        'Confirmed' => ['Paid', 'Cancelled'],
        'Paid'      => ['Completed'],
        'Completed' => [],
        'Cancelled' => [],
    ];

    /**
     * MIGRATION: trg_Order_StatusFlow
     *
     * Validates that a status transition is legal. Throws RuntimeException
     * if the move is invalid, so callers can wrap this in a try/catch and
     * roll back any open transaction.
     *
     * @param string $currentStatus  The order's current DB status.
     * @param string $targetStatus   The requested new status.
     * @throws RuntimeException      On illegal transition.
     */
    public function validateStatusTransition(string $currentStatus, string $targetStatus): void
    {
        $allowed = self::STATUS_TRANSITIONS[$currentStatus] ?? [];
        if (!in_array($targetStatus, $allowed, true)) {
            // trg_Order_CancelCheck is implicitly covered: 'Paid' and 'Completed'
            // have empty or non-Cancelled allowed lists, so cancellation of a
            // terminal order is caught here automatically.
            throw new RuntimeException(
                sprintf(
                    'Invalid order transition: cannot move from "%s" to "%s".',
                    $currentStatus,
                    $targetStatus
                )
            );
        }
    }

    /**
     * MIGRATION: sp_CreateOrder
     *
     * Atomically creates an order from the customer's cart. Validates stock,
     * calculates VAT, generates an invoice number, inserts order rows and
     * order items, then clears the cart — all inside one transaction.
     */
    public function placeOrderFromCart(
        string $customerId,
        string $shipping,
        string $destinationAddress,
        ?string $contactNo,
        ?string $branchId = null
    ): string {
        $this->db->beginTransaction();
        try {
            // 1. Fetch Cart ID
            $cartIdStmt = $this->db->prepare("SELECT Cart_Id FROM Cart WHERE Cart_CusId = :cid LIMIT 1");
            $cartIdStmt->execute([':cid' => $customerId]);
            $cartId = (string) ($cartIdStmt->fetchColumn() ?: '');
            if ($cartId === '') {
                throw new RuntimeException('No cart found.');
            }

            // 2. Fetch and Validate Items / Stock
            $itemsStmt = $this->db->prepare(
                "SELECT ci.*, COALESCE((
                    SELECT SUM(i.Inv_StockQty)
                    FROM Inventory i
                    WHERE i.Inv_ProdId = ci.Cait_ProdId
                ), 0) AS available_stock
                 FROM Cart_Item ci
                 WHERE ci.Cait_CartId = :cart"
            );
            $itemsStmt->execute([':cart' => $cartId]);
            $items = $itemsStmt->fetchAll();
            if (empty($items)) {
                throw new RuntimeException('Cart is empty.');
            }

            $subtotal = 0.0;
            foreach ($items as $item) {
                if ((int) $item['available_stock'] <= 0) {
                    throw new RuntimeException('One or more cart products are out of stock.');
                }
                if ((int) $item['Cait_Quantity'] > (int) $item['available_stock']) {
                    throw new RuntimeException('One or more cart quantities exceed available stock.');
                }
                $subtotal += (float) $item['Cait_Price'] * (int) $item['Cait_Quantity'];
            }
            $vat   = round($subtotal * 0.12, 2);
            $total = round($subtotal + $vat, 2);

            // 3. Insert Order Record
            $orderId   = $this->generateId('O');
            $invoiceNo = 'INV-' . date('Ymd') . '-' . substr($orderId, -4);

            $stmtOrder = $this->db->prepare(
                "INSERT INTO Orders
                 (Order_Id, Order_Date, Order_Status, Order_Shipping, Order_DestinationAddress,
                  Order_ContactNo, Order_CusId, Order_InvoiceNo, Order_InvoiceDate,
                  Order_VAT, Order_TotalAmount, Order_BranchId)
                 VALUES
                 (:id, NOW(), 'Pending', :ship, :address, :contact, :cid, :invoice, NOW(), :vat, :total, :branch)"
            );
            $stmtOrder->execute([
                ':id'      => $orderId,
                ':ship'    => $shipping,
                ':address' => $destinationAddress,
                ':contact' => $contactNo,
                ':cid'     => $customerId,
                ':invoice' => $invoiceNo,
                ':vat'     => $vat,
                ':total'   => $total,
                ':branch'  => $branchId, // NULL for open-pool deliveries
            ]);

            // 4. Insert Order Items
            $stmtItem = $this->db->prepare(
                "INSERT INTO Order_Item (Item_Id, Item_OrderID, Item_ProdId, Item_Quantity, Item_Price)
                 VALUES (:id, :orderId, :prodId, :qty, :price)"
            );
            foreach ($items as $item) {
                $stmtItem->execute([
                    ':id'      => $this->generateId('I'),
                    ':orderId' => $orderId,
                    ':prodId'  => $item['Cait_ProdId'],
                    ':qty'     => $item['Cait_Quantity'],
                    ':price'   => $item['Cait_Price'],
                ]);
            }

            // 5. Clear Cart
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

    /**
     * MIGRATION: trg_Order_CancelCheck (explicit cancel entry point)
     *
     * Atomically cancels an order after verifying the transition is legal.
     * 'Paid' and 'Completed' orders are hard-blocked by validateStatusTransition().
     */
    public function cancelOrder(string $orderId, ?string $byEmployeeId = null): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT Order_Status FROM Orders WHERE Order_Id = :id LIMIT 1 FOR UPDATE"
            );
            $stmt->execute([':id' => $orderId]);
            $current = $stmt->fetchColumn();
            if ($current === false) {
                throw new RuntimeException('Order not found.');
            }

            // --- trg_Order_CancelCheck / trg_Order_StatusFlow (PHP layer) ---
            $this->validateStatusTransition((string) $current, 'Cancelled');
            // --- end guard ---

            $update = $this->db->prepare(
                "UPDATE Orders SET Order_Status = 'Cancelled' WHERE Order_Id = :id"
            );
            $update->execute([':id' => $orderId]);
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // ----------------------------------------------------------------
    // Read helpers
    // ----------------------------------------------------------------

    public function getCustomerForCheckout(string $customerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT Cus_Id, Cus_Fname, Cus_Lname, Cus_Email, Cus_ContactNo, Cus_Address, Cus_IdAttachment
             FROM Customer
             WHERE Cus_Id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $customerId]);
        return $stmt->fetch() ?: [];
    }

    public function updateCustomerIdAttachment(string $customerId, string $path): void
    {
        $stmt = $this->db->prepare("UPDATE Customer SET Cus_IdAttachment = :path WHERE Cus_Id = :id");
        $stmt->execute([':path' => $path, ':id' => $customerId]);
    }

    public function getPickupBranchesForCart(string $customerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT b.Branch_Id, b.Branch_Name, b.Branch_Location, b.Branch_ContactNo
             FROM Branch b
             INNER JOIN Cart c ON c.Cart_CusId = :cid
             INNER JOIN Cart_Item ci ON ci.Cait_CartId = c.Cart_Id
             LEFT JOIN Inventory i ON i.Inv_BranchId = b.Branch_Id AND i.Inv_ProdId = ci.Cait_ProdId
             GROUP BY b.Branch_Id, b.Branch_Name, b.Branch_Location, b.Branch_ContactNo
             HAVING SUM(CASE WHEN COALESCE(i.Inv_StockQty, 0) >= ci.Cait_Quantity THEN 1 ELSE 0 END) = COUNT(*)
             ORDER BY b.Branch_Name ASC"
        );
        $stmt->execute([':cid' => $customerId]);
        return $stmt->fetchAll();
    }

    public function getPickupBranchForCart(string $customerId, string $branchId): ?array
    {
        foreach ($this->getPickupBranchesForCart($customerId) as $branch) {
            // trim() both sides: DB CHAR(10) padding vs plain form submission
            if (trim((string) $branch['Branch_Id']) === trim($branchId)) {
                return $branch;
            }
        }
        return null;
    }

    public function formatPickupAddress(array $branch): string
    {
        return sprintf(
            'Pickup at [%s] %s - %s (Contact: %s)',
            trim((string) ($branch['Branch_Id'] ?? '')),
            (string) ($branch['Branch_Name'] ?? ''),
            (string) ($branch['Branch_Location'] ?? ''),
            (string) ($branch['Branch_ContactNo'] ?? '')
        );
    }

    public function calculateCartTotal(string $customerId): float
    {
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

    public function getOrderById(string $orderId): ?array
    {
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

    public function getCustomerOrders(string $customerId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM Orders WHERE Order_CusId = :cid ORDER BY Order_Date DESC");
        $stmt->execute([':cid' => $customerId]);
        return $stmt->fetchAll();
    }

    public function listAllOrders(): array
    {
        $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                ORDER BY o.Order_Date DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function listOrdersForSalesRep(string $empId, ?string $branchId = null): array
    {
        if ($branchId !== null) {
            $stmt = $this->db->prepare(
                "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email
                 FROM Orders o
                 INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                 WHERE (o.Order_BranchId = :branchId OR (o.Order_Shipping = 'Delivery' AND o.Order_BranchId IS NULL))
                 AND o.Order_Status = 'Pending'"
            );
            $stmt->execute([':branchId' => trim($branchId)]);
            return $stmt->fetchAll();
        }

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

    public function getAllOrders(?string $scopeBranchId = null): array
    {
        if ($scopeBranchId !== null) {
            $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email
                    FROM Orders o
                    INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                    WHERE (o.Order_BranchId = :branchId OR (o.Order_Shipping = 'Delivery' AND o.Order_BranchId IS NULL))";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':branchId' => trim($scopeBranchId)]);
            return $stmt->fetchAll();
        }

        $sql = "SELECT o.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                ORDER BY o.Order_Date DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
