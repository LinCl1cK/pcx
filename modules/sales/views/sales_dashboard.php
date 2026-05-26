<?php
$summary = $summary ?? [];
$pendingOrders = $pendingOrders ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Sales Hub — PCX Admin';
$pageHeading = $pageHeading ?? 'Sales Console';
$pageSubtitle = 'Real-time overview of branch transactions and pipeline status.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="row g-3 mb-4">
  <?php foreach (
    [
      ['label' => 'Pending Orders', 'val' => $summary['pending_orders'] ?? 0, 'icon' => 'bi-clock-history'],
      ['label' => 'Verified By Me', 'val' => $summary['verified_orders'] ?? 0, 'icon' => 'bi-shield-check'],
      ['label' => 'Awaiting Fulfillment', 'val' => $summary['paid_orders'] ?? 0, 'icon' => 'bi-box-seam'],
      ['label' => 'Low Stock Items', 'val' => $summary['low_stock'] ?? 0, 'icon' => 'bi-exclamation-triangle', 'alert' => true],
    ] as $stat
  ): ?>
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm bg-white h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center fs-4 <?= !empty($stat['alert']) && $stat['val'] > 0 ? 'bg-red-light text-red' : 'bg-blue-light text-blue' ?>" style="width: 48px; height: 48px;">
            <i class="bi <?= $stat['icon'] ?>"></i>
          </div>
          <div>
            <span class="text-muted small d-block fw-medium"><?= htmlspecialchars($stat['label']) ?></span>
            <h3 class="fw-bold mb-0 <?= !empty($stat['alert']) && $stat['val'] > 0 ? 'text-red' : 'text-dark' ?>"><?= (int) $stat['val'] ?></h3>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <span class="fw-bold text-dark"><i class="bi bi-cart-check text-blue me-2"></i>Pending Order Queue</span>
    <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/?r=sales/sales/orders">Process Pipeline</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">Invoice No.</th>
            <th>Customer Name</th>
            <th class="text-end">Total Amount</th>
            <th class="text-end pe-4">Timestamp</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendingOrders as $order): ?>
            <tr>
              <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></td>
              <td class="fw-bold text-dark"><?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?></td>
              <td class="text-end fw-semibold text-blue">PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
              <td class="text-end pe-4 text-muted small"><?= htmlspecialchars((string) $order['Order_Date']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($pendingOrders)): ?>
            <tr>
              <td colspan="4" class="text-center text-muted py-4"><i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>All queues are clear.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>