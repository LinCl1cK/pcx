<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/VerificationModel.php';

class VerificationController extends BaseController
{
    private VerificationModel $model;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->model = new VerificationModel($pdo);
    }

    /**
     * Normalises any role string from the session into a lowercase,
     * underscore-free canonical form so all comparisons are consistent.
     *
     * FIX: The original code used strtolower() + str_replace('_', ' ', ...)
     * but the requireEmployee() gate above used original-casing strings like
     * 'Administrator'. This helper centralises normalisation so the gate
     * check and the business-logic check always agree.
     */
    private function normaliseRole(array $employee): string
    {
        $raw = (string) ($employee['Emp_Position'] ?? $employee['role'] ?? '');
        return str_replace('_', ' ', strtolower(trim($raw)));
    }

    public function index(): void
    {
        // FIX: Use lowercase role strings consistently to match normaliseRole() output.
        // Original code mixed 'Administrator' (title-case, from requireEmployee)
        // with 'administrator' (lowercase, from the business logic block), which
        // could cause the branch-restriction block to fire for actual Administrators.
        $this->requireEmployee([
            'Sales Representative',
            'sales representative',
            'Branch Admin',
            'branch admin',
            'Administrator',
            'administrator',
            'General Admin',
            'general admin'
        ]);

        $employee = $_SESSION['employee'] ?? [];
        $role     = $this->normaliseRole($employee);

        // General Admins see all branches; all others see their own branch scope
        $branchScope = ($role === 'general admin')
            ? null
            : (isset($employee['branch_id']) ? trim((string) $employee['branch_id']) : null);
        if ($branchScope === '') {
            $branchScope = null;
        }

        View::render(__DIR__ . '/../views/verification_index.php', [
            'orders'      => $this->model->getPendingOrders($branchScope),
            'employee'    => $employee,
            'navActive'   => 'verification',
            'pageTitle'   => 'Manual Verification',
            'pageHeading' => 'Manual Verification',
        ]);
    }

    public function process(): void
    {
        $this->requireEmployee([
            'Sales Representative',
            'sales representative',
            'Branch Admin',
            'branch admin',
            'Administrator',
            'administrator',
            'General Admin',
            'general admin'
        ]);

        $employee = $_SESSION['employee'] ?? [];
        $orderId  = trim((string) ($_POST['order_id'] ?? ''));
        $action   = (string) ($_POST['action'] ?? '');

        try {
            // 1. Fetch the target order
            $stmt = $this->db->prepare(
                "SELECT Order_BranchId, Order_Shipping FROM Orders WHERE Order_Id = :id LIMIT 1"
            );
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new RuntimeException('Order not found.');
            }

            // 2. Normalise the current employee's role
            $role = $this->normaliseRole($employee);

            // FIX: 'administrator' and 'general admin' are both exempt from the
            // branch restriction. Previously 'administrator' was compared as-is
            // against the raw session value, which could be 'Administrator' or
            // 'administrator' depending on the login path — causing a mismatch.
            if ($role !== 'administrator' && $role !== 'general admin') {

                // Safely trim CHAR(10) padding from both sides before comparison
                $userBranch  = trim((string) ($employee['branch_id'] ?? ''));
                $orderBranch = trim((string) ($order['Order_BranchId'] ?? ''));

                error_log("DEBUG: User Branch = '{$userBranch}'");
                error_log("DEBUG: Order Branch = '{$orderBranch}'");
                error_log("DEBUG: Role = '{$role}'");
                error_log("DEBUG: Order Shipping = '{$order['Order_Shipping']}'");

                // Open Pool Delivery: Delivery order with no assigned branch
                $isOpenPoolDelivery = ($order['Order_Shipping'] === 'Delivery' && $orderBranch === '');

                if ($userBranch === '') {
                    throw new RuntimeException('Access Denied: You must be assigned to a branch to process orders.');
                }

                if (!$isOpenPoolDelivery && $orderBranch !== $userBranch) {
                    throw new RuntimeException('Access Denied: This order belongs to a different branch.');
                }
            }

            // 3. Process the verification action
            if ($action === 'confirm') {
                $idChecked = !empty($_POST['id_verified']);
                $this->model->confirmOrder($orderId, (string) $employee['id'], $idChecked);
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
