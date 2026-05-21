<?php
$category = $category ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'categories';
$pageTitle = $pageTitle ?? 'Edit Category';
$pageHeading = $pageHeading ?? 'Edit Category';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Edit Category</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateCategory">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($category['Cat_Id']) ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['Cat_Name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($category['Cat_Description'] ?? '') ?></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Category</button>
                                <a href="<?= BASE_URL ?>/?r=admin/admin/manageCategories" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
