<?php
$payments = $payments ?? [];
$flash = $flash ?? null;
$canConfirm = !empty($canConfirm);
$employee = $employee ?? [];
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
  <table class="table table-striped mb-0 align-middle">
    <thead>
      <tr>
        <th>Pay ID</th>
        <th>Order</th>
        <th>Customer</th>
        <th>Method</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Gateway Ref</th>
        <th>Date</th>
        <?php if ($canConfirm): ?><th>Action</th><?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($payments as $p): ?>
        <tr>
          <td><?= htmlspecialchars((string) $p['Pay_Id']) ?></td>
          <td><?= htmlspecialchars((string) $p['Pay_OrderID']) ?></td>
          <td><?= htmlspecialchars((string) ($p['Pay_CusId'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) $p['Pay_Method']) ?></td>
          <td>PHP <?= number_format((float) $p['Pay_Amount'], 2) ?></td>
          <td><?= htmlspecialchars((string) $p['Pay_Status']) ?></td>
          <td><?= htmlspecialchars((string) ($p['Pay_GatewayRef'] ?? '-')) ?></td>
          <td><small><?= htmlspecialchars((string) $p['Pay_PaidAt']) ?></small></td>
          <?php if ($canConfirm): ?>
            <td>
              <?php if ($p['Pay_Status'] === 'Pending'): ?>
                <form method="post" action="<?= BASE_URL ?>/?r=payment/payment/confirm">
                  <input type="hidden" name="pay_id" value="<?= htmlspecialchars((string) $p['Pay_Id']) ?>">
                  <button class="btn btn-sm btn-dark" type="submit">Confirm Payment</button>
                </form>
              <?php else: ?>
                <span class="text-muted small">No action</span>
              <?php endif; ?>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
