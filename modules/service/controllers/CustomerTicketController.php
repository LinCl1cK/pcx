<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';

class CustomerTicketController extends BaseController {
    private ServiceModel $model;
    private ProductModel $productModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new ServiceModel($pdo);
        $this->productModel = new ProductModel($pdo);
    }

    public function request(): void {
        $this->requireCustomer('service/customerTicket/request');
        $cid = (string) $_SESSION['user']['id'];
        $orders = $this->completedOrdersForCustomer($cid);
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oid = trim((string) ($_POST['order_id'] ?? ''));
            $info = trim((string) ($_POST['problem_info'] ?? ''));
            try {
                if ($info === '' || strlen($info) < 5) {
                    throw new RuntimeException('Please describe the issue (at least 5 characters).');
                }
                $this->model->createCustomerTicket($oid, $cid, $info);
                $this->setFlash('success', 'Service ticket submitted.');
                $this->redirect(BASE_URL . '/?r=auth/auth/account');
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
        $categories = $this->productModel->getAllCategories();
        require __DIR__ . '/../views/customer_request.php';
    }

    private function completedOrdersForCustomer(string $customerId): array {
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
}
