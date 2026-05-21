<?php
$products = $products ?? [];
$categories = $categories ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'products';
$pageTitle = $pageTitle ?? 'Products';
$pageHeading = $pageHeading ?? 'Manage Products';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Products</h2>
            <a href="<?= BASE_URL ?>/?r=admin/admin/createProduct" class="btn btn-primary">Add New Product</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['Prod_Id']) ?></td>
                        <td><?= htmlspecialchars($product['Prod_Name']) ?></td>
                        <td><?= htmlspecialchars($product['Prod_Brand']) ?></td>
                        <td>PHP <?= number_format((float) $product['Prod_Price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['Cat_Name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($product['Prod_Status']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/editProduct&id=<?= urlencode($product['Prod_Id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/deleteProduct&id=<?= urlencode($product['Prod_Id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>