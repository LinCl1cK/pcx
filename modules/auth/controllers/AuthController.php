<?php
// modules/auth/controllers/AuthController.php
declare(strict_types=1);

require_once __DIR__ . '/../models/AuthModel.php';
if (file_exists(__DIR__ . '/../../catalog/models/ProductModel.php')) {
    require_once __DIR__ . '/../../catalog/models/ProductModel.php';
}

class AuthController extends BaseController {
    private AuthModel $model;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new AuthModel($pdo);
    }

    private static function passwordStrongEnough(string $password): bool {
        return strlen($password) >= 8
            && (bool) preg_match('/[A-Z]/', $password)
            && (bool) preg_match('/[a-z]/', $password)
            && (bool) preg_match('/\d/',    $password)
            && (bool) preg_match('/[^A-Za-z0-9]/', $password);
    }

    private static function jsonOut(array $payload, int $httpStatus = 200): never {
        http_response_code($httpStatus);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonOut(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $loginId   = trim($_POST['login_id'] ?? '');
        $password  = $_POST['password'] ?? '';
        $loginType = trim($_POST['login_type'] ?? 'customer');

        if ($loginId === '' || $password === '') {
            self::jsonOut(['success' => false, 'message' => 'Please fill in all fields.']);
        }

        $user = null;
        $role = 'user';

        // FIXED: Used if/else structure so employee queries aren't overwritten by customer queries
        if ($loginType === 'employee') {
            $user = $this->model->findEmployeeByEmail($loginId);
            $role = 'employee';
        } else {
            $user = $this->model->findCustomerByEmail($loginId);
            $role = 'user';
        }

        if (!$user) {
            self::jsonOut(['success' => false, 'message' => 'Invalid email/username or password.']);
        }

        // Verify password column names dynamically based on the active role structure
        $passwordHash = ($role === 'employee') ? $user['Emp_Password'] : $user['Cus_Password'];

        if (!password_verify($password, $passwordHash)) {
            self::jsonOut(['success' => false, 'message' => 'Invalid email/username or password.']);
        }

        // FIXED: Setup sessions to accurately line up with customer_header.php logic
        if ($role === 'employee') {
            $employeeRole = $this->normalizeRole((string) $user['Emp_Position']);
            $redirect = BASE_URL . '/?r=admin/admin/dashboard';
            if ($employeeRole === 'sales representative') {
                $redirect = BASE_URL . '/?r=sales/sales/dashboard';
            } elseif ($employeeRole === 'technician') {
                $redirect = BASE_URL . '/?r=technician/technician/dashboard';
            }

            $_SESSION['employee'] = [
                'id'    => $user['Emp_Id'],
                'name'  => trim($user['Emp_Fname'] . ' ' . $user['Emp_Lname']),
                'email' => $user['Emp_Email'],
                'role'  => $user['Emp_Position']
            ];
            
            self::jsonOut([
                'success'  => true,
                'message'  => 'Welcome back, Staff member!',
                'redirect' => $redirect
            ]);
        } else {
            $_SESSION['user'] = [
                'id'    => $user['Cus_Id'],
                'name'  => trim($user['Cus_Fname'] . ' ' . $user['Cus_Lname']),
                'email' => $user['Cus_Email'],
                'role'  => 'customer'
            ];

            self::jsonOut([
                'success'  => true,
                'message'  => 'Login successful!',
                'redirect' => BASE_URL . '/?r=catalog/product/home'
            ]);
        }
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonOut(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $fname           = trim((string)($_POST['fname'] ?? ''));
        $lname           = trim((string)($_POST['lname'] ?? ''));
        $email           = trim((string)($_POST['email'] ?? ''));
        $contact         = trim((string)($_POST['contact'] ?? ''));
        $address         = trim((string)($_POST['address'] ?? ''));
        $password        = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        if ($fname === '' || $lname === '' || $email === '' || $password === '' || $contact === '' || $address === '') {
            self::jsonOut(['success' => false, 'message' => 'Please complete all required fields.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::jsonOut(['success' => false, 'message' => 'Please provide a valid email format.']);
        }

        if ($password !== $confirmPassword) {
            self::jsonOut(['success' => false, 'message' => 'Form confirmation passwords do not match.']);
        }

        if (!self::passwordStrongEnough($password)) {
            self::jsonOut(['success' => false, 'message' => 'Password is too weak.']);
        }

        if ($this->model->customerEmailExists($email)) {
            self::jsonOut(['success' => false, 'message' => 'This email address is already registered.']);
        }

        $cusId = 'CUS' . str_pad((string)random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        
        $payload = [
            'Cus_Id'        => $cusId,
            'Cus_Fname'     => $fname,
            'Cus_Lname'     => $lname,
            'Cus_Email'     => $email,
            'Cus_Password'  => password_hash($password, PASSWORD_DEFAULT),
            'Cus_ContactNo' => $contact,
            'Cus_Address'   => $address
        ];

        if ($this->model->createCustomer($payload)) {
            $_SESSION['user'] = [
                'id'    => $cusId,
                'email' => $email,
                'name'  => $fname . ' ' . $lname
            ];
            self::jsonOut(['success' => true, 'redirect' => BASE_URL . '/?r=catalog/product/home']);
        }

        self::jsonOut(['success' => false, 'message' => 'Database operation error occurred during profile writing.']);
    }

    public function account(): void {
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $this->setFlash('danger', 'Please log in to access your dashboard account view.');
            $this->redirect(BASE_URL . '/?r=catalog/product/home');
        }
        $customer = $this->model->findCustomerByEmail($_SESSION['user']['email'] ?? '');
        $orders   = $this->model->getCustomerOrders($userId);
        
        $this->view('auth/views/account.php', [
            'user'     => $_SESSION['user'] ?? [],
            'customer' => $customer ?? [],
            'orders'   => $orders
        ]);
    }

    public function updateProfile(): void {
        $cid = $_SESSION['user']['id'] ?? null;
        if (!$cid || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=catalog/product/home');
        }

        $email   = trim((string)($_POST['email'] ?? ''));
        $fname   = trim((string)($_POST['fname'] ?? ''));
        $lname   = trim((string)($_POST['lname'] ?? ''));
        $contact = trim((string)($_POST['contact'] ?? ''));
        $address = trim((string)($_POST['address'] ?? ''));
        $newPw   = (string)($_POST['new_password'] ?? '');

        if ($email === '' || $fname === '' || $lname === '') {
            $this->setFlash('danger', 'All primary profile updates require explicit values.');
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }

        if ($this->model->customerEmailExistsExcept($email, $cid)) {
            $this->setFlash('danger', 'Email is already taken.');
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }

        if ($newPw !== '') {
            if (!self::passwordStrongEnough($newPw)) {
                $this->setFlash('danger', 'New password is too weak.');
                $this->redirect(BASE_URL . '/?r=auth/auth/account');
            }
            $this->db->prepare("UPDATE customer SET Cus_Password = :p WHERE Cus_Id = :id")
                     ->execute([':p' => password_hash($newPw, PASSWORD_DEFAULT), ':id' => $cid]);
        }

        $this->db->prepare("UPDATE customer SET Cus_Email = :e WHERE Cus_Id = :id")->execute([':e' => $email, ':id' => $cid]);
        $this->model->updateCustomerProfile($cid, ['fname' => $fname, 'lname' => $lname, 'contact' => $contact, 'address' => $address]);

        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['name']  = trim($fname . ' ' . $lname);

        $this->setFlash('success', 'Profile updated successfully.');
        $this->redirect(BASE_URL . '/?r=auth/auth/account');
    }

    public function logout(): void {
        unset($_SESSION['user']);
        $this->redirect(BASE_URL . '/?r=catalog/product/home');
    }

    public function employeeLogin(): void {
        $this->redirect(BASE_URL . '/?r=catalog/product/home&openModal=1&tab=employee');
    }

    public function employeeLogout(): void {
        unset($_SESSION['employee']);
        $this->redirect(BASE_URL . '/?r=catalog/product/home');
    }
}
