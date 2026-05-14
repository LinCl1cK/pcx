<?php
$tickets = $tickets ?? [];
$orders = $orders ?? [];
$technicians = $technicians ?? [];
$employee = $employee ?? [];
$isAdmin = !empty($isAdmin);
$isTech = !empty($isTech);
$navActive = $navActive ?? 'service';
$pageTitle = $pageTitle ?? 'Service Tickets';
$pageHeading = $pageHeading ?? 'Service Tickets';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>
<?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
<?php endif; ?>

<?php if ($isAdmin): ?>
  <div class="card mb-3 border-0 shadow-sm">
    <div class="card-body">
      <h2 class="h6">Create ticket (completed orders)</h2>
      <?php if (empty($orders)): ?>
        <p class="text-muted mb-0">No completed orders pending tickets.</p>
      <?php else: ?>
        <form method="post" action="<?= BASE_URL ?>/?r=service/service/create" class="row g-2">
          <div class="col-md-3">
            <select name="order_id" class="form-select" required>
              <?php foreach ($orders as $o): ?>
                <option value="<?= htmlspecialchars((string) $o['Order_Id']) ?>"><?= htmlspecialchars((string) $o['Order_Id']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <select name="emp_id" class="form-select" required>
              <?php foreach ($technicians as $tech): ?>
                <option value="<?= htmlspecialchars((string) $tech['Emp_Id']) ?>"><?= htmlspecialchars((string) ($tech['Emp_Fname'] . ' ' . $tech['Emp_Lname'])) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <input name="diagnosis" class="form-control" placeholder="Diagnosis / problem details" required maxlength="255">
          </div>
          <div class="col-md-2">
            <button class="btn btn-dark w-100" type="submit">Create</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class="table-responsive bg-white rounded shadow-sm">
  <table class="table table-striped mb-0 align-middle">
    <thead>
      <tr>
        <th>Ticket</th>
        <th>Order / Invoice</th>
        <th>Customer</th>
        <?php if (!$isTech): ?><th>Technician</th><?php endif; ?>
        <th>Status</th>
        <th>Details</th>
        <?php if ($isTech): ?><th>Action</th><?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tickets as $t): ?>
        <tr>
          <td><?= htmlspecialchars((string) $t['Tix_Id']) ?></td>
          <td><?= htmlspecialchars((string) ($t['Order_InvoiceNo'] ?? $t['Tix_OrderID'] ?? '-')) ?></td>
          <td><?= htmlspecialchars((string) (($t['Cus_Fname'] ?? '') . ' ' . ($t['Cus_Lname'] ?? ''))) ?></td>
          <?php if (!$isTech): ?>
            <td><?= htmlspecialchars((string) (($t['Emp_Fname'] ?? '') . ' ' . ($t['Emp_Lname'] ?? ''))) ?></td>
          <?php endif; ?>
          <td><?= htmlspecialchars((string) $t['Tix_Status']) ?></td>
          <td style="max-width:220px"><small><?= htmlspecialchars((string) $t['Tix_ProblemInfo']) ?></small></td>
          <?php if ($isTech): ?>
            <td>
              <form method="post" action="<?= BASE_URL ?>/?r=service/service/technicianUpdate" class="row g-1">
                <input type="hidden" name="tix_id" value="<?= htmlspecialchars((string) $t['Tix_Id']) ?>">
                <div class="col-12">
                  <select name="status" class="form-select form-select-sm">
                    <?php foreach (['Pending', 'In Progress', 'Completed'] as $st): ?>
                      <option value="<?= $st ?>" <?= ($t['Tix_Status'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <input name="problem_info" class="form-control form-control-sm" value="<?= htmlspecialchars((string) $t['Tix_ProblemInfo']) ?>" maxlength="255">
                </div>
                <div class="col-12">
                  <button class="btn btn-sm btn-dark w-100" type="submit">Save</button>
                </div>
              </form>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>
