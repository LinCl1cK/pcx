<?php
declare(strict_types=1);

class AdminModel extends BaseModel {
    public function getPendingOrders(): array {
        return $this->db->query("SELECT * FROM Orders WHERE Order_Status='Pending' ORDER BY Order_Date ASC")->fetchAll();
    }

    public function getLowStock(): array {
        $sql = "SELECT i.*, p.Prod_Name, b.Branch_Name
                FROM Inventory i
                INNER JOIN Product p ON p.Prod_Id = i.Inv_ProdId
                INNER JOIN Branch b ON b.Branch_Id = i.Inv_BranchId
                WHERE i.Inv_StockQty <= i.Inv_ReorderLevel
                ORDER BY i.Inv_LastUpdated DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getTickets(): array {
        return $this->db->query("SELECT * FROM Service_Ticket ORDER BY Tix_CreatedAt DESC LIMIT 20")->fetchAll();
    }

    public function getSalesReport(): array {
        $sql = "SELECT DATE(Order_Date) AS dt, SUM(Order_TotalAmount) AS total
                FROM Orders
                WHERE Order_Status IN ('Paid','Completed')
                GROUP BY DATE(Order_Date)
                ORDER BY dt DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // User Management
    public function getAllUsers(): array {
        return $this->db->query("SELECT * FROM Customer ORDER BY Cus_CreatedAt DESC")->fetchAll();
    }

    public function getUserById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Customer WHERE Cus_Id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function updateUser(string $id, array $data): bool {
        $sql = "UPDATE Customer SET Cus_Fname = ?, Cus_Lname = ?, Cus_Email = ?, Cus_ContactNo = ?, Cus_Address = ? WHERE Cus_Id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['fname'], $data['lname'], $data['email'], $data['contact'], $data['address'], $id]);
    }

    public function deleteUser(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Customer WHERE Cus_Id = ?");
        return $stmt->execute([$id]);
    }

    // Employee Management
    public function getAllEmployees(): array {
        $sql = "SELECT e.*, b.Branch_Name FROM Employee e INNER JOIN Branch b ON e.Emp_BranchId = b.Branch_Id ORDER BY e.Emp_CreatedAt DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getEmployeeById(string $id): ?array {
        $sql = "SELECT e.*, b.Branch_Name FROM Employee e INNER JOIN Branch b ON e.Emp_BranchId = b.Branch_Id WHERE e.Emp_Id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function updateEmployee(string $id, array $data): bool {
        $sql = "UPDATE Employee
                SET Emp_Fname = ?, Emp_Lname = ?, Emp_Position = ?, Emp_BranchId = ?, Emp_Email = ?, Emp_ContactNo = ?, Emp_Address = ?
                WHERE Emp_Id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['fname'],
            $data['lname'],
            $data['position'],
            $data['branch_id'],
            $data['email'],
            $data['contact'],
            $data['address'],
            $id,
        ]);
    }

    public function deleteEmployee(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Employee WHERE Emp_Id = ?");
        return $stmt->execute([$id]);
    }

    public function createEmployee(array $data): bool {
        $id = 'EMP-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $sql = "INSERT INTO Employee
                (Emp_Id, Emp_Fname, Emp_Lname, Emp_Position, Emp_BranchId, Emp_Email, Emp_Password, Emp_ContactNo, Emp_Address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $id,
            $data['fname'],
            $data['lname'],
            $data['position'],
            $data['branch_id'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['contact'],
            $data['address'],
        ]);
    }

    // Product Management
    public function getAllProducts(): array {
        $sql = "SELECT p.*, c.Cat_Name FROM Product p LEFT JOIN Category c ON p.Prod_CatId = c.Cat_Id ORDER BY p.Prod_CreatedAt DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getProductById(string $id): ?array {
        $sql = "SELECT p.*, c.Cat_Name FROM Product p LEFT JOIN Category c ON p.Prod_CatId = c.Cat_Id WHERE p.Prod_Id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getProductSubcategoryIds(string $productId): array {
        $stmt = $this->db->prepare("SELECT Subc_Id FROM product_subcategory WHERE Prod_Id = ?");
        $stmt->execute([$productId]);
        return array_map('strval', array_column($stmt->fetchAll(), 'Subc_Id'));
    }

    private function syncProductSubcategories(string $productId, array $subcategories): void {
        $this->db->prepare("DELETE FROM product_subcategory WHERE Prod_Id = ?")->execute([$productId]);
        if ($subcategories === []) {
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO product_subcategory (Prod_Id, Subc_Id) VALUES (?, ?)");
        foreach (array_unique(array_filter($subcategories)) as $subcId) {
            $stmt->execute([$productId, $subcId]);
        }
    }

    public function updateProduct(string $id, array $data): bool {
        $this->db->beginTransaction();
        try {
            $sql = "UPDATE Product SET Prod_Name = ?, Prod_Brand = ?, Prod_Price = ?, Prod_Warranty = ?, Prod_CatId = ?, Prod_Image = ?, Prod_Featured = ?, Prod_Description = ?, Prod_Status = ? WHERE Prod_Id = ?";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$data['name'], $data['brand'], $data['price'], $data['warranty'], $data['cat_id'], $data['image'], $data['featured'], $data['description'], $data['status'], $id]);
            $this->syncProductSubcategories($id, $data['subcategories'] ?? []);
            $this->db->commit();
            return $ok;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function deleteProduct(string $id): bool {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("DELETE FROM product_subcategory WHERE Prod_Id = ?")->execute([$id]);
            $stmt = $this->db->prepare("DELETE FROM Product WHERE Prod_Id = ?");
            $ok = $stmt->execute([$id]);
            $this->db->commit();
            return $ok;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function createProduct(array $data): bool {
        $id = 'PROD-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO Product (Prod_Id, Prod_Name, Prod_Brand, Prod_Price, Prod_Warranty, Prod_CatId, Prod_Image, Prod_Featured, Prod_Description, Prod_Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$id, $data['name'], $data['brand'], $data['price'], $data['warranty'], $data['cat_id'], $data['image'], $data['featured'], $data['description'], $data['status']]);
            $this->syncProductSubcategories($id, $data['subcategories'] ?? []);

            $branches = $this->getAllBranches();
            $stock = $this->db->prepare(
                "INSERT INTO Inventory (Inv_Id, Inv_ProdId, Inv_BranchId, Inv_StockQty, Inv_ReorderLevel, Inv_LastUpdated)
                 VALUES (?, ?, ?, 0, 10, NOW())"
            );
            foreach ($branches as $branch) {
                $invId = 'INV-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
                $stock->execute([$invId, $id, $branch['Branch_Id']]);
            }

            $this->db->commit();
            return $ok;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Category Management
    public function getAllCategories(): array {
        return $this->db->query("SELECT * FROM Category ORDER BY Cat_Name ASC")->fetchAll();
    }

    public function getAllSubcategories(): array {
        return $this->db->query("SELECT * FROM Subcategory ORDER BY Subc_Name ASC")->fetchAll();
    }

    public function getCategoryById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Category WHERE Cat_Id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function updateCategory(string $id, array $data): bool {
        $sql = "UPDATE Category SET Cat_Name = ?, Cat_Description = ? WHERE Cat_Id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['name'], $data['description'], $id]);
    }

    public function deleteCategory(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Category WHERE Cat_Id = ?");
        return $stmt->execute([$id]);
    }

    public function createCategory(array $data): bool {
        $id = 'CAT-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $sql = "INSERT INTO Category (Cat_Id, Cat_Name, Cat_Description) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $data['name'], $data['description']]);
    }

    // Branch Management (for employees)
    public function getAllBranches(): array {
        return $this->db->query("SELECT * FROM Branch ORDER BY Branch_Name ASC")->fetchAll();
    }

    public function createBranch(array $data): bool {
        $id = 'BR-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO Branch (Branch_Id, Branch_Name, Branch_Location, Branch_ContactNo, Branch_CreatedAt)
                 VALUES (?, ?, ?, ?, NOW())"
            );
            $ok = $stmt->execute([$id, $data['name'], $data['location'], $data['contact']]);

            $products = $this->db->query("SELECT Prod_Id FROM Product")->fetchAll();
            $stock = $this->db->prepare(
                "INSERT INTO Inventory (Inv_Id, Inv_ProdId, Inv_BranchId, Inv_StockQty, Inv_ReorderLevel, Inv_LastUpdated)
                 VALUES (?, ?, ?, 0, 10, NOW())"
            );
            foreach ($products as $product) {
                $invId = 'INV-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
                $stock->execute([$invId, $product['Prod_Id'], $id]);
            }

            $this->db->commit();
            return $ok;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function updateBranch(string $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE Branch SET Branch_Name = ?, Branch_Location = ?, Branch_ContactNo = ? WHERE Branch_Id = ?"
        );
        return $stmt->execute([$data['name'], $data['location'], $data['contact'], $id]);
    }

    public function deleteBranch(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Branch WHERE Branch_Id = ?");
        return $stmt->execute([$id]);
    }

    public function createSubcategory(array $data): bool {
        $id = 'SUB-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $stmt = $this->db->prepare(
            "INSERT INTO Subcategory (Subc_Id, Subc_Name, Subc_Description) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$id, $data['name'], $data['description']]);
    }

    public function updateSubcategory(string $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE Subcategory SET Subc_Name = ?, Subc_Description = ? WHERE Subc_Id = ?"
        );
        return $stmt->execute([$data['name'], $data['description'], $id]);
    }

    public function deleteSubcategory(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Subcategory WHERE Subc_Id = ?");
        return $stmt->execute([$id]);
    }

    public function employeeEmailExists(string $email, ?string $excludeEmpId = null): bool {
        if ($excludeEmpId) {
            $stmt = $this->db->prepare("SELECT 1 FROM Employee WHERE Emp_Email = ? AND Emp_Id <> ? LIMIT 1");
            $stmt->execute([$email, $excludeEmpId]);
        } else {
            $stmt = $this->db->prepare("SELECT 1 FROM Employee WHERE Emp_Email = ? LIMIT 1");
            $stmt->execute([$email]);
        }
        return (bool) $stmt->fetchColumn();
    }
}
