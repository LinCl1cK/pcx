<?php
$subcategories = $subcategories ?? [];
$flash         = $flash ?? null;
$employee      = $employee ?? ($_SESSION['employee'] ?? []);
$navActive     = 'subcategories';
$pageTitle     = $pageTitle ?? 'Subcategories — PCX Admin';
$pageHeading   = $pageHeading ?? 'Manage Subcategories';
$pageSubtitle  = 'Organize deep catalog classifications and attributes.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="row g-4">
  
  <div class="col-12 col-xl-4">
    <div class="card border-0 shadow-sm sticky-xl-top" style="top: 1.5rem; z-index: 10;">
      <div class="card-header bg-white py-3">
        <span class="card-title fw-bold text-dark mb-0">
          <i class="bi bi-plus-circle text-blue me-2"></i>Add Subcategory
        </span>
      </div>
      <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createSubcategory">
          <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary" for="name">Subcategory Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" maxlength="50" placeholder="e.g. Mechanical Keyboards" required>
          </div>
          <div class="mb-4">
            <label class="form-label small fw-semibold text-secondary" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" maxlength="255" rows="3" placeholder="Brief details..."></textarea>
          </div>
          <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-tag-fill me-1"></i> Create Subcategory
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-8">
    <div class="card border-0 shadow-sm bg-white">
      <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <span class="card-title fw-bold text-dark mb-0">
          <i class="bi bi-tags text-blue me-2"></i>Active Subcategories
        </span>
        <span class="badge bg-light text-secondary border"><?= count($subcategories) ?> Entries</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0" style="font-size:.875rem;">
            <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
              <tr>
                <th class="ps-4 py-3" style="width: 70px;">ID</th>
                <th>Name</th>
                <th>Description</th>
                <th class="text-end pe-4" style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($subcategories)): ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-5">
                    <i class="bi bi-bookmark-dash fs-3 d-block mb-2"></i> No subcategories defined.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($subcategories as $subcat): ?>
                <tr>
                  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateSubcategory">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $subcat['Subc_Id']) ?>">
                    
                    <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars((string) $subcat['Subc_Id']) ?></td>
                    
                    <td>
                      <input type="text" class="form-control form-control-sm fw-bold" name="name" maxlength="50" 
                        value="<?= htmlspecialchars((string) $subcat['Subc_Name']) ?>" required>
                    </td>
                    
                    <td>
                      <input type="text" class="form-control form-control-sm text-secondary" name="description" maxlength="255" 
                        value="<?= htmlspecialchars((string) ($subcat['Subc_Description'] ?? '')) ?>" placeholder="—">
                    </td>
                    
                    <td class="text-end pe-4">
                      <div class="d-inline-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" type="submit" title="Save Row">
                          <i class="bi bi-check-lg"></i>
                        </button>
                        <a class="btn btn-sm btn-outline-danger" href="<?= BASE_URL ?>/?r=admin/admin/deleteSubcategory&id=<?= urlencode((string) $subcat['Subc_Id']) ?>" onclick="return confirm('Delete this subcategory?')" title="Delete">
                          <i class="bi bi-trash"></i>
                        </a>
                      </div>
                    </td>
                  </form>
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

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>