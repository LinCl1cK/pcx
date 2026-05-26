<?php
$user = $user ?? [];
$customer = $customer ?? [];
$items = $items ?? [];
$subtotal = (float) ($subtotal ?? 0);
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Shopping Cart - PCX Store';
$flash = $flash ?? null;
$basePath = dirname(__DIR__, 3); // This gets C:\xampp\htdocs\pcx
require_once $basePath . '/app/views/layouts/customer_header.php';
?>
<div class="container py-5">
  <h1 class="h3 mb-3">Shopping Cart</h1>
  <p class="text-muted mb-4">Signed in as <?= htmlspecialchars((string) ($user['email'] ?? '')) ?></p>

  <?php if (empty($items)): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h2 class="h5">Your cart is empty.</h2>
        <p class="mb-0">Before proceed to checkout you must add some products to your shopping cart.</p>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/?r=catalog/product/list" class="btn btn-dark mt-3">Browse Products</a>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle bg-white shadow-sm pcx-checkout-card">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <?php $availableStock = (int) ($item['available_stock'] ?? 0); ?>
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars((string) $item['Prod_Image']) ?>" width="60" alt="<?= htmlspecialchars((string) $item['Prod_Name']) ?>">
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars((string) $item['Prod_Name']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars((string) $item['Prod_Brand']) ?></small>
                    <?php if ($availableStock <= 0): ?>
                      <div><span class="badge text-bg-secondary">Out of stock</span></div>
                    <?php elseif ((int) $item['quantity'] > $availableStock): ?>
                      <div><span class="badge text-bg-warning">Only <?= $availableStock ?> left</span></div>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
              <td>PHP <?= number_format((float) $item['unit_price'], 2) ?></td>
              <td>
                <form method="post" action="<?= BASE_URL ?>/?r=cart/cart/update" class="d-flex gap-2">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $item['Prod_Id']) ?>">
                  <input type="number" min="1" max="<?= max(1, $availableStock) ?>" name="quantity" value="<?= (int) $item['quantity'] ?>" class="form-control form-control-sm" style="max-width:80px;" <?= $availableStock <= 0 ? 'disabled' : '' ?>>
                  <button class="btn btn-sm btn-outline-dark" type="submit" <?= $availableStock <= 0 ? 'disabled' : '' ?>>Update</button>
                </form>
              </td>
              <td>PHP <?= number_format((float) $item['line_total'], 2) ?></td>
              <td>
                <form method="post" action="<?= BASE_URL ?>/?r=cart/cart/remove">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $item['Prod_Id']) ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="card border-0 shadow-sm pcx-checkout-card">
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-lg-5">
            <div class="pcx-summary-row"><span>Subtotal</span><strong>PHP <?= number_format($subtotal, 2) ?></strong></div>
            <div class="pcx-summary-row"><span>VAT (12%)</span><span>PHP <?= number_format($subtotal * 0.12, 2) ?></span></div>
            <div class="pcx-summary-row mb-0"><span>Total Estimate</span><strong>PHP <?= number_format($subtotal * 1.12, 2) ?></strong></div>
          </div>
          <form method="get" action="<?= BASE_URL ?>" class="col-lg-7">
            <input type="hidden" name="r" value="order/order/checkout">
            <div class="row g-2">
              <div class="col-md-12">
                <?php
                $hasStockIssue = false;
                foreach ($items as $item) {
                  $stock = (int) ($item['available_stock'] ?? 0);
                  if ($stock <= 0 || (int) $item['quantity'] > $stock) {
                    $hasStockIssue = true;
                    break;
                  }
                }
                ?>
                <button class="btn btn-dark w-100" type="submit" <?= $hasStockIssue ? 'disabled' : '' ?>>Checkout</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>