<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$flash = $flash ?? null;
$navActive = $navActive ?? 'fulfillment';
$pageTitle = $pageTitle ?? 'Fulfillment — PCX Admin';
$pageHeading = $pageHeading ?? 'Fulfillment Queue';
$pageSubtitle = 'Process verified and paid orders to finalize completion protocols.';
$readOnly = strtolower((string) ($employee['role'] ?? '')) !== 'administrator';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-truck text-blue me-2"></i>Active Dispatch Board
    </span>
    <span class="badge bg-light text-secondary border"><?= count($orders) ?> Active</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">Order Reference</th>
            <th>Total Allocation</th>
            <th>Payment Metrics</th>
            <?php if (!$readOnly): ?>
              <th class="text-end pe-4">Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="<?= !$readOnly ? 4 : 3 ?>" class="text-center text-muted py-5">
                <i class="bi bi-truck fs-3 d-block mb-2"></i>
                <span class="small fw-medium text-secondary">No verified orders currently waiting in the dispatch pipeline.</span>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td class="ps-4">
                  <span class="fw-bold text-dark d-block">#<?= htmlspecialchars((string) $order['Order_Id']) ?></span>
                  <span class="text-muted font-monospace small">QUEUE-SYS</span>
                </td>
                <td class="fw-semibold text-blue">PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
                <td>
                  <span class="fw-medium text-dark d-block"><?= htmlspecialchars((string) ($order['Pay_Method'] ?? 'Unspecified')) ?></span>
                  <?php if (!empty($order['Pay_Status'])): ?>
                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem;"><?= htmlspecialchars((string) $order['Pay_Status']) ?></span>
                  <?php endif; ?>
                </td>
                <?php if (!$readOnly): ?>
                  <td class="text-end pe-4">
                    <div class="d-flex flex-column align-items-end gap-2">
                      <?php if (($order['Order_Status'] ?? '') !== 'Completed'): ?>
                        <form method="post" action="<?= BASE_URL ?>/?r=fulfillment/fulfillment/complete" class="m-0">
                          <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                          <button class="btn btn-sm btn-primary rounded-2 px-3" type="submit">
                            <i class="bi bi-check2-circle me-1"></i>Mark Completed
                          </button>
                        </form>
                      <?php endif; ?>

                      <?php if (($order['Order_Status'] ?? '') === 'Completed' && ($order['Pay_Method'] ?? '') === 'COD' && ($order['Pay_Status'] ?? '') === 'Pending'): ?>
                        <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/confirmPayment" class="m-0">
                          <input type="hidden" name="pay_id" value="<?= htmlspecialchars((string) $order['Pay_Id']) ?>">
                          <input type="hidden" name="next" value="fulfillment">
                          <button class="btn btn-sm btn-dark rounded-2 px-3" type="submit">
                            <i class="bi bi-cash me-1"></i>Confirm COD Payment
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>