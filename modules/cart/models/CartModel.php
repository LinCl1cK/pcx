<?php
declare(strict_types=1);

class CartModel extends BaseModel {
    private function generateId(string $prefix): string {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

    private function getOrCreateCartId(string $customerId): string {
        $stmt = $this->db->prepare("SELECT Cart_Id FROM Cart WHERE Cart_CusId = :cid LIMIT 1");
        $stmt->execute([':cid' => $customerId]);
        $cartId = (string) ($stmt->fetchColumn() ?: '');
        if ($cartId !== '') {
            return $cartId;
        }

        $cartId = $this->generateId('T');
        $insert = $this->db->prepare(
            "INSERT INTO Cart (Cart_Id, Cart_CusId, Cart_CreatedAt, Cart_LastUpdated)
             VALUES (:id, :cid, NOW(), NOW())"
        );
        $insert->execute([':id' => $cartId, ':cid' => $customerId]);
        return $cartId;
    }

    private function getAvailableStock(string $productId): int {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(Inv_StockQty), 0)
             FROM Inventory
             WHERE Inv_ProdId = :pid"
        );
        $stmt->execute([':pid' => $productId]);
        return (int) $stmt->fetchColumn();
    }

    public function addToCart(string $customerId, string $productId, int $quantity = 1): bool {
        $quantity = max(1, $quantity);
        $cartId = $this->getOrCreateCartId($customerId);

        $itemStmt = $this->db->prepare(
            "SELECT Cait_Id, Cait_Quantity FROM Cart_Item
             WHERE Cait_CartId = :cart AND Cait_ProdId = :pid LIMIT 1"
        );
        $itemStmt->execute([':cart' => $cartId, ':pid' => $productId]);
        $existing = $itemStmt->fetch();
        $existingQty = $existing ? (int) $existing['Cait_Quantity'] : 0;
        $availableStock = $this->getAvailableStock($productId);
        if ($availableStock <= 0) {
            throw new RuntimeException('This product is out of stock.');
        }
        if ($existingQty + $quantity > $availableStock) {
            throw new RuntimeException('Requested quantity exceeds available stock.');
        }

        if ($existing) {
            $newQty = $existingQty + $quantity;
            $update = $this->db->prepare(
                "UPDATE Cart_Item SET Cait_Quantity = :qty
                 WHERE Cait_Id = :id"
            );
            return $update->execute([':qty' => $newQty, ':id' => $existing['Cait_Id']]);
        }

        $prodStmt = $this->db->prepare("SELECT Prod_Price FROM Product WHERE Prod_Id = :pid LIMIT 1");
        $prodStmt->execute([':pid' => $productId]);
        $price = $prodStmt->fetchColumn();
        if ($price === false) {
            return false;
        }

        $itemId = $this->generateId('I');
        $insert = $this->db->prepare(
            "INSERT INTO Cart_Item (Cait_Id, Cait_CartId, Cait_ProdId, Cait_Quantity, Cait_Price)
             VALUES (:id, :cart, :pid, :qty, :price)"
        );
        return $insert->execute([
            ':id' => $itemId,
            ':cart' => $cartId,
            ':pid' => $productId,
            ':qty' => $quantity,
            ':price' => $price,
        ]);
    }

    public function updateCartQuantity(string $customerId, string $productId, int $quantity): bool {
        $cartId = $this->getOrCreateCartId($customerId);
        if ($quantity <= 0) {
            return $this->removeFromCart($customerId, $productId);
        }
        $availableStock = $this->getAvailableStock($productId);
        if ($availableStock <= 0) {
            throw new RuntimeException('This product is out of stock.');
        }
        if ($quantity > $availableStock) {
            throw new RuntimeException('Requested quantity exceeds available stock.');
        }

        $stmt = $this->db->prepare(
            "UPDATE Cart_Item SET Cait_Quantity = :qty
             WHERE Cait_CartId = :cart AND Cait_ProdId = :pid"
        );
        return $stmt->execute([':qty' => $quantity, ':cart' => $cartId, ':pid' => $productId]);
    }

    public function removeFromCart(string $customerId, string $productId): bool {
        $cartId = $this->getOrCreateCartId($customerId);
        $stmt = $this->db->prepare(
            "DELETE FROM Cart_Item WHERE Cait_CartId = :cart AND Cait_ProdId = :pid"
        );
        return $stmt->execute([':cart' => $cartId, ':pid' => $productId]);
    }

    public function getCartItems(string $customerId): array {
        $cartId = $this->getOrCreateCartId($customerId);
        $stmt = $this->db->prepare(
            "SELECT
                ci.Cait_ProdId AS Prod_Id,
                ci.Cait_Quantity AS quantity,
                ci.Cait_Price AS unit_price,
                (ci.Cait_Quantity * ci.Cait_Price) AS line_total,
                p.Prod_Name,
                p.Prod_Brand,
                p.Prod_Image,
                COALESCE((
                    SELECT SUM(i.Inv_StockQty)
                    FROM Inventory i
                    WHERE i.Inv_ProdId = p.Prod_Id
                ), 0) AS available_stock
             FROM Cart_Item ci
             INNER JOIN Product p ON p.Prod_Id = ci.Cait_ProdId
             WHERE ci.Cait_CartId = :cart
             ORDER BY p.Prod_Name ASC"
        );
        $stmt->execute([':cart' => $cartId]);
        return $stmt->fetchAll();
    }

    public function getCartProductQuantities(string $customerId): array {
        $cartId = $this->getOrCreateCartId($customerId);
        $stmt = $this->db->prepare(
            "SELECT Cait_ProdId, Cait_Quantity FROM Cart_Item WHERE Cait_CartId = :cart"
        );
        $stmt->execute([':cart' => $cartId]);
        $items = $stmt->fetchAll();
        $map = [];
        foreach ($items as $item) {
            $map[$item['Cait_ProdId']] = (int) $item['Cait_Quantity'];
        }
        return $map;
    }
}
