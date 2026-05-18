<?php
/** @var array $orders */
/** @var string|null $error */
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Service Request - PCX';
require_once dirname(__DIR__, 3) . '/app/core/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PCX Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</head>
<body>
  <div class="container py-5" style="max-width:640px">
    <h1 class="h4 mb-3">Request a service ticket</h1>
    <p class="text-muted small">For <strong>completed</strong> orders only. One ticket per order.</p>
    <?php if ($error ?? null): ?>
      <div class="alert alert-danger"><?= htmlspecialchars((string) $error) ?></div>
    <?php endif; ?>
    <?php if (empty($orders)): ?>
      <div class="alert alert-info">You have no completed orders eligible for a new ticket.</div>
    <?php else: ?>
      <form method="post" class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Order</label>
            <select name="order_id" class="form-select" required>
              <?php foreach ($orders as $o): ?>
                <option value="<?= htmlspecialchars((string) $o['Order_Id']) ?>">
                  <?= htmlspecialchars((string) $o['Order_InvoiceNo']) ?> — <?= htmlspecialchars((string) $o['Order_Date']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Describe the issue *</label>
            <textarea name="problem_info" class="form-control" rows="4" required minlength="5" maxlength="255" placeholder="Symptoms, when it started, etc."></textarea>
          </div>
          <button type="submit" class="btn btn-dark">Submit request</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
<?php require_once dirname(__DIR__, 3) . '/app/core/footer.php'; ?>
