<?php
$branches    = $branches ?? [];
$flash       = $flash    ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'employees';
$pageTitle   = $pageTitle   ?? 'Create Employee — PCX Admin';
$pageHeading = $pageHeading ?? 'Create Employee';
$pageSubtitle = 'Register a new staff member and assign their portal access.';

if (!isset($allowedRoles)) {
  $currentRole = strtolower(trim($employee['Emp_Position'] ?? $employee['role'] ?? ''));

  // Adjusted role permissions: 
  // General Admin creates: Branch Admin, General Admin
  // Branch Admin creates: Sales Rep, Technician, Branch Admin
  $allowedRoles = ($currentRole === 'general admin' || $currentRole === 'general administrator')
    ? ['Branch Admin', 'General Admin']
    : ['Sales Representative', 'Technician', 'Branch Admin'];
}
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:740px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createEmployee" id="createEmployeeForm">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

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
            <input type="text" class="form-control" id="fname" name="fname" placeholder="John" required>
          </div>
          <div>
            <label class="form-label" for="lname">Last Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="lname" name="lname" placeholder="Doe" required>
          </div>
          <div>
            <label class="form-label" for="email">Work Email Address <span style="color:var(--red)">*</span></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="johndoe@pcx.com" required>
          </div>
          <div>
            <label class="form-label" for="contact">Contact Number <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="contact" name="contact" maxlength="15" placeholder="0917XXXXXXX" required>
          </div>
          <div style="grid-column:1/-1;">
            <label class="form-label" for="address">Home Address <span style="color:var(--red)">*</span></label>
            <textarea class="form-control" id="address" name="address" rows="2" maxlength="255" placeholder="Complete Street Address" required></textarea>
          </div>

        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-person-badge" style="color:var(--blue);margin-right:.4rem;"></i>Role & Branch Assignment</span>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="role">Role <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="role" name="role" required>
              <?php foreach ($allowedRoles as $roleOption): ?>
                <option value="<?= htmlspecialchars($roleOption) ?>"><?= htmlspecialchars($roleOption) ?></option>
              <?php endforeach; ?>
            </select>
            <p class="form-hint">Determines portal access level and visible modules.</p>
          </div>

          <div>
            <label class="form-label" for="branch_id">Branch <span id="branch-asterisk" style="color:var(--red)">*</span></label>
            <?php
            $currentRole = strtolower(trim($employee['Emp_Position'] ?? $employee['role'] ?? ''));
            // Make sure both versions match perfectly
            if ($currentRole === 'general admin' || $currentRole === 'general administrator'):
            ?>
              <select class="form-select" id="branch_id" name="branch_id">
                <option value="">Global / Corporate (No Branch)</option>
                <?php foreach ($branches as $branch): ?>
                  <option value="<?= htmlspecialchars($branch['Branch_Id']) ?>">
                    <?= htmlspecialchars($branch['Branch_Name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <input type="hidden" name="branch_id" value="<?= htmlspecialchars($employee['Emp_BranchId'] ?? '') ?>">
              <input type="text" class="form-control bg-light text-muted" value="Current Jurisdiction Location" disabled>
            <?php endif; ?>
          </div>

        </div>
      </div>

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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const branchSelect = document.getElementById('branch_id');
    const asterisk = document.getElementById('branch-asterisk');

    function handleRoleDependencies() {
      if (!roleSelect || !branchSelect) return;

      if (roleSelect.value === 'General Admin') {
        branchSelect.value = '';
        branchSelect.disabled = true;
        if (asterisk) asterisk.style.display = 'none';

        // Ensure an empty string gets explicitly posted even when input element is disabled
        if (!document.getElementById('hidden_branch_fallback')) {
          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = 'branch_id';
          hidden.value = '';
          hidden.id = 'hidden_branch_fallback';
          branchSelect.parentNode.appendChild(hidden);
        }
      } else {
        branchSelect.disabled = false;
        branchSelect.required = true;
        if (asterisk) asterisk.style.display = 'inline';

        const hidden = document.getElementById('hidden_branch_fallback');
        if (hidden) hidden.remove();
      }
    }

    roleSelect.addEventListener('change', handleRoleDependencies);
    handleRoleDependencies(); // Evaluate conditions immediately on DOM ready
  });
</script>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>