<?php
$currentUser = $_SESSION['user'] ?? null;
$currentEmployee = $_SESSION['employee'] ?? null;
$categories = $categories ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'PCX Store') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</head>
<body class="pcx-body">
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-2 sticky-top pcx-header">
    <div class="container-fluid px-4 px-xl-5 py-2">
      <a class="navbar-brand" href="<?= BASE_URL ?>/?r=catalog/product/home">
        <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="PC Express" width="220">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <form class="d-flex flex-grow-1 me-3 justify-content-center" action="<?= BASE_URL ?>" method="get">
          <input type="hidden" name="r" value="catalog/product/list">
          <div class="input-group pcx-search-bar">
            <select class="form-select w-auto category-dropdown" name="category">
              <option value="">All Categories</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>">
                  <?= htmlspecialchars($category['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <input class="form-control" type="search" name="q" placeholder="Search for products">

            <button class="btn btn-outline-primary px-3" type="submit">
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
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=order/order/track">Order Tracking</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=service/customerTicket/request">Service Request</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=wishlist/wishlist/view">Wishlist</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=cart/cart/view">Cart</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=auth/auth/logout">Logout</a></li>
              </ul>
            </div>
          <?php elseif ($currentEmployee): ?>
            <a class="btn btn-outline-secondary me-2" href="<?= BASE_URL ?>/?r=admin/admin/dashboard">Staff Portal</a>
            <a class="btn btn-outline-danger" href="<?= BASE_URL ?>/?r=auth/auth/employeeLogout">Staff Logout</a>
          <?php else: ?>
            <button class="btn btn-outline-secondary position-relative me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</button>
            <button class="btn btn-dark position-relative me-2" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
          <?php endif; ?>
          <?php if (!$currentEmployee): ?>
          <a href="<?= BASE_URL ?>/?r=wishlist/wishlist/view" class="btn btn-outline-secondary position-relative me-2">
            <i class="bi bi-heart" style="font-size: 1.6rem;"></i>
          </a>
          <a href="<?= BASE_URL ?>/?r=cart/cart/view" class="btn btn-outline-secondary position-relative">
            <i class="bi bi-cart" style="font-size: 1.6rem;"></i>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
  <?php $flash = $flash ?? null; if ($flash): ?>
    <div class="container mt-3">
      <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?> mb-0"><?= htmlspecialchars((string) $flash['message']) ?></div>
    </div>
  <?php endif; ?>
