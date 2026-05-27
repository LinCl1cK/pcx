<?php
$employees   = $employees ?? [];
$branches    = $branches ?? [];
$flash       = $flash ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);

// Strictly identify the General Admin
// Session stores 'role' and 'branch_id' (set by AuthController::login())
$rawRole     = strtolower(trim($employee['role'] ?? $employee['Emp_Position'] ?? ''));
$role        = str_replace('_', ' ', $rawRole);
$isGeneralAdmin = ($role === 'general admin') && empty($employee['branch_id'] ?? $employee['Emp_BranchId'] ?? '');

$navActive   = 'employees';
$pageTitle   = 'Employees — PCX Admin';
$pageHeading = 'Manage Employees';
$pageSubtitle = 'Directory of registered staff members and portal access roles.';

// Separate Administrative roles for the General Admin's manipulation view
$administrativeStaff = [];
if ($isGeneralAdmin) {
  foreach ($employees as $emp) {
    $empPos = strtolower(trim($emp['Emp_Position']));
    if ($empPos === 'branch admin' || $empPos === 'general admin') {
      $administrativeStaff[] = $emp;
    }
  }
}

require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<?php if ($isGeneralAdmin): ?>
  <ul class="nav nav-tabs mb-4 border-bottom-0" id="empTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active fw-bold px-4 shadow-sm rounded-top" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins-pane" type="button" role="tab">
        <i class="bi bi-person-gear me-2"></i>Administrative Staff
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-bold px-4 text-secondary" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-pane" type="button" role="tab">
        <i class="bi bi-people me-2"></i>All Staff (Read-Only)
      </button>
    </li>
  </ul>

  <div class="tab-content" id="empTabContent">

    <div class="tab-pane fade show active" id="admins-pane" role="tabpanel">
      <div class="card border-0 shadow-sm bg-white" id="container-admins">
        <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 300px;">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control bg-light border-start-0 search-input" placeholder="Search admins...">
            </div>
          </div>
          <a href="<?= BASE_URL ?>/?r=admin/admin/createEmployee" class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus me-1"></i>New Administrative Staff
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
              <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                  <th class="ps-4 py-3 user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-admins', 0)">ID <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                  <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-admins', 1)">Name <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                  <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-admins', 2)">Role <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                  <th>Email</th>
                  <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-admins', 4)">Branch <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                  <th class="text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody id="tbody-admins">
                <?php if (empty($administrativeStaff)): ?>
                  <tr class="empty-row">
                    <td colspan="6" class="text-center text-muted py-5">No administrative staff members found.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($administrativeStaff as $emp): ?>
                    <tr class="emp-row" data-role="<?= strtolower($emp['Emp_Position']) ?>" data-branch="<?= strtolower($emp['Branch_Name'] ?? 'global') ?>">
                      <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars($emp['Emp_Id']) ?></td>
                      <td><span class="fw-bold text-dark d-block"><?= htmlspecialchars($emp['Emp_Fname'] . ' ' . $emp['Emp_Lname']) ?></span></td>
                      <td><span class="badge bg-blue-light text-blue px-2 py-1"><?= htmlspecialchars($emp['Emp_Position']) ?></span></td>
                      <td class="text-secondary"><?= htmlspecialchars($emp['Emp_Email']) ?></td>
                      <td class="text-secondary"><?= htmlspecialchars(!empty($emp['Branch_Name']) ? $emp['Branch_Name'] : 'Global / Corporate') ?></td>
                      <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1">
                          <a href="<?= BASE_URL ?>/?r=admin/admin/editEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Admin"><i class="bi bi-pencil"></i></a>
                          <a href="<?= BASE_URL ?>/?r=admin/admin/deleteEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this administrator?')" title="Remove Admin"><i class="bi bi-trash"></i></a>
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
    </div>

    <div class="tab-pane fade" id="all-pane" role="tabpanel">
      <div class="card border-0 shadow-sm bg-white" id="container-all">
        <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center gap-3">
          <div class="input-group input-group-sm" style="max-width: 250px;">
            <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control bg-light border-start-0 search-input" placeholder="Search global staff...">
          </div>
          <select class="form-select form-select-sm bg-light border-0 role-filter" style="max-width: 180px;">
            <option value="">Filter by Role</option>
            <option value="general admin">General Admin</option>
            <option value="branch admin">Branch Admin</option>
            <option value="sales representative">Sales Representative</option>
            <option value="technician">Technician</option>
          </select>
          <select class="form-select form-select-sm bg-light border-0 branch-filter" style="max-width: 180px;">
            <option value="">Filter by Branch</option>
            <option value="global">Global / Corporate</option>
            <?php foreach ($branches as $b): ?>
              <option value="<?= strtolower(trim($b['Branch_Name'])) ?>"><?= htmlspecialchars($b['Branch_Name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
              <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                <tr>
                  <th class="ps-4 py-3 user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-all', 0)">ID <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                  <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-all', 1)">Name <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                  <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-all', 2)">Role <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                  <th>Email</th>
                  <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-all', 4)">Branch <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
                </tr>
              </thead>
              <tbody id="tbody-all">
                <?php if (empty($employees)): ?>
                  <tr class="empty-row">
                    <td colspan="5" class="text-center text-muted py-5">No employee records registered.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($employees as $emp): ?>
                    <tr class="emp-row" data-role="<?= strtolower($emp['Emp_Position']) ?>" data-branch="<?= strtolower($emp['Branch_Name'] ?? 'global') ?>">
                      <td class="ps-4 text-secondary">#<?= htmlspecialchars($emp['Emp_Id']) ?></td>
                      <td><span class="fw-bold text-dark d-block"><?= htmlspecialchars($emp['Emp_Fname'] . ' ' . $emp['Emp_Lname']) ?></span></td>
                      <td><span class="badge bg-light text-secondary border px-2 py-1"><?= htmlspecialchars($emp['Emp_Position']) ?></span></td>
                      <td class="text-secondary"><?= htmlspecialchars($emp['Emp_Email']) ?></td>
                      <td class="text-secondary"><?= htmlspecialchars(!empty($emp['Branch_Name']) ? $emp['Branch_Name'] : 'Global / Corporate') ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>

<?php else: ?>
  <div class="card border-0 shadow-sm bg-white" id="container-local">
    <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-2">
        <div class="input-group input-group-sm" style="max-width: 250px;">
          <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control bg-light border-start-0 search-input" placeholder="Search local staff...">
        </div>
        <select class="form-select form-select-sm bg-light border-0 role-filter" style="max-width: 180px;">
          <option value="">All Roles</option>
          <option value="sales representative">Sales Representative</option>
          <option value="technician">Technician</option>
        </select>
      </div>
      <a href="<?= BASE_URL ?>/?r=admin/admin/createEmployee" class="btn btn-sm btn-primary">
        <i class="bi bi-person-plus me-1"></i>Add Staff Member
      </a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
          <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4 py-3 user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-local', 0)">ID <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
              <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-local', 1)">Name <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
              <th class="user-select-none" style="cursor: pointer;" onclick="sortEmpTable('tbody-local', 2)">Role <i class="bi bi-arrow-down-up extra-small ms-1"></i></th>
              <th>Email</th>
              <th class="text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody id="tbody-local">
            <?php if (empty($employees)): ?>
              <tr class="empty-row">
                <td colspan="5" class="text-center text-muted py-5">No staff members assigned to this branch yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($employees as $emp): ?>
                <tr class="emp-row" data-role="<?= strtolower($emp['Emp_Position']) ?>" data-branch="">
                  <td class="ps-4 text-secondary">#<?= htmlspecialchars($emp['Emp_Id']) ?></td>
                  <td><span class="fw-bold text-dark d-block"><?= htmlspecialchars($emp['Emp_Fname'] . ' ' . $emp['Emp_Lname']) ?></span></td>
                  <td><span class="badge bg-light text-secondary border px-2 py-1"><?= htmlspecialchars($emp['Emp_Position']) ?></span></td>
                  <td class="text-secondary"><?= htmlspecialchars($emp['Emp_Email']) ?></td>
                  <td class="text-end pe-4">
                    <div class="d-inline-flex gap-1">
                      <a href="<?= BASE_URL ?>/?r=admin/admin/editEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Staff Member"><i class="bi bi-pencil"></i></a>
                      <a href="<?= BASE_URL ?>/?r=admin/admin/deleteEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete <?= htmlspecialchars($emp['Emp_Fname'] . ' ' . $emp['Emp_Lname']) ?>? This cannot be undone.')" title="Remove Staff Member"><i class="bi bi-trash"></i></a>
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
<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const setupFilter = (containerId) => {
      const container = document.getElementById(containerId);
      if (!container) return;

      const searchInput = container.querySelector('.search-input');
      const roleFilter = container.querySelector('.role-filter');
      const branchFilter = container.querySelector('.branch-filter');
      const rows = container.querySelectorAll('.emp-row');

      const applyFilters = () => {
        const sText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const rText = roleFilter ? roleFilter.value.toLowerCase().trim() : '';
        const bText = branchFilter ? branchFilter.value.toLowerCase().trim() : '';

        rows.forEach(row => {
          const rowText = row.textContent.toLowerCase();
          const rowRole = row.getAttribute('data-role') || '';
          const rowBranch = row.getAttribute('data-branch') || '';

          const matchesSearch = rowText.includes(sText);
          const matchesRole = rText === '' || rowRole === rText;
          const matchesBranch = bText === '' || rowBranch === bText;

          row.style.display = (matchesSearch && matchesRole && matchesBranch) ? '' : 'none';
        });
      };

      if (searchInput) searchInput.addEventListener('input', applyFilters);
      if (roleFilter) roleFilter.addEventListener('change', applyFilters);
      if (branchFilter) branchFilter.addEventListener('change', applyFilters);
    };

    setupFilter('container-admins');
    setupFilter('container-all');
    setupFilter('container-local');
  });

  let sortDirection = false;

  function sortEmpTable(tbodyId, colIndex) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('.emp-row'));

    sortDirection = !sortDirection;

    rows.sort((a, b) => {
      const valA = a.children[colIndex].textContent.trim().toLowerCase();
      const valB = b.children[colIndex].textContent.trim().toLowerCase();
      return sortDirection ? valA.localeCompare(valB) : valB.localeCompare(valA);
    });

    rows.forEach(row => tbody.appendChild(row));
  }
</script>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>