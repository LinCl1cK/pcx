<?php
$products = $products ?? [];
$newArrivals = $newArrivals ?? [];
$categories = $categories ?? [];
$promotions = $promotions ?? [];
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
  <title>PCX Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</head>
<body>
  <?php
    // Fix for app/core/header.php
    $basePath = dirname(__DIR__, 3); // This gets C:\xampp\htdocs\pcx
    require_once $basePath . '/app/views/layouts/customer_header.php';
  ?>

  <div class="container-fluid mt-3">
    <?php if ($flash): ?>
      <div class="container">
        <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
      </div>
    <?php endif; ?>
    <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php if (!empty($promotions)): ?>
          <?php foreach ($promotions as $index => $promo): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <img src="<?= BASE_URL ?>/assets/images/promos/<?= htmlspecialchars($promo['banner']) ?>" class="d-block w-100 px-2" alt="<?= htmlspecialchars($promo['title']) ?>">
              <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                <h5><?= htmlspecialchars($promo['title']) ?></h5>
                <p><?= htmlspecialchars($promo['description']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="carousel-item active">
            <div class="p-5 bg-light text-center border rounded mx-2">
              <h5>No active promotions yet</h5>
              <p class="mb-0 text-muted">Add records in the Promotion table to populate this section.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </div>

  <div class="container-fluid px-3">
    <div class="container mt-5">
      <h3 class="mb-4 text-center">Shop by Category</h3>
      <div class="row">
        <?php
          $categoryImages = [
            ['name' => 'PC Components', 'image' => 'Components-400x400.webp'],
            ['name' => 'Desktop PCs', 'image' => 'WebDesktopPC-400x400.webp'],
            ['name' => 'Laptops', 'image' => 'WebGamingLaptops-400x400.webp'],
            ['name' => 'Monitors', 'image' => 'Monitors-400x400.webp'],
            ['name' => 'Gaming Devices & Accessories', 'image' => 'WebGamingDevices-400x400.webp'],
            ['name' => 'Peripherals', 'image' => 'WebPeripherals-400x400.webp'],
          ];
        ?>

        <?php foreach ($categoryImages as $categoryItem): ?>
          <div class="col-md-4 mb-4">
            <a href="<?= BASE_URL ?>/?r=catalog/product/list&category=<?= urlencode($categoryItem['name']) ?>" class="text-decoration-none">
              <img src="<?= BASE_URL ?>/assets/images/categories/<?= htmlspecialchars($categoryItem['image']) ?>" class="img-fluid shadow-sm rounded" alt="<?= htmlspecialchars($categoryItem['name']) ?>">
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="container mt-5 mb-5">
    <h3 class="mb-4 text-center">Featured Products</h3>
    <div class="row">
      <?php foreach ($products as $product): ?>
        <?php $availableStock = (int) ($product['available_stock'] ?? 0); ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm product-card">
            <div class="position-relative">
              <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($product['Prod_Image'] ?? '') ?>" alt="<?= htmlspecialchars($product['Prod_Name'] ?? '') ?>" class="card-img-top fixed-img">
              <div class="overlay d-flex flex-column align-items-center justify-content-center">
                <a href="<?= BASE_URL ?>/?r=catalog/product/detail&id=<?= htmlspecialchars((string) ($product['Prod_Id'] ?? '')) ?>" class="btn btn-primary btn-sm mb-2">View More</a>
              </div>
            </div>
            <div class="card-body text-center">
              <p class="mb-1 text-uppercase text-muted"><?= htmlspecialchars($product['Prod_Brand'] ?? '') ?></p>
              <h6 class="card-title mb-2 text-truncate"><?= htmlspecialchars($product['Prod_Name'] ?? '') ?></h6>
              <p class="text-muted mb-0">PHP <?= isset($product['Prod_Price']) ? number_format((float) $product['Prod_Price'], 2) : '0.00' ?></p>
              <?php if ($availableStock <= 0): ?>
                <span class="badge text-bg-secondary mt-2">Out of stock</span>
              <?php endif; ?>
              <div class="d-flex gap-1 justify-content-center mt-2">
                <form method="post" action="<?= BASE_URL ?>/?r=wishlist/wishlist/add">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) ($product['Prod_Id'] ?? '')) ?>">
                  <input type="hidden" name="redirect" value="catalog/product/home">
                  <button class="btn btn-sm <?= in_array(($product['Prod_Id'] ?? ''), $wishlistIds, true) ? 'btn-danger' : 'btn-outline-danger' ?>">Wish</button>
                </form>
                <form method="post" action="<?= BASE_URL ?>/?r=cart/cart/add">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) ($product['Prod_Id'] ?? '')) ?>">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="redirect" value="catalog/product/home">
                  <button class="btn btn-sm <?= $availableStock <= 0 ? 'btn-secondary' : (isset($cartQuantities[$product['Prod_Id'] ?? '']) ? 'btn-success' : 'btn-dark') ?>" <?= $availableStock <= 0 ? 'disabled' : '' ?>>
                    <?= $availableStock <= 0 ? 'Out' : 'Cart' ?>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="container mt-2 mb-5">
    <h3 class="mb-4 text-center">New Arrivals</h3>
    <div class="row">
      <?php foreach ($newArrivals as $product): ?>
        <?php $availableStock = (int) ($product['available_stock'] ?? 0); ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm product-card">
            <div class="position-relative">
              <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($product['Prod_Image'] ?? '') ?>" alt="<?= htmlspecialchars($product['Prod_Name'] ?? '') ?>" class="card-img-top fixed-img">
              <div class="overlay d-flex flex-column align-items-center justify-content-center">
                <a href="<?= BASE_URL ?>/?r=catalog/product/detail&id=<?= htmlspecialchars((string) ($product['Prod_Id'] ?? '')) ?>" class="btn btn-primary btn-sm mb-2">View More</a>
              </div>
            </div>
            <div class="card-body text-center">
              <p class="mb-1 text-uppercase text-muted"><?= htmlspecialchars($product['Prod_Brand'] ?? '') ?></p>
              <h6 class="card-title mb-2 text-truncate"><?= htmlspecialchars($product['Prod_Name'] ?? '') ?></h6>
              <p class="text-muted mb-0">PHP <?= isset($product['Prod_Price']) ? number_format((float) $product['Prod_Price'], 2) : '0.00' ?></p>
              <?php if ($availableStock <= 0): ?>
                <span class="badge text-bg-secondary mt-2">Out of stock</span>
              <?php endif; ?>
              <div class="d-flex gap-1 justify-content-center mt-2">
                <form method="post" action="<?= BASE_URL ?>/?r=wishlist/wishlist/add">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) ($product['Prod_Id'] ?? '')) ?>">
                  <input type="hidden" name="redirect" value="catalog/product/home">
                  <button class="btn btn-sm <?= in_array(($product['Prod_Id'] ?? ''), $wishlistIds, true) ? 'btn-danger' : 'btn-outline-danger' ?>">Wish</button>
                </form>
                <form method="post" action="<?= BASE_URL ?>/?r=cart/cart/add">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars((string) ($product['Prod_Id'] ?? '')) ?>">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="redirect" value="catalog/product/home">
                  <button class="btn btn-sm <?= $availableStock <= 0 ? 'btn-secondary' : (isset($cartQuantities[$product['Prod_Id'] ?? '']) ? 'btn-success' : 'btn-dark') ?>" <?= $availableStock <= 0 ? 'disabled' : '' ?>>
                    <?= $availableStock <= 0 ? 'Out' : 'Cart' ?>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php
  // Fix for app/core/footer.php
  $basePath = dirname(__DIR__, 3);
  require_once $basePath . '/app/views/layouts/customer_footer.php';
?>
