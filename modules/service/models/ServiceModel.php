<?php

declare(strict_types=1);

class ServiceModel extends BaseModel
{
    private function generateId(string $prefix): string
    {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

    /** Completed orders that do not already have a ticket for that order. */
    public function getCompletedOrdersWithoutTicket(): array
    {
        $sql = "SELECT o.Order_Id, o.Order_CusId
                FROM Orders o
                LEFT JOIN Service_Ticket t ON t.Tix_OrderID = o.Order_Id
                WHERE o.Order_Status = 'Completed' AND t.Tix_Id IS NULL
                ORDER BY o.Order_Date DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getTechnicians(): array
    {
        return $this->db->query("SELECT * FROM Employee WHERE Emp_Position = 'Technician' ORDER BY Emp_Fname")->fetchAll();
    }

    public function getFirstTechnicianId(): ?string
    {
        $row = $this->db->query("SELECT Emp_Id FROM Employee WHERE Emp_Position = 'Technician' ORDER BY Emp_Id LIMIT 1")->fetch();
        return $row ? (string) $row['Emp_Id'] : null;
    }

    /** Updated to take the optional attachment string */
    public function createTicket(string $orderId, string $empId, string $diagnosis, ?string $attachment = null): void
    {
        $cusStmt = $this->db->prepare(
            "SELECT Order_CusId FROM Orders WHERE Order_Id = :id AND Order_Status = 'Completed' LIMIT 1"
        );
        $cusStmt->execute([':id' => $orderId]);
        $cusId = $cusStmt->fetchColumn();
        if (!$cusId) {
            throw new RuntimeException('Tickets can only be created for completed orders.');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO Service_Ticket (Tix_Id, Tix_EmpId, Tix_CusId, Tix_OrderID, Tix_ProblemInfo, Tix_Attachment, Tix_Status, Tix_CreatedAt)
             VALUES (:id, :emp, :cus, :oid, :diag, :attach, 'Pending', NOW())"
        );
        $stmt->execute([
            ':id' => $this->generateId('T'),
            ':emp' => $empId,
            ':cus' => $cusId,
            ':oid' => $orderId,
            ':diag' => $diagnosis,
            ':attach' => $attachment
        ]);
    }

    /** Updated to flow the attachment parameter smoothly */
    public function createCustomerTicket(string $orderId, string $customerId, string $problemInfo, ?string $attachment = null): void
    {
        $row = $this->db->prepare(
            "SELECT Order_CusId FROM Orders WHERE Order_Id = :oid AND Order_CusId = :cid AND Order_Status = 'Completed' LIMIT 1"
        );
        $row->execute([':oid' => $orderId, ':cid' => $customerId]);
        if (!$row->fetchColumn()) {
            throw new RuntimeException('Invalid completed order for this account.');
        }
        $exists = $this->db->prepare("SELECT 1 FROM Service_Ticket WHERE Tix_OrderID = :oid LIMIT 1");
        $exists->execute([':oid' => $orderId]);
        if ($exists->fetchColumn()) {
            throw new RuntimeException('A service ticket already exists for this order.');
        }
        $techId = $this->getFirstTechnicianId();
        if (!$techId) {
            throw new RuntimeException('No technician is available to assign yet. Please contact support.');
        }
        $this->createTicket($orderId, $techId, $problemInfo, $attachment);
    }

    public function listAllTickets(): array
    {
        $sql = "SELECT t.*, c.Cus_Fname, c.Cus_Lname, e.Emp_Fname, e.Emp_Lname, o.Order_InvoiceNo
                FROM Service_Ticket t
                INNER JOIN Customer c ON c.Cus_Id = t.Tix_CusId
                INNER JOIN Employee e ON e.Emp_Id = t.Tix_EmpId
                LEFT JOIN Orders o ON o.Order_Id = t.Tix_OrderID
                ORDER BY t.Tix_CreatedAt DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function listTicketsForTechnician(string $empId): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, c.Cus_Fname, c.Cus_Lname, o.Order_InvoiceNo
             FROM Service_Ticket t
             INNER JOIN Customer c ON c.Cus_Id = t.Tix_CusId
             LEFT JOIN Orders o ON o.Order_Id = t.Tix_OrderID
             WHERE t.Tix_EmpId = :eid
             ORDER BY t.Tix_CreatedAt DESC"
        );
        $stmt->execute([':eid' => $empId]);
        return $stmt->fetchAll();
    }

    public function updateTicketByTechnician(string $tixId, string $empId, string $status, string $problemInfo): void
    {
        $chk = $this->db->prepare("SELECT 1 FROM Service_Ticket WHERE Tix_Id = :id AND Tix_EmpId = :eid LIMIT 1");
        $chk->execute([':id' => $tixId, ':eid' => $empId]);
        if (!$chk->fetchColumn()) {
            throw new RuntimeException('Ticket not assigned to you.');
        }
        $stmt = $this->db->prepare(
            "UPDATE Service_Ticket SET Tix_Status = :st, Tix_ProblemInfo = :info WHERE Tix_Id = :id AND Tix_EmpId = :eid"
        );
        $stmt->execute([':st' => $status, ':info' => $problemInfo, ':id' => $tixId, ':eid' => $empId]);
    }

    public function listActivePipelineTickets(): array
    {
        $sql = "SELECT t.*, c.Cus_Fname, c.Cus_Lname, o.Order_InvoiceNo, e.Emp_Fname, e.Emp_Lname
            FROM Service_Ticket t
            INNER JOIN Customer c ON c.Cus_Id = t.Tix_CusId
            INNER JOIN Employee e ON e.Emp_Id = t.Tix_EmpId
            LEFT JOIN Orders o ON o.Order_Id = t.Tix_OrderID
            WHERE t.Tix_Status IN ('Pending', 'In Progress')
            ORDER BY 
                CASE WHEN t.Tix_Status = 'In Progress' THEN 1 ELSE 2 END, 
                t.Tix_CreatedAt ASC";
        return $this->db->query($sql)->fetchAll();
    }
}
