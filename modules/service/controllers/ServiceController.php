<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/ServiceModel.php';

/**
 * ServiceController
 * * Coordinates operations for the PCX Hardware Service Center Module.
 * Implements a split operational routing design pattern:
 * - Technicians receive an explicit, personal workbench showing all assigned tasks.
 * - Administrators receive a live, focused Active Service Pipeline tracking real-time bottlenecks.
 */
class ServiceController extends BaseController
{
    private ServiceModel $model;

    /**
     * Dependency injection initialization.
     * * @param PDO $pdo Shared application database transaction layer.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->model = new ServiceModel($pdo);
    }

    /**
     * Renders the unified operational dashboard component.
     * Route: ?r=service/service/index
     */
    public function index(): void
    {
        $this->requireEmployee();
        $emp = $_SESSION['employee'];

        // 1. Split-routing Track: Technician Personal Workbench
        if ($this->isTechnician()) {
            View::render(__DIR__ . '/../views/service_index.php', [
                'isAdmin'     => false,
                'isTech'      => true,
                'tickets'     => $this->model->listTicketsForTechnician((string) $emp['id']),
                'orders'      => [],
                'technicians' => [],
                'employee'    => $emp,
                'navActive'   => 'service',
                'pageTitle'   => 'My Service Tickets — PCX Admin',
                'pageHeading' => 'Service Tickets',
                'flash'       => $this->pullFlash()
            ]);
            return;
        }

        // 2. Split-routing Track: Administrator Core Pipeline Control
        if (!$this->isAdministrator()) {
            $this->forbid('Only administrators and technicians use this module.');
        }

        View::render(__DIR__ . '/../views/service_index.php', [
            'isAdmin'     => true,
            'isTech'      => false,
            // Leverages the focused pipeline extraction matrix instead of listing everything
            'tickets'     => $this->model->listActivePipelineTickets(),
            'orders'      => $this->model->getCompletedOrdersWithoutTicket(),
            'technicians' => $this->model->getTechnicians(),
            'employee'    => $emp,
            'navActive'   => 'service',
            'pageTitle'   => 'Active Service Pipeline — PCX Admin',
            'pageHeading' => 'Service Center Logistics',
            'flash'       => $this->pullFlash() // Synchronized flash state array mapping
        ]);
    }

    /**
     * Handles incoming POST requests from working technicians to log troubleshooting progression.
     * Route: ?r=service/service/technicianUpdate
     */
    public function technicianUpdate(): void
    {
        $this->requireEmployee();
        if (!$this->isTechnician()) {
            $this->forbid('Only technicians can update ticket logs.');
        }

        $empId       = (string) ($_SESSION['employee']['id'] ?? '');
        $tixId       = trim((string) ($_POST['tix_id'] ?? ''));
        $problemInfo = trim((string) ($_POST['problem_info'] ?? ''));
        $status      = trim((string) ($_POST['status'] ?? ''));

        try {
            if ($tixId === '' || $status === '' || $problemInfo === '') {
                throw new RuntimeException('All log updates require a status and descriptive progress notes.');
            }

            $this->model->updateTicketByTechnician($tixId, $empId, $status, $problemInfo);
            $this->setFlash('success', 'Service ticket log updated successfully.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }

        $this->redirect(BASE_URL . '/?r=service/service/index');
    }

    /**
     * Handles incoming POST requests from administrators to initialize and assign a hardware service log.
     * Route: ?r=service/service/create
     */
    public function create(): void
    {
        $this->requireEmployee();
        if (!$this->isAdministrator()) {
            $this->forbid('Only administrators can create and route service tickets.');
        }

        $orderId   = trim((string) ($_POST['order_id'] ?? ''));
        $empId     = trim((string) ($_POST['emp_id'] ?? ''));
        $diagnosis = trim((string) ($_POST['problem_info'] ?? $_POST['diagnosis'] ?? ''));

        try {
            if ($orderId === '' || $empId === '' || $diagnosis === '') {
                throw new RuntimeException('All routing fields are mandatory to assign a technician.');
            }
            $attachmentFilename = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['attachment'];

                // Perform standard size/mime-type verification checks here...
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file['tmp_name']);

                if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $attachmentFilename = 'tix_' . uniqid('', true) . '.' . $ext;

                    $uploadDir = __DIR__ . '/../../../public/assets/uploads/reports/';
                    move_uploaded_file($file['tmp_name'], $uploadDir . $attachmentFilename);
                }
            }

            // Pass the filename parameter to your database update method
            $this->model->createTicket($orderId, $empId, $diagnosis, $attachmentFilename);
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }

        $this->redirect(BASE_URL . '/?r=service/service/index');
    }
}
