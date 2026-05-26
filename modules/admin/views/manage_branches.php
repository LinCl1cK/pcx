<?php
$branches    = $branches    ?? [];
$flash       = $flash       ?? null;
$employee    = $employee    ?? ($_SESSION['employee'] ?? []);
$navActive   = 'branches';
$pageTitle   = $pageTitle   ?? 'Branches — PCX Admin';
$pageHeading = $pageHeading ?? 'Manage Branches';
$pageSubtitle = 'Configure physical storefront networks, operational hubs, and contact lines.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="row g-4">
  <div class="col-xl-4">
    <div class="card border-0 shadow-sm sticky-xl-top" style="top: 1.5rem; z-index: 10;">
      <div class="card-header">
        <span class="card-title">
          <i class="bi bi-plus-circle" style="color:var(--blue);margin-right:.4rem;"></i>Add New Branch
        </span>
      </div>
      <div class="card-body">
        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createBranch">
          <div style="margin-bottom:1rem;">
            <label class="form-label" for="add_name">Branch Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="add_name" name="name" maxlength="100" placeholder="e.g. Cebu Main Branch" required>
          </div>
          
          <div style="margin-bottom:1rem;">
            <label class="form-label" for="add_location">Location Address <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="add_location" name="location" maxlength="150" placeholder="e.g. North Reclamation Area, Cebu City" required>
          </div>
          
          <div style="margin-bottom:1.25rem;">
            <label class="form-label" for="add_contact">Contact Number <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="add_contact" name="contact" maxlength="15" placeholder="e.g. (032) 234-5678" required>
          </div>

          <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-building-add me-1"></i> Register Branch
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-xl-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <span class="card-title">
          <i class="bi bi-building" style="color:var(--blue);margin-right:.4rem;"></i>Active Branches
        </span>
        <span class="badge bg-secondary"><?= count($branches) ?> Branches</span>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0" style="font-size:.875rem;">
          <thead class="table-light text-secondary" style="font-size:.78rem; text-uppercase: uppercase; letter-spacing: 0.5px;">
            <tr>
              <th style="width: 70px; padding-left: 1.1rem;">ID</th>
              <th>Branch Details</th>
              <th>Location</th>
              <th>Contact Line</th>
              <th style="width: 140px; text-align: right; padding-right: 1.1rem;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($branches)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  <i class="bi bi-building-dash fs-3 d-block mb-2"></i> No store branches configured.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($branches as $branch): ?>
                <tr>
                  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateBranch">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $branch['Branch_Id']) ?>">
                    
                    <td style="padding-left: 1.1rem;" class="fw-medium text-secondary">
                      #<?= htmlspecialchars((string) $branch['Branch_Id']) ?>
                    </td>
                    
                    <td>
                      <input class="form-control form-control-sm fw-bold" name="name" maxlength="100" 
                        value="<?= htmlspecialchars((string) $branch['Branch_Name']) ?>" required style="max-width:180px;">
                      <small class="text-muted d-block mt-1" style="font-size: 11px;">
                        Registered: <?= date('Y-m-d', strtotime($branch['Branch_CreatedAt'])) ?>
                      </small>
                    </td>
                    
                    <td>
                      <input class="form-control form-control-sm text-secondary" name="location" maxlength="150" 
                        value="<?= htmlspecialchars((string) $branch['Branch_Location']) ?>" required style="min-width:180px;">
                    </td>
                    
                    <td>
                      <input class="form-control form-control-sm text-secondary" name="contact" maxlength="15" 
                        value="<?= htmlspecialchars((string) $branch['Branch_ContactNo']) ?>" required style="max-width:130px;">
                    </td>
                    
                    <td style="text-align: right; padding-right: 1.1rem;">
                      <div class="d-inline-flex gap-1">
                        <button class="btn btn-sm btn-primary" type="submit" title="Save changes to this row">
                          <i class="bi bi-check-lg"></i> Save
                        </button>
                        <a class="btn btn-sm btn-outline-danger" 
                           href="<?= BASE_URL ?>/?r=admin/admin/deleteBranch&id=<?= urlencode((string) $branch['Branch_Id']) ?>" 
                           onclick="return confirm('Delete this branch permanently?')" title="Delete Branch">
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

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>