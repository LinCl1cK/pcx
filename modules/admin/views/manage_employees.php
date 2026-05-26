<?php
$employees   = $employees ?? [];
$branches    = $branches ?? [];
$flash       = $flash ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'employees';
$pageTitle   = $pageTitle ?? 'Employees — PCX Admin';
$pageHeading = $pageHeading ?? 'Manage Employees';
$pageSubtitle = 'Directory of registered staff members and portal access roles.';
$pageActions = '<a href="' . BASE_URL . '/?r=admin/admin/createEmployee" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Add New Employee</a>';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-people text-blue me-2"></i>Staff Directory
    </span>
    <span class="badge bg-light text-secondary border"><?= count($employees) ?> Active</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Branch</th>
            <th class="text-end pe-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($employees)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-5">
                <i class="bi bi-person-x fs-3 d-block mb-2"></i> No employees found in the directory.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($employees as $emp): ?>
            <tr>
              <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars($emp['Emp_Id']) ?></td>
              <td>
                <span class="fw-bold text-dark d-block"><?= htmlspecialchars($emp['Emp_Fname'] . ' ' . $emp['Emp_Lname']) ?></span>
                <span class="text-muted" style="font-size: 0.75rem;">Joined <?= date('M Y', strtotime($emp['Emp_CreatedAt'])) ?></span>
              </td>
              <td>
                <span class="badge bg-blue-light text-blue px-2 py-1"><?= htmlspecialchars($emp['Emp_Position']) ?></span>
              </td>
              <td class="text-secondary"><?= htmlspecialchars($emp['Emp_Email']) ?></td>
              <td class="text-secondary"><?= htmlspecialchars($emp['Branch_Name']) ?></td>
              <td class="text-end pe-4">
                <div class="d-inline-flex gap-1">
                  <a href="<?= BASE_URL ?>/?r=admin/admin/editEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Employee">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="<?= BASE_URL ?>/?r=admin/admin/deleteEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this employee?')" title="Remove Access">
                    <i class="bi bi-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>