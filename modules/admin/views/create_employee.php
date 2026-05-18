<?php
$branches = $branches ?? [];
$flash = $flash ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Create Employee</title>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">PCX Employee Dashboard - Create Employee</h1>
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
                        <a class="nav-link dropdown-toggle active" href="#" id="userManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            User Management
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageUsers">Manage Users</a></li>
                            <li><a class="dropdown-item active" href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees">Manage Employees</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="productManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Product Management
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="productManagementDropdown">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?r=admin/admin/manageProducts">Manage Products</a></li>
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
                        <h2 class="h5 mb-0">Create New Employee</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createEmployee">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact" name="contact" maxlength="15" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" maxlength="255" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="Sales Representative">Sales Representative</option>
                                    <option value="Technician">Technician</option>
                                    <option value="Administrator">Administrator</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select class="form-select" id="branch_id" name="branch_id" required>
                                    <?php foreach ($branches as $branch): ?>
                                    <option value="<?= htmlspecialchars($branch['Branch_Id']) ?>"><?= htmlspecialchars($branch['Branch_Name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Create Employee</button>
                                <a href="<?= BASE_URL ?>/?r=admin/admin/manageEmployees" class="btn btn-secondary">Cancel</a>
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
