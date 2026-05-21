<?php
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'promotions';
$pageTitle = $pageTitle ?? 'New promotion';
$pageHeading = $pageHeading ?? 'New promotion';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Create Promotion</title>
</head>
<body>
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
  ov>
</form>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
