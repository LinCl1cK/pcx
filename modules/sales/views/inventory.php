<?php
$stocks = $stocks ?? [];
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'inventory';
$pageTitle = $pageTitle ?? 'Inventory';
$pageHeading = $pageHeading ?? 'Inventory';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<div class="alert alert-info">Read-only branch inventory. Stock changes are performed by administrators.</div>
<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped align-middle mb-0">
    <thead>
      <tr>
        <th>Product</th>
        <th>Brand</th>
        <th>Branch</th>
        <th>Stock</th>
        <th>Reorder</th>
        <th>Updated</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($stocks as $stock): ?>
        <tr>
          <td><?= htmlspecialchars((string) $stock['Prod_Name']) ?></td>
          <td><?= htmlspecialchars((string) $stock['Prod_Brand']) ?></td>
          <td><?= htmlspecialchars((string) $stock['Branch_Name']) ?></td>
          <td><?= (int) $stock['Inv_StockQty'] ?></td>
          <td><?= (int) $stock['Inv_ReorderLevel'] ?></td>
          <td><small><?= htmlspecialchars((string) $stock['Inv_LastUpdated']) ?></small></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>