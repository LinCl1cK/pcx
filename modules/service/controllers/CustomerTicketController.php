<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';

class CustomerTicketController extends BaseController
{
    private ServiceModel $model;
    private ProductModel $productModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->model = new ServiceModel($pdo);
        $this->productModel = new ProductModel($pdo);
    }

    public function request(): void
    {
        $this->requireCustomer('service/customerTicket/request');
        $cid = (string) $_SESSION['user']['id'];
        $orders = $this->completedOrdersForCustomer($cid);
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oid = trim((string) ($_POST['order_id'] ?? ''));
            $info = trim((string) ($_POST['problem_info'] ?? ''));
            $attachmentFilename = null;

            try {
                if ($info === '' || strlen($info) < 5) {
                    throw new RuntimeException('Please describe the issue (at least 5 characters).');
                }

                // --- IMAGE UPLOAD HANDLING BLOCK ---
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $file = $_FILES['attachment'];

                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        throw new RuntimeException('File upload failed with error code ' . $file['error']);
                    }

                    // Check file size (e.g., max 5MB)
                    if ($file['size'] > 5 * 1024 * 1024) {
                        throw new RuntimeException('Attachment size exceeds maximum limit of 5MB.');
                    }

                    // Validate file extension / MIME type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($file['tmp_name']);

                    if (!in_array($mimeType, $allowedTypes, true)) {
                        throw new RuntimeException('Invalid file format. Only JPG, PNG, and WEBP images are accepted.');
                    }

                    // Create unique file name to avoid collisions
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $attachmentFilename = 'tix_' . uniqid('', true) . '.' . $ext;

                    // Fixed: Redirected targeted storage layout to the reports folder asset tree
                    $uploadDir = __DIR__ . '/../../../public/assets/uploads/reports/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $attachmentFilename)) {
                        throw new RuntimeException('Failed to save uploaded image asset.');
                    }
                }
                // ------------------------------------

                // Pass the attachment filename down to the model layer
                $this->model->createCustomerTicket($oid, $cid, $info, $attachmentFilename);

                $this->setFlash('success', 'Service ticket submitted successfully with attachment log.');
                $this->redirect(BASE_URL . '/?r=auth/auth/account');
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
        $categories = $this->productModel->getAllCategories();
        require __DIR__ . '/../views/customer_request.php';
    }

    private function completedOrdersForCustomer(string $customerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT o.Order_Id, o.Order_InvoiceNo, o.Order_Date
             FROM Orders o
             LEFT JOIN Service_Ticket t ON t.Tix_OrderID = o.Order_Id
             WHERE o.Order_CusId = :cid AND o.Order_Status = 'Completed' AND t.Tix_Id IS NULL
             ORDER BY o.Order_Date DESC"
        );
        $stmt->execute([':cid' => $customerId]);
        return $stmt->fetchAll();
    }

    // Changed from view() to details()
    public function details(): void
    {
        $this->requireCustomer('service/customerTicket/details');
        $cid = (string) $_SESSION['user']['id'];
        $ticketId = trim((string) ($_GET['id'] ?? ''));

        if ($ticketId === '') {
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }

        $ticket = $this->model->getCustomerTicket($ticketId, $cid);

        // Security check
        if (!$ticket) {
            $this->redirect(BASE_URL . '/?r=auth/auth/account');
        }

        $pageTitle = 'Ticket #' . htmlspecialchars($ticketId) . ' - PCX Service';

        require __DIR__ . '/../views/customer_ticket_view.php';
    }
}
