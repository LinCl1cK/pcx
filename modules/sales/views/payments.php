<?php
$payments = $payments ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'payments';
$pageTitle = $pageTitle ?? 'Payments';
$pageHeading = $pageHeading ?? 'Payments';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>
<div class="alert alert-info">Safe payment metadata only. No card numbers, wallet credentials, PINs, or CVV are collected or displayed.</div>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>Payment</th><th>Order</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Gateway Ref</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($payments as $payment): ?>
        <tr>
          <td><?= htmlspecialchars((string) $payment['Pay_Id']) ?></td>
          <td><?= htmlspecialchars((string) $payment['Pay_OrderID']) ?></td>
          <td><?= htmlspecialchars((string) ($payment['Pay_CusId'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) $payment['Pay_Method']) ?></td>
          <td>PHP <?= number_format((float) $payment['Pay_Amount'], 2) ?></td>
          <td><?= htmlspecialchars((string) $payment['Pay_Status']) ?></td>
          <td><?= htmlspecialchars((string) ($payment['Pay_GatewayRef'] ?? '-')) ?></td>
          <td><small><?= htmlspecialchars((string) $payment['Pay_PaidAt']) ?></small></td>
          <td>
            <?php if ($payment['Pay_Status'] === 'Pending'): ?>
              <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/confirmPayment">
                <input type="hidden" name="pay_id" value="<?= htmlspecialchars((string) $payment['Pay_Id']) ?>">
                <button class="btn btn-sm btn-dark" type="submit">Confirm Payment</button>
              </form>
            <?php else: ?>
              <span class="text-muted small">No action</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
