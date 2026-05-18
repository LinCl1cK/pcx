<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';

class PaymentController extends BaseController {
    private PaymentModel $model;
    private ProductModel $productModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new PaymentModel($pdo);
        $this->productModel = new ProductModel($pdo);
    }

    public function pay(): void {
        $this->requireCustomer('auth/auth/account');
        $orderId = trim((string) ($_GET['id'] ?? $_POST['order_id'] ?? ''));
        $order = $this->model->getOrder($orderId);
        if (!$order || $order['Order_CusId'] !== $_SESSION['user']['id']) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }
        $requiresId = (float) ($order['Order_TotalAmount'] ?? 0) >= 50000;
        $hasId = !empty($order['Cus_IdAttachment']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $method = (string) ($_POST['method'] ?? 'COD');
            $region = (string) ($_POST['region'] ?? 'Metro Manila');
            $amount = (float) ($order['Order_TotalAmount'] ?? 0);
            if (!in_array($method, ['COD', 'GCash', 'Maya', 'Bank Transfer'], true)) {
                View::render(__DIR__ . '/../views/pay.php', [
                    'order' => $order,
                    'error' => 'Invalid payment method.',
                    'categories' => $this->productModel->getAllCategories(),
                    'requiresId' => $requiresId,
                    'hasId' => $hasId,
                ]);
                return;
            }
            if ($requiresId && !$hasId) {
                View::render(__DIR__ . '/../views/pay.php', [
                    'order' => $order,
                    'error' => 'A valid ID attachment is required before paying high-value orders.',
                    'categories' => $this->productModel->getAllCategories(),
                    'requiresId' => $requiresId,
                    'hasId' => $hasId,
                ]);
                return;
            }
            try {
                $this->model->simulatePayment($orderId, $method, $amount, $region);
                $this->setFlash('success', 'Payment simulation completed.');
                $this->redirect(BASE_URL . '/?r=order/order/track');
            } catch (Throwable $e) {
                View::render(__DIR__ . '/../views/pay.php', [
                    'order' => $order,
                    'error' => $e->getMessage(),
                    'categories' => $this->productModel->getAllCategories(),
                    'requiresId' => $requiresId,
                    'hasId' => $hasId,
                ]);
            }
            return;
        }

        View::render(__DIR__ . '/../views/pay.php', [
            'order' => $order,
            'categories' => $this->productModel->getAllCategories(),
            'requiresId' => $requiresId,
            'hasId' => $hasId,
        ]);
    }

    public function index(): void {
        $this->requireStaffOrdersPayments();
        $payments = $this->model->listAllWithOrders();
        View::render(__DIR__ . '/../views/staff_index.php', [
            'payments' => $payments,
            'employee' => $_SESSION['employee'],
            'readOnly' => !$this->isAdministrator(),
            'navActive' => 'payments',
            'pageTitle' => 'Payments',
            'pageHeading' => 'Payments',
        ]);
    }
}
