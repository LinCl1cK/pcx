<?php
$stocks = $stocks ?? [];
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'inventory';
$pageTitle = $pageTitle ?? 'Inventory';
$pageHeading = $pageHeading ?? 'Inventory';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PCX Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</head>
<body>
  <div class="alert alert-info">Read-only branch inventory. Stock changes are performed by administrators.</div>
  <div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-striped align-middle mb-0">
      <thead><tr><th>Product</th><th>Brand</th><th>Branch</th><th>Stock</th><th>Reorder</th><th>Updated</th></tr></thead>
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
</body>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
