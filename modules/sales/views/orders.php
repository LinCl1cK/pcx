<?php
$orders = $orders ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'orders';
$pageTitle = $pageTitle ?? 'Sales Orders';
$pageHeading = $pageHeading ?? 'Orders';
$employeeId = (string) ($employee['id'] ?? '');
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>

<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped align-middle mb-0">
    <thead>
      <tr><th>Order</th><th>Invoice</th><th>Customer</th><th>Status</th><th>Total</th><th>ID</th><th>Verified By</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars((string) $order['Order_Id']) ?></td>
          <td><?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></td>
          <td>
            <?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?>
            <div class="small text-muted"><?= htmlspecialchars((string) ($order['Cus_Email'] ?? '')) ?></div>
          </td>
          <td><?= htmlspecialchars((string) $order['Order_Status']) ?></td>
          <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
          <td>
            <?php if (!empty($order['Cus_IdAttachment'])): ?>
              <a class="btn btn-sm btn-outline-dark" href="<?= BASE_URL ?>/<?= htmlspecialchars((string) $order['Cus_IdAttachment']) ?>" target="_blank">View</a>
            <?php elseif ((float) $order['Order_TotalAmount'] >= 50000): ?>
              <span class="badge text-bg-warning">Required</span>
            <?php else: ?>
              <span class="text-muted small">Optional</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars(trim(($order['Emp_Fname'] ?? '') . ' ' . ($order['Emp_Lname'] ?? '')) ?: '-') ?></td>
          <td>
            <?php if ($order['Order_Status'] === 'Pending'): ?>
              <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/confirm" class="d-inline">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                <?php if ((float) $order['Order_TotalAmount'] >= 50000): ?>
                  <label class="small me-1"><input class="form-check-input me-1" type="checkbox" name="id_verified" value="1">ID</label>
                <?php endif; ?>
                <button class="btn btn-sm btn-dark" type="submit">Confirm</button>
              </form>
              <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="d-inline">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Cancel this order?')">Cancel</button>
              </form>
            <?php elseif ($order['Order_Status'] === 'Confirmed' && (string) ($order['Order_VerifiedBy'] ?? '') === $employeeId): ?>
              <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="d-inline">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Cancel this confirmed order?')">Cancel</button>
              </form>
            <?php else: ?>
              <span class="text-muted small">Read-only</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
