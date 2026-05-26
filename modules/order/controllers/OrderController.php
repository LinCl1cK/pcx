<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';
require_once __DIR__ . '/../../cart/models/CartModel.php';

class OrderController extends BaseController {
    private OrderModel $model;
    private ProductModel $productModel;
    private CartModel $cartModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new OrderModel($pdo);
        $this->productModel = new ProductModel($pdo);
        $this->cartModel = new CartModel($pdo);
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
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
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

    public function checkout(): void {
        $this->requireCustomer('cart/cart/view');
        $customerId = (string) $_SESSION['user']['id'];
        $items = $this->cartModel->getCartItems($customerId);
        if (empty($items)) {
            $this->setFlash('danger', 'Cart is empty.');
            $this->redirect(BASE_URL . '/?r=cart/cart/view');
        }

        $subtotal = array_reduce($items, static function (float $sum, array $item): float {
            return $sum + (float) $item['line_total'];
        }, 0.0);
        $total = round($subtotal * 1.12, 2);
        $customer = $this->model->getCustomerForCheckout($customerId);

        View::render(__DIR__ . '/../views/checkout.php', [
            'items' => $items,
            'customer' => $customer,
            'pickupBranches' => $this->model->getPickupBranchesForCart($customerId),
            'subtotal' => $subtotal,
            'total' => $total,
            'requiresId' => $total >= 50000,
            'categories' => $this->productModel->getAllCategories(),
            'flash' => $this->pullFlash(),
            'pageTitle' => 'Checkout - PCX Store',
        ]);
    }

    public function place(): void {
        $this->requireCustomer('cart/cart/view');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=cart/cart/view');
        }

        $shipping = (string) ($_POST['shipping'] ?? 'Delivery');
        if (!in_array($shipping, ['Delivery', 'Pickup'], true)) {
            $shipping = 'Delivery';
        }
        $destinationAddress = trim((string) ($_POST['destination_address'] ?? ''));
        $pickupBranchId = trim((string) ($_POST['pickup_branch_id'] ?? ''));
        $contactNo = trim((string) ($_POST['contact_no'] ?? ''));
        if ($shipping === 'Pickup') {
            $customerId = (string) $_SESSION['user']['id'];
            $branch = $this->model->getPickupBranchForCart($customerId, $pickupBranchId);
            if (!$branch) {
                $this->setFlash('danger', 'Please select a pickup branch with enough stock for every cart item.');
                $this->redirect(BASE_URL . '/?r=order/order/checkout');
            }
            $destinationAddress = $this->model->formatPickupAddress($branch);
        }
        if ($destinationAddress === '') {
            $this->setFlash('danger', 'Destination address is required.');
            $this->redirect(BASE_URL . '/?r=order/order/checkout');
        }
        if ($contactNo !== '' && strlen($contactNo) > 15) {
            $this->setFlash('danger', 'Contact number must be 15 characters or fewer.');
            $this->redirect(BASE_URL . '/?r=order/order/checkout');
        }

        try {
            $customerId = (string) $_SESSION['user']['id'];
            $total = $this->model->calculateCartTotal($customerId);
            $customer = $this->model->getCustomerForCheckout($customerId);
            $idPath = $this->handleIdUpload($customerId, $total >= 50000, $customer['Cus_IdAttachment'] ?? null);
            if ($idPath !== null) {
                $this->model->updateCustomerIdAttachment($customerId, $idPath);
            }

            $orderId = $this->model->placeOrderFromCart($customerId, $shipping, $destinationAddress, $contactNo !== '' ? $contactNo : null);
            $this->redirect(BASE_URL . '/?r=order/order/invoice&id=' . urlencode($orderId));
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
            $this->redirect(BASE_URL . '/?r=order/order/checkout');
        }
    }

    public function invoice(): void {
        $this->requireCustomer('auth/auth/account');
        $orderId = trim((string) ($_GET['id'] ?? ''));
        $order = $this->model->getOrderById($orderId);
        if (!$order || $order['Order_CusId'] !== $_SESSION['user']['id']) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }

        View::render(__DIR__ . '/../views/invoice.php', [
            'order' => $order,
            'categories' => $this->productModel->getAllCategories(),
            'pageTitle' => 'Invoice - PCX Store',
        ]);
    }

    public function track(): void {
        $this->requireCustomer('auth/auth/account');
        $orders = $this->model->getCustomerOrders((string) $_SESSION['user']['id']);
        View::render(__DIR__ . '/../views/tracking.php', [
            'orders' => $orders,
            'categories' => $this->productModel->getAllCategories(),
            'pageTitle' => 'Order Tracking - PCX Store',
        ]);
    }

    /** Staff order list (Administrator / Sales Representative). */
    public function index(): void {
        $this->requireStaffOrdersPayments();
        $emp = $_SESSION['employee'];
        $orders = $this->isAdministrator()
            ? $this->model->listAllOrders()
            : $this->model->listOrdersForSalesRep((string) $emp['id']);
        View::render(__DIR__ . '/../views/orders_staff_index.php', [
            'orders' => $orders,
            'employee' => $emp,
            'navActive' => 'orders',
            'pageTitle' => 'Orders',
            'pageHeading' => 'Orders',
        ]);
    }
}
