<?php
$payments = $payments ?? [];
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'payments';
$pageTitle = $pageTitle ?? 'Payments';
$pageHeading = $pageHeading ?? 'Payments';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<div class="alert alert-info">Read-only payment records linked to customer orders.</div>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>Payment</th><th>Invoice</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Paid At</th></tr></thead>
    <tbody>
      <?php foreach ($payments as $payment): ?>
        <tr>
          <td><?= htmlspecialchars((string) $payment['Pay_Id']) ?></td>
          <td><?= htmlspecialchars((string) $payment['Order_InvoiceNo']) ?></td>
          <td><?= htmlspecialchars(trim(($payment['Cus_Fname'] ?? '') . ' ' . ($payment['Cus_Lname'] ?? ''))) ?></td>
          <td><?= htmlspecialchars((string) $payment['Pay_Method']) ?></td>
          <td>PHP <?= number_format((float) $payment['Pay_Amount'], 2) ?></td>
          <td><?= htmlspecialchars((string) $payment['Pay_Status']) ?></td>
          <td><small><?= htmlspecialchars((string) $payment['Pay_PaidAt']) ?></small></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
