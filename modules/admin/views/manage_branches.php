<?php
$branches = $branches ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'branches';
$pageTitle = $pageTitle ?? 'Branches';
$pageHeading = $pageHeading ?? 'Branches';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h2 class="h6">Add Branch</h2>
        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createBranch">
          <input class="form-control mb-2" name="name" maxlength="100" placeholder="Branch name" required>
          <input class="form-control mb-2" name="location" maxlength="150" placeholder="Location" required>
          <input class="form-control mb-3" name="contact" maxlength="15" placeholder="Contact number" required>
          <button class="btn btn-primary w-100" type="submit">Create Branch</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="table-responsive bg-white rounded shadow-sm">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr><th>ID</th><th>Name</th><th>Location</th><th>Contact</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($branches as $branch): ?>
          <tr>
            <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateBranch">
              <input type="hidden" name="id" value="<?= htmlspecialchars((string) $branch['Branch_Id']) ?>">
              <td><?= htmlspecialchars((string) $branch['Branch_Id']) ?></td>
              <td><input class="form-control form-control-sm" name="name" maxlength="100" value="<?= htmlspecialchars((string) $branch['Branch_Name']) ?>" required></td>
              <td><input class="form-control form-control-sm" name="location" maxlength="150" value="<?= htmlspecialchars((string) $branch['Branch_Location']) ?>" required></td>
              <td><input class="form-control form-control-sm" name="contact" maxlength="15" value="<?= htmlspecialchars((string) $branch['Branch_ContactNo']) ?>" required></td>
              <td><small><?= htmlspecialchars((string) $branch['Branch_CreatedAt']) ?></small></td>
              <td class="text-nowrap">
                <button class="btn btn-sm btn-primary" type="submit">Save</button>
                <a class="btn btn-sm btn-outline-danger" href="<?= BASE_URL ?>/?r=admin/admin/deleteBranch&id=<?= urlencode((string) $branch['Branch_Id']) ?>" onclick="return confirm('Delete this branch?')">Delete</a>
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
