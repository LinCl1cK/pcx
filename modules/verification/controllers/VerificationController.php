<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/VerificationModel.php';

class VerificationController extends BaseController {
    private VerificationModel $model;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new VerificationModel($pdo);
    }

    public function index(): void {
        $this->requireEmployee(['Administrator', 'Sales Representative']);
        View::render(__DIR__ . '/../views/index.php', [
            'orders' => $this->model->getPendingOrders(),
            'employee' => $_SESSION['employee'],
            'navActive' => 'verification',
            'pageTitle' => 'Manual Verification',
            'pageHeading' => 'Manual Verification',
        ]);
    }

    public function process(): void {
        $this->requireEmployee(['Administrator', 'Sales Representative']);
        $orderId = trim((string) ($_POST['order_id'] ?? ''));
        $action = (string) ($_POST['action'] ?? '');
        try {
            if ($action === 'confirm') {
                $idChecked = !empty($_POST['id_verified']);
                $this->model->confirmOrder($orderId, (string) $_SESSION['employee']['id'], $idChecked);
            } else {
                $this->model->rejectOrder($orderId);
            }
            $this->setFlash('success', 'Verification action applied.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=verification/verification/index');
    }
}
