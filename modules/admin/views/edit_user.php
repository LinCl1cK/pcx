<?php
$user        = $user        ?? [];
$flash       = $flash       ?? null;
$employee    = $employee    ?? ($_SESSION['employee'] ?? []);
$navActive   = 'users';
$pageTitle   = $pageTitle   ?? 'Edit Customer — PCX Admin';
$pageHeading = $pageHeading ?? 'Edit Customer';
$pageSubtitle = htmlspecialchars(($user['Cus_Fname'] ?? '') . ' ' . ($user['Cus_Lname'] ?? ''));
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:740px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/updateUser">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user['Cus_Id'] ?? '') ?>">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <i class="bi bi-person" style="color:var(--blue);margin-right:.4rem;"></i>Customer Information
          </span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageUsers" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Users
          </a>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="fname">First Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="fname" name="fname" 
              value="<?= htmlspecialchars($user['Cus_Fname'] ?? '') ?>" placeholder="e.g. John" required>
          </div>

          <div>
            <label class="form-label" for="lname">Last Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="lname" name="lname" 
              value="<?= htmlspecialchars($user['Cus_Lname'] ?? '') ?>" placeholder="e.g. Doe" required>
          </div>

          <div style="grid-column:1/-1;">
            <label class="form-label" for="email">Email Address <span style="color:var(--red)">*</span></label>
            <input type="email" class="form-control" id="email" name="email" 
              value="<?= htmlspecialchars($user['Cus_Email'] ?? '') ?>" placeholder="e.g. john.doe@example.com" required>
          </div>

          <div style="grid-column:1/-1;">
            <label class="form-label" for="contact">Contact Number <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="contact" name="contact" 
              value="<?= htmlspecialchars($user['Cus_ContactNo'] ?? '') ?>" placeholder="e.g. 09123456789" required>
          </div>

          <div style="grid-column:1/-1;">
            <label class="form-label" for="address">Billing / Delivery Address <span style="color:var(--red)">*</span></label>
            <textarea class="form-control" id="address" name="address" rows="3" 
              placeholder="Complete street address, city, province..." required><?= htmlspecialchars($user['Cus_Address'] ?? '') ?></textarea>
          </div>

        </div>
      </div>

      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/manageUsers" class="btn btn-secondary">
          <i class="bi bi-x"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-lg"></i> Save Changes
        </button>
      </div>

    </div>
  </form>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>