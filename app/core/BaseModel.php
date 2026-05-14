<?php
// app/core/BaseModel.php
declare(strict_types=1);

class BaseModel {
    protected PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    protected function beginTransaction(): void {
        $this->db->beginTransaction();
    }

    protected function commit(): void {
        $this->db->commit();
    }

    protected function rollback(): void {
        if ($this->db->inTransaction()) $this->db->rollBack();
    }
}
