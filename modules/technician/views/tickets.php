<?php
$tickets = $tickets ?? [];
$orders = $orders ?? [];
$flash = $flash ?? null;
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'service';
$pageTitle = $pageTitle ?? 'Tickets — PCX Admin';
$pageHeading = $pageHeading ?? 'Service Center';
$pageSubtitle = 'Create, update, and manage hardware repair pipelines.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white mb-4">
  <div class="card-header bg-white py-3 fw-bold text-dark">
    <i class="bi bi-plus-circle text-blue me-2"></i>Initiate Service Ticket
  </div>
  <div class="card-body">
    <?php if (empty($orders)): ?>
      <p class="text-muted mb-0 small"><i class="bi bi-info-circle me-1"></i>No completed orders available in the database for ticket generation.</p>
    <?php else: ?>
      <form method="post" action="<?= BASE_URL ?>/?r=technician/technician/create" class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label small fw-semibold text-secondary">Target Invoice Reference</label>
          <select class="form-select" name="order_id" required>
            <?php foreach ($orders as $order): ?>
              <option value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                <?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?> — <?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-5">
          <label class="form-label small fw-semibold text-secondary">Initial Hardware Diagnosis</label>
          <input class="form-control" name="problem_info" maxlength="255" placeholder="Define the failure point..." required>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100" type="submit"><i class="bi bi-file-earmark-plus me-1"></i>Submit</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 fw-bold text-dark">
    <i class="bi bi-wrench-adjustable text-blue me-2"></i>Assigned Operations
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3 align-middle text-start" style="width: 100px;">Ticket ID</th>
            <th class="py-3 align-middle text-start" style="width: 250px;">Customer & Invoice</th>
            <th class="py-3 align-middle text-start">Diagnosis Update</th>
            <th class="text-end pe-4 align-middle" style="width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tickets as $ticket): ?>
            <tr>
              <td class="ps-4 fw-medium text-secondary align-middle text-start">#<?= htmlspecialchars((string) $ticket['Tix_Id']) ?></td>
              <td class="align-middle text-start">
                <span class="fw-bold text-dark d-block"><?= htmlspecialchars(trim(($ticket['Cus_Fname'] ?? '') . ' ' . ($ticket['Cus_Lname'] ?? ''))) ?></span>
                <span class="text-muted font-monospace" style="font-size: 0.75rem;">INV-<?= htmlspecialchars((string) ($ticket['Order_InvoiceNo'] ?? '-')) ?></span>
              </td>
              
              <td class="align-middle text-start">
                <div class="d-flex flex-wrap align-items-center gap-3 w-100">
                  <?php if (!empty($ticket['Tix_Attachment'])): ?>
                    <div>
                      <a href="<?= BASE_URL ?>/assets/uploads/reports/<?= htmlspecialchars((string) $ticket['Tix_Attachment']) ?>" 
                         target="_blank" 
                         class="btn btn-sm btn-light border d-inline-flex align-items-center justify-content-center gap-2 shadow-sm text-blue"
                         title="View Customer Uploaded Proof">
                        <i class="bi bi-image text-primary"></i>
                        <span class="small fw-medium">View Photo</span>
                      </a>
                    </div>
                  <?php endif; ?>

                  <form method="post" action="<?= BASE_URL ?>/?r=technician/technician/update" class="d-flex flex-grow-1 align-items-center gap-2 m-0">
                    <input type="hidden" name="tix_id" value="<?= htmlspecialchars((string) $ticket['Tix_Id']) ?>">
                    <div class="input-group input-group-sm flex-grow-1">
                      <input class="form-control" name="problem_info" maxlength="255" value="<?= htmlspecialchars((string) $ticket['Tix_ProblemInfo']) ?>" required>
                      <select class="form-select bg-light text-secondary" name="status" style="max-width: 130px;">
                        <?php foreach (['Pending', 'In Progress', 'Completed'] as $status): ?>
                          <option value="<?= htmlspecialchars($status) ?>" <?= $ticket['Tix_Status'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <button class="btn btn-sm btn-outline-primary text-nowrap" type="submit"><i class="bi bi-save me-1"></i>Commit Log</button>
                  </form>
                </div>
              </td>
              <td class="text-end pe-4 align-middle">
                <?php if ($ticket['Tix_Status'] === 'Pending'): ?>
                  <form method="post" action="<?= BASE_URL ?>/?r=technician/technician/delete" class="m-0">
                    <input type="hidden" name="tix_id" value="<?= htmlspecialchars((string) $ticket['Tix_Id']) ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Purge this ticket record?')"><i class="bi bi-trash me-1"></i>Purge</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted small"><i class="bi bi-lock me-1"></i>Locked</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>