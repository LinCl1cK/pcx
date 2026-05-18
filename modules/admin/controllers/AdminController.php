<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../../promotion/models/PromotionModel.php';

class AdminController extends BaseController {
    private AdminModel $model;
    private PromotionModel $promotionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new AdminModel($pdo);
        $this->promotionModel = new PromotionModel($pdo);
    }

    public function dashboard(): void {
        $this->requireAdministrator();
        View::render(__DIR__ . '/../views/dashboard.php', [
            'employee' => $_SESSION['employee'],
            'pendingOrders' => $this->model->getPendingOrders(),
            'lowStock' => $this->model->getLowStock(),
            'tickets' => $this->model->getTickets(),
            'sales' => $this->model->getSalesReport(),
            'navActive' => 'dashboard',
            'pageTitle' => 'Admin Dashboard',
            'pageHeading' => 'Admin dashboard',
        ]);
    }

    // User Management
    public function manageUsers(): void {
        $this->requireAdministrator();
        $users = $this->model->getAllUsers();
        View::render(__DIR__ . '/../views/manage_users.php', ['users' => $users, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function editUser(): void {
        $this->requireAdministrator();
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
        View::render(__DIR__ . '/../views/edit_user.php', ['user' => $user, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function updateUser(): void {
        $this->requireAdministrator();
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
            'fname' => trim($_POST['fname'] ?? ''),
            'lname' => trim($_POST['lname'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
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

    public function deleteUser(): void {
        $this->requireAdministrator();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
            return;
        }
        if ($this->model->deleteUser($id)) {
            $this->setFlash('success', 'User deleted successfully');
        } else {
            $this->setFlash('danger', 'Failed to delete user');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageUsers');
    }

    // Employee Management
    public function manageEmployees(): void {
        $this->requireAdministrator();
        $employees = $this->model->getAllEmployees();
        $branches = $this->model->getAllBranches();
        View::render(__DIR__ . '/../views/manage_employees.php', ['employees' => $employees, 'branches' => $branches, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function createEmployee(): void {
        $this->requireAdministrator();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'fname' => trim($_POST['fname'] ?? ''),
                'lname' => trim($_POST['lname'] ?? ''),
                'position' => $_POST['role'] ?? '',
                'branch_id' => $_POST['branch_id'] ?? '',
                'email' => trim($_POST['email'] ?? ''),
                'contact' => trim($_POST['contact'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'password' => $_POST['password'] ?? '',
            ];
            if ($data['fname'] === '' || $data['lname'] === '' || $data['email'] === '' || $data['contact'] === '' || $data['address'] === '' || $data['password'] === '') {
                $this->setFlash('danger', 'All fields are required.');
            } elseif (!in_array($data['position'], ['Sales Representative', 'Technician', 'Administrator'], true)) {
                $this->setFlash('danger', 'Invalid employee role.');
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email format.');
            } elseif (strlen($data['contact']) > 15) {
                $this->setFlash('danger', 'Contact number must be 15 characters or fewer.');
            } elseif ($this->model->employeeEmailExists($data['email'])) {
                $this->setFlash('danger', 'That work email is already in use.');
            } elseif (strlen($data['password']) < 8) {
                $this->setFlash('danger', 'Password must be at least 8 characters.');
            } elseif ($this->model->createEmployee($data)) {
                $this->setFlash('success', 'Employee created successfully');
            } else {
                $this->setFlash('danger', 'Failed to create employee');
            }
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }
        $branches = $this->model->getAllBranches();
        View::render(__DIR__ . '/../views/create_employee.php', ['branches' => $branches, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function editEmployee(): void {
        $this->requireAdministrator();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }
        $employee = $this->model->getEmployeeById($id);
        if (!$employee) {
            http_response_code(404);
            echo "Employee not found";
            return;
        }
        $branches = $this->model->getAllBranches();
        View::render(__DIR__ . '/../views/edit_employee.php', ['employee_data' => $employee, 'branches' => $branches, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function updateEmployee(): void {
        $this->requireAdministrator();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }
        $id = $_POST['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }
        $data = [
            'fname' => trim($_POST['fname'] ?? ''),
            'lname' => trim($_POST['lname'] ?? ''),
            'position' => $_POST['role'] ?? '',
            'branch_id' => $_POST['branch_id'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'contact' => trim($_POST['contact'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
        ];
        if ($data['fname'] === '' || $data['lname'] === '' || $data['email'] === '' || $data['contact'] === '' || $data['address'] === '') {
            $this->setFlash('danger', 'All fields are required.');
        } elseif (!in_array($data['position'], ['Sales Representative', 'Technician', 'Administrator'], true)) {
            $this->setFlash('danger', 'Invalid employee role.');
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('danger', 'Invalid email format.');
        } elseif (strlen($data['contact']) > 15) {
            $this->setFlash('danger', 'Contact number must be 15 characters or fewer.');
        } elseif ($this->model->employeeEmailExists($data['email'], $id)) {
            $this->setFlash('danger', 'That work email is already in use.');
        } elseif ($this->model->updateEmployee($id, $data)) {
            $this->setFlash('success', 'Employee updated successfully');
        } else {
            $this->setFlash('danger', 'Failed to update employee');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
    }

    public function deleteEmployee(): void {
        $this->requireAdministrator();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
            return;
        }
        if ($this->model->deleteEmployee($id)) {
            $this->setFlash('success', 'Employee deleted successfully');
        } else {
            $this->setFlash('danger', 'Failed to delete employee');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageEmployees');
    }

    // Product Management
    public function manageProducts(): void {
        $this->requireAdministrator();
        $products = $this->model->getAllProducts();
        $categories = $this->model->getAllCategories();
        View::render(__DIR__ . '/../views/manage_products.php', ['products' => $products, 'categories' => $categories, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function createProduct(): void {
        $this->requireAdministrator();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'brand' => trim($_POST['brand'] ?? ''),
                'price' => (float) ($_POST['price'] ?? 0),
                'warranty' => (int) ($_POST['warranty'] ?? 0),
                'cat_id' => $_POST['cat_id'] ?? null,
                'image' => trim($_POST['image'] ?? ''),
                'featured' => isset($_POST['featured']) ? 1 : 0,
                'description' => trim($_POST['description'] ?? ''),
                'status' => $_POST['status'] ?? 'Active',
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
        $categories = $this->model->getAllCategories();
        $subcategories = $this->model->getAllSubcategories();
        View::render(__DIR__ . '/../views/create_product.php', ['categories' => $categories, 'subcategories' => $subcategories, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function editProduct(): void {
        $this->requireAdministrator();
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
        $categories = $this->model->getAllCategories();
        $subcategories = $this->model->getAllSubcategories();
        $selectedSubcategories = $this->model->getProductSubcategoryIds($id);
        View::render(__DIR__ . '/../views/edit_product.php', [
            'product' => $product,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'selectedSubcategories' => $selectedSubcategories,
            'flash' => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
        ]);
    }

    public function updateProduct(): void {
        $this->requireAdministrator();
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
            'name' => trim($_POST['name'] ?? ''),
            'brand' => trim($_POST['brand'] ?? ''),
            'price' => (float) ($_POST['price'] ?? 0),
            'warranty' => (int) ($_POST['warranty'] ?? 0),
            'cat_id' => $_POST['cat_id'] ?? null,
            'image' => trim($_POST['image'] ?? ''),
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'Active',
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

    public function deleteProduct(): void {
        $this->requireAdministrator();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
            return;
        }
        try {
            if ($this->model->deleteProduct($id)) {
                $this->setFlash('success', 'Product deleted successfully');
            } else {
                $this->setFlash('danger', 'Failed to delete product');
            }
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageProducts');
    }

    // Category Management
    public function manageCategories(): void {
        $this->requireAdministrator();
        $categories = $this->model->getAllCategories();
        View::render(__DIR__ . '/../views/manage_categories.php', ['categories' => $categories, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function manageBranches(): void {
        $this->requireAdministrator();
        View::render(__DIR__ . '/../views/manage_branches.php', [
            'branches' => $this->model->getAllBranches(),
            'flash' => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
            'navActive' => 'branches',
            'pageTitle' => 'Branches',
            'pageHeading' => 'Branches',
        ]);
    }

    public function createBranch(): void {
        $this->requireAdministrator();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'contact' => trim($_POST['contact'] ?? ''),
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

    public function updateBranch(): void {
        $this->requireAdministrator();
        $id = trim($_POST['id'] ?? '');
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'contact' => trim($_POST['contact'] ?? ''),
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

    public function deleteBranch(): void {
        $this->requireAdministrator();
        $id = trim($_GET['id'] ?? '');
        if ($id !== '' && $this->model->deleteBranch($id)) {
            $this->setFlash('success', 'Branch deleted.');
        } else {
            $this->setFlash('danger', 'Failed to delete branch. It may still be referenced.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageBranches');
    }

    public function manageSubcategories(): void {
        $this->requireAdministrator();
        View::render(__DIR__ . '/../views/manage_subcategories.php', [
            'subcategories' => $this->model->getAllSubcategories(),
            'flash' => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
            'navActive' => 'subcategories',
            'pageTitle' => 'Subcategories',
            'pageHeading' => 'Subcategories',
        ]);
    }

    public function createSubcategory(): void {
        $this->requireAdministrator();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if ($data['name'] === '') {
            $this->setFlash('danger', 'Subcategory name is required.');
        } elseif ($this->model->createSubcategory($data)) {
            $this->setFlash('success', 'Subcategory created.');
        } else {
            $this->setFlash('danger', 'Failed to create subcategory.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageSubcategories');
    }

    public function updateSubcategory(): void {
        $this->requireAdministrator();
        $id = trim($_POST['id'] ?? '');
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if ($id === '' || $data['name'] === '') {
            $this->setFlash('danger', 'Subcategory name is required.');
        } elseif ($this->model->updateSubcategory($id, $data)) {
            $this->setFlash('success', 'Subcategory updated.');
        } else {
            $this->setFlash('danger', 'Failed to update subcategory.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageSubcategories');
    }

    public function deleteSubcategory(): void {
        $this->requireAdministrator();
        $id = trim($_GET['id'] ?? '');
        if ($id !== '' && $this->model->deleteSubcategory($id)) {
            $this->setFlash('success', 'Subcategory deleted.');
        } else {
            $this->setFlash('danger', 'Failed to delete subcategory. It may still be assigned to a product.');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageSubcategories');
    }

    public function createCategory(): void {
        $this->requireAdministrator();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
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
        View::render(__DIR__ . '/../views/create_category.php', ['flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function editCategory(): void {
        $this->requireAdministrator();
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
        View::render(__DIR__ . '/../views/edit_category.php', ['category' => $category, 'flash' => $this->pullFlash(), 'employee' => $_SESSION['employee']]);
    }

    public function updateCategory(): void {
        $this->requireAdministrator();
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
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if ($this->model->updateCategory($id, $data)) {
            $this->setFlash('success', 'Category updated successfully');
        } else {
            $this->setFlash('danger', 'Failed to update category');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
    }

    public function deleteCategory(): void {
        $this->requireAdministrator();
        $id = $_GET['id'] ?? '';
        if (!$id) {
            $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
            return;
        }
        if ($this->model->deleteCategory($id)) {
            $this->setFlash('success', 'Category deleted successfully');
        } else {
            $this->setFlash('danger', 'Failed to delete category');
        }
        $this->redirect(BASE_URL . '/?r=admin/admin/manageCategories');
    }

    public function managePromotions(): void {
        $this->requireAdministrator();
        View::render(__DIR__ . '/../views/manage_promotions.php', [
            'promotions' => $this->promotionModel->getAllPromotionsAdmin(),
            'flash' => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
            'navActive' => 'promotions',
            'pageTitle' => 'Promotions',
            'pageHeading' => 'Promotions',
        ]);
    }

    public function createPromotion(): void {
        $this->requireAdministrator();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'banner' => trim($_POST['banner'] ?? ''),
                'status' => $_POST['status'] ?? 'Active',
                'start' => trim($_POST['start'] ?? ''),
                'end' => trim($_POST['end'] ?? ''),
            ];
            if ($data['title'] === '' || $data['banner'] === '') {
                $this->setFlash('danger', 'Title and banner filename are required.');
                $this->redirect(BASE_URL . '/?r=admin/admin/createPromotion');
            } elseif (!in_array($data['status'], ['Active', 'Inactive'], true)) {
                $this->setFlash('danger', 'Invalid promotion status.');
                $this->redirect(BASE_URL . '/?r=admin/admin/createPromotion');
            } elseif ($this->promotionModel->createPromotion($data)) {
                $this->setFlash('success', 'Promotion created.');
                $this->redirect(BASE_URL . '/?r=admin/admin/managePromotions');
            } else {
                $this->setFlash('danger', 'Failed to create promotion.');
                $this->redirect(BASE_URL . '/?r=admin/admin/createPromotion');
            }
            return;
        }
        View::render(__DIR__ . '/../views/create_promotion.php', [
            'flash' => $this->pullFlash(),
            'employee' => $_SESSION['employee'],
            'navActive' => 'promotions',
            'pageTitle' => 'New promotion',
            'pageHeading' => 'New promotion',
        ]);
    }

    public function deletePromotion(): void {
        $this->requireAdministrator();
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

    public function managePermissions(): void {
        $this->requireAdministrator();
        View::render(__DIR__ . '/../views/manage_permissions.php', [
            'employee' => $_SESSION['employee'],
            'navActive' => 'permissions',
            'pageTitle' => 'Roles & permissions',
            'pageHeading' => 'Roles & permissions',
        ]);
    }
}
