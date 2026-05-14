<?php
declare(strict_types=1);

$stock = $stock ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'inventory';
$pageTitle = $pageTitle ?? 'Edit inventory';
$pageHeading = $pageHeading ?? 'Edit inventory';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($stock): ?>
<form method="post" action="<?= BASE_URL ?>/?r=inventory/inventory/edit" class="card border-0 shadow-sm" style="max-width:480px">
  <div class="card-body">
    <input type="hidden" name="Inv_Id" value="<?= htmlspecialchars((string) $stock['Inv_Id']) ?>">
    <label class="form-label">Stock Qty</label>
    <input class="form-control mb-2" type="number" min="0" name="Inv_StockQty" value="<?= (int) $stock['Inv_StockQty'] ?>" required>
    <label class="form-label">Reorder Level</label>
    <input class="form-control mb-3" type="number" min="0" name="Inv_ReorderLevel" value="<?= (int) $stock['Inv_ReorderLevel'] ?>" required>
    <button class="btn btn-dark" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>/?r=inventory/inventory/list">Cancel</a>
  </div>
</form>
<?php else: ?>
  <div class="alert alert-warning">Record not found.</div>
<?php endif; ?>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
