<?php
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'permissions';
$pageTitle   = $pageTitle ?? 'Roles & Permissions — PCX Admin';
$pageHeading = $pageHeading ?? 'Roles & Access Matrix';
$pageSubtitle = 'A summary reference of system intent and active module authorization.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="alert alert-info border-0 bg-blue-light text-blue d-flex align-items-center mb-4 rounded-3 p-3 shadow-sm" role="alert">
  <i class="bi bi-shield-lock fs-5 me-3"></i>
  <span class="small fw-medium">
    <strong>Note:</strong> Policy enforcement occurs securely within application code logic (`BaseController`) and database triggers. The matrix below serves as an operational summary guide.
  </span>
</div>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-key text-blue me-2"></i>Capability Matrix
    </span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3" style="width: 250px;">Assigned Role</th>
            <th class="pe-4">Key Access Rights & Restrictions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="ps-4 fw-bold text-dark"><i class="bi bi-person-gear text-muted me-2"></i>Administrator</td>
            <td class="pe-4 text-secondary lh-lg py-3">
              Full system CRUD across all modules: Customers, employees, products, categories, promotions, inventory management (edit/transfer), all logistics processing (orders, payments, fulfillment), service ticket routing, and identity verification.
            </td>
          </tr>
          <tr>
            <td class="ps-4 fw-bold text-dark"><i class="bi bi-headset text-muted me-2"></i>Sales Representative</td>
            <td class="pe-4 text-secondary lh-lg py-3">
              Handles commercial queues: Manage order list (pending + verified by self), manual verification overrides (confirm/reject pending transactions), process fulfillment, check payments (read-only insight), and view live inventory stock (view-only mode).
            </td>
          </tr>
          <tr>
            <td class="ps-4 fw-bold text-dark"><i class="bi bi-tools text-muted me-2"></i>Technician</td>
            <td class="pe-4 text-secondary lh-lg py-3">
              Restricted to active service infrastructure: Modify and update assigned service tickets, log technical diagnosis records. Strict lockdown on sales data, active orders, and payment architectures.
            </td>
          </tr>
          <tr>
            <td class="ps-4 fw-bold text-dark"><i class="bi bi-person text-muted me-2"></i>Customer</td>
            <td class="pe-4 text-secondary lh-lg py-3">
              External front-end interactions only: Control personal profile, shopping cart, wishlists, secure checkout, package tracking, and submitting service warranty requests for completed purchases. Complete restriction from internal staff routing.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>