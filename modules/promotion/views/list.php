<?php
$promotions = $promotions ?? [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Promotions - PCX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Latest Promotions</h1>
      <a href="<?= BASE_URL ?>/?r=catalog/product/home" class="btn btn-outline-secondary btn-sm">Back to Home</a>
    </div>

    <?php if (empty($promotions)): ?>
      <div class="alert alert-info">No active promotions found.</div>
    <?php else: ?>
      <div class="row">
        <?php foreach ($promotions as $promo): ?>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
              <img
                src="<?= BASE_URL ?>/assets/images/promos/<?= htmlspecialchars((string) $promo['banner']) ?>"
                class="card-img-top"
                alt="<?= htmlspecialchars((string) $promo['title']) ?>"
                style="height: 180px; object-fit: cover;"
              >
              <div class="card-body">
                <h2 class="h6 mb-2"><?= htmlspecialchars((string) $promo['title']) ?></h2>
                <p class="text-muted" style="max-height: 3.2em; overflow: hidden;">
                  <?= htmlspecialchars((string) $promo['description']) ?>
                </p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>

