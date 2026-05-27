<?php

/**
 * PCX Admin — Integrated Navigation Component
 */
$employee  = $employee  ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? '';

// Standardize position strings down to lowercase, space-separated format
$rawRole   = strtolower((string)($employee['Emp_Position'] ?? $employee['role'] ?? ''));
$role      = str_replace('_', ' ', $rawRole); // Normalizes 'branch_admin' to 'branch admin'

$isGeneralAdmin = ($role === 'general admin') && empty($employee['Emp_BranchId']);
$isBranchAdmin  = ($role === 'administrator' || $role === 'branch admin');
$isAdmin        = $isGeneralAdmin || $isBranchAdmin; // branch admins are included here
$isSales        = ($role === 'sales representative' || $role === 'sales rep');
$isTech         = ($role === 'technician' || $role === 'tech');

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

<style>
  :root {
    --sidebar-w: 260px;
    --brand-dark: #0f172a;
    --sidebar-bg: #1e293b;
    --sidebar-hover: rgba(255, 255, 255, 0.06);
    --active-accent: #0284c7;
    --border-color: #e2e8f0;
  }

  .layout-wrapper {
    display: flex;
    width: 100%;
    min-height: 100vh;
    align-items: stretch;
  }

  #pcxSidebar {
    min-width: var(--sidebar-w);
    max-width: var(--sidebar-w);
    background-color: var(--sidebar-bg) !important;
    color: #cbd5e1;
    transition: all 0.25s ease-in-out;
    z-index: 1050;
    display: flex;
    flex-direction: column;
  }

  #pcxSidebar .sidebar-header {
    background-color: var(--brand-dark);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }

  #pcxSidebar .nav-link {
    color: #94a3b8;
    padding: 0.7rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-radius: 0.375rem;
    margin: 0.15rem 0.75rem;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.15s ease;
    text-decoration: none;
  }

  #pcxSidebar .nav-link:hover {
    background-color: var(--sidebar-hover);
    color: #f8fafc;
  }

  #pcxSidebar .nav-link.active {
    background-color: var(--active-accent) !important;
    color: #ffffff !important;
  }

  #pcxSidebar .sidebar-heading {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    font-weight: 700;
    padding: 1.25rem 1.5rem 0.5rem;
  }

  .main-content-pane {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex-grow: 1;
    background-color: #f8fafc;
  }

  .top-navbar {
    background-color: #ffffff;
    border-bottom: 1px solid var(--border-color);
    min-height: 62px;
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
  }

  #sidebarOverlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(15, 23, 42, 0.5);
    z-index: 1040;
    backdrop-filter: blur(2px);
  }

  @media (max-width: 991.98px) {
    #pcxSidebar {
      margin-left: calc(-1 * var(--sidebar-w));
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
    }

    #pcxSidebar.open {
      margin-left: 0;
    }

    #sidebarOverlay.show {
      display: block;
    }
  }
</style>

<div class="layout-wrapper">
  <aside id="pcxSidebar" class="shadow">
    <div class="sidebar-header">
      <a href="<?= $dashboardHref ?>" class="text-white text-decoration-none d-flex align-items-center gap-2">
        <i class="bi bi-cpu-fill text-info fs-4"></i>
        <span class="fw-bold tracking-tight">PCX Console</span>
      </a>
    </div>

    <div class="flex-grow-1 overflow-y-auto pt-2">
      <ul class="nav flex-column mb-2">
        <?= navLink($dashboardHref, 'bi-grid-1x2-fill', 'Dashboard', $navActive === 'dashboard') ?>
      </ul>

      <?php if ($isGeneralAdmin): ?>
        <ul class="nav flex-column mb-2">
          <li class="nav-item mt-4 mb-2">
            <span class="sidebar-heading mt-3">Platform Config</span>
          </li>
          
          <?= navLink(BASE_URL . '/?r=admin/admin/manageBranches', 'bi-shop', 'Branches', $navActive === 'branches') ?>
          <?= navLink(BASE_URL . '/?r=admin/admin/managePermissions', 'bi-shield-lock', 'Roles & Permissions', $navActive === 'permissions') ?>
          <?= navLink(BASE_URL . '/?r=admin/admin/manageUsers', 'bi-people', 'User Accounts', $navActive === 'users') ?>

          <li class="nav-item mt-4 mb-2">
            <span class="sidebar-heading mt-3">Storefront Content</span>
          </li>
          
          <?= navLink(BASE_URL . '/?r=catalog/product/index', 'bi-box-seam', 'Products', $navActive === 'products') ?>
          <?= navLink(BASE_URL . '/?r=catalog/category/index', 'bi-tags', 'Categories', $navActive === 'categories') ?>
          <?= navLink(BASE_URL . '/?r=admin/admin/managePromotions', 'bi-megaphone', 'Promotions', $navActive === 'promotions') ?>
        </ul>
      <?php endif; ?>

      <div class="sidebar-heading mt-3">Core Workflows</div>
      <ul class="nav flex-column mb-2">
        <?= navLink(BASE_URL . '/?r=order/order/manageOrders', 'bi-receipt', 'Orders Console', $navActive === 'orders') ?>
        <?= navLink(BASE_URL . '/?r=inventory/inventory/index', 'bi-boxes', 'Inventory Control', $navActive === 'inventory') ?>

        <?php 
        // Verification Routing Strategy
        if ($isAdmin || $isBranchAdmin) {
            echo navLink(BASE_URL . '/?r=verification/verification/index', 'bi-shield-check', 'Verification Hub', $navActive === 'verification');
        } 
        elseif ($isSales) {
            echo navLink(BASE_URL . '/?r=verification/verification/index', 'bi-shield-check', 'Verification Hub', $navActive === 'verification');
        }
        ?>

        <?php if ($isAdmin || $isBranchAdmin || $isSales): ?>
          <?= navLink(BASE_URL . '/?r=fulfillment/fulfillment/index', 'bi-truck', 'Fulfillment Log', $navActive === 'fulfillment') ?>
          <?= navLink(BASE_URL . '/?r=payment/payment/index', 'bi-credit-card-2-front', 'Payments Gateway', $navActive === 'payments') ?>
        <?php endif; ?>

        <?php if ($isAdmin || $isBranchAdmin || $isTech): ?>
          <?= navLink(BASE_URL . '/?r=service/service/index', 'bi-tools', 'Service Center', $navActive === 'service') ?>
        <?php endif; ?>
      </ul>
    </div>

    <div class="p-3 bg-dark small border-top border-secondary-subtle text-secondary text-center" style="--bs-bg-opacity: 0.4;">
      Active Operator:<br>
      <strong class="text-light"><?= htmlspecialchars($employee['Emp_FirstName'] ?? $employee['username'] ?? 'PCX Team') ?></strong>
    </div>
  </aside>

  <div id="sidebarOverlay"></div>

  <div class="main-content-pane">
    <header class="top-navbar d-flex align-items-center justify-content-between gap-3 p-3 border-bottom bg-white">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light d-lg-none border shadow-sm p-2" id="sidebarToggle" aria-label="Toggle Menu">
          <i class="bi bi-list fs-5"></i>
        </button>
        <div>
          <h1 class="h5 mb-0 text-dark fw-bold"><?= htmlspecialchars((string)($pageHeading ?? 'PCX Control Center')) ?></h1>
          <?php if (!empty($pageSubtitle)): ?>
            <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 0.8rem;"><?= htmlspecialchars($pageSubtitle) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div class="dropdown">
        <button class="btn d-flex align-items-center gap-2 border-0 bg-transparent dropdown-toggle p-0" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
          <div class="rounded-circle bg-blue-light text-blue d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; min-width: 35px;">
            <?= strtoupper(substr(htmlspecialchars($employee['name'] ?? 'U'), 0, 1)) ?>
          </div>
          <div class="text-start d-none d-sm-block">
            <div class="fw-semibold small" style="line-height: 1.1;"><?= htmlspecialchars($employee['name'] ?? 'User') ?></div>
            <div class="text-muted small" style="font-size: 0.72rem;"><?= htmlspecialchars(ucwords($role ?? 'Staff')) ?></div>
          </div>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-light" aria-labelledby="userMenu">
          <li>
            <a class="dropdown-item text-secondary small d-flex align-items-center py-2" href="<?= BASE_URL ?>/?r=catalog/product/home" target="_blank">
              <i class="bi bi-arrow-up-right-square me-2"></i>View Storefront
            </a>
          </li>
          <li>
            <hr class="dropdown-divider my-1 border-light">
          </li>
          <li>
            <a class="dropdown-item text-danger small d-flex align-items-center py-2" href="<?= BASE_URL ?>/?r=auth/auth/logout">
              <i class="bi bi-box-arrow-right me-2"></i>Sign Out
            </a>
          </li>
        </ul>
      </div>
    </header>

    <div class="px-4 pt-3 container-fluid">
      <?php
      $activeFlash = $flash ?? ($_SESSION['flash'] ?? null);
      if ($activeFlash):
        unset($_SESSION['flash']);
        $isDanger = ($activeFlash['type'] ?? '') === 'danger' || ($activeFlash['type'] ?? '') === 'error';
      ?>
        <div id="flashToast" class="alert <?= $isDanger ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 p-3 mb-0 rounded-3" role="alert">
          <i class="bi <?= $isDanger ? 'bi-exclamation-octagon-fill text-danger' : 'bi-check-circle-fill text-success' ?> fs-5"></i>
          <div class="small fw-semibold"><?= htmlspecialchars($activeFlash['message'] ?? '') ?></div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($employee['Emp_BranchId'])): ?>
        <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis d-flex align-items-center mb-0 mt-3 rounded-3 p-2.5 shadow-xs">
          <i class="bi bi-geo-alt-fill me-2 fs-5"></i>
          <span class="small fw-medium">
            <strong>Active Scope:</strong> Operations isolated to Branch Ecosystem ID: #<?= htmlspecialchars($employee['Emp_BranchId']) ?>
          </span>
        </div>
      <?php endif; ?>
    </div>

    <main class="container-fluid px-4 py-4 flex-grow-1">