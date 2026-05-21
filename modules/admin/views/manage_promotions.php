<?php
$promotions = $promotions ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = 'promotions';
$pageTitle = $pageTitle ?? 'Promotions';
$pageHeading = $pageHeading ?? 'Promotions';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h5 mb-0">Promotions</h2>
      <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/?r=admin/admin/createPromotion">Add promotion</a>
    </div>
    <div class="table-responsive bg-white rounded shadow-sm">
      <table class="table table-striped mb-0 align-middle">
        <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Start</th><th>End</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($promotions as $p): ?>
            <tr>
              <td><?= (int) $p['Promo_Id'] ?></td>
              <td><?= htmlspecialchars((string) $p['Promo_Title']) ?></td>
              <td><?= htmlspecialchars((string) $p['Promo_Status']) ?></td>
              <td><?= htmlspecialchars((string) ($p['Promo_Start'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string) ($p['Promo_End'] ?? '')) ?></td>
              <td>
                <a class="btn btn-sm btn-outline-danger" href="<?= BASE_URL ?>/?r=admin/admin/deletePromotion&id=<?= (int) $p['Promo_Id'] ?>" onclick="return confirm('Delete this promotion?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
