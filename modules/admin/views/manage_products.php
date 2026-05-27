<?php
$products    = $products ?? [];
$categories  = $categories ?? [];
$flash       = $flash ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);

$rawRole = strtolower(trim($employee['Emp_Position'] ?? $employee['role'] ?? ''));
$isGeneralAdmin = ($rawRole === 'general admin');

$navActive   = 'products';
$pageTitle   = $pageTitle ?? 'Products — PCX Admin';
$pageHeading = $pageHeading ?? 'Manage Products';
$pageSubtitle = 'Monitor, update, and expand the primary storefront catalog.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <span class="card-title fw-bold text-dark mb-0">
        <i class="bi bi-boxes text-blue me-2"></i>Product Inventory
      </span>
      <span class="badge bg-light text-secondary border"><?= count($products) ?> Items</span>
    </div>
    
    <?php if ($isGeneralAdmin): ?>
      <a href="<?= BASE_URL ?>/?r=admin/admin/createProduct" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add New Product
      </a>
    <?php endif; ?>
  </div>
  
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">ID</th>
            <th>Product Name</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Status</th>
            <th class="text-end">Price</th>
            <?php if ($isGeneralAdmin): ?>
              <th class="text-end pe-4">Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr>
              <td colspan="<?= $isGeneralAdmin ? '7' : '6' ?>" class="text-center text-muted py-5">
                <i class="bi bi-inboxes fs-3 d-block mb-2"></i> No products listed in the catalog.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($products as $product): ?>
            <tr>
              <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars($product['Prod_Id']) ?></td>
              <td class="fw-bold text-dark">
                <?= htmlspecialchars($product['Prod_Name']) ?>
              </td>
              <td class="text-secondary"><?= htmlspecialchars($product['Prod_Brand']) ?></td>
              <td class="text-secondary"><?= htmlspecialchars($product['Cat_Name'] ?? 'Uncategorized') ?></td>
              <td>
                <?php if ($product['Prod_Status'] === 'Active'): ?>
                  <span class="badge bg-green-light text-success px-2 py-1"><i class="bi bi-circle-fill me-1" style="font-size:0.4rem;"></i>Active</span>
                <?php elseif ($product['Prod_Status'] === 'Inactive'): ?>
                  <span class="badge bg-light text-secondary border px-2 py-1">Inactive</span>
                <?php else: ?>
                  <span class="badge bg-red-light text-danger px-2 py-1">Discontinued</span>
                <?php endif; ?>
              </td>
              <td class="text-end fw-semibold text-blue">PHP <?= number_format((float) $product['Prod_Price'], 2) ?></td>
              
              <?php if ($isGeneralAdmin): ?>
              <td class="text-end pe-4">
                <div class="d-inline-flex gap-1">
                  <a href="<?= BASE_URL ?>/?r=admin/admin/editProduct&id=<?= urlencode($product['Prod_Id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Product">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="<?= BASE_URL ?>/?r=admin/admin/deleteProduct&id=<?= urlencode($product['Prod_Id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product permanently?')" title="Delete Product">
                    <i class="bi bi-trash"></i>
                  </a>
                </div>
              </td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>