<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';

class OrderController extends BaseController {
    private OrderModel $model;
    private ProductModel $productModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new OrderModel($pdo);
        $this->productModel = new ProductModel($pdo);
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

        try {
            $orderId = $this->model->placeOrderFromCart((string) $_SESSION['user']['id'], $shipping);
            $this->redirect(BASE_URL . '/?r=order/order/invoice&id=' . urlencode($orderId));
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
            $this->redirect(BASE_URL . '/?r=cart/cart/view');
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
        View::render(__DIR__ . '/../views/staff_index.php', [
            'orders' => $orders,
            'employee' => $emp,
            'navActive' => 'orders',
            'pageTitle' => 'Orders',
            'pageHeading' => 'Orders',
        ]);
    }
}
