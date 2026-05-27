<?php
// app/core/BaseController.php
declare(strict_types=1);

class BaseController
{
    protected PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    protected function view(string $path, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../modules/' . $path;
    }

    protected function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function normalizeRole(string $role): string
    {
        $key = strtolower(trim($role));
        $map = [
            'sales rep' => 'sales representative',
            'admin'     => 'administrator',
        ];
        return $map[$key] ?? $key;
    }

    protected function currentEmployeeRole(): ?string
    {
        // Fallback to 'role' or 'Emp_Position' depending on what your login session Populates
        $r = $_SESSION['employee']['Emp_Position'] ?? $_SESSION['employee']['role'] ?? null;
        return $r !== null ? $this->normalizeRole((string) $r) : null;
    }

    // Inside BaseController
    public function isAdministrator(): bool
    {
        if (!isset($_SESSION['employee'])) {
            return false;
        }

        $rawRole = strtolower(trim($_SESSION['employee']['Emp_Position'] ?? $_SESSION['employee']['role'] ?? ''));
        $role = str_replace('_', ' ', $rawRole);

        return in_array($role, ['administrator', 'general admin', 'branch admin']);
    }

    protected function isSalesRepresentative(): bool
    {
        return $this->currentEmployeeRole() === 'sales representative';
    }

    protected function isTechnician(): bool
    {
        return $this->currentEmployeeRole() === 'technician';
    }

    protected function forbid(string $message = 'Access denied'): void
    {
        http_response_code(403);
        echo htmlspecialchars($message);
        exit;
    }

    /** Customer-only session (not employee storefront actions). */
    protected function requireCustomer(string $nextRoute): void
    {
        if (!empty($_SESSION['employee'])) {
            $this->forbid('Please sign out from the staff portal to use the customer storefront.');
        }
        if (empty($_SESSION['user']['id'])) {
            $this->redirect(BASE_URL . '/?r=auth/auth/login&next=' . urlencode($nextRoute));
        }
    }

    /** Any logged-in employee. */
    protected function requireEmployee(array $roles = [], string $nextRoute = 'auth/auth/login'): void
    {
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

    protected function requireAdministrator(): void
    {
        $this->requireEmployee(['Administrator', 'Branch Admin']);
    }

    /** Blocks technicians from order/payment modules. */
    protected function requireStaffOrdersPayments(): void
    {
        $this->requireEmployee();
        if ($this->isTechnician()) {
            $this->forbid('Technicians cannot access orders or payments.');
        }
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function pullFlash(): ?array
    {
        if (empty($_SESSION['flash'])) {
            return null;
        }
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return is_array($flash) ? $flash : null;
    }

    protected function redirectToRoute(string $routeSpec): void
    {
        $clean = trim($routeSpec);
        if ($clean === '') {
            $this->redirect(BASE_URL . '/?r=catalog/product/home');
        }
        $this->redirect(BASE_URL . '/?r=' . urlencode($clean));
    }

    /**
     * Authenticates and verifies fine-grained privileges based on employee session roles.
     * Prevents "undefined method" runtime crashes.
     */
    protected function requireRoles(array $allowedPositions): void
    {
        $currentRole = $this->currentEmployeeRole();
        $normalizedAllowed = array_map([$this, 'normalizeRole'], $allowedPositions);

        // If not logged in, or their position isn't in the allowed list, reject them
        if (!$currentRole || !in_array($currentRole, $normalizedAllowed, true)) {
            http_response_code(403);
            echo "<div style='font-family:sans-serif; padding:2rem; max-width:500px; margin:auto;'>";
            echo "<h1 style='color:#dc3545;'>403 Access Forbidden</h1>";
            echo "<p>Your staff position (<strong>" . htmlspecialchars($currentRole ?? 'Guest') . "</strong>) does not have clearance to access this module.</p>";
            echo "<a href='" . BASE_URL . "'>Return to Safety</a>";
            echo "</div>";
            exit;
        }
    }

    /**
     * Strict check for items only a General (Global) Admin should perform
     */
    protected function requireGeneralAdmin(): void
    {
        // Must be an administrator/general admin strictly by string role
        // We explicitly removed the destructive `Emp_BranchId` verification here
        $role = $this->currentEmployeeRole();
        
        if ($role !== 'administrator' && $role !== 'general admin') {
            http_response_code(403);
            echo "<div style='padding:2rem; max-width:500px; margin:auto; font-family:sans-serif;'>";
            echo "<h2 style='color:#dc3545;'>Enterprise Clearance Required</h2>";
            echo "<p>Only General Administrators have clearance to modify global configurations.</p>";
            echo "<a href='" . BASE_URL . "'>Return to Safety</a>";
            echo "</div>";
            exit;
        }
    }

    private function denyAccess(): void
    {
        http_response_code(403);
        echo "403 Access Forbidden";
        exit;
    }
}