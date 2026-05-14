<?php
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$pageTitle = $pageTitle ?? 'PCX Staff';
require __DIR__ . '/header.php';
?>
<header class="bg-primary text-white py-3">
  <div class="container-fluid px-3">
    <div class="d-flex justify-content-between align-items-center">
      <h1 class="h5 mb-0"><?= htmlspecialchars((string) ($pageHeading ?? 'PCX Employee')) ?></h1>
      <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/?r=catalog/product/home">Storefront</a>
    </div>
  </div>
</header>
<?php require __DIR__ . '/nav.php'; ?>
<main class="container-fluid px-3 py-4">
