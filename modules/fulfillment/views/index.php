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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PCX Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</head>
<body>
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
</body>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
