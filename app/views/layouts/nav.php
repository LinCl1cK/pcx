<?php
/**
 * Employee area navigation. Expects:
 * - $employee (array from $_SESSION['employee'])
 * - $navActive (string key: dashboard|users|products|categories|verification|payments|fulfillment|service|orders|inventory)
 */
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? '';
$role = strtolower((string) ($employee['role'] ?? ''));
$isAdmin = $role === 'administrator';
$isSales = $role === 'sales representative';
$isTech = $role === 'technician';
$dashboardHref = BASE_URL . '/?r=admin/admin/dashboard';
if ($isSales) {
    $dashboardHref = BASE_URL . '/?r=sales/sales/dashboard';
} elseif ($isTech) {
    $dashboardHref = BASE_URL . '/?r=technician/technician/dashboard';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="<?= $dashboardHref ?>">PCX Staff</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#empNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="empNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 flex-wrap">
        <?php if ($isAdmin): ?>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=admin/admin/dashboard">Dashboard</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= in_array($navActive, ['users', 'employees'], true) ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">Users</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageUsers">Customers</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees">Employees</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= in_array($navActive, ['products', 'categories', 'subcategories', 'promotions'], true) ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">Catalog</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageProducts">Products</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageCategories">Categories</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageSubcategories">Subcategories</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/managePromotions">Promotions</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'branches' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=admin/admin/manageBranches">Branches</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'permissions' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=admin/admin/managePermissions">Roles</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'orders' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=order/order/index">Orders</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'payments' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=payment/payment/index">Payments</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'fulfillment' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=fulfillment/fulfillment/index">Fulfillment</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'verification' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=verification/verification/index">Verification</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'service' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=service/service/index">Service Tickets</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'inventory' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=inventory/inventory/index">Inventory</a></li>
        <?php elseif ($isSales): ?>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=sales/sales/dashboard">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'orders' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=sales/sales/orders">Orders</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'verification' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=sales/sales/verification">Verification</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'payments' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=sales/sales/payments">Payments</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'fulfillment' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=sales/sales/fulfillment">Fulfillment</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'inventory' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=sales/sales/inventory">Inventory</a></li>
        <?php elseif ($isTech): ?>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=technician/technician/dashboard">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive === 'service' ? 'active' : '' ?>" href="<?= BASE_URL ?>/?r=technician/technician/tickets">Service Tickets</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center text-white-50 small me-3">
        <?= htmlspecialchars((string) ($employee['name'] ?? '')) ?>
        <span class="mx-2">·</span>
        <?= htmlspecialchars((string) ($employee['role'] ?? '')) ?>
      </div>
      <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/?r=auth/auth/employeeLogout">Logout</a>
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