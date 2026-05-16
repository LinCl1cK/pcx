<?php
// modules/auth/models/AuthModel.php
declare(strict_types=1);

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'BaseModel.php';

class AuthModel extends BaseModel {

    public function createCustomer(array $data): bool {
        $sql = "INSERT INTO customer
                    (Cus_Id, Cus_Fname, Cus_Lname, Cus_Email, Cus_Password, Cus_ContactNo, Cus_Address, Cus_CreatedAt)
                VALUES 
                    (:id, :fname, :lname, :email, :password, :contact, :address, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id'       => $data['Cus_Id'],
                ':fname'    => $data['Cus_Fname'],
                ':lname'    => $data['Cus_Lname'],
                ':email'    => $data['Cus_Email'],
                ':password' => $data['Cus_Password'],
                ':contact'  => $data['Cus_ContactNo'],
                ':address'  => $data['Cus_Address'],
            ]);
        } catch (\PDOException $e) {
            error_log('AuthModel::createCustomer Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function customerEmailExists(string $email): bool {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM customer WHERE Cus_Email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            return (bool) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log('AuthModel::customerEmailExists Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function findCustomerByEmail(string $email): ?array {
        $sql = "SELECT 
                    Cus_Id, 
                    Cus_Fname, 
                    Cus_Lname, 
                    Cus_Email, 
                    Cus_Password, 
                    Cus_ContactNo, 
                    Cus_Address 
                FROM customer 
                WHERE Cus_Email = :email 
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\PDOException $e) {
            error_log('AuthModel::findCustomerByEmail Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function findEmployeeByEmail(string $email): ?array {
        $sql = "SELECT 
                    Emp_Id, 
                    Emp_Fname, 
                    Emp_Lname, 
                    Emp_Email, 
                    Emp_Password, 
                    Emp_Position,
                    Emp_BranchId 
                FROM employee 
                WHERE Emp_Email = :email 
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\PDOException $e) {
            error_log('AuthModel::findEmployeeByEmail Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function updateCustomerProfile(string $id, array $data): bool {
        $sql = "UPDATE customer SET Cus_Fname = :fn, Cus_Lname = :ln, Cus_ContactNo = :co, Cus_Address = :ad WHERE Cus_Id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':fn' => $data['fname'],
                ':ln' => $data['lname'],
                ':co' => $data['contact'],
                ':ad' => $data['address'],
                ':id' => $id,
            ]);
        } catch (\PDOException $e) {
            error_log('AuthModel::updateCustomerProfile Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function customerEmailExistsExcept(string $email, string $excludeCustomerId): bool {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM customer WHERE Cus_Email = :e AND Cus_Id != :id LIMIT 1");
            $stmt->execute([':e' => $email, ':id' => $excludeCustomerId]);
            return (bool) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log('AuthModel::customerEmailExistsExcept Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function getCustomerOrders(string $customerId): array {
        $sql = "SELECT 
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
                    o.Order_Id, o.Order_Date, o.Order_Status, o.Order_Shipping, o.Order_InvoiceNo, o.Order_TotalAmount
                 ORDER BY o.Order_Date DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cid' => $customerId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log('AuthModel::getCustomerOrders Exception: ' . $e->getMessage());
            return [];
        }
    }
}