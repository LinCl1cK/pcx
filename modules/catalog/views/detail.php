<?php
$product = $product ?? null;
$categories = $categories ?? [];
$currentUser = $_SESSION['user'] ?? null;
$wishlistIds = $wishlistIds ?? [];
$cartQuantities = $cartQuantities ?? [];
$flash = $flash ?? null;
?>
<?php
// Fix for app/core/header.php
$basePath = dirname(__DIR__, 2); // This gets C:\xampp\htdocs\pcx
require_once $basePath . '/app/views/layouts/customer_header.php';
?>

<div class="container py-4">
  <a href="<?= BASE_URL ?>/?r=catalog/product/list" class="btn btn-outline-secondary mb-3">Back to Products</a>
  <?php if (!$product): ?>
    <div class="alert alert-warning">Product not found.</div>
  <?php else: ?>
    <?php $availableStock = (int) ($product['available_stock'] ?? 0); ?>
    <div class="row g-4">
      <div class="col-md-5">
        <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($product['Prod_Image']) ?>" class="img-fluid border rounded" alt="<?= htmlspecialchars($product['Prod_Name']) ?>">
      </div>
      <div class="col-md-7">
        <h2><?= htmlspecialchars($product['Prod_Name']) ?></h2>
        <p class="text-muted mb-2"><?= htmlspecialchars($product['Prod_Brand']) ?></p>
        <h4 class="mb-3">PHP <?= number_format((float) $product['Prod_Price'], 2) ?></h4>
        <p><?= nl2br(htmlspecialchars((string) ($product['Prod_Description'] ?? 'No description available.'))) ?></p>
        <p class="mb-1"><strong>Warranty:</strong> <?= htmlspecialchars((string) ($product['Prod_Warranty'] ?? 'N/A')) ?> months</p>
        <p class="mb-3"><strong>Status:</strong> <?= htmlspecialchars((string) ($product['Prod_Status'] ?? 'Unknown')) ?></p>
        <?php if ($availableStock <= 0): ?>
          <div class="alert alert-secondary py-2">Out of stock</div>
        <?php else: ?>
          <p class="text-muted small mb-3"><?= $availableStock ?> in stock</p>
        <?php endif; ?>
        <div class="d-flex gap-2">
          <form method="post" action="<?= BASE_URL ?>/?r=wishlist/wishlist/add">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $product['Prod_Id']) ?>">
            <input type="hidden" name="redirect" value="catalog/product/detail">
            <button class="btn <?= in_array($product['Prod_Id'], $wishlistIds, true) ? 'btn-danger' : 'btn-outline-danger' ?>">
              <?= in_array($product['Prod_Id'], $wishlistIds, true) ? 'Wishlisted' : 'Add to Wishlist' ?>
            </button>
          </form>
          <form method="post" action="<?= BASE_URL ?>/?r=cart/cart/add" class="d-flex gap-2">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $product['Prod_Id']) ?>">
            <input type="hidden" name="redirect" value="catalog/product/detail">
            <input type="number" name="quantity" value="1" min="1" max="<?= max(1, $availableStock) ?>" class="form-control" style="max-width:90px;" <?= $availableStock <= 0 ? 'disabled' : '' ?>>
            <button class="btn <?= $availableStock <= 0 ? 'btn-secondary' : (isset($cartQuantities[$product['Prod_Id']]) ? 'btn-success' : 'btn-dark') ?>" <?= $availableStock <= 0 ? 'disabled' : '' ?>>
              <?= $availableStock <= 0 ? 'Out of Stock' : (isset($cartQuantities[$product['Prod_Id']]) ? 'In Cart (' . (int) $cartQuantities[$product['Prod_Id']] . ')' : 'Add to Cart') ?>
            </button>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php
// Ensure the multiplier matches your actual folder layout depth!
$basePath = dirname(__DIR__, 3);
require_once $basePath . '/app/views/layouts/customer_footer.php';
?>