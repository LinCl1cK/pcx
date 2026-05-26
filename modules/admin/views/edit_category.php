<?php
$category    = $category ?? [];
$flash       = $flash    ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'categories';
$pageTitle   = $pageTitle   ?? 'Edit Category — PCX Admin';
$pageHeading = $pageHeading ?? 'Edit Category';
$pageSubtitle = 'Update the details of this catalog category.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:680px;">
  <div class="card">
    <div class="card-header">
      <span class="card-title">
        <i class="bi bi-tag" style="color:var(--blue);margin-right:.35rem;"></i>
        <?= htmlspecialchars($category['Cat_Name'] ?? 'Edit Category') ?>
      </span>
      <a href="<?= BASE_URL ?>/?r=admin/admin/manageCategories" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
    <div class="card-body">
      <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateCategory">
        <input type="hidden" name="id" value="<?= htmlspecialchars($category['Cat_Id'] ?? '') ?>">

        <div style="margin-bottom:1.1rem;">
          <label class="form-label" for="name">Category Name <span style="color:var(--red)">*</span></label>
          <input type="text" class="form-control" id="name" name="name"
            value="<?= htmlspecialchars($category['Cat_Name'] ?? '') ?>" required>
        </div>

        <div style="margin-bottom:1.1rem;">
          <label class="form-label" for="description">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3"
            ><?= htmlspecialchars($category['Cat_Description'] ?? '') ?></textarea>
          <p class="form-hint">Optional. Shown in catalog listings and SEO metadata.</p>
        </div>

        <div style="display:flex;gap:.6rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--gray-100);">
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageCategories" class="btn btn-secondary">
            <i class="bi bi-x"></i> Cancel
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Save Changes
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
