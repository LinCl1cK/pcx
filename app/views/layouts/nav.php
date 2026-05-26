<?php

/**
 * PCX Admin — Integrated Navigation Component
 */
$employee  = $employee  ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? '';
$role      = strtolower((string)($employee['role'] ?? ''));
$isAdmin   = $role === 'administrator';
$isSales   = $role === 'sales representative';
$isTech    = $role === 'technician';

$dashboardHref = BASE_URL . '/?r=admin/admin/dashboard';
if ($isSales) $dashboardHref = BASE_URL . '/?r=sales/sales/dashboard';
elseif ($isTech) $dashboardHref = BASE_URL . '/?r=technician/technician/dashboard';

function navLink(string $href, string $icon, string $label, bool $active): string
{
  $cls = $active ? 'nav-link active' : 'nav-link';
  return "<li>
              <a class=\"{$cls}\" href=\"{$href}\">
                <i class=\"bi {$icon}\"></i>
                <span>{$label}</span>
              </a>
            </li>";
}
?>

  <div class="layout-container">
    <header class="top-header">
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-link d-lg-none p-0 text-dark fs-4 me-2" id="sidebarToggle" aria-label="Menu Toggle">
          <i class="bi bi-list"></i>
        </button>
        <a class="header-brand" href="<?= $dashboardHref ?>">
          <i class="bi bi-cpu-fill"></i> <span>PCX Hub</span>
        </a>
      </div>

      <div class="dropdown">
        <button class="btn d-flex align-items-center gap-2 border-0 bg-transparent dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown">
          <div class="rounded-circle bg-blue-light text-blue d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
            <?= strtoupper(substr(htmlspecialchars($employee['name'] ?? 'U'), 0, 1)) ?>
          </div>
          <div class="text-start d-none d-sm-block">
            <div class="fw-semibold small" style="line-height: 1.1;"><?= htmlspecialchars($employee['name'] ?? 'User') ?></div>
            <div class="text-muted small" style="font-size: 0.72rem;"><?= htmlspecialchars(ucwords($role)) ?></div>
          </div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-light">
          <li><a class="dropdown-item text-danger small" href="<?= BASE_URL ?>/?r=auth/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
        </ul>
      </div>
    </header>

    <aside class="sidebar-nav" id="pcxSidebar">
      <div>
        <p class="sidebar-section-title">Telemetry</p>
        <ul class="nav-menu">
          <?= navLink($dashboardHref, 'bi-speedometer2', 'Dashboard Console', $navActive === 'dashboard') ?>
        </ul>

        <?php if ($isAdmin): ?>
          <p class="sidebar-section-title">Directory & People</p>
          <ul class="nav-menu">
            <?= navLink(BASE_URL . '/?r=admin/admin/manageUsers', 'bi-person-lines-fill', 'Customers', $navActive === 'users') ?>
            <?= navLink(BASE_URL . '/?r=admin/admin/manageEmployees', 'bi-people', 'Staff Members', $navActive === 'employees') ?>
          </ul>

          <p class="sidebar-section-title">Catalog Control</p>
          <ul class="nav-menu">
            <?= navLink(BASE_URL . '/?r=admin/admin/manageProducts', 'bi-box-seam', 'Products', $navActive === 'products') ?>
            <?= navLink(BASE_URL . '/?r=admin/admin/manageCategories', 'bi-tags', 'Categories', $navActive === 'categories') ?>
            <?= navLink(BASE_URL . '/?r=admin/admin/manageSubcategories', 'bi-diagram-2', 'Subcategories', $navActive === 'subcategories') ?>
            <?= navLink(BASE_URL . '/?r=admin/admin/managePromotions', 'bi-megaphone', 'Promotions', $navActive === 'promotions') ?>
          </ul>

          <p class="sidebar-section-title">Operations Oversight</p>
          <ul class="nav-menu">
            <?= navLink(BASE_URL . '/?r=sales/sales/orders', 'bi-cart-check', 'Order Directory', $navActive === 'orders') ?>
            <?= navLink(BASE_URL . '/?r=verification/verification/index', 'bi-shield-check', 'ID Verification Hub', $navActive === 'verification') ?>
            <?= navLink(BASE_URL . '/?r=payment/payment/index', 'bi-credit-card', 'Settlements Board', $navActive === 'payments') ?>
            <?= navLink(BASE_URL . '/?r=fulfillment/fulfillment/index', 'bi-truck', 'Dispatch Floor', $navActive === 'fulfillment') ?>
          </ul>

          <p class="sidebar-section-title">System Parameters</p>
          <ul class="nav-menu">
            <?= navLink(BASE_URL . '/?r=admin/admin/manageBranches', 'bi-building', 'Store Branches', $navActive === 'branches') ?>
            <?= navLink(BASE_URL . '/?r=admin/admin/managePermissions', 'bi-shield-lock', 'Roles & Auth', $navActive === 'permissions') ?>
          </ul>
        <?php endif; ?>

        <?php if ($isSales): ?>
          <p class="sidebar-section-title">Sales Logistics</p>
          <ul class="nav-menu">
            <?= navLink(BASE_URL . '/?r=sales/sales/orders', 'bi-cart-check', 'Order Pipeline', $navActive === 'orders') ?>
            <?= navLink(BASE_URL . '/?r=verification/verification/index', 'bi-shield-check', 'ID Verification', $navActive === 'verification') ?>
            <?= navLink(BASE_URL . '/?r=payment/payment/index', 'bi-credit-card', 'Settlements', $navActive === 'payments') ?>
            <?= navLink(BASE_URL . '/?r=fulfillment/fulfillment/index', 'bi-truck', 'Fulfillment', $navActive === 'fulfillment') ?>
            <?= navLink(BASE_URL . '/?r=inventory/inventory/list', 'bi-inboxes', 'Branch Stock', $navActive === 'inventory') ?>
          </ul>
        <?php endif; ?>

        <?php if ($isTech): ?>
          <p class="sidebar-section-title">Service Center</p>
          <ul class="nav-menu">
            <?= navLink(BASE_URL . '/?r=technician/technician/tickets', 'bi-wrench', 'Assigned Tickets', $navActive === 'service') ?>
          </ul>
        <?php endif; ?>
      </div>
    </aside>

    <div class="modal-backdrop fade d-none" id="sidebarOverlay"></div>

    <main class="main-content" style="min-width: 0; width: 80%; overflow-x: hidden;">
      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1 text-dark"><?= htmlspecialchars($pageHeading ?? 'Operations Hub') ?></h1>
          <?php if (!empty($pageSubtitle)): ?>
            <p class="text-secondary small mb-0"><?= htmlspecialchars($pageSubtitle) ?></p>
          <?php endif; ?>
        </div>
        <?php if (!empty($pageActions)): ?>
          <div class="d-flex gap-2 align-items-center"><?= $pageActions ?></div>
        <?php endif; ?>
      </div>

      <?php
      $activeFlash = $flash ?? ($_SESSION['flash'] ?? null);
      if ($activeFlash):
        unset($_SESSION['flash']);
        $isDanger = ($activeFlash['type'] ?? '') === 'danger' || ($activeFlash['type'] ?? '') === 'error';
      ?>
        <div class="pcx-toast-wrap" id="toastWrap">
          <div class="alert <?= $isDanger ? 'alert-danger' : 'alert-success' ?> border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3" role="alert">
            <div class="d-flex align-items-center gap-2">
              <i class="bi <?= $isDanger ? 'bi-exclamation-octagon text-red' : 'bi-check-circle text-success' ?> fs-5"></i>
              <span class="small fw-medium"><?= htmlspecialchars($activeFlash['message'] ?? '') ?></span>
            </div>
            <button type="button" class="btn-close" onclick="document.getElementById('toastWrap').remove()"></button>
          </div>
        </div>
        <script>
          setTimeout(() => {
            const w = document.getElementById('toastWrap');
            if (w) {
              w.style.opacity = '0';
              w.style.transition = 'opacity 0.35s ease';
            }
          }, 3500);
          setTimeout(() => {
            const w = document.getElementById('toastWrap');
            if (w) w.remove();
          }, 3900);
        </script>
      <?php endif; ?>