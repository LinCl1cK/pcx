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

<form method="post" action=\"<?= BASE_URL ?>/?r=admin/admin/createProduct\">
  <div class="row g-4">
    
    <div class="col-12 col-lg-8">
      <div class="card border-0 shadow-sm bg-white mb-3">
        <div class="card-header bg-white py-3 fw-bold text-dark">
          <i class="bi bi-info-circle text-blue me-2"></i>Core Product Specifications
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary" for="name">Product Name <span class="text-red">*</span></label>
            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Intel Core i9-14900K Processor" required>
          </div>
          
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold text-secondary" for="brand">Brand / OEM <span class="text-red">*</span></label>
              <input type="text" class="form-control" id="brand" name="brand" placeholder="e.g. Intel" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold text-secondary" for="price">MSRP Price (PHP) <span class="text-red">*</span></label>
              <input type="number" step="0.01" class="form-control" id=\"price\" name=\"price\" placeholder=\"0.00\" required>
            </div>
          </div>

          <div class="mb-0">
            <label class="form-label small fw-semibold text-secondary" for="description">Technical Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter exhaustive parameters, architectural metrics..."></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card border-0 shadow-sm bg-white mb-4">
        <div class="card-header bg-white py-3 fw-bold text-dark">
          <i class="bi bi-sliders text-blue me-2"></i>Logistics Metadata
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary" for="status">System Status</label>
            <select class="form-select" id="status" name="status" required>
              <option value="Active">Active Listing</option>
              <option value="Inactive">Inactive / Suspended</option>
              <option value="Discontinued">Discontinued Product</option>
            </select>
          </div>

          <div class="p-3 bg-light rounded border mb-0">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="featured" name="featured">
              <label class="form-check-label small fw-medium text-dark" for="featured">Storefront Feature Visibility</label>
            </div>
            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Elevates current item indexing priority to site home displays.</small>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 justify-content-end">
        <a href="<?= BASE_URL ?>/?r=admin/admin/manageProducts" class="btn btn-light border px-4 small">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 small"><i class="bi bi-save me-1"></i> Register Product</button>
      </div>
    </div>

  </div>
</form>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>