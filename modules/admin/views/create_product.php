<?php
$categories = $categories ?? [];
$subcategories = $subcategories ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'products';
$pageTitle = $pageTitle ?? 'Create Product';
$pageHeading = $pageHeading ?? 'Create Product';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Create New Product</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createProduct">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="brand" name="brand" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="warranty" class="form-label">Warranty (months)</label>
                                    <input type="number" class="form-control" id="warranty" name="warranty" min="0" max="36" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="cat_id" class="form-label">Category</label>
                                <select class="form-select" id="cat_id" name="cat_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['Cat_Id']) ?>"><?= htmlspecialchars($cat['Cat_Name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="subcategories" class="form-label">Subcategories</label>
                                <select class="form-select" id="subcategories" name="subcategories[]" multiple>
                                    <?php foreach ($subcategories as $subcat): ?>
                                    <option value="<?= htmlspecialchars($subcat['Subc_Id']) ?>"><?= htmlspecialchars($subcat['Subc_Name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="image" name="image" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Discontinued">Discontinued</option>
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured">
                                <label class="form-check-label" for="featured">Featured Product</label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Create Product</button>
                                <a href="<?= BASE_URL ?>/?r=admin/admin/manageProducts" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
