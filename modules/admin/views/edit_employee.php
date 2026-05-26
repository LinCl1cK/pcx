<?php
$employee_data = $employee_data ?? [];
$branches      = $branches      ?? [];
$flash         = $flash         ?? null;
$employee      = $employee      ?? ($_SESSION['employee'] ?? []);
$navActive     = 'employees';
$pageTitle     = $pageTitle   ?? 'Edit Employee — PCX Admin';
$pageHeading   = $pageHeading ?? 'Edit Employee';
$pageSubtitle  = htmlspecialchars(($employee_data['Emp_Fname'] ?? '') . ' ' . ($employee_data['Emp_Lname'] ?? ''));
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:740px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateEmployee">
    <input type="hidden" name="id" value="<?= htmlspecialchars($employee_data['Emp_Id'] ?? '') ?>">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <!-- ── Personal Info ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-person" style="color:var(--blue);margin-right:.4rem;"></i>Personal Information
          </span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="fname">First Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="fname" name="fname"
              value="<?= htmlspecialchars($employee_data['Emp_Fname'] ?? '') ?>" required>
          </div>
          <div>
            <label class="form-label" for="lname">Last Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="lname" name="lname"
              value="<?= htmlspecialchars($employee_data['Emp_Lname'] ?? '') ?>" required>
          </div>
          <div>
            <label class="form-label" for="email">Email Address <span style="color:var(--red)">*</span></label>
            <input type="email" class="form-control" id="email" name="email"
              value="<?= htmlspecialchars($employee_data['Emp_Email'] ?? '') ?>" required>
          </div>
          <div>
            <label class="form-label" for="contact">Contact Number <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="contact" name="contact" maxlength="15"
              value="<?= htmlspecialchars($employee_data['Emp_ContactNo'] ?? '') ?>" required>
          </div>
          <div style="grid-column:1/-1;">
            <label class="form-label" for="address">Address <span style="color:var(--red)">*</span></label>
            <textarea class="form-control" id="address" name="address" rows="2" maxlength="255"
              required><?= htmlspecialchars($employee_data['Emp_Address'] ?? '') ?></textarea>
          </div>

        </div>
      </div>

      <!-- ── Role & Branch ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-person-badge" style="color:var(--blue);margin-right:.4rem;"></i>Role & Branch</span>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="role">Role <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="role" name="role" required>
              <?php foreach (['Sales Representative','Technician','Administrator'] as $r): ?>
              <option value="<?= $r ?>" <?= ($employee_data['Emp_Position'] ?? '') === $r ? 'selected' : '' ?>><?= $r ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="form-label" for="branch_id">Branch <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="branch_id" name="branch_id" required>
              <?php foreach ($branches as $branch): ?>
              <option value="<?= htmlspecialchars($branch['Branch_Id']) ?>"
                <?= ($employee_data['Emp_BranchId'] ?? '') === $branch['Branch_Id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($branch['Branch_Name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>
      </div>

      <!-- ── Danger Zone ── -->
      <div class="card" style="border-color:var(--red-mid);">
        <div class="card-header" style="background:var(--red-light);border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
          <span class="card-title" style="color:#991B1B;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Danger Zone</span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
          <div>
            <p style="font-size:.875rem;font-weight:500;color:var(--gray-800);margin:0;">Remove employee account</p>
            <p style="font-size:.78rem;color:var(--gray-500);margin:0;">This will permanently revoke portal access. This action cannot be undone.</p>
          </div>
          <button type="button" class="btn btn-danger btn-sm"
            onclick="if(confirm('Delete this employee? This cannot be undone.')) window.location='<?= BASE_URL ?>/?r=admin/admin/deleteEmployee&id=<?= urlencode($employee_data['Emp_Id'] ?? '') ?>'">
            <i class="bi bi-trash3"></i> Delete Account
          </button>
        </div>
      </div>

      <!-- ── Actions ── -->
      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees" class="btn btn-secondary">
          <i class="bi bi-x"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-lg"></i> Save Changes
        </button>
      </div>

    </div>
  </form>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
