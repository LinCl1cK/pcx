<?php
$user = $user ?? [];
$items = $items ?? [];
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Wishlist - PCX Store';
$flash = $flash ?? null;
require_once __DIR__ . '/../../../app/core/header.php';
?>
  <div class="container py-5">
    <h1 class="h3 mb-3">Wishlist</h1>
    <p class="text-muted mb-4">Signed in as <?= htmlspecialchars((string) ($user['email'] ?? '')) ?></p>

    <?php if (empty($items)): ?>
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="h5">Wishlist is empty.</h2>
          <p class="mb-0">You don't have any products in the wishlist yet. You will find a lot of interesting products on our shop page.</p>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/?r=catalog/product/list" class="btn btn-dark mt-3">Browse Products</a>
    <?php else: ?>
      <div class="row">
        <?php foreach ($items as $item): ?>
          <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0">
              <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars((string) $item['Prod_Image']) ?>" class="card-img-top fixed-img" alt="<?= htmlspecialchars((string) $item['Prod_Name']) ?>">
              <div class="card-body">
                <h2 class="h6"><?= htmlspecialchars((string) $item['Prod_Name']) ?></h2>
                <p class="text-muted mb-2"><?= htmlspecialchars((string) $item['Prod_Brand']) ?></p>
                <p class="mb-2">PHP <?= number_format((float) $item['Prod_Price'], 2) ?></p>

                <div class="d-flex gap-2">
                  <form method="post" action="<?= BASE_URL ?>/?r=cart/cart/add" class="d-inline">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $item['Prod_Id']) ?>">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="redirect" value="wishlist/wishlist/view">
                    <button class="btn btn-dark btn-sm" type="submit">Add to Cart</button>
                  </form>

                  <form method="post" action="<?= BASE_URL ?>/?r=wishlist/wishlist/remove" class="d-inline">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $item['Prod_Id']) ?>">
                    <input type="hidden" name="redirect" value="wishlist/wishlist/view">
                    <button class="btn btn-outline-danger btn-sm" type="submit">Remove</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>

