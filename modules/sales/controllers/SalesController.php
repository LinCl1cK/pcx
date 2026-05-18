<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/SalesModel.php';

class SalesController extends BaseController {
    private SalesModel $model;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new SalesModel($pdo);
    }

    private function requireSales(): void {
        $this->requireEmployee(['Sales Representative']);
    }

    public function dashboard(): void {
        $this->requireSales();
        $emp = $_SESSION['employee'];
        View::render(__DIR__ . '/../views/dashboard.php', [
            'employee' => $emp,
            'summary' => $this->model->dashboardSummary((string) $emp['id']),
            'pendingOrders' => $this->model->getPendingOrders(),
            'flash' => $this->pullFlash(),
            'navActive' => 'dashboard',
            'pageTitle' => 'Sales Dashboard',
            'pageHeading' => 'Sales dashboard',
        ]);
    }

    public function orders(): void {
        $this->requireSales();
        View::render(__DIR__ . '/../views/orders.php', [
            'employee' => $_SESSION['employee'],
            'orders' => $this->model->getOrdersForSalesRep((string) $_SESSION['employee']['id']),
            'flash' => $this->pullFlash(),
            'navActive' => 'orders',
            'pageTitle' => 'Sales Orders',
            'pageHeading' => 'Orders',
        ]);
    }

    public function verification(): void {
        $this->requireSales();
        View::render(__DIR__ . '/../views/orders.php', [
            'employee' => $_SESSION['employee'],
            'orders' => $this->model->getOrdersForSalesRep((string) $_SESSION['employee']['id']),
            'flash' => $this->pullFlash(),
            'navActive' => 'verification',
            'pageTitle' => 'Sales Verification',
            'pageHeading' => 'Order verification',
        ]);
    }

    public function confirm(): void {
        $this->requireSales();
        $orderId = trim((string) ($_POST['order_id'] ?? ''));
        try {
            $this->model->confirmPendingOrder($orderId, (string) $_SESSION['employee']['id'], !empty($_POST['id_verified']));
            $this->setFlash('success', 'Order confirmed after stock check.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=sales/sales/orders');
    }

    public function cancel(): void {
        $this->requireSales();
        $orderId = trim((string) ($_POST['order_id'] ?? ''));
        try {
            $this->model->cancelSalesOrder($orderId, (string) $_SESSION['employee']['id']);
            $this->setFlash('success', 'Order cancelled.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=sales/sales/orders');
    }

    public function payments(): void {
        $this->requireSales();
        View::render(__DIR__ . '/../views/payments.php', [
            'employee' => $_SESSION['employee'],
            'payments' => $this->model->getPayments(),
            'flash' => $this->pullFlash(),
            'navActive' => 'payments',
            'pageTitle' => 'Payments',
            'pageHeading' => 'Payments',
        ]);
    }

    public function fulfillment(): void {
        $this->requireSales();
        View::render(__DIR__ . '/../views/fulfillment.php', [
            'employee' => $_SESSION['employee'],
            'orders' => $this->model->getPaidOrders(),
            'navActive' => 'fulfillment',
            'pageTitle' => 'Fulfillment',
            'pageHeading' => 'Fulfillment queue',
        ]);
    }

    public function inventory(): void {
        $this->requireSales();
        View::render(__DIR__ . '/../views/inventory.php', [
            'employee' => $_SESSION['employee'],
            'stocks' => $this->model->getInventory(),
            'navActive' => 'inventory',
            'pageTitle' => 'Inventory',
            'pageHeading' => 'Inventory',
        ]);
    }

    public function confirmPayment(): void {
        $this->requireSales();
        require_once __DIR__ . '/../../payment/models/PaymentModel.php';
        $paymentModel = new PaymentModel($this->db);
        try {
            $paymentModel->confirmPayment(trim((string) ($_POST['pay_id'] ?? '')));
            $this->setFlash('success', 'Payment confirmed. Order is now paid.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=sales/sales/payments');
    }
}
