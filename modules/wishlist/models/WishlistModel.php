<?php
declare(strict_types=1);

class WishlistModel extends BaseModel {
    public function hasWishlistItem(string $customerId, string $productId): bool {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM Wishlist WHERE Wish_CusId = :cid AND Wish_ProdId = :pid LIMIT 1"
        );
        $stmt->execute([':cid' => $customerId, ':pid' => $productId]);
        return (bool) $stmt->fetchColumn();
    }

    public function addToWishlist(string $customerId, string $productId): bool {
        if ($this->hasWishlistItem($customerId, $productId)) {
            return true;
        }

        $wishId = $this->generateId('W');
        $stmt = $this->db->prepare(
            "INSERT INTO Wishlist (Wish_Id, Wish_CusId, Wish_ProdId, Wish_AddedAt)
             VALUES (:id, :cid, :pid, NOW())"
        );
        return $stmt->execute([':id' => $wishId, ':cid' => $customerId, ':pid' => $productId]);
    }

    public function removeFromWishlist(string $customerId, string $productId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM Wishlist WHERE Wish_CusId = :cid AND Wish_ProdId = :pid"
        );
        return $stmt->execute([':cid' => $customerId, ':pid' => $productId]);
    }

    public function getWishlistProductIds(string $customerId): array {
        $stmt = $this->db->prepare("SELECT Wish_ProdId FROM Wishlist WHERE Wish_CusId = :cid");
        $stmt->execute([':cid' => $customerId]);
        $rows = $stmt->fetchAll();
        return array_column($rows, 'Wish_ProdId');
    }

    public function getWishlistItems(string $customerId): array {
        $stmt = $this->db->prepare(
            "SELECT p.*
             FROM Wishlist w
             INNER JOIN Product p ON p.Prod_Id = w.Wish_ProdId
             WHERE w.Wish_CusId = :cid AND p.Prod_Status = 'Active'
             ORDER BY w.Wish_AddedAt DESC"
        );
        $stmt->execute([':cid' => $customerId]);
        return $stmt->fetchAll();
    }

    private function generateId(string $prefix): string {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }
}

