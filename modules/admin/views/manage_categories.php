<?php
$categories  = $categories ?? [];
$flash       = $flash      ?? null;
$employee    = $employee   ?? ($_SESSION['employee'] ?? []);

$rawRole = strtolower(trim($employee['Emp_Position'] ?? $employee['role'] ?? ''));
$isGeneralAdmin = ($rawRole === 'general admin');

$navActive   = 'categories';
$pageTitle   = $pageTitle   ?? 'Categories — PCX Admin';
$pageHeading = $pageHeading ?? 'Manage Categories';
$pageSubtitle = 'View, organize, and manage the storefront product categories.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div>
      <span class="card-title">
        <i class="bi bi-tags" style="color:var(--blue);margin-right:.4rem;"></i>Product Categories
      </span>
      <span class="badge bg-secondary ms-2"><?= count($categories) ?> Total</span>
    </div>
    
    <?php if ($isGeneralAdmin): ?>
      <a href="<?= BASE_URL ?>/?r=admin/admin/createCategory" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add New Category
      </a>
    <?php endif; ?>
  </div>
  
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" style="font-size:.9rem;">
      <thead class="table-light text-secondary" style="font-size:.78rem; text-uppercase: uppercase; letter-spacing: 0.5px;">
        <tr>
          <th style="width: 80px; padding-left: 1.25rem;">ID</th>
          <th>Category Name</th>
          <th>Description</th>
          <?php if ($isGeneralAdmin): ?>
            <th style="width: 160px; text-align: right; padding-right: 1.25rem;">Actions</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($categories)): ?>
          <tr>
            <td colspan="<?= $isGeneralAdmin ? '4' : '3' ?>" class="text-center text-muted py-4">
              <i class="bi bi-folder-x fs-3 d-block mb-2"></i> No categories found.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td style="padding-left: 1.25rem;" class="fw-medium text-secondary">
                #<?= htmlspecialchars($cat['Cat_Id']) ?>
              </td>
              <td>
                <strong style="color:var(--gray-800);"><?= htmlspecialchars($cat['Cat_Name']) ?></strong>
              </td>
              <td class="text-secondary text-truncate" style="max-width: 350px;">
                <?= htmlspecialchars($cat['Cat_Description'] ?? '—') ?>
              </td>
              
              <?php if ($isGeneralAdmin): ?>
              <td style="text-align: right; padding-right: 1.25rem;">
                <div class="d-inline-flex gap-1">
                  <a href="<?= BASE_URL ?>/?r=admin/admin/editCategory&id=<?= urlencode($cat['Cat_Id']) ?>" 
                     class="btn btn-sm btn-outline-primary" title="Edit Category">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                  <a href="<?= BASE_URL ?>/?r=admin/admin/deleteCategory&id=<?= urlencode($cat['Cat_Id']) ?>" 
                     class="btn btn-sm btn-outline-danger" 
                     onclick="return confirm('Are you sure you want to delete this category?')" title="Delete Category">
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

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>