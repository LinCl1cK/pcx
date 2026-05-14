<?php $error = $error ?? null; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - PCX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h1 class="h4 mb-4">Register</h1>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/register">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">First Name</label>
                  <input name="fname" class="form-control" required maxlength="50">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Last Name</label>
                  <input name="lname" class="form-control" required maxlength="50">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required maxlength="255">
              </div>
              <div class="mb-3">
                <label class="form-label">Contact no. *</label>
                <input type="text" name="contact" class="form-control" required maxlength="15">
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
              <div class="d-grid">
                <button class="btn btn-dark">Register</button>
              </div>
            </form>
            <p class="mt-4 mb-0">
              Already have an account?
              <a href="<?= BASE_URL ?>/?r=auth/auth/login" class="text-decoration-none">Login here</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
