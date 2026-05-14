<?php
$users = $users ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'users';
$pageTitle = 'Manage Customers';
$pageHeading = 'Manage customers';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
        <?php if ($flash): ?>
        <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars((string) ($flash['message'] ?? '')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Users</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['Cus_Id']) ?></td>
                        <td><?= htmlspecialchars($user['Cus_Fname'] . ' ' . $user['Cus_Lname']) ?></td>
                        <td><?= htmlspecialchars($user['Cus_Email']) ?></td>
                        <td><?= htmlspecialchars($user['Cus_ContactNo']) ?></td>
                        <td><?= htmlspecialchars($user['Cus_Address']) ?></td>
                        <td><?= htmlspecialchars($user['Cus_CreatedAt']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/editUser&id=<?= urlencode($user['Cus_Id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/deleteUser&id=<?= urlencode($user['Cus_Id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>