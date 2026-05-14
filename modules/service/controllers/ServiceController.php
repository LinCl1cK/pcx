<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/ServiceModel.php';

class ServiceController extends BaseController {
    private ServiceModel $model;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new ServiceModel($pdo);
    }

    public function index(): void {
        $this->requireEmployee();
        $emp = $_SESSION['employee'];
        if ($this->isTechnician()) {
            View::render(__DIR__ . '/../views/index.php', [
                'isAdmin' => false,
                'isTech' => true,
                'tickets' => $this->model->listTicketsForTechnician((string) $emp['id']),
                'orders' => [],
                'technicians' => [],
                'employee' => $emp,
                'navActive' => 'service',
                'pageTitle' => 'My Service Tickets',
                'pageHeading' => 'Service Tickets',
            ]);
            return;
        }
        if (!$this->isAdministrator()) {
            $this->forbid('Only administrators and technicians use this module.');
        }
        View::render(__DIR__ . '/../views/index.php', [
            'isAdmin' => true,
            'isTech' => false,
            'tickets' => $this->model->listAllTickets(),
            'orders' => $this->model->getCompletedOrdersWithoutTicket(),
            'technicians' => $this->model->getTechnicians(),
            'employee' => $emp,
            'navActive' => 'service',
            'pageTitle' => 'Service Tickets (Admin)',
            'pageHeading' => 'Service Tickets',
        ]);
    }

    public function create(): void {
        $this->requireAdministrator();
        $orderId = trim((string) ($_POST['order_id'] ?? ''));
        $empId = trim((string) ($_POST['emp_id'] ?? ''));
        $diagnosis = trim((string) ($_POST['diagnosis'] ?? ''));
        try {
            if ($diagnosis === '') {
                throw new RuntimeException('Diagnosis / problem details are required.');
            }
            $this->model->createTicket($orderId, $empId, $diagnosis);
            $this->setFlash('success', 'Service ticket created.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=service/service/index');
    }

    public function technicianUpdate(): void {
        $this->requireEmployee(['Technician']);
        $tixId = trim((string) ($_POST['tix_id'] ?? ''));
        $status = trim((string) ($_POST['status'] ?? ''));
        $info = trim((string) ($_POST['problem_info'] ?? ''));
        if (!in_array($status, ['Pending', 'In Progress', 'Completed'], true)) {
            $this->setFlash('danger', 'Invalid status.');
            $this->redirect(BASE_URL . '/?r=service/service/index');
        }
        try {
            $this->model->updateTicketByTechnician($tixId, (string) $_SESSION['employee']['id'], $status, $info);
            $this->setFlash('success', 'Ticket updated.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=service/service/index');
    }
}
