<?php
$product = $product ?? [];
$categories = $categories ?? [];
$subcategories = $subcategories ?? [];
$selectedSubcategories = $selectedSubcategories ?? [];
$flash = $flash ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Product</title>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">PCX Employee Dashboard - Edit Product</h1>
                <div class="d-flex align-items-center">
                    <span class="me-3">Welcome, <?= htmlspecialchars((string) ($employee['name'] ?? '')) ?> (<?= htmlspecialchars((string) ($employee['role'] ?? '')) ?>)</span>
                    <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/?r=auth/auth/employeeLogout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?r=admin/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            User Management
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageUsers">Manage Users</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees">Manage Employees</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="productManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Product Management
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="productManagementDropdown">
                            <li><a class="dropdown-item active" href="<?= BASE_URL ?>/?r=admin/admin/manageProducts">Manage Products</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageCategories">Manage Categories</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?r=verification/verification/index">Manual Verification</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?r=fulfillment/fulfillment/index">Fulfillment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?r=service/service/index">Service Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?r=order/order/index">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?r=inventory/inventory/index">Inventory</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

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

    <!-- Footer -->
    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 PCX. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
