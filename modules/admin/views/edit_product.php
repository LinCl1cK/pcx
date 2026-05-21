<?php
$product = $product ?? [];
$categories = $categories ?? [];
$subcategories = $subcategories ?? [];
$selectedSubcategories = $selectedSubcategories ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'products';
$pageTitle = $pageTitle ?? 'Edit Product';
$pageHeading = $pageHeading ?? 'Edit Product';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Edit Product</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateProduct">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($product['Prod_Id']) ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['Prod_Name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="brand" name="brand" value="<?= htmlspecialchars($product['Prod_Brand']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['Prod_Price']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="warranty" class="form-label">Warranty (months)</label>
                                    <input type="number" class="form-control" id="warranty" name="warranty" min="0" max="36" value="<?= htmlspecialchars($product['Prod_Warranty']) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="cat_id" class="form-label">Category</label>
                                <select class="form-select" id="cat_id" name="cat_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['Cat_Id']) ?>" <?= $product['Prod_CatId'] === $cat['Cat_Id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['Cat_Name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="subcategories" class="form-label">Subcategories</label>
                                <select class="form-select" id="subcategories" name="subcategories[]" multiple>
                                    <?php foreach ($subcategories as $subcat): ?>
                                    <option value="<?= htmlspecialchars($subcat['Subc_Id']) ?>" <?= in_array($subcat['Subc_Id'], $selectedSubcategories, true) ? 'selected' : '' ?>><?= htmlspecialchars($subcat['Subc_Name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="image" name="image" value="<?= htmlspecialchars($product['Prod_Image']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['Prod_Description'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Active" <?= $product['Prod_Status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= $product['Prod_Status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="Discontinued" <?= $product['Prod_Status'] === 'Discontinued' ? 'selected' : '' ?>>Discontinued</option>
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" <?= $product['Prod_Featured'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="featured">Featured Product</label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Product</button>
                                <a href="<?= BASE_URL ?>/?r=admin/admin/manageProducts" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
