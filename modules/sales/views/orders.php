<?php
$orders = $orders ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'orders';
$pageTitle = $pageTitle ?? 'Sales Orders — PCX Admin';
$pageHeading = $pageHeading ?? 'Order Pipeline';
$pageSubtitle = 'Process, confirm, or safely cancel incoming customer purchases.';
$employeeId = (string) ($employee['id'] ?? '');
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3">
    <span class="fw-bold text-dark"><i class="bi bi-cart-check text-blue me-2"></i>Sales Orders</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">ID / Invoice</th>
            <th>Customer Name</th>
            <th>Status</th>
            <th>Total Amount</th>
            <th>ID Check</th>
            <th class="text-end pe-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td class="ps-4">
                <span class="d-block fw-medium text-dark">#<?= htmlspecialchars((string) $order['Order_Id']) ?></span>
                <span class="text-muted font-monospace" style="font-size: 0.75rem;"><?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></span>
              </td>
              <td>
                <span class="fw-bold text-dark d-block"><?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?></span>
                <span class="text-secondary" style="font-size: 0.75rem;"><?= htmlspecialchars((string) ($order['Cus_Email'] ?? '')) ?></span>
              </td>
              <td><span class="badge bg-light text-secondary border px-2 py-1"><?= htmlspecialchars((string) $order['Order_Status']) ?></span></td>
              <td class="fw-bold text-blue">PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
              <td>
                <?php if (!empty($order['Cus_IdAttachment'])): ?>
                  <a class="badge bg-blue-light text-blue text-decoration-none" href="<?= BASE_URL ?>/<?= htmlspecialchars((string) $order['Cus_IdAttachment']) ?>" target="_blank"><i class="bi bi-file-earmark-person me-1"></i>View File</a>
                <?php elseif ((float) $order['Order_TotalAmount'] >= 50000): ?>
                  <span class="badge bg-red-light text-red"><i class="bi bi-exclamation-circle me-1"></i>Required</span>
                <?php else: ?>
                  <span class="text-muted small">Optional</span>
                <?php endif; ?>
              </td>
              <td class="text-end pe-4">
                <?php if ($order['Order_Status'] === 'Pending'): ?>
                  <div class="d-inline-flex gap-2 align-items-center">
                    <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/confirm" class="m-0 d-flex align-items-center gap-2">
                      <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                      <?php if ((float) $order['Order_TotalAmount'] >= 50000): ?>
                        <div class="form-check m-0">
                          <input class="form-check-input" type="checkbox" name="id_verified" value="1" id="id_<?= $order['Order_Id'] ?>">
                          <label class="form-check-label small" for="id_<?= $order['Order_Id'] ?>">Verified</label>
                        </div>
                      <?php endif; ?>
                      <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>Confirm</button>
                    </form>
                    <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="m-0">
                      <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                      <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Cancel this order?')"><i class="bi bi-x-lg"></i></button>
                    </form>
                  </div>
                <?php elseif ($order['Order_Status'] === 'Confirmed' && (string) ($order['Order_VerifiedBy'] ?? '') === $employeeId): ?>
                  <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="m-0">
                    <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Cancel this confirmed order?')"><i class="bi bi-x-lg me-1"></i>Cancel</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted small">Locked</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>