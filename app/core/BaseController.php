<?php
// app/core/BaseController.php
declare(strict_types=1);

class BaseController {
    protected PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    protected function view(string $path, array $data = []): void {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../modules/' . $path;
    }

    protected function json($data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    protected function normalizeRole(string $role): string {
        $key = strtolower(trim($role));
        $map = [
            'sales rep' => 'sales representative',
            'admin' => 'administrator',
        ];
        return $map[$key] ?? $key;
    }

    protected function currentEmployeeRole(): ?string {
        $r = $_SESSION['employee']['role'] ?? null;
        return $r !== null ? $this->normalizeRole((string) $r) : null;
    }

    protected function isAdministrator(): bool {
        return $this->currentEmployeeRole() === 'administrator';
    }

    protected function isSalesRepresentative(): bool {
        return $this->currentEmployeeRole() === 'sales representative';
    }

    protected function isTechnician(): bool {
        return $this->currentEmployeeRole() === 'technician';
    }

    protected function forbid(string $message = 'Access denied'): void {
        http_response_code(403);
        echo htmlspecialchars($message);
        exit;
    }

    /** Customer-only session (not employee storefront actions). */
    protected function requireCustomer(string $nextRoute): void {
        if (!empty($_SESSION['employee'])) {
            $this->forbid('Please sign out from the staff portal to use the customer storefront.');
        }
        if (empty($_SESSION['user']['id'])) {
            $this->redirect(BASE_URL . '/?r=auth/auth/login&next=' . urlencode($nextRoute));
        }
    }

    /** Any logged-in employee. */
    protected function requireEmployee(array $roles = [], string $nextRoute = 'auth/auth/login'): void {
        $employee = $_SESSION['employee'] ?? null;
        if (!$employee || empty($employee['id'])) {
            $this->redirect(BASE_URL . '/?r=' . urlencode($nextRoute));
        }
        if ($roles !== []) {
            $normalized = array_map([$this, 'normalizeRole'], $roles);
            $current = $this->normalizeRole((string) ($employee['role'] ?? ''));
            if (!in_array($current, $normalized, true)) {
                $this->forbid();
            }
        }
    }

    protected function requireAdministrator(): void {
        $this->requireEmployee(['Administrator']);
    }

    /** Blocks technicians from order/payment modules. */
    protected function requireStaffOrdersPayments(): void {
        $this->requireEmployee();
        if ($this->isTechnician()) {
            $this->forbid('Technicians cannot access orders or payments.');
        }
    }

    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function pullFlash(): ?array {
        if (empty($_SESSION['flash'])) {
            return null;
        }
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return is_array($flash) ? $flash : null;
    }

    protected function redirectToRoute(string $routeSpec): void {
        $clean = trim($routeSpec);
        if ($clean === '') {
            $this->redirect(BASE_URL . '/?r=catalog/product/home');
        }
        $this->redirect(BASE_URL . '/?r=' . urlencode($clean));
    }
}
