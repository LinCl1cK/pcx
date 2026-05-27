<?php
$orders = $orders ?? [];
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$navActive = $navActive ?? 'verification';
$pageTitle = $pageTitle ?? 'Manual Verification — PCX Admin';
$pageHeading = $pageHeading ?? 'Manual Verification';
$pageSubtitle = 'Review and confirm customer identities for high-value transactions.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="alert alert-info border-0 bg-blue-light text-blue d-flex align-items-center mb-4 rounded-3 p-3 shadow-sm" role="alert">
  <i class="bi bi-shield-lock fs-5 me-3"></i>
  <span class="small fw-medium">Limited customer view: name only. Full billing details remain secured in the primary order record.</span>
</div>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-shield-check text-blue me-2"></i>Verification Queue
    </span>
    <span class="badge bg-light text-secondary border"><?= count($orders) ?> Pending</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">Order ID</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>ID Status</th>
            <th class="text-end pe-4">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-shield-check fs-3 d-block mb-2 text-success"></i>No pending verifications.</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <tr>
              <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars((string) $order['Order_Id']) ?></td>
              <td class="fw-bold text-dark"><?= htmlspecialchars(trim(($order['Cus_Fname'] ?? '') . ' ' . ($order['Cus_Lname'] ?? ''))) ?></td>
              <td class="fw-semibold text-blue">PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
              <td>
                <?php if (!empty($order['Cus_IdAttachment'])): ?>
                  <a class="badge bg-blue-light text-blue text-decoration-none px-2 py-1" href="<?= BASE_URL ?>/<?= htmlspecialchars((string) $order['Cus_IdAttachment']) ?>" target="_blank"><i class="bi bi-file-earmark-person me-1"></i>View ID</a>
                <?php elseif ((float) $order['Order_TotalAmount'] >= 50000): ?>
                  <span class="badge bg-red-light text-red px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Required</span>
                <?php else: ?>
                  <span class="badge bg-light text-secondary border px-2 py-1">Optional</span>
                <?php endif; ?>
              </td>
              <td class="text-end pe-4">
                <form method="post" action="<?= BASE_URL ?>/?r=verification/verification/process" class="d-inline-flex flex-wrap gap-2 align-items-center justify-content-end m-0">
                  <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                  <div class="form-check mb-0 me-2">
                    <input class="form-check-input" type="checkbox" name="id_verified" value="1" id="idv_<?= htmlspecialchars((string) $order['Order_Id']) ?>">
                    <label class="form-check-label small text-secondary" for="idv_<?= htmlspecialchars((string) $order['Order_Id']) ?>">ID Checked</label>
                  </div>
                  <button class="btn btn-sm btn-primary" name="action" value="confirm" type="submit"><i class="bi bi-check-lg me-1"></i>Confirm</button>
                  <button class="btn btn-sm btn-outline-danger" name="action" value="reject" type="submit"><i class="bi bi-x-lg me-1"></i>Reject</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>