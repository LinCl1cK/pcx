<?php
$flash       = $flash    ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'categories';
$pageTitle   = $pageTitle   ?? 'Create Category — PCX Admin';
$pageHeading = $pageHeading ?? 'Create Category';
$pageSubtitle = 'Add a new product category to the catalog.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:680px;">
  <div class="card">
    <div class="card-header">
      <span class="card-title">New Category</span>
      <a href="<?= BASE_URL ?>/?r=admin/admin/manageCategories" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Categories
      </a>
    </div>
    <div class="card-body">
      <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createCategory">

        <div style="margin-bottom:1.1rem;">
          <label class="form-label" for="name">Category Name <span style="color:var(--red)">*</span></label>
          <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Laptops" required>
        </div>

        <div style="margin-bottom:1.1rem;">
          <label class="form-label" for="description">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3"
            placeholder="Brief description of this category…"></textarea>
          <p class="form-hint">Optional. Shown in catalog listings and SEO metadata.</p>
        </div>

        <div style="display:flex;gap:.6rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--gray-100);">
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageCategories" class="btn btn-secondary">
            <i class="bi bi-x"></i> Cancel
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-tag"></i> Create Category
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
