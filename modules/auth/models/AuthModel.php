<?php
// modules/auth/models/AuthModel.php
declare(strict_types=1);

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'BaseModel.php';

class AuthModel extends BaseModel {
    // Customer registration
    public function createCustomer(array $data): bool {
        $sql = "INSERT INTO Customer
            (Cus_Id, Cus_Fname, Cus_Lname, Cus_Email, Cus_Password, Cus_ContactNo, Cus_Address, Cus_CreatedAt)
            VALUES (:id, :fname, :lname, :email, :password, :contact, :address, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $data['Cus_Id'],
            ':fname' => $data['Cus_Fname'],
            ':lname' => $data['Cus_Lname'],
            ':email' => $data['Cus_Email'],
            ':password' => $data['Cus_Password'],
            ':contact' => $data['Cus_ContactNo'],
            ':address' => $data['Cus_Address'],
        ]);
    }

    public function customerEmailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM Customer WHERE Cus_Email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function findCustomerByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Customer WHERE Cus_Email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Employee login
    public function findEmployeeByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Employee WHERE Emp_Email = :u");
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getEmployeeById(string $employeeId): ?array {
        $stmt = $this->db->prepare(
            "SELECT e.*, b.Branch_Name
             FROM Employee e
             INNER JOIN Branch b ON b.Branch_Id = e.Emp_BranchId
             WHERE e.Emp_Id = :id"
        );
        $stmt->execute([':id' => $employeeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getCustomerById(string $customerId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Customer WHERE Cus_Id = :id");
        $stmt->execute([':id' => $customerId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getCustomerOrders(string $customerId): array {
        $stmt = $this->db->prepare(
            "SELECT
                o.Order_Id,
                o.Order_Date,
                o.Order_Status,
                o.Order_Shipping,
                o.Order_InvoiceNo,
                o.Order_TotalAmount,
                COALESCE(SUM(oi.Item_Quantity), 0) AS item_count,
                COALESCE(MAX(p.Pay_Status), 'Pending') AS payment_status
             FROM Orders o
             LEFT JOIN Order_Item oi ON oi.Item_OrderID = o.Order_Id
             LEFT JOIN Payment p ON p.Pay_OrderID = o.Order_Id
             WHERE o.Order_CusId = :cid
             GROUP BY
                o.Order_Id,
                o.Order_Date,
                o.Order_Status,
                o.Order_Shipping,
                o.Order_InvoiceNo,
                o.Order_TotalAmount
             ORDER BY o.Order_Date DESC"
        );
        $stmt->execute([':cid' => $customerId]);
        return $stmt->fetchAll();
    }

    public function updateCustomerProfile(string $id, array $data): bool {
        $sql = "UPDATE Customer SET Cus_Fname = :fn, Cus_Lname = :ln, Cus_ContactNo = :co, Cus_Address = :ad WHERE Cus_Id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':fn' => $data['fname'],
            ':ln' => $data['lname'],
            ':co' => $data['contact'],
            ':ad' => $data['address'],
            ':id' => $id,
        ]);
    }

    public function customerEmailExistsExcept(string $email, string $excludeCustomerId): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM Customer WHERE Cus_Email = :e AND Cus_Id <> :id LIMIT 1");
        $stmt->execute([':e' => $email, ':id' => $excludeCustomerId]);
        return (bool) $stmt->fetchColumn();
    }
}
