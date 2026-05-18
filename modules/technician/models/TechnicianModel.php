<?php
declare(strict_types=1);

class TechnicianModel extends BaseModel {
    private function generateId(string $prefix): string {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

    public function dashboardSummary(string $empId): array {
        $stmt = $this->db->prepare(
            "SELECT Tix_Status, COUNT(*) AS total
             FROM Service_Ticket
             WHERE Tix_EmpId = :emp
             GROUP BY Tix_Status"
        );
        $stmt->execute([':emp' => $empId]);
        $summary = ['Pending' => 0, 'In Progress' => 0, 'Completed' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $summary[(string) $row['Tix_Status']] = (int) $row['total'];
        }
        return $summary;
    }

    public function getAssignedTickets(string $empId, int $limit = 0): array {
        $sql = "SELECT t.*, c.Cus_Fname, c.Cus_Lname, c.Cus_Email, o.Order_InvoiceNo, o.Order_Status
                FROM Service_Ticket t
                INNER JOIN Customer c ON c.Cus_Id = t.Tix_CusId
                LEFT JOIN Orders o ON o.Order_Id = t.Tix_OrderID
                WHERE t.Tix_EmpId = :emp
                ORDER BY t.Tix_CreatedAt DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':emp', $empId);
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCompletedOrdersWithoutTicket(): array {
        $sql = "SELECT o.Order_Id, o.Order_InvoiceNo, o.Order_Date, c.Cus_Fname, c.Cus_Lname
                FROM Orders o
                INNER JOIN Customer c ON c.Cus_Id = o.Order_CusId
                LEFT JOIN Service_Ticket t ON t.Tix_OrderID = o.Order_Id
                WHERE o.Order_Status = 'Completed' AND t.Tix_Id IS NULL
                ORDER BY o.Order_Date DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function createAssignedTicket(string $orderId, string $empId, string $problemInfo): void {
        $stmt = $this->db->prepare(
            "SELECT o.Order_CusId
             FROM Orders o
             LEFT JOIN Service_Ticket t ON t.Tix_OrderID = o.Order_Id
             WHERE o.Order_Id = :oid AND o.Order_Status = 'Completed' AND t.Tix_Id IS NULL
             LIMIT 1"
        );
        $stmt->execute([':oid' => $orderId]);
        $customerId = $stmt->fetchColumn();
        if (!$customerId) {
            throw new RuntimeException('Technicians can create tickets only for completed orders without an existing ticket.');
        }

        $insert = $this->db->prepare(
            "INSERT INTO Service_Ticket (Tix_Id, Tix_EmpId, Tix_CusId, Tix_OrderID, Tix_ProblemInfo, Tix_Status, Tix_CreatedAt)
             VALUES (:id, :emp, :cus, :oid, :info, 'Pending', NOW())"
        );
        $insert->execute([
            ':id' => $this->generateId('T'),
            ':emp' => $empId,
            ':cus' => $customerId,
            ':oid' => $orderId,
            ':info' => $problemInfo,
        ]);
    }

    public function updateAssignedTicket(string $ticketId, string $empId, string $status, string $problemInfo): void {
        $chk = $this->db->prepare("SELECT 1 FROM Service_Ticket WHERE Tix_Id = :id AND Tix_EmpId = :emp LIMIT 1");
        $chk->execute([':id' => $ticketId, ':emp' => $empId]);
        if (!$chk->fetchColumn()) {
            throw new RuntimeException('Ticket not assigned to you.');
        }

        $stmt = $this->db->prepare(
            "UPDATE Service_Ticket SET Tix_Status = :status, Tix_ProblemInfo = :info WHERE Tix_Id = :id AND Tix_EmpId = :emp"
        );
        $stmt->execute([':status' => $status, ':info' => $problemInfo, ':id' => $ticketId, ':emp' => $empId]);
    }

    public function deleteAssignedTicket(string $ticketId, string $empId): void {
        $stmt = $this->db->prepare(
            "DELETE FROM Service_Ticket
             WHERE Tix_Id = :id AND Tix_EmpId = :emp AND Tix_Status = 'Pending'"
        );
        $stmt->execute([':id' => $ticketId, ':emp' => $empId]);
        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Only pending tickets assigned to you can be deleted.');
        }
    }
}
