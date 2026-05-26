<?php
$branches    = $branches ?? [];
$flash       = $flash    ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'employees';
$pageTitle   = $pageTitle   ?? 'Create Employee — PCX Admin';
$pageHeading = $pageHeading ?? 'Create Employee';
$pageSubtitle = 'Register a new staff member and assign their portal access.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:740px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createEmployee">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <!-- ── Personal Info ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-person" style="color:var(--blue);margin-right:.4rem;"></i>Personal Information</span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="fname">First Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="fname" name="fname" placeholder="e.g. Maria" required>
          </div>
          <div>
            <label class="form-label" for="lname">Last Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="lname" name="lname" placeholder="e.g. Santos" required>
          </div>
          <div>
            <label class="form-label" for="email">Email Address <span style="color:var(--red)">*</span></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="employee@pcxstore.ph" required>
          </div>
          <div>
            <label class="form-label" for="contact">Contact Number <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="contact" name="contact" maxlength="15"
              placeholder="+63 912 345 6789" required>
          </div>
          <div style="grid-column:1/-1;">
            <label class="form-label" for="address">Address <span style="color:var(--red)">*</span></label>
            <textarea class="form-control" id="address" name="address" rows="2" maxlength="255"
              placeholder="Street, Barangay, City, Province, ZIP" required></textarea>
          </div>

        </div>
      </div>

      <!-- ── Role & Branch ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-person-badge" style="color:var(--blue);margin-right:.4rem;"></i>Role & Branch Assignment</span>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="role">Role <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="role" name="role" required>
              <option value="Sales Representative">Sales Representative</option>
              <option value="Technician">Technician</option>
              <option value="Administrator">Administrator</option>
            </select>
            <p class="form-hint">Determines portal access level and visible modules.</p>
          </div>

          <div>
            <label class="form-label" for="branch_id">Branch <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="branch_id" name="branch_id" required>
              <?php foreach ($branches as $branch): ?>
              <option value="<?= htmlspecialchars($branch['Branch_Id']) ?>">
                <?= htmlspecialchars($branch['Branch_Name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>
      </div>

      <!-- ── Credentials ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-lock" style="color:var(--blue);margin-right:.4rem;"></i>Portal Credentials</span>
        </div>
        <div class="card-body">
          <label class="form-label" for="password">Password <span style="color:var(--red)">*</span></label>
          <input type="password" class="form-control" id="password" name="password"
            placeholder="Minimum 8 characters" required style="max-width:360px;">
          <p class="form-hint">The employee will use this to log in. Advise them to change it on first sign-in.</p>
        </div>
      </div>

      <!-- ── Actions ── -->
      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees" class="btn btn-secondary">
          <i class="bi bi-x"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-person-plus"></i> Create Employee
        </button>
      </div>

    </div>
  </form>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
