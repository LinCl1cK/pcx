<?php
$items = $items ?? [];
$customer = $customer ?? [];
$subtotal = (float) ($subtotal ?? 0);
$total = (float) ($total ?? 0);
$requiresId = !empty($requiresId);
$flash = $flash ?? null;
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Checkout - PCX Store';
require_once __DIR__ . '/../../../app/core/header.php';
?>
<div class="container py-4">
  <h1 class="h4 mb-3">Checkout</h1>
  <?php if ($flash): ?>
    <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
  <?php endif; ?>
  <?php if ($requiresId): ?>
    <div class="alert alert-warning">This is a high-value order. Please submit a valid government ID before payment verification.</div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="h6">Order Items</h2>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>Product</th><th>Qty</th><th>Total</th></tr></thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td><?= htmlspecialchars((string) $item['Prod_Name']) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td>PHP <?= number_format((float) $item['line_total'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="text-end">
            <div>Subtotal: PHP <?= number_format($subtotal, 2) ?></div>
            <div>VAT: PHP <?= number_format($subtotal * 0.12, 2) ?></div>
            <strong>Total: PHP <?= number_format($total, 2) ?></strong>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="h6">Delivery, Verification, and Payment</h2>
          <form method="post" action="<?= BASE_URL ?>/?r=payment/payment/checkout" enctype="multipart/form-data">
            <div class="mb-2">
              <label class="form-label">Shipping</label>
              <select name="shipping" class="form-select">
                <option value="Delivery">Delivery</option>
                <option value="Pickup">Pickup</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Contact Number</label>
              <input class="form-control" name="contact_no" maxlength="15" value="<?= htmlspecialchars((string) ($customer['Cus_ContactNo'] ?? '')) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label">Destination Address</label>
              <textarea class="form-control" name="destination_address" rows="3" required><?= htmlspecialchars((string) ($customer['Cus_Address'] ?? '')) ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Valid ID Attachment <?= $requiresId ? '(required)' : '(optional)' ?></label>
              <input class="form-control" type="file" name="id_attachment" accept="image/jpeg,image/png,image/webp,application/pdf" <?= ($requiresId && empty($customer['Cus_IdAttachment'])) ? 'required' : '' ?>>
              <div class="form-text">Accepted: JPG, PNG, WEBP, PDF. Maximum 5 MB.</div>
              <?php if (!empty($customer['Cus_IdAttachment'])): ?>
                <div class="small mt-1">Existing ID on file: <a href="<?= BASE_URL ?>/<?= htmlspecialchars((string) $customer['Cus_IdAttachment']) ?>" target="_blank">View attachment</a></div>
              <?php endif; ?>
            </div>
            <div class="border-top pt-3 mt-3">
              <label class="form-label d-block">Payment Option</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_option" id="pay_cod" value="COD" checked>
                <label class="form-check-label" for="pay_cod">COD</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_option" id="pay_cashless" value="Cashless">
                <label class="form-check-label" for="pay_cashless">Cashless simulation</label>
              </div>
              <div id="cod_fields" class="mb-2">
                <label class="form-label">COD Region</label>
                <select class="form-select" name="cod_region">
                  <option value="Metro Manila">Metro Manila - up to PHP 50,000</option>
                  <option value="Provincial">Provincial - up to PHP 30,000</option>
                </select>
              </div>
              <div id="cashless_fields" class="border rounded p-2 mb-2 d-none">
                <div class="mb-2">
                  <label class="form-label">Mock Method</label>
                  <select class="form-select" name="cashless_method">
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="GCash">GCash</option>
                    <option value="Maya">Maya</option>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="form-label">Transaction Reference</label>
                  <input class="form-control" name="gateway_ref" maxlength="100" placeholder="e.g. SIM-REF-2026-001">
                </div>
                <div>
                  <label class="form-label">Amount</label>
                  <input class="form-control" type="number" step="0.01" name="payment_amount" value="<?= htmlspecialchars(number_format($total, 2, '.', '')) ?>">
                </div>
                <div class="form-text">Do not enter PINs, CVV, card numbers, wallet passwords, or account credentials.</div>
              </div>
              <input type="hidden" name="payment_amount_expected" value="<?= htmlspecialchars(number_format($total, 2, '.', '')) ?>">
            </div>
            <button class="btn btn-dark w-100" type="submit">Place Order</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
window.addEventListener('load', function () {
  const cod = document.getElementById('pay_cod');
  const cashless = document.getElementById('pay_cashless');
  const codFields = document.getElementById('cod_fields');
  const cashlessFields = document.getElementById('cashless_fields');
  function syncPaymentFields() {
    const isCashless = cashless && cashless.checked;
    codFields.classList.toggle('d-none', isCashless);
    cashlessFields.classList.toggle('d-none', !isCashless);
  }
  cod && cod.addEventListener('change', syncPaymentFields);
  cashless && cashless.addEventListener('change', syncPaymentFields);
  syncPaymentFields();
});
</script>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>
