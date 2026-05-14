<?php
$stocks = $stocks ?? [];
$branches = $branches ?? [];
$flash = $flash ?? null;
$readOnly = !empty($readOnly);
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'inventory';
$pageTitle = $pageTitle ?? 'Inventory';
$pageHeading = $pageHeading ?? 'Branch inventory';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
<?php endif; ?>
<?php if ($readOnly): ?>
  <div class="alert alert-secondary">View-only inventory. Stock changes are performed by administrators.</div>
<?php endif; ?>

<div class="table-responsive bg-white rounded shadow-sm mb-3">
  <table class="table table-striped mb-0 align-middle">
    <thead>
      <tr><th>Product</th><th>Branch</th><th>Stock</th><th>Reorder</th><th>Updated</th><?php if (!$readOnly): ?><th></th><?php endif; ?></tr>
    </thead>
    <tbody>
    <?php foreach ($stocks as $s): ?>
      <tr>
        <td><?= htmlspecialchars((string) $s['Prod_Name']) ?></td>
        <td><?= htmlspecialchars((string) $s['Branch_Name']) ?></td>
        <td><?= (int) $s['Inv_StockQty'] ?></td>
        <td><?= (int) $s['Inv_ReorderLevel'] ?></td>
        <td><small><?= htmlspecialchars((string) $s['Inv_LastUpdated']) ?></small></td>
        <?php if (!$readOnly): ?>
        <td><a href="<?= BASE_URL ?>/?r=inventory/inventory/edit&id=<?= urlencode((string) $s['Inv_Id']) ?>" class="btn btn-sm btn-primary">Edit</a></td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if (!$readOnly): ?>
<div class="card border-0 shadow-sm">
  <div class="card-body">
    <h2 class="h6">Branch transfer</h2>
    <form method="post" action="<?= BASE_URL ?>/?r=inventory/inventory/transfer" class="row g-2">
      <div class="col-md-3"><input class="form-control" name="Inv_ProdId" placeholder="Prod_Id" required></div>
      <div class="col-md-3">
        <select class="form-select" name="from_branch"><?php foreach ($branches as $b): ?><option value="<?= htmlspecialchars((string) $b['Branch_Id']) ?>"><?= htmlspecialchars((string) $b['Branch_Name']) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="to_branch"><?php foreach ($branches as $b): ?><option value="<?= htmlspecialchars((string) $b['Branch_Id']) ?>"><?= htmlspecialchars((string) $b['Branch_Name']) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-2"><input class="form-control" type="number" min="1" name="qty" placeholder="Qty" required></div>
      <div class="col-md-1"><button class="btn btn-dark w-100" type="submit">Go</button></div>
    </form>
  </div>
</div>
<?php endif; ?>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
