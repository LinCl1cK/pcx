<?php
$branches = $branches ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'employees';
$pageTitle = $pageTitle ?? 'Create Employee';
$pageHeading = $pageHeading ?? 'Create Employee';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

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

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>

