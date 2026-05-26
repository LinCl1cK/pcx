<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/TechnicianModel.php';

class TechnicianController extends BaseController {
    private TechnicianModel $model;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new TechnicianModel($pdo);
    }

    private function requireTechnicianRole(): void {
        $this->requireEmployee(['Technician']);
    }

    public function dashboard(): void {
        $this->requireTechnicianRole();
        $emp = $_SESSION['employee'];
        View::render(__DIR__ . '/../views/tech_dashboard.php', [
            'employee' => $emp,
            'summary' => $this->model->dashboardSummary((string) $emp['id']),
            'tickets' => $this->model->getAssignedTickets((string) $emp['id'], 8),
            'flash' => $this->pullFlash(),
            'navActive' => 'dashboard',
            'pageTitle' => 'Technician Dashboard',
            'pageHeading' => 'Technician dashboard',
        ]);
    }

    public function tickets(): void {
        $this->requireTechnicianRole();
        $emp = $_SESSION['employee'];
        View::render(__DIR__ . '/../views/tickets.php', [
            'employee' => $emp,
            'tickets' => $this->model->getAssignedTickets((string) $emp['id']),
            'orders' => $this->model->getCompletedOrdersWithoutTicket(),
            'flash' => $this->pullFlash(),
            'navActive' => 'service',
            'pageTitle' => 'Service Tickets',
            'pageHeading' => 'My service tickets',
        ]);
    }

    public function create(): void {
        $this->requireTechnicianRole();
        $orderId = trim((string) ($_POST['order_id'] ?? ''));
        $problemInfo = trim((string) ($_POST['problem_info'] ?? ''));
        try {
            if ($problemInfo === '' || strlen($problemInfo) < 5) {
                throw new RuntimeException('Diagnosis details must be at least 5 characters.');
            }
            if (strlen($problemInfo) > 255) {
                throw new RuntimeException('Diagnosis details must be 255 characters or fewer.');
            }
            $this->model->createAssignedTicket($orderId, (string) $_SESSION['employee']['id'], $problemInfo);
            $this->setFlash('success', 'Service ticket created.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=technician/technician/tickets');
    }

    public function update(): void {
        $this->requireTechnicianRole();
        $ticketId = trim((string) ($_POST['tix_id'] ?? ''));
        $status = trim((string) ($_POST['status'] ?? ''));
        $problemInfo = trim((string) ($_POST['problem_info'] ?? ''));
        try {
            if (!in_array($status, ['Pending', 'In Progress', 'Completed'], true)) {
                throw new RuntimeException('Invalid ticket status.');
            }
            if ($problemInfo === '' || strlen($problemInfo) > 255) {
                throw new RuntimeException('Diagnosis details are required and must be 255 characters or fewer.');
            }
            $this->model->updateAssignedTicket($ticketId, (string) $_SESSION['employee']['id'], $status, $problemInfo);
            $this->setFlash('success', 'Ticket updated.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=technician/technician/tickets');
    }

    public function delete(): void {
        $this->requireTechnicianRole();
        $ticketId = trim((string) ($_POST['tix_id'] ?? ''));
        try {
            $this->model->deleteAssignedTicket($ticketId, (string) $_SESSION['employee']['id']);
            $this->setFlash('success', 'Pending ticket deleted.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=technician/technician/tickets');
    }
}
