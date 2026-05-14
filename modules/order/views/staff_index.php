<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$navActive = $navActive ?? 'orders';
$pageTitle = $pageTitle ?? 'Orders';
$pageHeading = $pageHeading ?? 'Orders';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
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
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
