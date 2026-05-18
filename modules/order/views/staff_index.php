<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$navActive = $navActive ?? 'orders';
$pageTitle = $pageTitle ?? 'Orders';
$pageHeading = $pageHeading ?? 'Orders';
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
  <div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-striped mb-0 align-middle">
      <thead>
        <tr>
          <th>Order</th>
          <th>Invoice</th>
          <th>Customer</th>
          <th>Status</th>
          <th>Total</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= htmlspecialchars((string) $o['Order_Id']) ?></td>
            <td><?= htmlspecialchars((string) $o['Order_InvoiceNo']) ?></td>
            <td>
              <?= htmlspecialchars(trim(($o['Cus_Fname'] ?? '') . ' ' . ($o['Cus_Lname'] ?? ''))) ?>
              <?php if (!empty($o['Cus_Email'])): ?>
                <div class="small text-muted"><?= htmlspecialchars((string) $o['Cus_Email']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars((string) $o['Order_Status']) ?></td>
            <td>PHP <?= number_format((float) $o['Order_TotalAmount'], 2) ?></td>
            <td><small><?= htmlspecialchars((string) $o['Order_Date']) ?></small></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
