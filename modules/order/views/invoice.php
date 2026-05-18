<?php
$order = $order ?? null;
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Invoice - PCX';
require_once __DIR__ . '/../../../app/core/header.php';
?>
<div class="container py-4">
  <?php if (!$order): ?>
    <div class="alert alert-warning">Invoice not found.</div>
  <?php else: ?>
    <div class="card shadow-sm border-0 pcx-invoice-card">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
          <div>
            <h1 class="h4 mb-1">Invoice <?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></h1>
            <p class="text-muted mb-0">Order <?= htmlspecialchars((string) $order['Order_Id']) ?></p>
          </div>
          <span class="badge text-bg-dark px-3 py-2"><?= htmlspecialchars((string) $order['Order_Status']) ?></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Line Total</th></tr></thead>
            <tbody>
              <?php foreach (($order['items'] ?? []) as $item): ?>
              <tr>
                <td><?= htmlspecialchars((string) $item['Prod_Name']) ?></td>
                <td><?= (int) $item['Item_Quantity'] ?></td>
                <td>PHP <?= number_format((float) $item['Item_Price'], 2) ?></td>
                <td>PHP <?= number_format((float) $item['Item_Price'] * (int) $item['Item_Quantity'], 2) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="text-end border-top pt-3">
          <div class="pcx-summary-row"><span>VAT</span><span>PHP <?= number_format((float) $order['Order_VAT'], 2) ?></span></div>
          <h5 class="mb-3">Total: PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></h5>
        </div>
        <div class="d-flex gap-2">
          <a href="<?= BASE_URL ?>/?r=payment/payment/pay&id=<?= urlencode((string) $order['Order_Id']) ?>" class="btn btn-dark">Submit Payment</a>
          <a href="<?= BASE_URL ?>/?r=order/order/track" class="btn btn-outline-dark">Track Order</a>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>
