<?php
$error = $error ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Login - PCX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <h1 class="h4 mb-3">Employee Login</h1>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars((string) $error) ?></div>
            <?php endif; ?>
            <p class="text-muted small">Employee sign-in now uses the shared login module.</p>
            <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/login">
              <input type="hidden" name="next" value="admin/admin/dashboard">
              <div class="mb-3">
                <label class="form-label">Employee Username</label>
                <input type="text" name="login_id" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-dark w-100" type="submit">Sign In</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
