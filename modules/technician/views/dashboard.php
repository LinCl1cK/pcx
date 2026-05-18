<?php
$summary = $summary ?? [];
$tickets = $tickets ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Technician Dashboard';
$pageHeading = $pageHeading ?? 'Technician dashboard';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <?php foreach (['Pending', 'In Progress', 'Completed'] as $status): ?>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small"><?= htmlspecialchars($status) ?></div>
          <div class="display-6 fw-semibold"><?= (int) ($summary[$status] ?? 0) ?></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="d-flex justify-content-end mb-3">
  <a class="btn btn-dark" href="<?= BASE_URL ?>/?r=technician/technician/tickets">Manage Service Tickets</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <h2 class="h5 mb-3">Assigned Tickets</h2>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Ticket</th><th>Invoice</th><th>Customer</th><th>Status</th><th>Details</th></tr></thead>
        <tbody>
          <?php foreach ($tickets as $ticket): ?>
            <tr>
              <td><?= htmlspecialchars((string) $ticket['Tix_Id']) ?></td>
              <td><?= htmlspecialchars((string) ($ticket['Order_InvoiceNo'] ?? '-')) ?></td>
              <td><?= htmlspecialchars(trim(($ticket['Cus_Fname'] ?? '') . ' ' . ($ticket['Cus_Lname'] ?? ''))) ?></td>
              <td><?= htmlspecialchars((string) $ticket['Tix_Status']) ?></td>
              <td style="max-width:320px"><small><?= htmlspecialchars((string) $ticket['Tix_ProblemInfo']) ?></small></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($tickets)): ?>
            <tr><td colspan="5" class="text-muted">No assigned tickets.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
