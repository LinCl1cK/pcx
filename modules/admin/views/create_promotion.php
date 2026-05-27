<?php
$flash       = $flash    ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'promotions';
$pageTitle   = $pageTitle   ?? 'New Promotion — PCX Admin';
$pageHeading = $pageHeading ?? 'Create Promotion';
$pageSubtitle = 'Set up a new marketing promotion or sale event.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:700px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createPromotion" enctype="multipart/form-data">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-megaphone" style="color:var(--blue);margin-right:.4rem;"></i>Promotion Details</span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/managePromotions" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:1rem;">

          <div>
            <label class="form-label" for="title">Promotion Title <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="title" name="title"
              placeholder="e.g. Mid-Year Mega Sale" maxlength="255" required>
          </div>

          <div>
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2"
              placeholder="Briefly describe this promotion for internal reference…"></textarea>
          </div>

          <div>
            <label class="form-label" for="banner">Banner Image <span style="color:var(--red)">*</span></label>
            <input type="file" class="form-control" id="banner" name="banner" accept="image/*" required>
            <p class="form-hint">Upload your promotional banner. It will be securely stored in <code>/assets/images/promos/</code>.</p>
          </div>

          <div>
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status" style="max-width:200px;">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-calendar-event" style="color:var(--blue);margin-right:.4rem;"></i>Schedule</span>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="start">Start Date</label>
            <input type="date" class="form-control" id="start" name="start">
          </div>
          <div>
            <label class="form-label" for="end">End Date</label>
            <input type="date" class="form-control" id="end" name="end">
          </div>
          <div style="grid-column:1/-1;">
            <p class="form-hint">Leave blank for an open-ended promotion.</p>
          </div>

        </div>
      </div>

      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/managePromotions" class="btn btn-secondary">
          <i class="bi bi-x"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-megaphone"></i> Create Promotion
        </button>
      </div>

    </div>
  </form>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>