<?php
$promotions  = $promotions ?? [];
$flash       = $flash ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);

$rawRole = strtolower(trim($employee['Emp_Position'] ?? $employee['role'] ?? ''));
$isGeneralAdmin = ($rawRole === 'general admin');

$navActive   = 'promotions';
$pageTitle   = $pageTitle ?? 'Promotions — PCX Admin';
$pageHeading = $pageHeading ?? 'Manage Promotions';
$pageSubtitle = 'Oversee marketing campaigns, banners, and scheduled storefront sales.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-star text-blue me-2"></i>Campaign Registry
    </span>
    
    <?php if ($isGeneralAdmin): ?>
      <a href="<?= BASE_URL ?>/?r=admin/admin/createPromotion" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i>New Promotion
      </a>
    <?php endif; ?>
  </div>
  
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3" style="width: 80px;">ID</th>
            <th>Title & Description</th>
            <th>Status</th>
            <th>Duration</th>
            <?php if ($isGeneralAdmin): ?>
              <th class="text-end pe-4">Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($promotions)): ?>
            <tr>
              <td colspan="<?= $isGeneralAdmin ? '5' : '4' ?>" class="text-center text-muted py-5">
                <i class="bi bi-megaphone fs-3 d-block mb-2 opacity-50"></i> No active promotions.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($promotions as $p): ?>
              <tr>
                <td class="ps-4 fw-medium text-secondary">#<?= (int) $p['Promo_Id'] ?></td>
                <td>
                  <span class="fw-bold text-dark d-block"><?= htmlspecialchars((string) $p['Promo_Title']) ?></span>
                </td>
                <td>
                  <?php if ($p['Promo_Status'] === 'Active'): ?>
                    <span class="badge bg-blue-light text-blue px-2 py-1">Active</span>
                  <?php else: ?>
                    <span class="badge bg-light text-secondary border px-2 py-1">Inactive</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="text-secondary small">
                    <i class="bi bi-calendar me-1"></i>
                    <?= !empty($p['Promo_Start']) ? htmlspecialchars((string) $p['Promo_Start']) : '—' ?> 
                    <i class="bi bi-arrow-right mx-1"></i> 
                    <?= !empty($p['Promo_End']) ? htmlspecialchars((string) $p['Promo_End']) : 'Open-ended' ?>
                  </span>
                </td>
                
                <?php if ($isGeneralAdmin): ?>
                <td class="text-end pe-4">
                  <div class="d-inline-flex gap-1">
                    <a class="btn btn-sm btn-outline-primary"
                       href="<?= BASE_URL ?>/?r=admin/admin/editPromotion&id=<?= (int) $p['Promo_Id'] ?>"
                       title="Edit Promotion">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a class="btn btn-sm btn-outline-danger"
                       href="<?= BASE_URL ?>/?r=admin/admin/deletePromotion&id=<?= (int) $p['Promo_Id'] ?>"
                       onclick="return confirm('Delete this promotion permanently?')"
                       title="Delete Promotion">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>