<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$navActive = $navActive ?? 'fulfillment';
$pageTitle = $pageTitle ?? 'Fulfillment';
$pageHeading = $pageHeading ?? 'Fulfillment queue';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
<?php endif; ?>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped mb-0 align-middle">
    <thead><tr><th>Order</th><th>Total</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars((string) $order['Order_Id']) ?></td>
          <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
          <td>
            <form method="post" action="<?= BASE_URL ?>/?r=fulfillment/fulfillment/complete" class="d-inline">
              <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
              <button class="btn btn-sm btn-dark" type="submit">Mark completed</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
