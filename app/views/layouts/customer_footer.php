  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">Sign In</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/login" id="loginForm">
            <input type="hidden" name="next" value="<?= htmlspecialchars($_GET['next'] ?? 'catalog/product/home') ?>">
            <div class="mb-3">
              <label class="form-label">Customer email or employee work email *</label>
              <input type="text" name="login_id" class="form-control" required autocomplete="username">
            </div>
            <div class="mb-3">
              <label class="form-label">Password *</label>
              <input type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>
            <div id="loginError" class="alert alert-danger d-none"></div>
            <div class="d-grid">
              <button type="submit" class="btn btn-dark">Sign In</button>
            </div>
          </form>
        </div>
        <div class="modal-footer border-0">
          <p class="mb-0 small text-muted">Staff: use your work email and password. Customers: use registered email.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">Create Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/register" id="registerForm">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">First Name *</label>
                <input type="text" name="fname" class="form-control" required maxlength="50">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Last Name *</label>
                <input type="text" name="lname" class="form-control" required maxlength="50">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-control" required maxlength="255">
            </div>
            <div class="mb-3">
              <label class="form-label">Contact No. *</label>
              <input type="text" name="contact" class="form-control" required maxlength="15" pattern="[0-9+\-\s]{7,15}">
            </div>
            <div class="mb-3">
              <label class="form-label">Address *</label>
              <input type="text" name="address" class="form-control" required maxlength="255">
            </div>
            <div class="mb-3">
              <label class="form-label">Password *</label>
              <input type="password" name="password" class="form-control" minlength="8" required>
              <div class="form-text">At least 8 characters with upper, lower, digit, and symbol.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm Password *</label>
              <input type="password" name="confirm_password" class="form-control" minlength="8" required>
            </div>
            <div id="registerError" class="alert alert-danger d-none"></div>
            <div class="d-grid">
              <button type="submit" class="btn btn-dark">Register</button>
            </div>
          </form>
        </div>
        <div class="modal-footer border-0">
          <p class="mb-0 small">You will stay signed out until you log in.</p>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
