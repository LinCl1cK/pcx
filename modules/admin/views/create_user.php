<?php
$flash       = $flash    ?? null;
$employee    = $employee ?? ($_SESSION['employee'] ?? []);
$navActive   = 'users';
$pageTitle   = $pageTitle   ?? 'Add Customer — PCX Admin';
$pageHeading = $pageHeading ?? 'Add Customer';
$pageSubtitle = 'Manually register a new customer account.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div style="max-width:740px;">
  <form method="post" action="<?= BASE_URL ?>/?r=admin/admin/createUser">
    <div style="display:flex;flex-direction:column;gap:1.1rem;">

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="bi bi-person-lines-fill" style="color:var(--blue);margin-right:.4rem;"></i>Customer Details</span>
          <a href="<?= BASE_URL ?>/?r=admin/admin/manageUsers" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;">

          <div>
            <label class="form-label" for="fname">First Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required>
          </div>
          <div>
            <label class="form-label" for="lname">Last Name <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" required>
          </div>
          <div>
            <label class="form-label" for="email">Email Address <span style="color:var(--red)">*</span></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" required>
          </div>
          <div>
            <label class="form-label" for="contact">Contact Number <span style="color:var(--red)">*</span></label>
            <input type="text" class="form-control" id="contact" name="contact" maxlength="15" placeholder="09XXXXXXXXX" required>
          </div>
          <div style="grid-column:1/-1;">
            <label class="form-label" for="address">Address <span style="color:var(--red)">*</span></label>
            <textarea class="form-control" id="address" name="address" rows="2" maxlength="255" placeholder="Complete Street Address" required></textarea>
          </div>
          <div style="grid-column:1/-1;">
            <label class="form-label" for="password">Temporary Password <span style="color:var(--red)">*</span></label>
            <input type="password" class="form-control" id="password" name="password" placeholder="At least 8 characters" style="max-width:300px;" required>
          </div>

        </div>
      </div>

      <div style="display:flex;gap:.6rem;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/?r=admin/admin/manageUsers" class="btn btn-secondary">
          <i class="bi bi-x"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-person-plus"></i> Register Customer
        </button>
      </div>

    </div>
  </form>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>