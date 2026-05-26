<?php
declare(strict_types=1);
$stock = $stock ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'inventory';
$pageTitle = $pageTitle ?? 'Edit Inventory — PCX Admin';
$pageHeading = $pageHeading ?? 'Modify Stock Levels';
$pageSubtitle = 'Update specific quantity and alert thresholds for this branch record.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<?php if ($stock): ?>
  <div class="row">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
      <form method="post" action="<?= BASE_URL ?>/?r=inventory/inventory/edit" class="card border-0 shadow-sm bg-white">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <span class="card-title fw-bold text-dark mb-0">
            <i class="bi bi-box-seam text-blue me-2"></i>Stock Parameters
          </span>
          <span class="badge bg-light text-secondary border">ID: #<?= htmlspecialchars((string) $stock['Inv_Id']) ?></span>
        </div>
        <div class="card-body">
          <input type="hidden" name="Inv_Id" value="<?= htmlspecialchars((string) $stock['Inv_Id']) ?>">
          
          <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary" for="stockQty">Current Stock Quantity</label>
            <input class="form-control" id="stockQty" type="number" min="0" name="Inv_StockQty" value="<?= (int) $stock['Inv_StockQty'] ?>" required>
          </div>
          
          <div class="mb-4">
            <label class="form-label small fw-semibold text-secondary" for="reorderLevel">Automated Reorder Alert Level</label>
            <input class="form-control" id="reorderLevel" type="number" min="0" name="Inv_ReorderLevel" value="<?= (int) $stock['Inv_ReorderLevel'] ?>" required>
            <p class="form-hint small text-muted mt-2">The system will flag this item in red when stock falls below this threshold.</p>
          </div>
          
          <div class="d-flex gap-2 justify-content-end pt-3 border-top">
            <a class="btn btn-light border px-4" href="<?= BASE_URL ?>/?r=inventory/inventory/list">Cancel</a>
            <button class="btn btn-primary px-4" type="submit"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php else: ?>
  <div class="alert alert-warning border-0 bg-warning-light text-dark d-flex align-items-center mb-4 rounded-3 p-3 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle fs-5 me-3 text-warning"></i>
    <span class="small fw-medium">Inventory record not found or inaccessible.</span>
  </div>
<?php endif; ?>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>