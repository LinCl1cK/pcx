<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';

class AuthController extends BaseController {
    private AuthModel $model;
    private ProductModel $productModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new AuthModel($pdo);
        $this->productModel = new ProductModel($pdo);
    }

    private static function passwordStrongEnough(string $password): bool {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/\d/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginId = trim((string) ($_POST['login_id'] ?? $_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $next = trim((string) ($_POST['next'] ?? 'catalog/product/home'));

            if ($loginId === '' || $password === '') {
                View::render(__DIR__ . '/../views/login.php', ['error' => 'Email and password are required.', 'next' => $next]);
                return;
            }

            $customer = $this->model->findCustomerByEmail($loginId);
            if ($customer && password_verify($password, (string) $customer['Cus_Password'])) {
                unset($_SESSION['employee']);
                $_SESSION['user'] = [
                    'id' => $customer['Cus_Id'],
                    'role' => 'customer',
                    'email' => $customer['Cus_Email'],
                    'name' => trim($customer['Cus_Fname'] . ' ' . $customer['Cus_Lname']),
                ];
                $this->redirect(BASE_URL . '/?r=' . urlencode($next));
                return;
            }

            $employee = $this->model->findEmployeeByUsername($loginId);
            if ($employee && password_verify($password, (string) $employee['Emp_PasswordHash'])) {
                unset($_SESSION['user']);
                $_SESSION['employee'] = [
                    'id' => $employee['Emp_Id'],
                    'name' => trim($employee['Emp_Fname'] . ' ' . $employee['Emp_Lname']),
                    'role' => $employee['Emp_Role'],
                    'branch_id' => $employee['Emp_BranchId'],
                ];

                $role = strtolower((string) $employee['Emp_Role']);
                if ($role === 'sales representative') {
                    $this->redirect(BASE_URL . '/?r=verification/verification/index');
                } elseif ($role === 'technician') {
                    $this->redirect(BASE_URL . '/?r=service/service/index');
                } else {
                    $this->redirect(BASE_URL . '/?r=admin/admin/dashboard');
                }
                return;
            }

            View::render(__DIR__ . '/../views/login.php', ['error' => 'Invalid credentials.', 'next' => $next]);
            return;
        }

        View::render(__DIR__ . '/../views/login.php', [
            'next' => trim((string) ($_GET['next'] ?? 'catalog/product/home')),
        ]);
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname = trim((string) ($_POST['fname'] ?? ''));
            $lname = trim((string) ($_POST['lname'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
            $contact = trim((string) ($_POST['contact'] ?? ''));
            $address = trim((string) ($_POST['address'] ?? ''));

            if ($fname === '' || $lname === '' || $email === '' || $password === '' || $contact === '' || $address === '') {
                View::render(__DIR__ . '/../views/register.php', ['error' => 'All required fields must be filled out.']);
                return;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                View::render(__DIR__ . '/../views/register.php', ['error' => 'Please enter a valid email address.']);
                return;
            }
            if ($password !== $confirmPassword) {
                View::render(__DIR__ . '/../views/register.php', ['error' => 'Password confirmation does not match.']);
                return;
            }
            if (!self::passwordStrongEnough($password)) {
                View::render(__DIR__ . '/../views/register.php', [
                    'error' => 'Password must be at least 8 characters and include upper, lower, number, and symbol.',
                ]);
                return;
            }
            if ($this->model->customerEmailExists($email)) {
                View::render(__DIR__ . '/../views/register.php', ['error' => 'Email is already registered. Please login instead.']);
                return;
            }

            $data = [
                'Cus_Id' => 'CUST-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT),
                'Cus_Fname' => $fname,
                'Cus_Lname' => $lname,
                'Cus_Email' => $email,
                'Cus_Password' => password_hash($password, PASSWORD_DEFAULT),
                'Cus_ContactNo' => $contact,
                'Cus_Address' => $address,
            ];
            if (!$this->model->createCustomer($data)) {
                View::render(__DIR__ . '/../views/register.php', ['error' => 'Registration failed.']);
                return;
            }

            $this->setFlash('success', 'Account created. Please sign in.');
            $this->redirect(BASE_URL . '/?r=catalog/product/home');
            return;
        }

        View::render(__DIR__ . '/../views/register.php', []);
    }

    public function account(): void {
        $this->requireCustomer('auth/auth/account');
        $customerId = (string) $_SESSION['user']['id'];
        View::render(__DIR__ . '/../views/account.php', [
            'pageTitle' => 'My Account - PCX Store',
            'user' => $_SESSION['user'],
            'customer' => $this->model->getCustomerById($customerId),
            'orders' => $this->model->getCustomerOrders($customerId),
            'categories' => $this->productModel->getAllCategories(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function updateProfile(): void {
        $this->requireCustomer('auth/auth/account');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }
        $cid = (string) $_SESSION['user']['id'];
        $email = trim((string) ($_POST['email'] ?? ''));
        $data = [
            'fname' => trim((string) ($_POST['fname'] ?? '')),
            'lname' => trim((string) ($_POST['lname'] ?? '')),
            'contact' => trim((string) ($_POST['contact'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
        ];
        if ($data['fname'] === '' || $data['lname'] === '' || $email === '' || $data['contact'] === '' || $data['address'] === '') {
            $this->setFlash('danger', 'All profile fields are required.');
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('danger', 'Invalid email.');
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }
        if ($this->model->customerEmailExistsExcept($email, $cid)) {
            $this->setFlash('danger', 'That email is already used by another account.');
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }
        $pw = (string) ($_POST['new_password'] ?? '');
        if ($pw !== '') {
            if (!self::passwordStrongEnough($pw)) {
                $this->setFlash('danger', 'New password does not meet strength rules.');
                $this->redirect(BASE_URL . '/?r=auth/auth/account');
            }
            $this->db->prepare("UPDATE Customer SET Cus_Password = :p WHERE Cus_Id = :id")->execute([
                ':p' => password_hash($pw, PASSWORD_DEFAULT),
                ':id' => $cid,
            ]);
        }
        $this->db->prepare("UPDATE Customer SET Cus_Email = :e WHERE Cus_Id = :id")->execute([':e' => $email, ':id' => $cid]);
        $this->model->updateCustomerProfile($cid, $data);
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['name'] = trim($data['fname'] . ' ' . $data['lname']);
        $this->setFlash('success', 'Profile updated.');
        $this->redirect(BASE_URL . '/?r=auth/auth/account');
    }

    public function logout(): void {
        unset($_SESSION['user']);
        $this->redirect(BASE_URL . '/?r=catalog/product/home');
    }

    public function employeeLogin(): void {
        $this->redirect(BASE_URL . '/?r=auth/auth/login&next=admin/admin/dashboard');
    }

    public function employeeLogout(): void {
        unset($_SESSION['employee']);
        $this->redirect(BASE_URL . '/?r=auth/auth/login');
    }
}
