<?php
$order = $order ?? null;
$error = $error ?? null;
$requiresId = !empty($requiresId);
$hasId = !empty($hasId);
$categories = $categories ?? [];
$pageTitle = 'Payment Submission - PCX';
require_once __DIR__ . '/../../../app/core/header.php';
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
  <div class="container py-4">
    <h1 class="h4 mb-3">Payment Submission</h1>
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
            <input type="hidden" name="payment_amount" value="<?= htmlspecialchars(number_format((float) $order['Order_TotalAmount'], 2, '.', '')) ?>">
            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label">Payment Option</label>
                <select name="payment_option" class="form-select" id="payment_option">
                  <option value="COD">COD</option>
                  <option value="Cashless">Cashless simulation</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">COD Region</label>
                <select name="cod_region" class="form-select">
                  <option value="Metro Manila">Metro Manila</option>
                  <option value="Provincial">Provincial</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Gateway Reference</label>
                <input class="form-control" name="gateway_ref" maxlength="100" placeholder="Cashless only">
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-dark w-100" type="submit" <?= ($requiresId && !$hasId) ? 'disabled' : '' ?>>Submit Payment</button>
              </div>
              <div class="col-12">
                <div class="form-text">Cashless simulation stores only method, amount, status, reference, date, order, and customer. Do not enter PINs, CVV, card numbers, or wallet credentials.</div>
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>
</body>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>
