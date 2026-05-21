<?php
$employees = $employees ?? [];
$branches = $branches ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'employees';
$pageTitle = $pageTitle ?? 'Employees';
$pageHeading = $pageHeading ?? 'Manage Employees';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Employees</h2>
            <a href="<?= BASE_URL ?>/?r=admin/admin/createEmployee" class="btn btn-primary">Add New Employee</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['Emp_Id']) ?></td>
                        <td><?= htmlspecialchars($emp['Emp_Fname'] . ' ' . $emp['Emp_Lname']) ?></td>
                        <td><?= htmlspecialchars($emp['Emp_Position']) ?></td>
                        <td><?= htmlspecialchars($emp['Emp_Email']) ?></td>
                        <td><?= htmlspecialchars($emp['Branch_Name']) ?></td>
                        <td><?= htmlspecialchars($emp['Emp_CreatedAt']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/editEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?= BASE_URL ?>/?r=admin/admin/deleteEmployee&id=<?= urlencode($emp['Emp_Id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
