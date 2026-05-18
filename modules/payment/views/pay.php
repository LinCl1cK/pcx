<?php
$order = $order ?? null;
$error = $error ?? null;
$requiresId = !empty($requiresId);
$hasId = !empty($hasId);
$categories = $categories ?? [];
$pageTitle = 'Payment Simulation - PCX';
require_once __DIR__ . '/../../../app/core/header.php';
?>
<div class="container py-4">
  <h1 class="h4 mb-3">Payment Simulation</h1>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars((string) $error) ?></div><?php endif; ?>
  <?php if ($order): ?>
    <?php if ($requiresId && !$hasId): ?>
      <div class="alert alert-warning">This high-value order needs a valid ID attachment before payment can be confirmed. Return to checkout or account support to submit an ID.</div>
    <?php endif; ?>
    <div class="card border-0 shadow-sm pcx-checkout-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <div><strong>Order:</strong> <?= htmlspecialchars((string) $order['Order_Id']) ?></div>
            <div><strong>Invoice:</strong> <?= htmlspecialchars((string) ($order['Order_InvoiceNo'] ?? '-')) ?></div>
          </div>
          <span class="badge text-bg-secondary"><?= htmlspecialchars((string) $order['Order_Status']) ?></span>
        </div>
        <div class="pcx-summary-row"><span>Total Amount</span><strong>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></strong></div>
        <form method="post" action="<?= BASE_URL ?>/?r=payment/payment/pay">
          <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label">Method</label>
              <select name="method" class="form-select">
                <option value="COD">COD</option>
                <option value="GCash">GCash</option>
                <option value="Maya">Maya</option>
                <option value="Bank Transfer">Bank Transfer</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Region</label>
              <select name="region" class="form-select">
                <option value="Metro Manila">Metro Manila</option>
                <option value="Provincial">Provincial</option>
              </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button class="btn btn-dark w-100" type="submit" <?= ($requiresId && !$hasId) ? 'disabled' : '' ?>>Confirm Payment</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>
