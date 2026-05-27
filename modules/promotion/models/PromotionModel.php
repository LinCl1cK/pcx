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
        // Generate a random integer for the ID (since it's explicitly required in the INSERT)
        $promoId = random_int(10000, 99999);
        
        // Exact SQL structure as requested
        $sql = "INSERT INTO Promotion (Promo_Id, Promo_Title, Promo_Description, Promo_Banner, Promo_Status, Promo_Start, Promo_End)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $promoId,
            $data['title'],
            $data['description'],
            $data['banner'],
            $data['status'],
            $data['start'] ?: null,
            $data['end'] ?: null,
        ]);
    }

    public function getPromotionById(int $id): ?array {
        $sql = "SELECT * FROM Promotion WHERE Promo_Id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch() ?: null;
    }

    public function updatePromotion(int $id, array $data): bool {
        $sql = "UPDATE Promotion 
                SET Promo_Title = ?, 
                    Promo_Description = ?, 
                    Promo_Banner = ?, 
                    Promo_Status = ?, 
                    Promo_Start = ?, 
                    Promo_End = ? 
                WHERE Promo_Id = ?";
                
        // Handle empty date strings from the HTML date picker by converting them to NULL
        $start = !empty($data['start']) ? $data['start'] : null;
        $end   = !empty($data['end'])   ? $data['end']   : null;

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['banner'],
            $data['status'],
            $start,
            $end,
            $id
        ]);
    }
}