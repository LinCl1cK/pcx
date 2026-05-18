<?php
class InventoryModel extends BaseModel {
    public function getAllInventory(): array {
        $sql = "SELECT i.*, p.Prod_Name, b.Branch_Name
                FROM Inventory i
                JOIN Product p ON i.Inv_ProdId = p.Prod_Id
                JOIN Branch b ON i.Inv_BranchId = b.Branch_Id";
        return $this->db->query($sql)->fetchAll();
    }

    public function getInventoryById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Inventory WHERE Inv_Id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }

    public function updateStock(string $id, int $qty, int $reorder): bool {
        $stmt = $this->db->prepare("UPDATE Inventory SET Inv_StockQty=:q, Inv_ReorderLevel=:r, Inv_LastUpdated=NOW() WHERE Inv_Id=:id");
        return $stmt->execute([':q'=>$qty, ':r'=>$reorder, ':id'=>$id]);
    }

    public function getBranches(): array {
        return $this->db->query("SELECT * FROM Branch ORDER BY Branch_Name")->fetchAll();
    }

    public function getProducts(): array {
        return $this->db->query("SELECT Prod_Id, Prod_Name FROM Product ORDER BY Prod_Name")->fetchAll();
    }

    public function createStock(string $productId, string $branchId, int $qty, int $reorder): bool {
        $exists = $this->db->prepare("SELECT 1 FROM Inventory WHERE Inv_ProdId = :pid AND Inv_BranchId = :bid LIMIT 1");
        $exists->execute([':pid' => $productId, ':bid' => $branchId]);
        if ($exists->fetchColumn()) {
            throw new RuntimeException('Inventory row already exists for that product and branch.');
        }
        $id = 'INV-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $stmt = $this->db->prepare(
            "INSERT INTO Inventory (Inv_Id, Inv_ProdId, Inv_BranchId, Inv_StockQty, Inv_ReorderLevel, Inv_LastUpdated)
             VALUES (:id, :pid, :bid, :qty, :reorder, NOW())"
        );
        return $stmt->execute([':id' => $id, ':pid' => $productId, ':bid' => $branchId, ':qty' => $qty, ':reorder' => $reorder]);
    }

    public function transferStock(string $productId, string $fromBranch, string $toBranch, int $qty): void {
        $this->db->beginTransaction();
        try {
            $dec = $this->db->prepare(
                "UPDATE Inventory
                 SET Inv_StockQty = Inv_StockQty - :qty, Inv_LastUpdated = NOW()
                 WHERE Inv_ProdId = :pid AND Inv_BranchId = :bid"
            );
            $dec->execute([':qty' => $qty, ':pid' => $productId, ':bid' => $fromBranch]);
            if ($dec->rowCount() !== 1) {
                throw new RuntimeException('Source inventory row was not found.');
            }

            $inc = $this->db->prepare(
                "UPDATE Inventory
                 SET Inv_StockQty = Inv_StockQty + :qty, Inv_LastUpdated = NOW()
                 WHERE Inv_ProdId = :pid AND Inv_BranchId = :bid"
            );
            $inc->execute([':qty' => $qty, ':pid' => $productId, ':bid' => $toBranch]);
            if ($inc->rowCount() !== 1) {
                throw new RuntimeException('Destination inventory row was not found.');
            }
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
