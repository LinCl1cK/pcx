<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../../promotion/models/PromotionModel.php';

class AdminController extends BaseController
{
    private AdminModel $model;
    private PromotionModel $promotionModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->model = new AdminModel($pdo);
        $this->promotionModel = new PromotionModel($pdo);
    }

    public function dashboard(): void
    {
        $this->requireRoles(['general admin', 'branch admin']);
        $employee = $_SESSION['employee'];

        // STRICT JURISDICTION SCOPING
        // Session stores 'role' and 'branch_id' (set by AuthController::login())
        $rawRole = strtolower(trim($employee['role'] ?? $employee['Emp_Position'] ?? ''));
        $isGeneralAdmin = ($rawRole === 'general admin');

        if ($isGeneralAdmin) {
            $scopeBranchId = null; // Global Access
        } else {
            $scopeBranchId = trim((string)($employee['branch_id'] ?? $employee['Emp_BranchId'] ?? ''));
            // Fail-safe: Prevent local admins from gaining global access if branch ID is missing
            if ($scopeBranchId === '') {
                $scopeBranchId = 'INVALID_NO_BRANCH';
            }
        }

        View::render(__DIR__ . '/../views/dashboard.php', [
            'employee'      => $employee,
            'pendingOrders' => $this->model->getPendingOrders($scopeBranchId),
            'lowStock'      => $this->model->getLowStock($scopeBranchId),
            'tickets'       => $this->model->getTickets($scopeBranchId),
            'sales'         => $this->model->getSalesReport($scopeBranchId),
            'navActive'     => 'dashboard',
            'pageTitle'     => 'Admin Dashboard',
        ]);
    }

    // ----------------------------------------------------------------
    // User Management
    // ----------------------------------------------------------------

    public function manageUsers(): void
    {
        $this->requireGeneralAdmin();
        $users = $this->model->getAllUsers();
        View::render(__DIR__ . '/../views/manage_users.php', [
            'users'    => $users,
            'flash'    => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
        ]);
    }

    public function createUser(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'fname'    => trim($_POST['fname'] ?? ''),
                'lname'    => trim($_POST['lname'] ?? ''),
                'email'    => trim($_POST['email'] ?? ''),
                'contact'  => trim($_POST['contact'] ?? ''),
                'address'  => trim($_POST['address'] ?? ''),
                'password' => $_POST['password'] ?? '',
            ];
            if ($data['fname'] === '' || $data['lname'] === '' || $data['email'] === '' || $data['password'] === '') {
                $this->setFlash('danger', 'First name, last name, email, and password are required.');
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email address.');
            } elseif (strlen($data['password']) < 8) {
                $this->setFlash('danger', 'Password must be at least 8 characters.');
            } elseif ($this->model->createUser($data)) {
                $this->setFlash('success', 'Customer account created successfully.');
                $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
                return;
            } else {
                $this->setFlash('danger', 'Failed to create customer. Email may already be in use.');
            }
            $this->redirect(BASE_URL . '/?r=admin/admin/createUser');
            return;
        }
        View::render(__DIR__ . '/../views/create_user.php', [
            'flash'    => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
        ]);
    }

    public function editUser(): void
    {
        $this->requireGeneralAdmin();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
            return;
        }
        $user = $this->model->getUserById($id);
        if (!$user) {
            http_response_code(404);
            echo "User not found";
            return;
        }
        View::render(__DIR__ . '/../views/edit_user.php', [
            'user'     => $user,
            'flash'    => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
        ]);
    }

    public function updateUser(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
            return;
        }
        $id = $_POST['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
            return;
        }
        $data = [
            'fname'   => trim($_POST['fname'] ?? ''),
            'lname'   => trim($_POST['lname'] ?? ''),
            'email'   => trim($_POST['email'] ?? ''),
            'contact' => trim($_POST['contact'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
        ];
        if ($this->model->updateUser($id, $data)) {
            $this->setFlash('success', 'User updated successfully');
        } else {
            $this->setFlash('danger', 'Failed to update user');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
    }

    public function deleteUser(): void
    {
        $this->requireGeneralAdmin();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
            return;
        }
        try {
            if ($this->model->deleteUser($id)) {
                $this->setFlash('success', 'Customer deleted successfully.');
            } else {
                $this->setFlash('danger', 'Failed to delete customer account.');
            }
        } catch (PDOException $e) {
            $this->setFlash('danger', 'Cannot delete customer: This account is permanently linked to existing order histories.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
    }

    // ----------------------------------------------------------------
    // Employee Management (Secure Jurisdiction Pipeline)
    // ----------------------------------------------------------------

    public function manageEmployees(): void
    {
        $this->requireRoles(['general admin', 'branch admin', 'administrator']);
        $employee = $_SESSION['employee'];

        // Session stores 'role' and 'branch_id' (set by AuthController::login())
        $rawRole = strtolower(trim($employee['role'] ?? $employee['Emp_Position'] ?? ''));
        $isGeneralAdmin = ($rawRole === 'general admin');

        if ($isGeneralAdmin) {
            $scopeBranchId = null;
        } else {
            $scopeBranchId = trim((string)($employee['branch_id'] ?? $employee['Emp_BranchId'] ?? ''));
            if ($scopeBranchId === '') {
                $scopeBranchId = 'INVALID_NO_BRANCH';
            }
        }

        View::render(__DIR__ . '/../views/manage_employees.php', [
            'employee'  => $employee,
            'employees' => $this->model->getAllEmployees($scopeBranchId),
            'branches'  => $this->model->getAllBranches(),
            'navActive' => 'employees',
            'pageTitle' => 'Staff Directory',
        ]);
    }

    public function createEmployee(): void
    {
        $this->requireRoles(['general admin', 'branch admin', 'administrator']);

        $rawRole = strtolower(str_replace('_', ' ', (string)($_SESSION['employee']['role'] ?? $_SESSION['employee']['Emp_Position'] ?? '')));
        $isGeneralAdmin = ($rawRole === 'general admin');

        $rawBranch       = $_SESSION['employee']['branch_id'] ?? $_SESSION['employee']['Emp_BranchId'] ?? null;
        $currentBranchId = ($rawBranch !== null && trim((string) $rawBranch) !== '') ? trim((string) $rawBranch) : null;

        // UPDATED: Adjust backend validation to match the new view options
        $allowedRoles = $isGeneralAdmin
            ? ['Branch Admin', 'General Admin']
            : ['Sales Representative', 'Technician', 'Branch Admin'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $targetPosition      = trim($_POST['role'] ?? '');
            $targetPositionLower = strtolower($targetPosition);
            $allowedTargetsLower = array_map('strtolower', $allowedRoles);

            if (!in_array($targetPositionLower, $allowedTargetsLower, true)) {
                $this->setFlash('danger', 'Security Restriction: You do not have clearance to create this role.');
                $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
                return;
            }

            if ($isGeneralAdmin) {
                $branch_id = !empty($_POST['branch_id']) ? trim($_POST['branch_id']) : null;
            } else {
                $branch_id = $currentBranchId; // Override any form tampering manipulation
            }

            $data = [
                'fname'     => trim($_POST['fname'] ?? ''),
                'lname'     => trim($_POST['lname'] ?? ''),
                'position'  => $targetPosition,
                'branch_id' => $branch_id,
                'email'     => trim($_POST['email'] ?? ''),
                'contact'   => trim($_POST['contact'] ?? ''),
                'address'   => trim($_POST['address'] ?? ''),
                'password'  => $_POST['password'] ?? '',
            ];

            if ($data['fname'] === '' || $data['lname'] === '' || $data['email'] === '' || $data['password'] === '') {
                $this->setFlash('danger', 'Core fields are required.');
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email format.');
            } elseif ($this->model->employeeEmailExists($data['email'])) {
                $this->setFlash('danger', 'That work email is already in use.');
            } elseif ($this->model->createEmployee($data)) {
                $this->setFlash('success', 'Employee created successfully');
                $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
                return;
            } else {
                $this->setFlash('danger', 'Failed to create employee. Please try again.');
            }

            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }

        View::render(__DIR__ . '/../views/create_employee.php', [
            'branches'     => $this->model->getAllBranches(),
            'flash'        => $this->pullFlash(),
            'employee'     => $_SESSION['employee'],
            'allowedRoles' => $allowedRoles,
        ]);
    }

    public function editEmployee(): void
    {
        $this->requireRoles(['general admin', 'branch admin', 'administrator']);

        // Session stores 'role' and 'branch_id' (set by AuthController::login())
        $rawRole = strtolower(str_replace('_', ' ', (string)($_SESSION['employee']['role'] ?? $_SESSION['employee']['Emp_Position'] ?? '')));
        $isGeneralAdmin = ($rawRole === 'general admin');

        $rawBranch       = $_SESSION['employee']['branch_id'] ?? $_SESSION['employee']['Emp_BranchId'] ?? null;
        $currentBranchId = ($rawBranch !== null && trim((string) $rawBranch) !== '') ? trim((string) $rawBranch) : null;

        $targetId       = $_GET['id'] ?? $_POST['Emp_Id'] ?? '';
        $targetEmployee = $this->model->getEmployeeById($targetId);

        if (!$targetEmployee) {
            $this->setFlash('danger', 'Employee record not found.');
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }

        $targetBranchId = trim((string) ($targetEmployee['Emp_BranchId'] ?? ''));

        if (!$isGeneralAdmin && $targetBranchId !== (string) $currentBranchId) {
            $this->setFlash('danger', 'Access Denied: This staff member is outside your branch jurisdiction.');
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }

        View::render(__DIR__ . '/../views/edit_employee.php', [
            'employee_data' => $targetEmployee,
            'branches'      => $this->model->getAllBranches(),
            'flash'         => $this->pullFlash(),
            'employee'      => $_SESSION['employee']
        ]);
    }

    public function updateEmployee(): void
    {
        $this->requireRoles(['general admin', 'branch admin', 'administrator']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }

        // Session stores 'role' and 'branch_id' (set by AuthController::login())
        $rawRole = strtolower(str_replace('_', ' ', (string)($_SESSION['employee']['role'] ?? $_SESSION['employee']['Emp_Position'] ?? '')));
        $isGeneralAdmin = ($rawRole === 'general admin');

        $rawBranch       = $_SESSION['employee']['branch_id'] ?? $_SESSION['employee']['Emp_BranchId'] ?? null;
        $currentBranchId = ($rawBranch !== null && trim((string) $rawBranch) !== '') ? trim((string) $rawBranch) : null;

        $id             = $_POST['id'] ?? '';
        $targetEmployee = $this->model->getEmployeeById($id);

        if (!$targetEmployee) {
            $this->setFlash('danger', 'Employee not found.');
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }

        $targetBranchId = trim((string) ($targetEmployee['Emp_BranchId'] ?? ''));

        if (!$isGeneralAdmin && $targetBranchId !== (string) $currentBranchId) {
            $this->setFlash('danger', 'Unauthorized modification attempt on external staff.');
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }

        // Prevent Branch Admins from elevating a technician to a General Admin role via POST manipulation
        $targetPosition = trim($_POST['role'] ?? '');
        if (!$isGeneralAdmin) {
            $allowedTargetsLower = ['sales representative', 'technician'];
            if (!in_array(strtolower($targetPosition), $allowedTargetsLower, true)) {
                $targetPosition = $targetEmployee['Emp_Position'];
            }
        }

        $branch_id = !empty($_POST['branch_id']) ? trim($_POST['branch_id']) : null;
        if (!$isGeneralAdmin) {
            $branch_id = $currentBranchId; // Block changing an existing employee's branch to an external one
        }

        $data = [
            'fname'     => trim($_POST['fname'] ?? ''),
            'lname'     => trim($_POST['lname'] ?? ''),
            'position'  => $targetPosition,
            'branch_id' => $branch_id,
            'email'     => trim($_POST['email'] ?? ''),
            'contact'   => trim($_POST['contact'] ?? ''),
            'address'   => trim($_POST['address'] ?? ''),
        ];

        if ($this->model->updateEmployee($id, $data)) {
            $this->setFlash('success', 'Employee updated successfully');
        } else {
            $this->setFlash('danger', 'Failed to update employee');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
    }

    public function deleteEmployee(): void
    {
        $this->requireRoles(['general admin', 'branch admin', 'administrator']);

        // Session stores 'role' and 'branch_id' (set by AuthController::login())
        $rawRole = strtolower(str_replace('_', ' ', (string)($_SESSION['employee']['role'] ?? $_SESSION['employee']['Emp_Position'] ?? '')));
        $isGeneralAdmin = ($rawRole === 'general admin');

        $rawBranch       = $_SESSION['employee']['branch_id'] ?? $_SESSION['employee']['Emp_BranchId'] ?? null;
        $currentBranchId = ($rawBranch !== null && trim((string) $rawBranch) !== '') ? trim((string) $rawBranch) : null;

        $targetId       = $_GET['id'] ?? '';
        $targetEmployee = $this->model->getEmployeeById($targetId);

        if (!$targetEmployee) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }

        $targetBranchId = trim((string) ($targetEmployee['Emp_BranchId'] ?? ''));

        if (!$isGeneralAdmin) {
            if ($targetBranchId !== (string) $currentBranchId) {
                $this->setFlash('danger', 'Jurisdiction Error: You cannot delete staff from other branches.');
                $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
                return;
            }

            $targetPosition = strtolower(trim($targetEmployee['Emp_Position'] ?? ''));
            $targetIsAdmin = in_array($targetPosition, ['administrator', 'branch admin', 'general admin'], true);
            if ($targetIsAdmin) {
                $this->setFlash('danger', 'Security Restriction: Branch Admins cannot delete other management roles.');
                $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
                return;
            }
        }

        try {
            if ($this->model->deleteEmployee($targetId)) {
                $this->setFlash('success', 'Employee deleted successfully.');
            } else {
                $this->setFlash('danger', 'Failed to delete employee.');
            }
        } catch (PDOException $e) {
            $this->setFlash('danger', 'Cannot delete employee: They are tied to active system logs, orders, or service tickets.');
        }
        
        $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
    }

    // ----------------------------------------------------------------
    // Product Management
    // ----------------------------------------------------------------

    public function manageProducts(): void
    {
        $this->requireGeneralAdmin();
        $products   = $this->model->getAllProducts();
        $categories = $this->model->getAllCategories();
        View::render(__DIR__ . '/../views/manage_products.php', [
            'products'   => $products,
            'categories' => $categories,
            'flash'      => $this->pullFlash(),
            'employee'   => $_SESSION['employee'],
        ]);
    }

    public function createProduct(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Process the File Upload
            $imageFilename = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // Define target directory
                $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/products/';
                
                // Create directory if it doesn't exist
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Generate a unique filename to prevent overwriting
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageFilename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
                
                // Save the file
                move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $imageFilename);
            }

            // 2. Map the Data
            $data = [
                'name'          => trim($_POST['name'] ?? ''),
                'brand'         => trim($_POST['brand'] ?? ''),
                'price'         => (float) ($_POST['price'] ?? 0),
                'warranty'      => (int) ($_POST['warranty'] ?? 0),
                'cat_id'        => !empty($_POST['cat_id']) ? $_POST['cat_id'] : null,
                'image'         => $imageFilename, // Use the uploaded file's generated name
                'featured'      => isset($_POST['featured']) ? 1 : 0,
                'description'   => trim($_POST['description'] ?? ''),
                'status'        => $_POST['status'] ?? 'Active',
                'subcategories' => array_map('strval', $_POST['subcategories'] ?? []),
            ];

            // 3. Validation and Database Insertion
            if ($data['name'] === '' || $data['brand'] === '' || $data['image'] === '') {
                $this->setFlash('danger', 'Name, brand, and a valid product image are required.');
            } elseif ($data['price'] <= 0) {
                $this->setFlash('danger', 'Price must be greater than zero.');
            } elseif ($data['warranty'] < 0 || $data['warranty'] > 36) {
                $this->setFlash('danger', 'Warranty must be between 0 and 36 months.');
            } elseif (!in_array($data['status'], ['Active', 'Inactive', 'Discontinued'], true)) {
                $this->setFlash('danger', 'Invalid product status.');
            } else {
                try {
                    if ($this->model->createProduct($data)) {
                        $this->setFlash('success', 'Product created successfully');
                    } else {
                        $this->setFlash('danger', 'Failed to create product');
                    }
                } catch (Throwable $e) {
                    $this->setFlash('danger', $e->getMessage());
                }
            }
            $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
            return;
        }
        
        $categories    = $this->model->getAllCategories();
        $subcategories = $this->model->getAllSubcategories();
        
        View::render(__DIR__ . '/../views/create_product.php', [
            'categories'    => $categories,
            'subcategories' => $subcategories,
            'flash'         => $this->pullFlash(),
            'employee'      => $_SESSION['employee'],
        ]);
    }

    public function editProduct(): void
    {
        $this->requireGeneralAdmin();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
            return;
        }
        $product = $this->model->getProductById($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }
        View::render(__DIR__ . '/../views/edit_product.php', [
            'product'              => $product,
            'categories'           => $this->model->getAllCategories(),
            'subcategories'        => $this->model->getAllSubcategories(),
            'selectedSubcategories' => $this->model->getProductSubcategoryIds($id),
            'flash'                => $this->pullFlash(),
            'employee'             => $_SESSION['employee'],
        ]);
    }

    public function updateProduct(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
            return;
        }
        $id = $_POST['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
            return;
        }
        $data = [
            'name'          => trim($_POST['name'] ?? ''),
            'brand'         => trim($_POST['brand'] ?? ''),
            'price'         => (float) ($_POST['price'] ?? 0),
            'warranty'      => (int) ($_POST['warranty'] ?? 0),
            'cat_id'        => $_POST['cat_id'] ?? null,
            'image'         => trim($_POST['image'] ?? ''),
            'featured'      => isset($_POST['featured']) ? 1 : 0,
            'description'   => trim($_POST['description'] ?? ''),
            'status'        => $_POST['status'] ?? 'Active',
            'subcategories' => array_map('strval', $_POST['subcategories'] ?? []),
        ];
        if ($data['name'] === '' || $data['brand'] === '' || $data['image'] === '') {
            $this->setFlash('danger', 'Name, brand, and image are required.');
        } elseif ($data['price'] <= 0) {
            $this->setFlash('danger', 'Price must be greater than zero.');
        } elseif ($data['warranty'] < 0 || $data['warranty'] > 36) {
            $this->setFlash('danger', 'Warranty must be between 0 and 36 months.');
        } elseif (!in_array($data['status'], ['Active', 'Inactive', 'Discontinued'], true)) {
            $this->setFlash('danger', 'Invalid product status.');
        } else {
            try {
                if ($this->model->updateProduct($id, $data)) {
                    $this->setFlash('success', 'Product updated successfully');
                } else {
                    $this->setFlash('danger', 'Failed to update product');
                }
            } catch (Throwable $e) {
                $this->setFlash('danger', $e->getMessage());
            }
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
    }

    public function deleteProduct(): void
    {
        $this->requireGeneralAdmin();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
            return;
        }
        try {
            if ($this->model->deleteProduct($id)) {
                $this->setFlash('success', 'Product and its inventory records deleted successfully.');
            } else {
                $this->setFlash('danger', 'Failed to delete product.');
            }
        } catch (PDOException $e) {
            // Catches constraint violations if the product exists in historical Orders
            $this->setFlash('danger', 'Cannot delete product: It is permanently linked to existing customer orders. Please edit the product and set its status to "Discontinued" instead.');
        } catch (Throwable $e) {
            $this->setFlash('danger', 'An unexpected error occurred: ' . $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
    }

    // ----------------------------------------------------------------
    // Category Management
    // ----------------------------------------------------------------

    public function manageCategories(): void
    {
        $this->requireGeneralAdmin();
        $categories = $this->model->getAllCategories();
        View::render(__DIR__ . '/../views/manage_categories.php', [
            'categories' => $categories,
            'flash'      => $this->pullFlash(),
            'employee'   => $_SESSION['employee'],
        ]);
    }

    public function createCategory(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
            ];
            if ($this->model->createCategory($data)) {
                $this->setFlash('success', 'Category created successfully');
            } else {
                $this->setFlash('danger', 'Failed to create category');
            }
            $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
            return;
        }
        View::render(__DIR__ . '/../views/create_category.php', [
            'flash'    => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
        ]);
    }

    public function editCategory(): void
    {
        $this->requireGeneralAdmin();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
            return;
        }
        $category = $this->model->getCategoryById($id);
        if (!$category) {
            http_response_code(404);
            echo "Category not found";
            return;
        }
        View::render(__DIR__ . '/../views/edit_category.php', [
            'category' => $category,
            'flash'    => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
        ]);
    }

    public function updateCategory(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
            return;
        }
        $id = $_POST['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
            return;
        }
        $data = [
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if ($this->model->updateCategory($id, $data)) {
            $this->setFlash('success', 'Category updated successfully');
        } else {
            $this->setFlash('danger', 'Failed to update category');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
    }

    public function deleteCategory(): void
    {
        $this->requireGeneralAdmin();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
            return;
        }
        try {
            if ($this->model->deleteCategory($id)) {
                $this->setFlash('success', 'Category deleted successfully.');
            } else {
                $this->setFlash('danger', 'Failed to delete category.');
            }
        } catch (PDOException $e) {
            $this->setFlash('danger', 'Cannot delete category: It is currently assigned to one or more products in the catalog.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
    }

    // ----------------------------------------------------------------
    // Branch Management
    // ----------------------------------------------------------------

    public function manageBranches(): void
    {
        $this->requireGeneralAdmin();
        View::render(__DIR__ . '/../views/manage_branches.php', [
            'branches'    => $this->model->getAllBranches(),
            'flash'       => $this->pullFlash(),
            'employee'    => $_SESSION['employee'],
            'navActive'   => 'branches',
            'pageTitle'   => 'Branches',
            'pageHeading' => 'Branches',
        ]);
    }

    public function createBranch(): void
    {
        $this->requireGeneralAdmin();
        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'contact'  => trim($_POST['contact'] ?? ''),
        ];
        if ($data['name'] === '' || $data['location'] === '' || $data['contact'] === '') {
            $this->setFlash('danger', 'Branch name, location, and contact are required.');
        } elseif (strlen($data['contact']) > 15) {
            $this->setFlash('danger', 'Branch contact must be 15 characters or fewer.');
        } else {
            try {
                if ($this->model->createBranch($data)) {
                    $this->setFlash('success', 'Branch created.');
                } else {
                    $this->setFlash('danger', 'Failed to create branch.');
                }
            } catch (Throwable $e) {
                $this->setFlash('danger', $e->getMessage());
            }
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageBranches');
    }

    public function updateBranch(): void
    {
        $this->requireGeneralAdmin();
        $id   = trim($_POST['id'] ?? '');
        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'contact'  => trim($_POST['contact'] ?? ''),
        ];
        if ($id === '' || $data['name'] === '' || $data['location'] === '' || $data['contact'] === '') {
            $this->setFlash('danger', 'All branch fields are required.');
        } elseif (strlen($data['contact']) > 15) {
            $this->setFlash('danger', 'Branch contact must be 15 characters or fewer.');
        } elseif ($this->model->updateBranch($id, $data)) {
            $this->setFlash('success', 'Branch updated.');
        } else {
            $this->setFlash('danger', 'Failed to update branch.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageBranches');
    }

    public function deleteBranch(): void
    {
        $this->requireGeneralAdmin();
        $id = trim($_GET['id'] ?? '');
        if ($id !== '') {
            try {
                if ($this->model->deleteBranch($id)) {
                    $this->setFlash('success', 'Branch and its inventory allocations were successfully deleted.');
                } else {
                    $this->setFlash('danger', 'Failed to delete branch.');
                }
            } catch (PDOException $e) {
                // Catches 1451 errors if Employees or Orders are linked to this branch
                $this->setFlash('danger', 'Cannot delete branch: It is actively linked to existing employees, orders, or service tickets.');
            }
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageBranches');
    }

    // ----------------------------------------------------------------
    // Subcategories Management
    // ----------------------------------------------------------------

    public function manageSubcategories(): void
    {
        $this->requireRoles(['general admin', 'branch admin']);

        View::render(__DIR__ . '/../views/manage_subcategories.php', [
            'employee'      => $_SESSION['employee'],
            'subcategories' => $this->model->getAllSubcategories(),
            'navActive'     => 'subcategories',
            'pageTitle'     => 'Subcategories — PCX Admin',
            'pageHeading'   => 'Manage Subcategories',
        ]);
    }

    public function createSubcategory(): void
    {
        $this->requireGeneralAdmin(); // Based on your view, only general admins can add

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            if (empty(trim($data['name']))) {
                $this->setFlash('danger', 'Subcategory name is required.');
            } else {
                if ($this->model->createSubcategory($data)) {
                    $this->setFlash('success', 'Subcategory created successfully.');
                } else {
                    $this->setFlash('danger', 'Failed to create subcategory.');
                }
            }
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageSubcategories');
    }

    public function updateSubcategory(): void
    {
        $this->requireGeneralAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $data = [
                'name'        => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            if (!empty($id) && !empty(trim($data['name']))) {
                if ($this->model->updateSubcategory($id, $data)) {
                    $this->setFlash('success', 'Subcategory updated successfully.');
                } else {
                    $this->setFlash('danger', 'Failed to update subcategory.');
                }
            } else {
                $this->setFlash('danger', 'Invalid data provided for update. Name is required.');
            }
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageSubcategories');
    }

    public function deleteSubcategory(): void
    {
        $this->requireGeneralAdmin();
        $id = $_GET['id'] ?? '';

        if (!empty($id)) {
            try {
                if ($this->model->deleteSubcategory($id)) {
                    $this->setFlash('success', 'Subcategory deleted successfully.');
                } else {
                    $this->setFlash('danger', 'Failed to delete subcategory.');
                }
            } catch (PDOException $e) {
                $this->setFlash('danger', 'Cannot delete subcategory due to a database constraint.');
            }
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageSubcategories');
    }

    // ----------------------------------------------------------------
    // Promotions
    // ----------------------------------------------------------------

    public function managePromotions(): void
    {
        $this->requireGeneralAdmin();
        View::render(__DIR__ . '/../views/manage_promotions.php', [
            'promotions'  => $this->promotionModel->getAllPromotionsAdmin(),
            'flash'       => $this->pullFlash(),
            'employee'    => $_SESSION['employee'],
            'navActive'   => 'promotions',
            'pageTitle'   => 'Promotions',
            'pageHeading' => 'Promotions',
        ]);
    }

    public function createPromotion(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Process the File Upload
            $bannerFilename = '';
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                // Define target directory (adjust if your assets folder is located elsewhere)
                $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/promos/';
                
                // Create directory if it doesn't exist
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Generate a unique filename to prevent overwriting
                $ext = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
                $bannerFilename = 'promo_' . time() . '_' . uniqid() . '.' . $ext;
                
                // Save the file
                move_uploaded_file($_FILES['banner']['tmp_name'], $targetDir . $bannerFilename);
            }

            // 2. Map the Data
            $data = [
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'banner'      => $bannerFilename, // Use the generated filename, not $_POST
                'status'      => $_POST['status'] ?? 'Active',
                'start'       => trim($_POST['start'] ?? ''),
                'end'         => trim($_POST['end'] ?? ''),
            ];

            // 3. Validation and Database Insertion
            if ($data['title'] === '' || $data['banner'] === '') {
                $this->setFlash('danger', 'Title and banner image are required.');
                $this->redirect(BASE_URL . '/?r=admin/admin/createPromotion');
            } elseif (!in_array($data['status'], ['Active', 'Inactive'], true)) {
                $this->setFlash('danger', 'Invalid promotion status.');
                $this->redirect(BASE_URL . '/?r=admin/admin/createPromotion');
            } elseif ($this->promotionModel->createPromotion($data)) {
                $this->setFlash('success', 'Promotion created successfully.');
                $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
            } else {
                $this->setFlash('danger', 'Failed to create promotion in the database.');
                $this->redirect(BASE_URL . '/?r=admin/admin/createPromotion');
            }
            return;
        }
        
        View::render(__DIR__ . '/../views/create_promotion.php', [
            'flash'       => $this->pullFlash(),
            'employee'    => $_SESSION['employee'],
            'navActive'   => 'promotions',
            'pageTitle'   => 'New promotion',
            'pageHeading' => 'New promotion',
        ]);
    }

    public function editPromotion(): void
    {
        $this->requireGeneralAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
            return;
        }
        $promotion = $this->promotionModel->getPromotionById($id);
        if (!$promotion) {
            http_response_code(404);
            echo "Promotion not found";
            return;
        }
        View::render(__DIR__ . '/../views/edit_promotion.php', [
            'promotion'   => $promotion,
            'flash'       => $this->pullFlash(),
            'employee'    => $_SESSION['employee'],
            'navActive'   => 'promotions',
            'pageTitle'   => 'Edit Promotion',
            'pageHeading' => 'Edit Promotion',
        ]);
    }

    public function updatePromotion(): void
    {
        $this->requireGeneralAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
            return;
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
            return;
        }
        $data = [
            'title'       => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'banner'      => trim($_POST['banner'] ?? ''),
            'status'      => $_POST['status'] ?? 'Active',
            'start'       => trim($_POST['start'] ?? ''),
            'end'         => trim($_POST['end'] ?? ''),
        ];
        if ($data['title'] === '' || $data['banner'] === '') {
            $this->setFlash('danger', 'Title and banner filename are required.');
            $this->redirect(BASE_URL . '/?r=admin/admin/editPromotion&id=' . $id);
        } elseif (!in_array($data['status'], ['Active', 'Inactive'], true)) {
            $this->setFlash('danger', 'Invalid promotion status.');
            $this->redirect(BASE_URL . '/?r=admin/admin/editPromotion&id=' . $id);
        } elseif ($this->promotionModel->updatePromotion($id, $data)) {
            $this->setFlash('success', 'Promotion updated.');
            $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
        } else {
            $this->setFlash('danger', 'Failed to update promotion.');
            $this->redirect(BASE_URL . '/?r=admin/admin/editPromotion&id=' . $id);
        }
    }

    public function deletePromotion(): void
    {
        $this->requireGeneralAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
        }
        if ($this->promotionModel->deletePromotion($id)) {
            $this->setFlash('success', 'Promotion deleted.');
        } else {
            $this->setFlash('danger', 'Failed to delete promotion.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
    }

    // ----------------------------------------------------------------
    // Permissions / Orders
    // ----------------------------------------------------------------

    public function managePermissions(): void
    {
        $this->requireGeneralAdmin();
        View::render(__DIR__ . '/../views/manage_permissions.php', [
            'employee'    => $_SESSION['employee'],
            'navActive'   => 'permissions',
            'pageTitle'   => 'Roles & permissions',
            'pageHeading' => 'Roles & permissions',
        ]);
    }

    public function manageOrders(): void
    {
        $this->requireRoles(['general admin', 'branch admin', 'sales representative']);

        $employee = $_SESSION['employee'];
        $rawRole = strtolower(trim($employee['Emp_Position'] ?? ''));
        $isGeneralAdmin = ($rawRole === 'general admin');

        if ($isGeneralAdmin) {
            $scopeBranchId = null;
        } else {
            $scopeBranchId = trim((string)($employee['Emp_BranchId'] ?? ''));
            if ($scopeBranchId === '') {
                $scopeBranchId = 'INVALID_NO_BRANCH';
            }
        }

        View::render(__DIR__ . '/../views/orders_staff_index.php', [
            'employee'    => $employee,
            'orders'      => $this->model->getAllOrders($scopeBranchId),
            'navActive'   => 'orders',
            'pageTitle'   => 'Order Management Log',
        ]);
    }
}
