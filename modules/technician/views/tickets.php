<?php
$tickets = $tickets ?? [];
$orders = $orders ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'service';
$pageTitle = $pageTitle ?? 'Service Tickets';
$pageHeading = $pageHeading ?? 'My service tickets';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
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
  <?php if ($flash): ?>
    <div class="alert alert-<?= ($flash['type'] ?? '') === 'success' ? 'success' : 'danger' ?>"><?= htmlspecialchars((string) ($flash['message'] ?? '')) ?></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <h2 class="h6">Create Ticket From Completed Order</h2>
      <?php if (empty($orders)): ?>
        <p class="text-muted mb-0">No completed orders are available for new tickets.</p>
      <?php else: ?>
        <form method="post" action="<?= BASE_URL ?>/?r=technician/technician/create" class="row g-2">
          <div class="col-md-4">
            <select class="form-select" name="order_id" required>
              <?php foreach ($orders as $order): ?>
                <option value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                  <?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?> - <?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <input class="form-control" name="problem_info" maxlength="255" placeholder="Diagnosis / problem details" required>
          </div>
          <div class="col-md-2">
            <button class="btn btn-dark w-100" type="submit">Create</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-striped align-middle mb-0">
      <thead><tr><th>Ticket</th><th>Invoice</th><th>Customer</th><th>Status</th><th>Diagnosis</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($tickets as $ticket): ?>
          <tr>
            <td><?= htmlspecialchars((string) $ticket['Tix_Id']) ?></td>
            <td><?= htmlspecialchars((string) ($ticket['Order_InvoiceNo'] ?? '-')) ?></td>
            <td>
              <?= htmlspecialchars(trim(($ticket['Cus_Fname'] ?? '') . ' ' . ($ticket['Cus_Lname'] ?? ''))) ?>
              <div class="small text-muted"><?= htmlspecialchars((string) ($ticket['Cus_Email'] ?? '')) ?></div>
            </td>
            <td><?= htmlspecialchars((string) $ticket['Tix_Status']) ?></td>
            <td style="min-width:260px">
              <form method="post" action="<?= BASE_URL ?>/?r=technician/technician/update" class="row g-2">
                <input type="hidden" name="tix_id" value="<?= htmlspecialchars((string) $ticket['Tix_Id']) ?>">
                <div class="col-12">
                  <input class="form-control form-control-sm" name="problem_info" maxlength="255" value="<?= htmlspecialchars((string) $ticket['Tix_ProblemInfo']) ?>" required>
                </div>
                <div class="col-12">
                  <select class="form-select form-select-sm" name="status">
                    <?php foreach (['Pending', 'In Progress', 'Completed'] as $status): ?>
                      <option value="<?= htmlspecialchars($status) ?>" <?= $ticket['Tix_Status'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-sm btn-dark w-100" type="submit">Save</button>
                </div>
              </form>
            </td>
            <td>
              <?php if ($ticket['Tix_Status'] === 'Pending'): ?>
                <form method="post" action="<?= BASE_URL ?>/?r=technician/technician/delete">
                  <input type="hidden" name="tix_id" value="<?= htmlspecialchars((string) $ticket['Tix_Id']) ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this pending ticket?')">Delete</button>
                </form>
              <?php else: ?>
                <span class="text-muted small">Locked</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tickets)): ?>
          <tr><td colspan="6" class="text-muted">No assigned tickets.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
