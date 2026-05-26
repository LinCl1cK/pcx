<?php

/** @var array $orders */
/** @var string|null $error */
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'Service Request - PCX';
require_once dirname(__DIR__, 3) . '/app/core/header.php';
?>

<div class="container py-5" style="max-width: 680px;">
  <div class="d-flex align-items-center mb-4">
    <div class="rounded-3 bg-blue-light text-blue d-flex align-items-center justify-content-center fs-3 me-3" style="width: 52px; height: 52px;">
      <i class="bi bi-headset"></i>
    </div>
    <div>
      <h1 class="h4 fw-bold mb-1 text-dark">Hardware Service Request</h1>
      <p class="text-muted small mb-0">Open an official support ticket for a completed purchase.</p>
    </div>
  </div>

  <?php if ($error ?? null): ?>
    <div class="alert alert-danger bg-red-light border-0 shadow-sm d-flex align-items-center rounded-3 p-3 mb-4">
      <i class="bi bi-exclamation-triangle fs-5 me-2 text-danger"></i>
      <span class="small fw-medium text-dark"><?= htmlspecialchars((string) $error) ?></span>
    </div>
  <?php endif; ?>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info border-0 bg-blue-light text-blue d-flex flex-column align-items-center justify-content-center text-center rounded-3 p-5 shadow-sm">
      <i class="bi bi-box-seam fs-1 mb-2"></i>
      <span class="fw-medium">No Eligible Orders</span>
      <small class="text-muted mt-1">You must have a completed, fulfilled order linked to your account to open a warranty or service ticket.</small>
    </div>
  <?php else: ?>
    <form method="post" class="card border-0 shadow-sm bg-white" enctype="multipart/form-data">
      <div class="card-header bg-white py-3 fw-bold text-dark">
        <i class="bi bi-card-text text-blue me-2"></i>Submit Diagnostic Details
      </div>
      <div class="card-body p-4">
        <div class="mb-4">
          <label class="form-label small fw-semibold text-secondary">Target Purchase Reference <span class="text-danger">*</span></label>
          <select name="order_id" class="form-select" required>
            <option value="">— Select an eligible invoice —</option>
            <?php foreach ($orders as $o): ?>
              <option value="<?= htmlspecialchars((string) $o['Order_Id']) ?>">
                Invoice #<?= htmlspecialchars((string) $o['Order_InvoiceNo']) ?> &middot; <?= htmlspecialchars((string) $o['Order_Date']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-4">
          <label class="form-label small fw-semibold text-secondary">Describe the Issue <span class="text-danger">*</span></label>
          <textarea name="problem_info" class="form-control" rows="5" required minlength="5" maxlength="255" placeholder="Please provide specific hardware symptoms, when the issue started, and any error codes you encountered..."></textarea>
          <small class="text-muted mt-2 d-block">Be as descriptive as possible so our technicians can prepare replacement parts if necessary.</small>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold text-secondary">Upload Proof / Hardware Fault Photo</label>
          <input type="file" name="attachment" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
          <div class="form-text small" style="font-size: 0.75rem;">Max file size: 5MB. Accepted extension formats: JPG, PNG, WEBP.</div>
        </div>
        <div class="pt-3 border-top text-end">
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send me-2"></i>Submit Ticket Request</button>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php require_once dirname(__DIR__, 3) . '/app/core/footer.php'; ?>