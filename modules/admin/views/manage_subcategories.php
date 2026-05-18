<?php
$subcategories = $subcategories ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'subcategories';
$pageTitle = $pageTitle ?? 'Subcategories';
$pageHeading = $pageHeading ?? 'Subcategories';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h2 class="h6">Add Subcategory</h2>
        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createSubcategory">
          <input class="form-control mb-2" name="name" maxlength="50" placeholder="Subcategory name" required>
          <textarea class="form-control mb-3" name="description" maxlength="255" rows="3" placeholder="Description"></textarea>
          <button class="btn btn-primary w-100" type="submit">Create Subcategory</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="table-responsive bg-white rounded shadow-sm">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr><th>ID</th><th>Name</th><th>Description</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($subcategories as $subcat): ?>
          <tr>
            <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateSubcategory">
              <input type="hidden" name="id" value="<?= htmlspecialchars((string) $subcat['Subc_Id']) ?>">
              <td><?= htmlspecialchars((string) $subcat['Subc_Id']) ?></td>
              <td><input class="form-control form-control-sm" name="name" maxlength="50" value="<?= htmlspecialchars((string) $subcat['Subc_Name']) ?>" required></td>
              <td><input class="form-control form-control-sm" name="description" maxlength="255" value="<?= htmlspecialchars((string) ($subcat['Subc_Description'] ?? '')) ?>"></td>
              <td class="text-nowrap">
                <button class="btn btn-sm btn-primary" type="submit">Save</button>
                <a class="btn btn-sm btn-outline-danger" href="<?= BASE_URL ?>/?r=admin/admin/deleteSubcategory&id=<?= urlencode((string) $subcat['Subc_Id']) ?>" onclick="return confirm('Delete this subcategory?')">Delete</a>
              </td>
            </form>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
