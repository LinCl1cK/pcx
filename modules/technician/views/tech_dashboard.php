<?php
$summary = $summary ?? [];
$tickets = $tickets ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Technician Desk — PCX Admin';
$pageHeading = $pageHeading ?? 'Service Console';
$pageSubtitle = 'Monitor diagnostic pipelines and hardware servicing statuses.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="row g-3 mb-4">
  <?php foreach ([
    ['status' => 'Pending', 'icon' => 'bi-hourglass-split'],
    ['status' => 'In Progress', 'icon' => 'bi-tools'],
    ['status' => 'Completed', 'icon' => 'bi-check-circle']
  ] as $stat): ?>
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm bg-white h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-3 bg-blue-light text-blue d-flex align-items-center justify-content-center fs-4" style="width: 48px; height: 48px;">
            <i class="bi <?= $stat['icon'] ?>"></i>
          </div>
          <div>
            <span class="text-muted small d-block fw-medium"><?= htmlspecialchars($stat['status']) ?> Tickets</span>
            <h3 class="fw-bold mb-0 text-dark"><?= (int) ($summary[$stat['status']] ?? 0) ?></h3>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <span class="fw-bold text-dark"><i class="bi bi-wrench text-blue me-2"></i>My Assigned Backlog</span>
    <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/?r=technician/technician/tickets">Manage Board</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3" style="width: 80px;">Ticket</th>
            <th>Ref Invoice</th>
            <th>Client Name</th>
            <th>Status</th>
            <th class="pe-4">Diagnosis Info</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tickets as $ticket): ?>
            <tr>
              <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars((string) $ticket['Tix_Id']) ?></td>
              <td><span class="font-monospace text-muted"><?= htmlspecialchars((string) ($ticket['Order_InvoiceNo'] ?? '-')) ?></span></td>
              <td class="fw-bold text-dark"><?= htmlspecialchars(trim(($ticket['Cus_Fname'] ?? '') . ' ' . ($ticket['Cus_Lname'] ?? ''))) ?></td>
              <td>
                <span class="badge <?= $ticket['Tix_Status'] === 'Completed' ? 'bg-green-light text-success' : 'bg-blue-light text-blue' ?> px-2 py-1">
                  <?= htmlspecialchars((string) $ticket['Tix_Status']) ?>
                </span>
              </td>
              <td class="pe-4 text-secondary text-truncate" style="max-width:320px;" title="<?= htmlspecialchars((string) $ticket['Tix_ProblemInfo']) ?>">
                <?= htmlspecialchars((string) $ticket['Tix_ProblemInfo']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($tickets)): ?>
            <tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No servicing tasks currently assigned.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>