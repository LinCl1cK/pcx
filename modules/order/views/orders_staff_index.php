<?php
$orders = $orders ?? [];
$employee = $employee ?? [];
$employeeId = (string) ($employee['id'] ?? '');
$navActive = $navActive ?? 'orders';
$pageTitle = $pageTitle ?? 'Order Directory — PCX Admin';
$pageHeading = $pageHeading ?? 'Order Directory';
$pageSubtitle = 'Read-only archive of global sales invoices and receipts.';

$rawRole    = strtolower((string)($employee['Emp_Position'] ?? $employee['role'] ?? ''));
$role       = str_replace('_', ' ', $rawRole);
$isGeneralAdmin = ($role === 'general admin') && empty($employee['Emp_BranchId']);

// ADD THIS ROLE DEFINITION MATCHING YOUR nav.php RULE:
$isBranchAdmin  = ($role === 'administrator' || $role === 'branch admin');

require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="card border-0 shadow-sm bg-white">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <span class="card-title fw-bold text-dark mb-0">
      <i class="bi bi-bag text-blue me-2"></i>Global Order Index
    </span>
    <span class="badge bg-light text-secondary border"><?= count($orders) ?> Total</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
        <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
          <tr>
            <th class="ps-4 py-3">Order ID / Invoice</th>
            <th>Customer</th>
            <th>Order Status</th>
            <th class="text-end">Total Allocation</th>
            <th class="text-end pe-4">Timestamp</th>

            <th class="text-end pe-4">
              <?= $isGeneralAdmin ? 'Clearance Status' : 'Fulfillment Actions' ?>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-4">No records found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td class="ps-4 font-monospace fw-semibold text-secondary">
                  <?= htmlspecialchars((string) ($o['Order_InvoiceNo'] ?? $o['Order_Id'])) ?>
                </td>
                <td class="fw-bold text-dark">
                  <?= htmlspecialchars(trim(($o['Cus_Fname'] ?? '') . ' ' . ($o['Cus_Lname'] ?? ''))) ?>
                </td>
                <td>
                  <span class="badge bg-light text-dark border"><?= htmlspecialchars((string)$o['Order_Status']) ?></span>
                </td>
                <td class="text-end fw-semibold text-blue">
                  PHP <?= number_format((float) ($o['Order_TotalAmount'] ?? 0), 2) ?>
                </td>
                <td class="text-end pe-4 text-muted small">
                  <?= htmlspecialchars((string) $o['Order_Date']) ?>
                </td>

                <td class="text-end py-3 pe-4">
                  <?php if ($isGeneralAdmin): ?>
                    <span class="text-muted small text-uppercase fw-semibold">
                      <i class="bi bi-eye-fill me-1"></i>Read-Only (Global)
                    </span>
                  <?php elseif ($isBranchAdmin): ?>
                    <span class="text-muted small text-uppercase fw-semibold">
                      <i class="bi bi-eye-fill me-1"></i>Read-Only (Branch)
                    </span>
                  <?php else: ?>
                    <?php if ($o['Order_Status'] === 'Pending'): ?>
                      <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="m-0 d-inline-block">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $o['Order_Id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Cancel this pending order?')">
                          <i class="bi bi-trash3 me-1"></i>Drop
                        </button>
                      </form>
                    <?php elseif ($o['Order_Status'] === 'Confirmed' && (string) ($o['Order_VerifiedBy'] ?? '') === $employeeId): ?>
                      <form method="post" action="<?= BASE_URL ?>/?r=sales/sales/cancel" class="m-0 d-inline-block">
                        <input type="hidden" name=\"order_id\" value=\"<?= htmlspecialchars((string) $o['Order_Id']) ?>\">
                        <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Warning: Revoke order?')">
                          <i class="bi bi-x-octagon me-1\"></i>Revoke
                        </button>
                      </form>
                    <?php else: ?>
                      <span class="text-muted small"><i class="bi bi-lock me-1"></i>System Locked</span>
                    <?php endif; ?>
                  <?php endif; ?>
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