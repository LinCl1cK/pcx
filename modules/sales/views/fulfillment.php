<?php
$orders = $orders ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'fulfillment';
$pageTitle = $pageTitle ?? 'Fulfillment';
$pageHeading = $pageHeading ?? 'Fulfillment queue';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>
<div class="alert alert-info">Administrators check fulfillment first. Sales representatives confirm COD payment after the order is completed.</div>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>Order</th><th>Invoice</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars((string) $order['Order_Id']) ?></td>
          <td><?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></td>
          <td><?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?></td>
          <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
          <td>
            <?= htmlspecialchars((string) ($order['Pay_Method'] ?? '-')) ?>
            <?php if (!empty($order['Pay_Status'])): ?>
              <span class="badge text-bg-secondary ms-1"><?= htmlspecialchars((string) $order['Pay_Status']) ?></span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars((string) $order['Order_Status']) ?></td>
          <td><small><?= htmlspecialchars((string) $order['Order_Date']) ?></small></td>
          <td>
            <?php if (($order['Order_Status'] ?? '') === 'Completed' && ($order['Pay_Method'] ?? '') === 'COD' && ($order['Pay_Status'] ?? '') === 'Pending'): ?>
              <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/confirmPayment">
                <input type="hidden" name="pay_id" value="<?= htmlspecialchars((string) $order['Pay_Id']) ?>">
                <input type="hidden" name="next" value="fulfillment">
                <button class="btn btn-sm btn-dark" type="submit">Confirm COD Payment</button>
              </form>
            <?php else: ?>
              <span class="text-muted small">Read-only</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
