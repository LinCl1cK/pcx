<?php
declare(strict_types=1);
$stock = $stock ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'inventory';
$pageTitle = $pageTitle ?? 'Edit inventory';
$pageHeading = $pageHeading ?? 'Edit inventory';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h2 class="h5 mb-1">Stock settings</h2>
    <p class="text-muted mb-0">Update quantity and reorder threshold for this inventory row.</p>
  </div>
  <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/?r=inventory/inventory/list">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

<?php if ($stock): ?>
  <form method="post" action="<?= BASE_URL ?>/?r=inventory/inventory/edit" class="card border-0 shadow-sm" style="max-width:520px">
    <div class="card-body">
      <input type="hidden" name="Inv_Id" value="<?= htmlspecialchars((string) $stock['Inv_Id']) ?>">
      <div class="mb-3">
        <label class="form-label" for="stockQty">Stock Qty</label>
        <input class="form-control" id="stockQty" type="number" min="0" name="Inv_StockQty" value="<?= (int) $stock['Inv_StockQty'] ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="reorderLevel">Reorder Level</label>
        <input class="form-control" id="reorderLevel" type="number" min="0" name="Inv_ReorderLevel" value="<?= (int) $stock['Inv_ReorderLevel'] ?>" required>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Save changes</button>
        <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/?r=inventory/inventory/list">Cancel</a>
      </div>
    </div>
  </form>
<?php else: ?>
  <div class="alert alert-warning">Record not found.</div>
<?php endif; ?>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
