<?php
$currentUser = $_SESSION['user'] ?? null;
$currentEmployee = $_SESSION['employee'] ?? null;
$categories = $categories ?? [];
$staffPortalRoute = 'admin/admin/dashboard';
if ($currentEmployee) {
  $employeeRole = strtolower(trim((string) ($currentEmployee['role'] ?? '')));
  if ($employeeRole === 'sales representative') {
    $staffPortalRoute = 'sales/sales/dashboard';
  } elseif ($employeeRole === 'technician') {
    $staffPortalRoute = 'technician/technician/dashboard';
  }
}
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
          
          <div class="input-group pcx-search-bar" style="max-width: 700px; height: 50px;">
            <select class="form-select w-auto category-dropdown" name="category" style="max-width: 150px;">
              <option value="">All Categories</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['id']) ?>">
                  <?= htmlspecialchars($cat['name']) ?>
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
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=auth/auth/logout">Logout</a></li>
              </ul>
            </div>
          <?php elseif ($currentEmployee): ?>
            <a class="btn btn-outline-secondary me-2" href="<?= BASE_URL ?>/?r=<?= htmlspecialchars($staffPortalRoute) ?>">Staff Portal</a>
            <a class="btn btn-outline-danger" href="<?= BASE_URL ?>/?r=auth/auth/employeeLogout">Staff Logout</a>
          <?php else: ?>
            <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#authModal">
              <i class="bi bi-person" style="font-size: 1.5rem;"></i>
            </button>
          <?php endif; ?>

          <?php if (!$currentEmployee): ?>
            <?php if ($currentUser): ?>
              <a href="<?= BASE_URL ?>/?r=wishlist/wishlist/view" class="btn btn-outline-secondary position-relative me-2">
                <i class="bi bi-heart" style="font-size: 1.5rem;"></i>
              </a>
              <a href="<?= BASE_URL ?>/?r=cart/cart/view" class="btn btn-outline-secondary position-relative">
                <i class="bi bi-cart" style="font-size: 1.5rem;"></i>
              </a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
  
  <?php 
    // Pull the flash data reliably from either local view assignment or direct session flash variables
    $activeFlash = $flash ?? ($_SESSION['flash'] ?? null); 
    if ($activeFlash): 
        // Clear it immediately so it doesn't repeat on next page click
        if (isset($_SESSION['flash'])) { unset($_SESSION['flash']); }
  ?>
    <div class="position-fixed top-1 end-0 p-1" style="z-index: 1100;">
      <div id="flashToast" class="toast align-items-center text-white bg-<?= $activeFlash['type'] === 'danger' ? 'danger' : ($activeFlash['type'] === 'success' ? 'success' : 'dark') ?> border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
        <div class="d-flex">
          <div class="toast-body d-flex align-items-center gap-2">
            <?php if ($activeFlash['type'] === 'success'): ?>
              <i class="bi bi-check-circle-fill fs-5"></i>
            <?php else: ?>
              <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <?php endif; ?>
            <div><?= htmlspecialchars($activeFlash['message']) ?></div>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>
    
    <script>
      // Force execution to wait until the window engine has fully mounted all third-party UI libraries
      window.addEventListener('load', function() {
        const toastEl = document.getElementById('flashToast');
        if (toastEl && typeof bootstrap !== 'undefined') {
          const bootstrapToast = new bootstrap.Toast(toastEl);
          bootstrapToast.show();
        } else {
          console.warn('Bootstrap runtime script delayed or toast element missing initialization boundaries.');
        }
      });
    </script>
  <?php endif; ?>
