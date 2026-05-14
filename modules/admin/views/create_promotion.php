<?php
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'promotions';
$pageTitle = $pageTitle ?? 'New promotion';
$pageHeading = $pageHeading ?? 'New promotion';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>
<form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createPromotion" class="card border-0 shadow-sm" style="max-width:560px">
  <div class="card-body">
    <div class="mb-2">
      <label class="form-label">Title *</label>
      <input name="title" class="form-control" required maxlength="255">
    </div>
    <div class="mb-2">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="2"></textarea>
    </div>
    <div class="mb-2">
      <label class="form-label">Banner image filename *</label>
      <input name="banner" class="form-control" required placeholder="e.g. promo1.webp">
    </div>
    <div class="mb-2">
      <label class="form-label">Status</label>
      <select name="status" class="form-select"><option>Active</option><option>Inactive</option></select>
    </div>
    <div class="row g-2 mb-2">
      <div class="col-md-6">
        <label class="form-label">Start date</label>
        <input type="date" name="start" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">End date</label>
        <input type="date" name="end" class="form-control">
      </div>
    </div>
    <button class="btn btn-dark" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/?r=admin/admin/managePromotions">Cancel</a>
  </div>
</form>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
