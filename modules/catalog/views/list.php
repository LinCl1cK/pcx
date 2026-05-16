<?php
$products = $products ?? [];
$search = $search ?? '';
$category = $category ?? '';
$categories = $categories ?? [];
$currentUser = $_SESSION['user'] ?? null;
$wishlistIds = $wishlistIds ?? [];
$cartQuantities = $cartQuantities ?? [];
$flash = $flash ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - PCX Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</head>
<body>
  <?php
    // Fix for app/core/header.php
    $basePath = dirname(__DIR__, 2); // This gets C:\xampp\htdocs\pcx
    require_once $basePath . '/app/views/layouts/customer_header.php';
  ?>
  <?php if ($flash): ?>
    <div class="container mt-3">
      <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?> mb-0"><?= htmlspecialchars((string) $flash['message']) ?></div>
    </div>
  <?php endif; ?>

  <div class="container py-4">
    <form method="get" action="<?= BASE_URL ?>" class="row g-2 mb-4">
      <input type="hidden" name="r" value="catalog/product/list">
      <div class="col-md-5">
        <input type="search" class="form-control" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by product or brand">
      </div>
      <div class="col-md-5">
        <select class="form-select" name="category">
          <option value="">All categories</option>
          <?php foreach ($categories as $cat): ?>
            <?php $selected = ($category === (string) $cat['id'] || $category === (string) $cat['name']) ? 'selected' : ''; ?>
            <option value="<?= htmlspecialchars((string) $cat['id']) ?>" <?= $selected ?>>
              <?= htmlspecialchars((string) $cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-dark" type="submit">Search</button>
      </div>
    </form>

    <div class="row">
      <?php foreach ($products as $p): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm product-card">
            <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($p['Prod_Image']) ?>" class="card-img-top fixed-img" alt="<?= htmlspecialchars($p['Prod_Name']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($p['Prod_Name']) ?></h5>
              <p class="mb-1 text-muted"><?= htmlspecialchars($p['Prod_Brand']) ?></p>
              <p class="text-muted mb-3">PHP <?= number_format((float) $p['Prod_Price'], 2) ?></p>
              <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/?r=catalog/product/detail&id=<?= urlencode($p['Prod_Id']) ?>" class="btn btn-outline-dark btn-sm">View</a>
                <form method="post" action="<?= BASE_URL ?>/?r=wishlist/wishlist/add" class="d-inline">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $p['Prod_Id']) ?>">
                  <input type="hidden" name="redirect" value="catalog/product/list">
                  <button class="btn btn-sm <?= in_array($p['Prod_Id'], $wishlistIds, true) ? 'btn-danger' : 'btn-outline-danger' ?>" type="submit">
                    <?= in_array($p['Prod_Id'], $wishlistIds, true) ? 'Wishlisted' : 'Wishlist' ?>
                  </button>
                </form>
              </div>
              <form method="post" action="<?= BASE_URL ?>/?r=cart/cart/add" class="mt-2 d-flex gap-2">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $p['Prod_Id']) ?>">
                <input type="hidden" name="redirect" value="catalog/product/list">
                <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm" style="max-width: 80px;">
                <button class="btn btn-sm <?= isset($cartQuantities[$p['Prod_Id']]) ? 'btn-success' : 'btn-dark' ?>" type="submit">
                  <?= isset($cartQuantities[$p['Prod_Id']]) ? 'In Cart (' . (int) $cartQuantities[$p['Prod_Id']] . ')' : 'Add to Cart' ?>
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($products)): ?>
        <div class="col-12">
          <div class="alert alert-info">No products matched your filters.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php
  // Fix for app/core/footer.php
  $basePath = dirname(__DIR__, 2);
  require_once $basePath . '/app/views/layouts/customer_footer.php';
?>