<?php
$categories   = $categories   ?? [];
$subcategories = $subcategories ?? [];
$flash        = $flash   ?? null;
$employee     = $employee ?? ($_SESSION['employee'] ?? []);
$navActive    = 'products';
$pageTitle    = 'Create Product — PCX Hub';
$pageHeading  = 'Catalog Procurement';
$pageSubtitle = 'Add a newly verified commercial product component into index registry.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:820px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createProduct" enctype="multipart/form-data">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-info-circle" style="color:var(--blue);margin-right:.4rem;"></i>Core Product Specifications
          </span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageProducts" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div style="grid-column:1/-1;">
            <label class="form-label" for="name">Product Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="name" name="name"
              placeholder="e.g. Intel Core i9-14900K Processor" required>
          </div>

          <div>
            <label class="form-label" for="brand">Brand / OEM <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="brand" name="brand" placeholder="e.g. Intel" required>
          </div>

          <div>
            <label class="form-label" for="price">MSRP Price (PHP) <span style="color:var(--red)">*</span></label>
            <input type="number" step="0.01" min="0.01" class="form-control" id="price" name="price"
              placeholder="0.00" required>
          </div>

          <div>
            <label class="form-label" for="warranty">Warranty (months) <span style="color:var(--red)">*</span></label>
            <input type="number" class="form-control" id="warranty" name="warranty"
              min="0" max="36" placeholder="e.g. 12" required>
            <p class="form-hint">Enter 0 for no warranty. Maximum 36 months.</p>
          </div>

          <div>
            <label class="form-label" for="status">System Status <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="status" name="status" required>
              <option value="Active">Active Listing</option>
              <option value="Inactive">Inactive / Suspended</option>
              <option value="Discontinued">Discontinued Product</option>
            </select>
          </div>

          <div style="grid-column:1/-1;">
            <label class="form-label" for="description">Technical Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"
              placeholder="Enter exhaustive parameters, architectural metrics, compatibility notes…"></textarea>
          </div>

        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-tags" style="color:var(--blue);margin-right:.4rem;"></i>Classification
          </span>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="cat_id">Category <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="cat_id" name="cat_id" required>
              <option value="">— Select category —</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['Cat_Id']) ?>">
                  <?= htmlspecialchars($cat['Cat_Name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="form-label" for="subcategories">Subcategories</label>
            <select class="form-select" id="subcategories" name="subcategories[]" multiple style="min-height:100px;">
              <?php foreach ($subcategories as $subcat): ?>
                <option value="<?= htmlspecialchars($subcat['Subc_Id']) ?>">
                  <?= htmlspecialchars($subcat['Subc_Name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <p class="form-hint">Hold Ctrl / ⌘ to select multiple.</p>
          </div>

        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-image" style="color:var(--blue);margin-right:.4rem;"></i>Media
          </span>
        </div>
        <div class="card-body">
          <label class="form-label" for="image">Product Image <span style="color:var(--red)">*</span></label>
          <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
          <p class="form-hint">Upload a high-quality product image. Recommended size: 800×800 px. It will be stored in <code>/assets/images/products/</code>.</p>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-toggles" style="color:var(--blue);margin-right:.4rem;"></i>Options
          </span>
        </div>
        <div class="card-body">
          <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;font-size:.875rem;color:var(--gray-700);">
            <input type="checkbox" class="form-check-input" id="featured" name="featured">
            <span>
              <strong>Featured Product</strong>
              <span class="form-hint" style="display:block;margin-top:0;">Elevates item indexing priority to storefront home displays.</span>
            </span>
          </label>
        </div>
      </div>

      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/manageProducts" class="btn btn-secondary">
          <i class="bi bi-x"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> Register Product
        </button>
      </div>

    </div>
  </form>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>