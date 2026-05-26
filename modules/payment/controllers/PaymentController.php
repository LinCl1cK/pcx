<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';
require_once __DIR__ . '/../../order/models/OrderModel.php';

class PaymentController extends BaseController {
    private PaymentModel $model;
    private ProductModel $productModel;
    private OrderModel $orderModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new PaymentModel($pdo);
        $this->productModel = new ProductModel($pdo);
        $this->orderModel = new OrderModel($pdo);
    }

    private function handleIdUpload(string $customerId, bool $required, ?string $existingPath): ?string {
        $file = $_FILES['id_attachment'] ?? null;
        $hasUpload = is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if (!$hasUpload) {
            if ($required && !$existingPath) {
                throw new RuntimeException('A valid ID attachment is required for high-value orders.');
            }
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('ID upload failed. Please try again.');
        }
        if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new RuntimeException('ID attachment must be 5 MB or smaller.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $mime = $tmp !== '' ? (string) mime_content_type($tmp) : '';
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        if (!isset($allowed[$mime])) {
            throw new RuntimeException('Valid ID must be a JPG, PNG, WEBP, or PDF file.');
        }

        $uploadDir = PCX_ROOT . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'ids';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Unable to prepare ID upload directory.');
        }
        $filename = $customerId . '-' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $target = $uploadDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($tmp, $target)) {
            throw new RuntimeException('Unable to save ID attachment.');
        }
        return 'assets/uploads/ids/' . $filename;
    }

    private function validatePaymentChoice(string $paymentChoice, float $amount, string $region, string $gatewayRef, float $expectedAmount): array {
        if (!in_array($paymentChoice, ['COD', 'Cashless'], true)) {
            throw new RuntimeException('Invalid payment option.');
        }
        if (abs($amount - $expectedAmount) > 0.01) {
            throw new RuntimeException('Payment amount must match the order total.');
        }
        if ($paymentChoice === 'COD') {
            if (!in_array($region, ['Metro Manila', 'Provincial'], true)) {
                throw new RuntimeException('Invalid COD region.');
            }
            $cap = $region === 'Metro Manila' ? 50000.00 : 30000.00;
            if ($expectedAmount > $cap) {
                throw new RuntimeException('COD amount exceeds the cap for the selected region.');
            }
            return ['method' => 'COD', 'gatewayRef' => null];
        }

        if ($gatewayRef === '' || strlen($gatewayRef) > 100 || !preg_match('/^[A-Za-z0-9._-]+$/', $gatewayRef)) {
            throw new RuntimeException('Cashless transaction reference is required and may contain only letters, numbers, dot, dash, or underscore.');
        }
        return ['method' => 'Cashless', 'gatewayRef' => $gatewayRef];
    }

    public function checkout(): void {
        $this->requireCustomer('order/order/checkout');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=order/order/checkout');
        }

        $customerId = (string) $_SESSION['user']['id'];
        $shipping = (string) ($_POST['shipping'] ?? 'Delivery');
        $destinationAddress = trim((string) ($_POST['destination_address'] ?? ''));
        $pickupBranchId = trim((string) ($_POST['pickup_branch_id'] ?? ''));
        $contactNo = trim((string) ($_POST['contact_no'] ?? ''));
        $paymentChoice = (string) ($_POST['payment_option'] ?? 'COD');
        $region = (string) ($_POST['cod_region'] ?? 'Metro Manila');
        $gatewayRef = trim((string) ($_POST['gateway_ref'] ?? ''));
        $amount = (float) ($_POST['payment_amount'] ?? 0);

        try {
            if (!in_array($shipping, ['Delivery', 'Pickup'], true)) {
                throw new RuntimeException('Invalid shipping method.');
            }
            if ($shipping === 'Pickup') {
                $branch = $this->orderModel->getPickupBranchForCart($customerId, $pickupBranchId);
                if (!$branch) {
                    throw new RuntimeException('Please select a pickup branch with enough stock for every cart item.');
                }
                $destinationAddress = $this->orderModel->formatPickupAddress($branch);
            }
            if ($destinationAddress === '') {
                throw new RuntimeException('Destination address is required.');
            }
            if ($contactNo !== '' && strlen($contactNo) > 15) {
                throw new RuntimeException('Contact number must be 15 characters or fewer.');
            }

            $expectedAmount = $this->orderModel->calculateCartTotal($customerId);
            if ($paymentChoice === 'COD') {
                $amount = $expectedAmount;
            }
            $customer = $this->orderModel->getCustomerForCheckout($customerId);
            $idPath = $this->handleIdUpload($customerId, $expectedAmount >= 50000, $customer['Cus_IdAttachment'] ?? null);
            if ($idPath !== null) {
                $this->orderModel->updateCustomerIdAttachment($customerId, $idPath);
            }
            $payment = $this->validatePaymentChoice($paymentChoice, $amount, $region, $gatewayRef, $expectedAmount);

            $orderId = $this->orderModel->placeOrderFromCart($customerId, $shipping, $destinationAddress, $contactNo !== '' ? $contactNo : null);
            $this->model->createPendingPayment($orderId, $customerId, $payment['method'], $expectedAmount, $payment['gatewayRef']);
            $this->setFlash('success', 'Order placed. Payment is pending staff confirmation.');
            $this->redirect(BASE_URL . '/?r=order/order/track');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
            $this->redirect(BASE_URL . '/?r=order/order/checkout');
        }
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
            $paymentChoice = (string) ($_POST['payment_option'] ?? 'COD');
            $region = (string) ($_POST['cod_region'] ?? 'Metro Manila');
            $gatewayRef = trim((string) ($_POST['gateway_ref'] ?? ''));
            $amount = (float) ($order['Order_TotalAmount'] ?? 0);
            try {
                $payment = $this->validatePaymentChoice($paymentChoice, (float) ($_POST['payment_amount'] ?? $amount), $region, $gatewayRef, $amount);
            } catch (Throwable $e) {
                View::render(__DIR__ . '/../views/pay.php', [
                    'order' => $order,
                    'error' => $e->getMessage(),
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
                $this->model->createPendingPayment($orderId, (string) $_SESSION['user']['id'], $payment['method'], $amount, $payment['gatewayRef']);
                $this->setFlash('success', 'Payment submitted and pending staff confirmation.');
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
        View::render(__DIR__ . '/../views/payment_staff_index.php', [
            'payments' => $payments,
            'employee' => $_SESSION['employee'],
            'flash' => $this->pullFlash(),
            'canConfirm' => $this->isAdministrator() || $this->isSalesRepresentative(),
            'navActive' => 'payments',
            'pageTitle' => 'Payments',
            'pageHeading' => 'Payments',
        ]);
    }

    public function confirm(): void {
        $this->requireEmployee(['Administrator', 'Sales Representative']);
        $paymentId = trim((string) ($_POST['pay_id'] ?? ''));
        try {
            $result = $this->model->confirmPayment($paymentId, $this->isSalesRepresentative());
            $message = ($result['method'] ?? '') === 'COD'
                ? 'COD payment confirmed after fulfillment check.'
                : 'Payment confirmed. Order is now paid.';
            $this->setFlash('success', $message);
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=payment/payment/index');
    }
}
