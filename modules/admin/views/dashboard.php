<?php
$employee = $employee ?? [];
$pendingOrders = $pendingOrders ?? [];
$lowStock = $lowStock ?? [];
$tickets = $tickets ?? [];
$sales = $sales ?? [];
$navActive = $navActive ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Admin Dashboard';
$pageHeading = $pageHeading ?? 'Admin dashboard';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6">Pending orders</h2>
                        <p class="display-6 mb-0"><?= count($pendingOrders) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6">Low stock alerts</h2>
                        <p class="display-6 mb-0"><?= count($lowStock) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6">Service tickets</h2>
                        <p class="display-6 mb-0"><?= count($tickets) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h2 class="h6">Sales report</h2>
                <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $row['dt']) ?></td>
                            <td>PHP <?= number_format((float) $row['total'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
