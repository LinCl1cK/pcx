<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$navActive = $navActive ?? 'verification';
$pageTitle = $pageTitle ?? 'Manual Verification';
$pageHeading = $pageHeading ?? 'Manual Verification';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
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
  <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
  <?php endif; ?>
  <p class="text-muted small">Limited customer view: name only. Full details remain in the order record.</p>
  <div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-striped mb-0 align-middle">
      <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>ID</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars((string) $order['Order_Id']) ?></td>
          <td><?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?></td>
          <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
          <td>
            <?php if (!empty($order['Cus_IdAttachment'])): ?>
              <a class="btn btn-sm btn-outline-dark" href="<?= BASE_URL ?>/<?= htmlspecialchars((string) $order['Cus_IdAttachment']) ?>" target="_blank">View ID</a>
            <?php elseif ((float) $order['Order_TotalAmount'] >= 50000): ?>
              <span class="badge text-bg-warning">Required</span>
            <?php else: ?>
              <span class="text-muted small">Optional</span>
            <?php endif; ?>
          </td>
          <td>
            <form method="post" action="<?= BASE_URL ?>/?r=verification/verification/process" class="d-flex flex-wrap gap-2 align-items-center">
              <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="id_verified" value="1" id="idv_<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                <label class="form-check-label" for="idv_<?= htmlspecialchars((string) $order['Order_Id']) ?>">ID checked</label>
              </div>
              <button class="btn btn-sm btn-success" name="action" value="confirm" type="submit">Confirm</button>
              <button class="btn btn-sm btn-outline-danger" name="action" value="reject" type="submit">Reject</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
