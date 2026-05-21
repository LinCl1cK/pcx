<?php
$user = $user ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'users';
$pageTitle = $pageTitle ?? 'Edit Customer';
$pageHeading = $pageHeading ?? 'Edit Customer';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Edit User</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateUser">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($user['Cus_Id']) ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname" value="<?= htmlspecialchars($user['Cus_Fname']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" value="<?= htmlspecialchars($user['Cus_Lname']) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['Cus_Email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($user['Cus_ContactNo']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($user['Cus_Address']) ?></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <a href="<?= BASE_URL ?>/?r=admin/admin/manageUsers" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
