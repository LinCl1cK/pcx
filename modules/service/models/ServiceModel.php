<?php

declare(strict_types=1);

class ServiceModel extends BaseModel
{
    private function generateId(string $prefix): string
    {
        return $prefix . str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
    }

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
        return $this->db->query(
            "SELECT * FROM Employee WHERE Emp_Position = 'Technician' ORDER BY Emp_Fname"
        )->fetchAll();
    }

    public function getFirstTechnicianId(): ?string
    {
        $row = $this->db->query(
            "SELECT Emp_Id FROM Employee WHERE Emp_Position = 'Technician' ORDER BY Emp_Id LIMIT 1"
        )->fetch();
        return $row ? trim((string) $row['Emp_Id']) : null;
    }

    /**
     * MIGRATION: sp_CreateServiceTicket
     *
     * The legacy stored procedure wrapped ticket instantiation in a transaction,
     * validated the completed-order guard, and associated an active technician ID.
     *
     * This method now enforces all three rules inside an explicit PHP transaction
     * so partial failures are rolled back cleanly — a requirement for MongoDB
     * portability where stored procedures do not exist.
     *
     * Rules enforced:
     *  1. Ticket can only be created for a 'Completed' order.
     *  2. A technician must be explicitly provided or auto-assigned.
     *  3. The entire INSERT is atomic; any failure is rolled back.
     */
    public function createTicket(
        string $orderId,
        string $empId,
        string $diagnosis,
        ?string $attachment = null
    ): void {
        $this->db->beginTransaction();
        try {
            // Guard: order must exist and be 'Completed'
            $cusStmt = $this->db->prepare(
                "SELECT Order_CusId FROM Orders WHERE Order_Id = :id AND Order_Status = 'Completed' LIMIT 1 FOR UPDATE"
            );
            $cusStmt->execute([':id' => $orderId]);
            $cusId = $cusStmt->fetchColumn();
            if (!$cusId) {
                throw new RuntimeException('Service tickets can only be created for completed orders.');
            }

            // Guard: technician must exist
            $techCheck = $this->db->prepare(
                "SELECT 1 FROM Employee WHERE Emp_Id = :eid AND Emp_Position = 'Technician' LIMIT 1"
            );
            $techCheck->execute([':eid' => $empId]);
            if (!$techCheck->fetchColumn()) {
                throw new RuntimeException(
                    'The assigned employee is not a registered Technician. Please select a valid technician.'
                );
            }

            $stmt = $this->db->prepare(
                "INSERT INTO Service_Ticket
                 (Tix_Id, Tix_EmpId, Tix_CusId, Tix_OrderID, Tix_ProblemInfo, Tix_Attachment, Tix_Status, Tix_CreatedAt)
                 VALUES (:id, :emp, :cus, :oid, :diag, :attach, 'Pending', NOW())"
            );
            $stmt->execute([
                ':id'     => $this->generateId('T'),
                ':emp'    => $empId,
                ':cus'    => $cusId,
                ':oid'    => $orderId,
                ':diag'   => $diagnosis,
                ':attach' => $attachment,
            ]);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Customer-facing ticket creation.
     * Delegates to createTicket() after validating ownership and auto-assigning
     * the first available technician.
     */
    public function createCustomerTicket(
        string $orderId,
        string $customerId,
        string $problemInfo,
        ?string $attachment = null
    ): void {
        // Validate ownership and completed status
        $row = $this->db->prepare(
            "SELECT Order_CusId FROM Orders WHERE Order_Id = :oid AND Order_CusId = :cid AND Order_Status = 'Completed' LIMIT 1"
        );
        $row->execute([':oid' => $orderId, ':cid' => $customerId]);
        if (!$row->fetchColumn()) {
            throw new RuntimeException('Invalid completed order for this account.');
        }

        // Block duplicate tickets
        $exists = $this->db->prepare("SELECT 1 FROM Service_Ticket WHERE Tix_OrderID = :oid LIMIT 1");
        $exists->execute([':oid' => $orderId]);
        if ($exists->fetchColumn()) {
            throw new RuntimeException('A service ticket already exists for this order.');
        }

        $techId = $this->getFirstTechnicianId();
        if (!$techId) {
            throw new RuntimeException('No technician is available to assign yet. Please contact support.');
        }

        // Delegate to the transactional createTicket()
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

    public function updateTicketByTechnician(
        string $tixId,
        string $empId,
        string $status,
        string $problemInfo
    ): void {
        if (!in_array($status, ['Pending', 'In Progress', 'Completed'], true)) {
            throw new RuntimeException('Invalid ticket status.');
        }

        $chk = $this->db->prepare(
            "SELECT 1 FROM Service_Ticket WHERE Tix_Id = :id AND Tix_EmpId = :eid LIMIT 1"
        );
        $chk->execute([':id' => $tixId, ':eid' => $empId]);
        if (!$chk->fetchColumn()) {
            throw new RuntimeException('Ticket not assigned to you.');
        }

        $completedAt = $status === 'Completed' ? 'NOW()' : 'NULL';
        $stmt = $this->db->prepare(
            "UPDATE Service_Ticket
             SET Tix_Status = :st, Tix_ProblemInfo = :info, Tix_DateCompleted = {$completedAt}
             WHERE Tix_Id = :id AND Tix_EmpId = :eid"
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
