<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/FulfillmentModel.php';

class FulfillmentController extends BaseController
{
    private FulfillmentModel $model;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->model = new FulfillmentModel($pdo);
    }

    public function index(): void
    {
        // In FulfillmentController.php (index) and VerificationController.php (index/process)
        $this->requireEmployee(['Administrator', 'branch admin', 'general admin', 'Sales Representative']);
        View::render(__DIR__ . '/../views/fulfillment_index.php', [
            'orders' => $this->model->getPaidOrders(),
            'employee' => $_SESSION['employee'],
            'flash' => $this->pullFlash(),
        ]);
    }

    public function complete(): void
    {
        $this->requireAdministrator();
        $orderId = trim((string) ($_POST['order_id'] ?? ''));
        try {
            $this->model->completeOrder($orderId);
            $this->setFlash('success', 'Order marked completed.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=fulfillment/fulfillment/index');
    }
}
