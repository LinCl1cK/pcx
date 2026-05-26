<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$employeeId = (string) ($employee['id'] ?? '');
$navActive = $navActive ?? 'orders';
$pageTitle = $pageTitle ?? 'Order Directory — PCX Admin';
$pageHeading = $pageHeading ?? 'Order Directory';
$pageSubtitle = 'Read-only archive of global sales invoices and receipts.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-bag text-blue me-2"></i>Global Order Index
    </span>
    <span class="badge bg-light text-secondary border"><?= count($orders) ?> Total</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">Order ID / Invoice</th>
            <th>Customer</th>
            <th>Order Status</th>
            <th class="text-end">Total Allocation</th>
            <th class="text-end pe-4">Timestamp</th>
            <th class="text-end pe-4">Actions</th>
          </tr>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-5"><i class="bi bi-basket fs-3 d-block mb-2"></i>No orders logged in the database.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td class="ps-4">
                  <span class="fw-bold text-dark d-block">#<?= htmlspecialchars((string) $o['Order_Id']) ?></span>
                  <span class="text-muted font-monospace small">INV-<?= htmlspecialchars((string) $o['Order_InvoiceNo']) ?></span>
                </td>
                <td>
                  <span class="fw-bold text-dark d-block"><?= htmlspecialchars(trim(($o['Cus_Fname'] ?? '') . ' ' . ($o['Cus_Lname'] ?? ''))) ?></span>
                  <?php if (!empty($o['Cus_Email'])): ?>
                    <span class="text-secondary small"><?= htmlspecialchars((string) $o['Cus_Email']) ?></span>
                  <?php endif; ?>
                </td>
                <td><span class="badge bg-light text-secondary border px-2 py-1"><?= htmlspecialchars((string) $o['Order_Status']) ?></span></td>
                <td class="text-end fw-semibold text-blue">PHP <?= number_format((float) $o['Order_TotalAmount'], 2) ?></td>
                <td class="text-end pe-4 text-muted small"><?= htmlspecialchars((string) $o['Order_Date']) ?></td>

                <td class="text-end pe-4">
                  <?php if ($o['Order_Status'] === 'Pending'): ?>
                    <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="m-0">
                      <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $o['Order_Id']) ?>">
                      <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Cancel this pending order?')"><i class="bi bi-trash3 me-1"></i>Drop</button>
                    </form>
                  <?php elseif ($o['Order_Status'] === 'Confirmed' && (string) ($o['Order_VerifiedBy'] ?? '') === $employeeId): ?>
                    <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="m-0">
                      <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $o['Order_Id']) ?>">
                      <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Warning: You are cancelling an order you previously verified. Proceed?')"><i class="bi bi-x-octagon me-1"></i>Revoke</button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted small"><i class="bi bi-lock me-1"></i>System Locked</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>