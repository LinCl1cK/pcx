<?php
$orders = $orders ?? [];
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Order Tracking - PCX';
require_once __DIR__ . '/../../../app/core/header.php';
?>
<div class="container py-4">
  <h1 class="h4 mb-3">Order Tracking</h1>
  <div class="card border-0 shadow-sm pcx-tracking-card">
    <div class="card-body">
      <?php foreach ($orders as $order): ?>
        <?php
        $statuses = ['Pending', 'Confirmed', 'Paid', 'Completed'];
        $currentIdx = array_search((string) $order['Order_Status'], $statuses, true);
        $currentIdx = $currentIdx === false ? -1 : $currentIdx;
        ?>
        <div class="border rounded-3 p-3 mb-3">
          <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
            <div>
              <div class="fw-semibold"><?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></div>
              <small class="text-muted"><?= htmlspecialchars((string) $order['Order_Id']) ?> • <?= htmlspecialchars((string) $order['Order_Date']) ?></small>
            </div>
            <div class="text-end">
              <span class="badge text-bg-dark"><?= htmlspecialchars((string) $order['Order_Status']) ?></span>
              <div class="small mt-1">PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></div>
            </div>
          </div>
          <ul class="pcx-timeline">
            <?php foreach ($statuses as $idx => $status): ?>
              <li class="pcx-timeline-item">
                <span class="pcx-timeline-dot <?= $idx <= $currentIdx ? 'active' : '' ?>"></span>
                <div class="fw-semibold"><?= htmlspecialchars($status) ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>