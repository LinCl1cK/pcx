<?php
$categories = $categories ?? [];
$currentUser = $_SESSION['user'] ?? null;
?>

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