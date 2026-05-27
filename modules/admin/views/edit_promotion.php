<?php
$promotion   = $promotion ?? [];
$flash       = $flash     ?? null;
$employee    = $employee  ?? ($_SESSION['employee'] ?? []);
$navActive   = 'promotions';
$pageTitle   = $pageTitle   ?? 'Edit Promotion — PCX Admin';
$pageHeading = $pageHeading ?? 'Edit Promotion';
$pageSubtitle = htmlspecialchars($promotion['Promo_Title'] ?? '');
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:700px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updatePromotion">
    <input type="hidden" name="id" value="<?= htmlspecialchars((string)($promotion['Promo_Id'] ?? '')) ?>">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <!-- ── Promotion Details ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-megaphone" style="color:var(--blue);margin-right:.4rem;"></i>Promotion Details
          </span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/managePromotions" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:1rem;">

          <div>
            <label class="form-label" for="title">Promotion Title <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="title" name="title"
              value="<?= htmlspecialchars((string)($promotion['Promo_Title'] ?? '')) ?>"
              maxlength="255" required>
          </div>

          <div>
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2"
              placeholder="Briefly describe this promotion for internal reference…"><?= htmlspecialchars((string)($promotion['Promo_Description'] ?? '')) ?></textarea>
          </div>

          <div>
            <label class="form-label" for="banner">Banner Image Filename <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="banner" name="banner"
              value="<?= htmlspecialchars((string)($promotion['Promo_Banner'] ?? '')) ?>"
              placeholder="e.g. midyear-sale-2025.webp" required>
            <p class="form-hint">Enter only the filename. The file must already be uploaded to <code>/assets/banners/</code>.</p>
          </div>

          <div>
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status" style="max-width:200px;">
              <option value="Active"   <?= ($promotion['Promo_Status'] ?? '') === 'Active'   ? 'selected' : '' ?>>Active</option>
              <option value="Inactive" <?= ($promotion['Promo_Status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>

        </div>
      </div>

      <!-- ── Schedule ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-calendar-event" style="color:var(--blue);margin-right:.4rem;"></i>Schedule
          </span>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="start">Start Date</label>
            <input type="date" class="form-control" id="start" name="start"
              value="<?= htmlspecialchars((string)($promotion['Promo_Start'] ?? '')) ?>">
          </div>
          <div>
            <label class="form-label" for="end">End Date</label>
            <input type="date" class="form-control" id="end" name="end"
              value="<?= htmlspecialchars((string)($promotion['Promo_End'] ?? '')) ?>">
          </div>
          <div style="grid-column:1/-1;">
            <p class="form-hint">Leave blank for an open-ended promotion.</p>
          </div>

        </div>
      </div>

      <!-- ── Danger Zone ── -->
      <div class="card" style="border-color:var(--red-mid);">
        <div class="card-header" style="background:var(--red-light);border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
          <span class="card-title" style="color:#991B1B;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Danger Zone
          </span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
          <div>
            <p style="font-size:.875rem;font-weight:500;color:var(--gray-800);margin:0;">Delete this promotion</p>
            <p style="font-size:.78rem;color:var(--gray-500);margin:0;">This will permanently remove the campaign. This action cannot be undone.</p>
          </div>
          <a href="<?= BASE_URL ?>/?r=admin/admin/deletePromotion&id=<?= (int)($promotion['Promo_Id'] ?? 0) ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Delete this promotion permanently? This cannot be undone.')">
            <i class="bi bi-trash3"></i> Delete Promotion
          </a>
        </div>
      </div>

      <!-- ── Actions ── -->
      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/managePromotions" class="btn btn-secondary">
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
