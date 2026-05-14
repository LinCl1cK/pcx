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
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-2">
    <div class="container-fluid px-5 py-2">
      <a class="navbar-brand" href="<?= BASE_URL ?>/?r=catalog/product/home">
        <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="PC Express" width="220">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <form class="d-flex flex-grow-1 me-3 justify-content-center" action="<?= BASE_URL ?>" method="get">
          <input type="hidden" name="r" value="catalog/product/list">
          <div class="input-group" style="width: 700px; height: 58px;">
            <select class="form-select w-auto category-dropdown" name="category" style="min-width:150px; max-width: 30px;">
              <option value="">All Categories</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>">
                  <?= htmlspecialchars($category['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <input class="form-control" type="search" name="q" placeholder="Search for products">

            <button class="btn btn-outline-primary" style="width: 58px;" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>

        <div class="d-flex align-items-center justify-content-end">
          <?php if ($currentUser): ?>
            <div class="dropdown me-2">
              <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <?= htmlspecialchars((string) ($currentUser['name'] ?? 'My Account')) ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=auth/auth/account">My Account</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=wishlist/wishlist/view">Wishlist</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=cart/cart/view">Cart</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=auth/auth/logout">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <button class="btn btn-outline-secondary position-relative me-2" data-bs-toggle="modal" data-bs-target="#authModal">
              <i class="bi bi-person" style="font-size: 1.6rem;"></i>
            </button>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/?r=wishlist/wishlist/view" class="btn btn-outline-secondary position-relative me-2">
            <i class="bi bi-heart" style="font-size: 1.6rem;"></i>
          </a>
          <a href="<?= BASE_URL ?>/?r=cart/cart/view" class="btn btn-outline-secondary position-relative">
            <i class="bi bi-cart" style="font-size: 1.6rem;"></i>
          </a>
        </div>
      </div>
    </div>
  </nav>
  <?php if ($flash): ?>
    <div class="container mt-3">
      <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?> mb-0"><?= htmlspecialchars((string) $flash['message']) ?></div>
    </div>
  <?php endif; ?>

  <div class="container-fluid mt-3">
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
                  <button class="btn btn-sm <?= isset($cartQuantities[$product['Prod_Id'] ?? '']) ? 'btn-success' : 'btn-dark' ?>">Cart</button>
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
                  <button class="btn btn-sm <?= isset($cartQuantities[$product['Prod_Id'] ?? '']) ? 'btn-success' : 'btn-dark' ?>">Cart</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Auth Modal (Right-aligned) -->
  <div class="modal fade" id="authModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen-sm-down" style="margin-left: auto;">
      <div class="modal-content">
        <div class="modal-header border-0">
          <ul class="nav nav-underline ms-auto" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active fw-bold" id="loginTab" data-bs-toggle="tab" data-bs-target="#loginContent" type="button" role="tab">LOGIN</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link fw-bold" id="registerTab" data-bs-toggle="tab" data-bs-target="#registerContent" type="button" role="tab">REGISTER</button>
            </li>
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="tab-content">
            <!-- Login Tab -->
            <div class="tab-pane fade show active" id="loginContent" role="tabpanel">
              <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/login" id="loginForm">
                <input type="hidden" name="next" value="catalog/product/home">
                <div class="mb-3">
                  <label class="form-label">Email or Employee Username *</label>
                  <input type="text" name="login_id" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password *</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-dark btn-lg">Sign in</button>
                </div>
                <div class="mt-3 text-center">
                  <a href="#" class="small text-decoration-none">Forgot your password?</a>
                </div>
              </form>
            </div>
            <!-- Register Tab -->
            <div class="tab-pane fade" id="registerContent" role="tabpanel">
              <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/register" id="registerForm">
                <div class="row">
                  <div class="col-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="fname" class="form-control" required>
                  </div>
                  <div class="col-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="lname" class="form-control" required>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Email *</label>
                  <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password *</label>
                  <input type="password" name="password" class="form-control" minlength="8" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Confirm Password *</label>
                  <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-dark btn-lg">Register</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
