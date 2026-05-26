<?php
$users       = $users ?? [];
$flash       = $flash ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'users';
$pageTitle   = $pageTitle ?? 'Customers — PCX Admin';
$pageHeading = 'Manage Customers';
$pageSubtitle = 'Directory of registered customer accounts and contact profiles.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-person-lines-fill text-blue me-2"></i>Customer Database
    </span>
    <span class="badge bg-light text-secondary border"><?= count($users) ?> Customers</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">ID</th>
            <th>Name</th>
            <th>Contact Details</th>
            <th>Address</th>
            <th class="text-end pe-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-5">
                <i class="bi bi-person-dash fs-3 d-block mb-2"></i> No registered customers found.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $user): ?>
            <tr>
              <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars($user['Cus_Id']) ?></td>
              <td>
                <span class="fw-bold text-dark d-block"><?= htmlspecialchars($user['Cus_Fname'] . ' ' . $user['Cus_Lname']) ?></span>
                <span class="text-muted" style="font-size: 0.75rem;">Since <?= date('Y-m-d', strtotime($user['Cus_CreatedAt'])) ?></span>
              </td>
              <td>
                <div class="text-secondary mb-1"><i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($user['Cus_Email']) ?></div>
                <div class="text-secondary"><i class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($user['Cus_ContactNo']) ?></div>
              </td>
              <td>
                <span class="text-truncate d-inline-block text-secondary" style="max-width: 250px;" title="<?= htmlspecialchars($user['Cus_Address']) ?>">
                  <?= htmlspecialchars($user['Cus_Address']) ?>
                </span>
              </td>
              <td class="text-end pe-4">
                <div class="d-inline-flex gap-1">
                  <a href="<?= BASE_URL ?>/?r=admin/admin/editUser&id=<?= urlencode($user['Cus_Id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Customer">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="<?= BASE_URL ?>/?r=admin/admin/deleteUser&id=<?= urlencode($user['Cus_Id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this customer account?')" title="Delete Customer">
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