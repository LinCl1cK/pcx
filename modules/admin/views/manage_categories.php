<?php
$categories = $categories ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'categories';
$pageTitle = $pageTitle ?? 'Categories';
$pageHeading = $pageHeading ?? 'Manage Categories';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Categories</h2>
            <a href="<?= BASE_URL ?>/?r=admin/admin/createCategory" class="btn btn-primary">Add New Category</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['Cat_Id']) ?></td>
                        <td><?= htmlspecialchars($cat['Cat_Name']) ?></td>
                        <td><?= htmlspecialchars($cat['Cat_Description'] ?? '') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/editCategory&id=<?= urlencode($cat['Cat_Id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/deleteCategory&id=<?= urlencode($cat['Cat_Id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
