<?php
$payments = $payments ?? [];
$flash = $flash ?? null;
$canConfirm = !empty($canConfirm);
$employee = $employee ?? [];
$isSalesRepresentative = strtolower((string) ($employee['role'] ?? '')) === 'sales representative';
$navActive = $navActive ?? 'payments';
$pageTitle = $pageTitle ?? 'Payments';
$pageHeading = $pageHeading ?? 'Payments';
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
                <?php
                  $canConfirmCashless = $p['Pay_Status'] === 'Pending' && $p['Pay_Method'] !== 'COD' && $p['Order_Status'] === 'Confirmed';
                  $canConfirmCod = $isSalesRepresentative && $p['Pay_Status'] === 'Pending' && $p['Pay_Method'] === 'COD' && $p['Order_Status'] === 'Completed';
                ?>
                <?php if ($canConfirmCashless || $canConfirmCod): ?>
                  <form method="post" action="<?= BASE_URL ?>/?r=payment/payment/confirm">
                    <input type="hidden" name="pay_id" value="<?= htmlspecialchars((string) $p['Pay_Id']) ?>">
                    <button class="btn btn-sm btn-dark" type="submit"><?= $canConfirmCod ? 'Confirm COD Payment' : 'Confirm Payment' ?></button>
                  </form>
                <?php elseif ($p['Pay_Status'] === 'Pending' && $p['Pay_Method'] === 'COD'): ?>
                  <span class="text-muted small">Awaiting fulfillment</span>
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
</body>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
