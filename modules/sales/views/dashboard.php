<?php
$summary = $summary ?? [];
$pendingOrders = $pendingOrders ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Sales Dashboard';
$pageHeading = $pageHeading ?? 'Sales dashboard';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <?php foreach ([
    'Pending Orders' => $summary['pending_orders'] ?? 0,
    'Verified By Me' => $summary['verified_orders'] ?? 0,
    'Ready For Fulfillment' => $summary['paid_orders'] ?? 0,
    'Low Stock Items' => $summary['low_stock'] ?? 0,
  ] as $label => $value): ?>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small"><?= htmlspecialchars($label) ?></div>
          <div class="display-6 fw-semibold"><?= (int) $value ?></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
  <?php foreach ([
    ['Orders', 'sales/sales/orders'],
    ['Verification', 'sales/sales/verification'],
    ['Payments', 'sales/sales/payments'],
    ['Fulfillment', 'sales/sales/fulfillment'],
    ['Inventory', 'sales/sales/inventory'],
  ] as $link): ?>
    <div class="col-sm-6 col-xl">
      <a class="btn btn-dark w-100 py-3" href="<?= BASE_URL ?>/?r=<?= htmlspecialchars($link[1]) ?>"><?= htmlspecialchars($link[0]) ?></a>
    </div>
  <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h5 mb-0">Pending Orders</h2>
      <a class="btn btn-sm btn-outline-dark" href="<?= BASE_URL ?>/?r=sales/sales/orders">View all</a>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($pendingOrders as $order): ?>
            <tr>
              <td><?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></td>
              <td><?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?></td>
              <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
              <td><small><?= htmlspecialchars((string) $order['Order_Date']) ?></small></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($pendingOrders)): ?>
            <tr><td colspan="4" class="text-muted">No pending orders.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
