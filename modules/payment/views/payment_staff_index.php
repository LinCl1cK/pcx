<?php
$payments = $payments ?? [];
$flash = $flash ?? null;
$canConfirm = !empty($canConfirm);
$employee = $employee ?? [];
$isSalesRepresentative = strtolower((string) ($employee['role'] ?? '')) === 'sales representative';
$isAdmin = strtolower((string) ($employee['role'] ?? '')) === 'administrator'; // Added Administrator check
$navActive = $navActive ?? 'payments';
$pageTitle = $pageTitle ?? 'Payments — PCX Admin';
$pageHeading = $pageHeading ?? 'Payment Settlements';
$pageSubtitle = 'Monitor gateway tracking codes and manually approve Cash-On-Delivery handoffs.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="alert alert-info border-0 bg-blue-light text-blue d-flex align-items-center mb-4 rounded-3 p-3 shadow-sm" role="alert">
  <i class="bi bi-shield-lock fs-5 me-3"></i>
  <span class="small fw-medium">Safe payment metadata only. All sensitive card credentials, wallet pins, and CVVs are strictly isolated and encrypted externally.</span>
</div>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-credit-card text-blue me-2"></i>Ledger & Activity
    </span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">Pay ID</th>
            <th>Ref Order</th>
            <th>Method</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Gateway Ref</th>
            <th>Timestamp</th>
            <?php if ($canConfirm): ?><th class="text-end pe-4">Action</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($payments)): ?>
            <tr>
              <td colspan="<?= $canConfirm ? '8' : '7' ?>" class="text-center text-muted py-5"><i class="bi bi-receipt fs-3 d-block mb-2"></i>No recorded payment history.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($payments as $p): ?>
              <tr>
                <td class="ps-4 fw-medium text-secondary">#<?= htmlspecialchars((string) $p['Pay_Id']) ?></td>
                <td><span class="font-monospace text-muted small">ORD-<?= htmlspecialchars((string) $p['Pay_OrderID']) ?></span></td>
                <td class="fw-bold text-dark"><?= htmlspecialchars((string) $p['Pay_Method']) ?></td>
                <td class="fw-semibold text-blue">PHP <?= number_format((float) $p['Pay_Amount'], 2) ?></td>
                <td>
                  <span class="badge <?= $p['Pay_Status'] === 'Completed' ? 'bg-green-light text-success' : 'bg-light text-secondary border' ?> px-2 py-1">
                    <?= htmlspecialchars((string) $p['Pay_Status']) ?>
                  </span>
                </td>
                <td class="text-secondary font-monospace small"><?= htmlspecialchars((string) ($p['Pay_GatewayRef'] ?? '-')) ?></td>
                <td class="text-muted small"><?= htmlspecialchars((string) $p['Pay_PaidAt']) ?></td>
                <?php if ($canConfirm): ?>
                  <td class="text-end pe-4">
                    <?php
                    $canConfirmCashless = $p['Pay_Status'] === 'Pending' && $p['Pay_Method'] !== 'COD' && $p['Order_Status'] === 'Confirmed';
                    // Fixed: Now evaluates both Sales Representatives and Administrators
                    $canConfirmCod = ($isSalesRepresentative || $isAdmin) && $p['Pay_Status'] === 'Pending' && $p['Pay_Method'] === 'COD' && $p['Order_Status'] === 'Completed';
                    ?>
                    <?php if ($canConfirmCashless || $canConfirmCod): ?>
                      <form method="post" action="<?= BASE_URL ?>/?r=payment/payment/confirm" class="m-0">
                        <input type="hidden" name="pay_id" value="<?= htmlspecialchars((string) $p['Pay_Id']) ?>">
                        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i><?= $canConfirmCod ? 'Confirm COD' : 'Confirm Payment' ?></button>
                      </form>
                    <?php elseif ($p['Pay_Status'] === 'Pending' && $p['Pay_Method'] === 'COD'): ?>
                      <span class="text-muted small"><i class="bi bi-truck me-1"></i>Awaiting Dispatch</span>
                    <?php else: ?>
                      <span class="text-muted small"><i class="bi bi-lock me-1"></i>Locked</span>
                    <?php endif; ?>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>