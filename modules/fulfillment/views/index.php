<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$flash = $flash ?? null;
$navActive = $navActive ?? 'fulfillment';
$pageTitle = $pageTitle ?? 'Fulfillment';
$pageHeading = $pageHeading ?? 'Fulfillment queue';
$readOnly = strtolower((string) ($employee['role'] ?? '')) !== 'administrator';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
<?php endif; ?>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped mb-0 align-middle">
    <thead><tr><th>Order</th><th>Total</th><th>Payment</th><?php if (!$readOnly): ?><th>Action</th><?php endif; ?></tr></thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars((string) $order['Order_Id']) ?></td>
          <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
          <td>
            <?= htmlspecialchars((string) ($order['Pay_Method'] ?? '-')) ?>
            <?php if (!empty($order['Pay_Status'])): ?>
              <span class="badge text-bg-secondary ms-1"><?= htmlspecialchars((string) $order['Pay_Status']) ?></span>
            <?php endif; ?>
          </td>
          <?php if (!$readOnly): ?><td>
            <form method="post" action="<?= BASE_URL ?>/?r=fulfillment/fulfillment/complete" class="d-inline">
              <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
              <button class="btn btn-sm btn-dark" type="submit">Mark completed</button>
            </form>
          </td><?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
