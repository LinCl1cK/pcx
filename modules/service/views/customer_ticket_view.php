<?php
$ticket = $ticket ?? [];
$pageTitle = $pageTitle ?? 'Ticket Details - PCX';
require_once dirname(__DIR__, 3) . '/app/core/header.php';

// Determine status badge colors
$status = trim((string)$ticket['Tix_Status']);
if ($status === 'Completed') {
    $badgeClass = 'bg-success-subtle text-success border-success-subtle';
    $icon = 'bi-check-circle-fill';
} elseif ($status === 'In Progress') {
    $badgeClass = 'bg-primary-subtle text-primary border-primary-subtle';
    $icon = 'bi-tools';
} else {
    $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
    $icon = 'bi-clock-fill';
}
?>

<div class="container py-5" style="max-width: 800px;">
  <a href="<?= BASE_URL ?>/?r=auth/auth/account" class="btn btn-sm btn-light border mb-4 text-secondary">
    <i class="bi bi-arrow-left me-1"></i> Back to Account
  </a>

  <div class="card border-0 shadow-sm bg-white overflow-hidden">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <h1 class="h4 fw-bold mb-1 text-dark">Service Ticket #<?= htmlspecialchars((string) $ticket['Tix_Id']) ?></h1>
        <p class="text-muted small mb-0">Opened on <?= htmlspecialchars(date('F j, Y, g:i a', strtotime((string)$ticket['Tix_CreatedAt']))) ?></p>
      </div>
      <span class="badge <?= $badgeClass ?> border px-3 py-2 fs-6 rounded-pill">
        <i class="bi <?= $icon ?> me-1"></i> <?= htmlspecialchars($status) ?>
      </span>
    </div>

    <div class="card-body p-4">
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="p-3 bg-light rounded-3 border h-100">
            <h3 class="h6 fw-bold text-dark mb-2"><i class="bi bi-receipt me-2 text-secondary"></i>Related Purchase</h3>
            <p class="mb-1 fw-medium">Invoice #<?= htmlspecialchars((string) ($ticket['Order_InvoiceNo'] ?? 'N/A')) ?></p>
            <p class="small text-muted mb-0">Order Date: <?= htmlspecialchars(date('M d, Y', strtotime((string)$ticket['Order_Date']))) ?></p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="p-3 bg-light rounded-3 border h-100">
            <h3 class="h6 fw-bold text-dark mb-2"><i class="bi bi-person-badge me-2 text-secondary"></i>Assigned Technician</h3>
            <?php if (!empty($ticket['Emp_Fname'])): ?>
              <p class="mb-1 fw-medium"><?= htmlspecialchars(trim($ticket['Emp_Fname'] . ' ' . $ticket['Emp_Lname'])) ?></p>
              <p class="small text-muted mb-0">Hardware Specialist</p>
            <?php else: ?>
              <p class="mb-0 text-muted fst-italic small mt-2">Waiting for technician assignment...</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <h3 class="h6 fw-bold text-dark mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Diagnostic Notes & Updates</h3>
      <div class="bg-blue-light border border-primary-subtle rounded-3 p-4 mb-4">
        <p class="mb-0 text-dark" style="white-space: pre-wrap;"><?= htmlspecialchars((string) $ticket['Tix_ProblemInfo']) ?></p>
      </div>

      <?php if (!empty($ticket['Tix_Attachment'])): ?>
        <h3 class="h6 fw-bold text-dark mb-3"><i class="bi bi-paperclip me-2 text-secondary"></i>Hardware Fault Photo</h3>
        <div class="border rounded-3 p-2 bg-light d-inline-block shadow-sm">
          <a href="<?= BASE_URL ?>/assets/uploads/reports/<?= htmlspecialchars((string) $ticket['Tix_Attachment']) ?>" target="_blank">
            <img src="<?= BASE_URL ?>/assets/uploads/reports/<?= htmlspecialchars((string) $ticket['Tix_Attachment']) ?>" alt="Attached hardware photo" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
          </a>
          <div class="text-center mt-2 small text-muted">Click to view full size</div>
        </div>
      <?php endif; ?>

      <?php if ($status === 'Completed' && !empty($ticket['Tix_DateCompleted'])): ?>
        <div class="alert alert-success border-0 d-flex align-items-center mt-4 mb-0">
          <i class="bi bi-check-circle-fill fs-4 me-3"></i>
          <div>
            <strong>Service Completed</strong><br>
            <span class="small">This ticket was resolved and closed on <?= htmlspecialchars(date('F j, Y', strtotime((string)$ticket['Tix_DateCompleted']))) ?>.</span>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php require_once dirname(__DIR__, 3) . '/app/core/footer.php'; ?>