<?php
$payments = $payments ?? [];
$readOnly = !empty($readOnly);
$employee = $employee ?? [];
$navActive = $navActive ?? 'payments';
$pageTitle = $pageTitle ?? 'Payments';
$pageHeading = $pageHeading ?? 'Payments';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($readOnly): ?>
  <div class="alert alert-info">Read-only: payment records for reference. Customer payment simulation happens on the storefront after checkout.</div>
<?php endif; ?>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped mb-0 align-middle">
    <thead>
      <tr>
        <th>Pay ID</th>
        <th>Order</th>
        <th>Invoice</th>
        <th>Method</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Paid at</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($payments as $p): ?>
        <tr>
          <td><?= htmlspecialchars((string) $p['Pay_Id']) ?></td>
          <td><?= htmlspecialchars((string) $p['Pay_OrderID']) ?></td>
          <td><?= htmlspecialchars((string) ($p['Order_InvoiceNo'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) $p['Pay_Method']) ?></td>
          <td>PHP <?= number_format((float) $p['Pay_Amount'], 2) ?></td>
          <td><?= htmlspecialchars((string) $p['Pay_Status']) ?></td>
          <td><small><?= htmlspecialchars((string) $p['Pay_PaidAt']) ?></small></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
