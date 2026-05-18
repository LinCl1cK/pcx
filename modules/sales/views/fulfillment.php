<?php
$orders = $orders ?? [];
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'fulfillment';
$pageTitle = $pageTitle ?? 'Fulfillment';
$pageHeading = $pageHeading ?? 'Fulfillment queue';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<div class="alert alert-info">Read-only fulfillment queue. Administrators complete orders.</div>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>Order</th><th>Invoice</th><th>Customer</th><th>Total</th><th>Date</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars((string) $order['Order_Id']) ?></td>
          <td><?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></td>
          <td><?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?></td>
          <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
          <td><small><?= htmlspecialchars((string) $order['Order_Date']) ?></small></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
