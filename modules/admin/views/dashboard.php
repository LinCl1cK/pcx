<?php
$employee      = $employee      ?? [];
$pendingOrders = $pendingOrders ?? [];
$lowStock      = $lowStock      ?? [];
$tickets       = $tickets       ?? [];
$sales         = $sales         ?? [];
$fulfillmentOrders = $fulfillmentOrders ?? [];
$navActive     = 'dashboard';
$pageTitle     = 'Dashboard — PCX Admin';
$pageHeading   = 'Operational Overview';
$pageSubtitle  = 'Real-time telemetry and management metrics.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="row g-3 mb-4">

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm bg-white h-100 position-relative dashboard-metric-card">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 bg-blue-light text-blue d-flex align-items-center justify-content-center fs-3" style="width: 52px; height: 52px;">
          <i class="bi bi-cart-dash"></i>
        </div>
        <div>
          <span class="text-muted small d-block fw-medium">Pending Orders</span>
          <h3 class="fw-bold mb-0 text-dark"><?= count($pendingOrders) ?></h3>
          <span class="small text-blue fw-semibold"><i class="bi bi-clock"></i> Action Required</span>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/?r=sales/sales/orders" class="stretched-link" title="Open Pending Orders Workspace"></a>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm bg-white h-100 position-relative dashboard-metric-card">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center fs-3" style="width: 52px; height: 52px; background-color: #fff3cd; color: #856404;">
          <i class="bi bi-truck"></i>
        </div>
        <div>
          <span class="text-muted small d-block fw-medium">Fulfillment Queue</span>
          <h3 class="fw-bold mb-0 text-dark"><?= count($fulfillmentOrders) ?></h3>
          <span class="small text-warning fw-semibold"><i class="bi bi-box-seam"></i> Awaiting Dispatch</span>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/?r=fulfillment/fulfillment/index" class="stretched-link" title="Open Dispatch Board"></a>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm bg-white h-100 position-relative dashboard-metric-card">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 <?= count($lowStock) > 0 ? 'bg-red-light text-red' : 'bg-light text-secondary' ?> d-flex align-items-center justify-content-center fs-3" style="width: 52px; height: 52px;">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div>
          <span class="text-muted small d-block fw-medium">Low Stock Alerts</span>
          <h3 class="fw-bold mb-0 <?= count($lowStock) > 0 ? 'text-red' : 'text-dark' ?>"><?= count($lowStock) ?></h3>
          <span class="small <?= count($lowStock) > 0 ? 'text-red fw-medium' : 'text-muted' ?>">
            <?= count($lowStock) > 0 ? '<i class="bi bi-arrow-down-square"></i> Restock Needed' : 'Inventory Levels Stable' ?>
          </span>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/?r=inventory/inventory/list" class="stretched-link" title="Open Restocking Manager"></a>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm bg-white h-100 position-relative dashboard-metric-card">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-3 bg-light text-secondary d-flex align-items-center justify-content-center fs-3" style="width: 52px; height: 52px;">
          <i class="bi bi-ticket-perforated"></i>
        </div>
        <div>
          <span class="text-muted small d-block fw-medium">Service Tickets</span>
          <h3 class="fw-bold mb-0 text-dark"><?= count($tickets) ?></h3>
          <span class="small text-muted">Open support inquiries</span>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/?r=service/service/index" class="stretched-link" title="Open Support Ticket Desk"></a>
    </div>
  </div>

</div>

<div class="row g-4">

  <div class="col-12 col-xl-8">
    <div class="card border-0 shadow-sm bg-white">
      <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <span class="fw-bold text-dark"><i class="bi bi-graph-up text-blue me-2"></i>Recent Sales Tracking</span>
        <button class="btn btn-sm btn-outline-primary py-1 px-3" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print Matrix</button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 0.875rem;">
            <thead class="table-light text-secondary small text-uppercase">
              <tr>
                <th class="ps-4 py-2.5">Settlement Window</th>
                <th class="text-end pe-4 py-2.5">Gross Yield Generated</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $row): ?>
                  <tr>
                    <td class="ps-4 fw-medium text-dark"><?= htmlspecialchars((string) $row['dt']) ?></td>
                    <td class="text-end pe-4 fw-bold text-blue">PHP <?= number_format((float) $row['total'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="2" class="text-center py-4 text-muted small">No recorded sales data files found for this tracking cycle.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="card border-0 shadow-sm bg-white">
      <div class="card-header bg-white py-3">
        <span class="fw-bold <?= count($lowStock) > 0 ? 'text-red' : 'text-dark' ?>">
          <i class="bi bi-lightning-charge-fill me-1"></i>Critical Shortage Feed
        </span>
      </div>
      <div class="card-body p-3">
        <?php if (!empty($lowStock)): ?>
          <div class="d-flex flex-column gap-2">
            <?php foreach (array_slice($lowStock, 0, 5) as $item): ?>
              <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border-start border-3 border-danger">
                <div class="overflow-hidden text-truncate me-2" style="max-width: 210px;">
                  <span class="fw-semibold text-dark small d-block text-truncate"><?= htmlspecialchars((string)($item['name'] ?? 'Item Description')) ?></span>
                  <span class="text-muted small font-monospace" style="font-size:0.7rem;">SYSTEM-ID-<?= rand(1000, 9999) ?></span>
                </div>
                <span class="badge bg-red-light text-red fw-bold px-2.5 py-1">
                  <?= (int)($item['qty'] ?? 0) ?> units left
                </span>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($lowStock) > 5): ?>
            <div class="text-center pt-3 border-top mt-3">
              <a href="<?= BASE_URL ?>/?r=inventory/inventory/index" class="text-decoration-none text-blue small fw-bold">
                Examine All <?= count($lowStock) ?> Shortages <i class="bi bi-chevron-right"></i>
              </a>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i>
            <span class="small fw-medium text-secondary">All database logistics check out optimally.</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>