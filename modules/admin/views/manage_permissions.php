<?php
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'permissions';
$pageTitle = $pageTitle ?? 'Roles';
$pageHeading = $pageHeading ?? 'Roles & Permissions';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<p class="text-muted small">Enforcement is in application code (BaseController) and database triggers. This page summarizes intent.</p>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-bordered mb-0 align-middle">
    <thead class="table-light"><tr><th>Role</th><th>Key access</th></tr></thead>
    <tbody>
      <tr>
        <td>Administrator</td>
        <td>Full CRUD: customers, employees, products, categories, promotions, inventory (edit/transfer), all orders, payments list, fulfillment, service ticket creation, verification.</td>
      </tr>
      <tr>
        <td>Sales Representative</td>
        <td>Order list (pending + verified by self), manual verification (confirm/reject pending), payments (read-only), fulfillment, inventory (view-only).</td>
      </tr>
      <tr>
        <td>Technician</td>
        <td>Service tickets assigned to them only: update status and diagnosis text. No orders or payments.</td>
      </tr>
      <tr>
        <td>Customer</td>
        <td>Profile, cart, wishlist, checkout, order tracking, service request for completed orders. No staff modules.</td>
      </tr>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
