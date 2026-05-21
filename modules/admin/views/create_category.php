<?php
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'categories';
$pageTitle = $pageTitle ?? 'Create Category';
$pageHeading = $pageHeading ?? 'Create Category';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Create New Category</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createCategory">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Create Category</button>
                                <a href="<?= BASE_URL ?>/?r=admin/admin/manageCategories" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
