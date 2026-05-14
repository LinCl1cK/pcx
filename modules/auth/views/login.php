<?php
$error = $error ?? null;
$next = $next ?? 'catalog/product/home';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account - PCX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h1 class="h4 mb-4">LOGIN</h1>
            <p class="text-muted small mb-3">Sign in with your customer or employee account</p>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/login">
              <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
              <div class="mb-3">
                <label class="form-label">Email or Employee Username *</label>
                <input type="text" name="login_id" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="d-grid">
                <button class="btn btn-dark">Sign In</button>
              </div>
            </form>
            <div class="mt-3">
              <a href="#" class="small text-decoration-none text-muted">Forgot your password?</a>
            </div>
            <p class="mt-4 mb-0">
              New customer?
              <a href="<?= BASE_URL ?>/?r=auth/auth/register" class="text-decoration-none">Create your account</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
