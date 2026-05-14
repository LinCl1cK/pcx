<?php
declare(strict_types=1);

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'BaseModel.php';

class PromotionModel extends BaseModel {
    public function getActivePromotions(): array {
        try {
            $stmt = $this->db->query(
                "SELECT
                    Promo_Id AS id,
                    Promo_Title AS title,
                    Promo_Description AS description,
                    Promo_Banner AS banner
                 FROM Promotion
                 WHERE Promo_Status = 'Active'
                 ORDER BY Promo_Start DESC"
            );
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getAllPromotionsAdmin(): array {
        return $this->db->query("SELECT * FROM Promotion ORDER BY Promo_Id DESC")->fetchAll();
    }

    public function deletePromotion(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Promotion WHERE Promo_Id = ?");
        return $stmt->execute([$id]);
    }

    public function createPromotion(array $data): bool {
        $sql = "INSERT INTO Promotion (Promo_Title, Promo_Description, Promo_Banner, Promo_Status, Promo_Start, Promo_End)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['banner'],
            $data['status'],
            $data['start'] ?: null,
            $data['end'] ?: null,
        ]);
    }
}

