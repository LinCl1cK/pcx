<?php
$product = $product ?? null;
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
  <title>Product Detail - PCX Store</title>
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
            <button class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#authModal">
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

  <div class="container py-4">
    <a href="<?= BASE_URL ?>/?r=catalog/product/list" class="btn btn-outline-secondary mb-3">Back to Products</a>
    <?php if (!$product): ?>
      <div class="alert alert-warning">Product not found.</div>
    <?php else: ?>
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
              <input type="number" name="quantity" value="1" min="1" class="form-control" style="max-width:90px;">
              <button class="btn <?= isset($cartQuantities[$product['Prod_Id']]) ? 'btn-success' : 'btn-dark' ?>">
                <?= isset($cartQuantities[$product['Prod_Id']]) ? 'In Cart (' . (int) $cartQuantities[$product['Prod_Id']] . ')' : 'Add to Cart' ?>
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>
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
                <input type="hidden" name="next" value="catalog/product/detail&id=<?= htmlspecialchars($_GET['id'] ?? '') ?>">
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
