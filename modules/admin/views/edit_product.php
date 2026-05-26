<?php
$product              = $product              ?? [];
$categories           = $categories           ?? [];
$subcategories        = $subcategories        ?? [];
$selectedSubcategories = $selectedSubcategories ?? [];
$flash                = $flash    ?? null;
$employee             = $employee ?? ($_SESSION['employee'] ?? []);
$navActive            = 'products';
$pageTitle            = $pageTitle   ?? 'Edit Product — PCX Admin';
$pageHeading          = $pageHeading ?? 'Edit Product';
$pageSubtitle         = htmlspecialchars($product['Prod_Name'] ?? '');
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:780px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateProduct">
    <input type="hidden" name="id" value="<?= htmlspecialchars($product['Prod_Id'] ?? '') ?>">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <!-- ── Basic Info ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-box-seam" style="color:var(--blue);margin-right:.4rem;"></i>Basic Information</span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageProducts" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div style="grid-column:1/-1;">
            <label class="form-label" for="name">Product Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="name" name="name"
              value="<?= htmlspecialchars($product['Prod_Name'] ?? '') ?>" required>
          </div>

          <div>
            <label class="form-label" for="brand">Brand <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="brand" name="brand"
              value="<?= htmlspecialchars($product['Prod_Brand'] ?? '') ?>" required>
          </div>

          <div>
            <label class="form-label" for="status">Status <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="status" name="status" required>
              <?php foreach (['Active','Inactive','Discontinued'] as $s): ?>
              <option value="<?= $s ?>" <?= ($product['Prod_Status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="form-label" for="price">Price (PHP) <span style="color:var(--red)">*</span></label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price"
              value="<?= htmlspecialchars($product['Prod_Price'] ?? '') ?>" required>
          </div>

          <div>
            <label class="form-label" for="warranty">Warranty (months) <span style="color:var(--red)">*</span></label>
            <input type="number" class="form-control" id="warranty" name="warranty" min="0" max="36"
              value="<?= htmlspecialchars($product['Prod_Warranty'] ?? '') ?>" required>
          </div>

          <div style="grid-column:1/-1;">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"
              ><?= htmlspecialchars($product['Prod_Description'] ?? '') ?></textarea>
          </div>

        </div>
      </div>

      <!-- ── Classification ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-tags" style="color:var(--blue);margin-right:.4rem;"></i>Classification</span>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="cat_id">Category <span style="color:var(--red)">*</span></label>
            <select class="form-select" id="cat_id" name="cat_id" required>
              <option value="">— Select category —</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat['Cat_Id']) ?>"
                <?= ($product['Prod_CatId'] ?? '') === $cat['Cat_Id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['Cat_Name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="form-label" for="subcategories">Subcategories</label>
            <select class="form-select" id="subcategories" name="subcategories[]" multiple>
              <?php foreach ($subcategories as $subcat): ?>
              <option value="<?= htmlspecialchars($subcat['Subc_Id']) ?>"
                <?= in_array($subcat['Subc_Id'], $selectedSubcategories, true) ? 'selected' : '' ?>>
                <?= htmlspecialchars($subcat['Subc_Name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
            <p class="form-hint">Hold Ctrl / ⌘ to select multiple.</p>
          </div>

        </div>
      </div>

      <!-- ── Media ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-image" style="color:var(--blue);margin-right:.4rem;"></i>Media</span>
        </div>
        <div class="card-body">
          <label class="form-label" for="image">Image URL <span style="color:var(--red)">*</span></label>
          <input type="url" class="form-control" id="image" name="image"
            value="<?= htmlspecialchars($product['Prod_Image'] ?? '') ?>" required>
          <p class="form-hint">Paste a publicly accessible image URL. Recommended size: 800×800 px.</p>
        </div>
      </div>

      <!-- ── Options ── -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-toggles" style="color:var(--blue);margin-right:.4rem;"></i>Options</span>
        </div>
        <div class="card-body">
          <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;font-size:.875rem;color:var(--gray-700);">
            <input type="checkbox" class="form-check-input" id="featured" name="featured"
              <?= !empty($product['Prod_Featured']) ? 'checked' : '' ?>>
            <span>
              <strong>Featured Product</strong>
              <span class="form-hint" style="display:block;margin-top:0;">Highlight on the storefront homepage.</span>
            </span>
          </label>
        </div>
      </div>

      <!-- ── Actions ── -->
      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/manageProducts" class="btn btn-secondary">
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
