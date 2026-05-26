<?php
$tickets = $tickets ?? [];
$orders = $orders ?? [];
$technicians = $technicians ?? [];
$employee = $employee ?? [];
$isAdmin = !empty($isAdmin);
$isTech = !empty($isTech);
$navActive = $navActive ?? 'service';
$pageTitle = $pageTitle ?? 'Service Tickets — PCX Admin';
$pageHeading = $pageHeading ?? 'Service Center Logistics';
$pageSubtitle = 'Monitor, route, and resolve customer hardware service requests.';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<div class="row g-4">
  <?php if ($isAdmin): ?>
    <div class="col-12 col-xl-4">
      <div class="card border-0 shadow-sm bg-white sticky-xl-top" style="top: 1.5rem; z-index: 10;">
        <div class="card-header bg-white py-3">
          <span class="card-title fw-bold text-dark mb-0">
            <i class="bi bi-headset text-blue me-2"></i>Initialize Ticket
          </span>
        </div>
        <div class="card-body">
          <?php if (empty($orders)): ?>
            <div class="text-center py-3 text-muted">
              <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
              <span class="small fw-medium text-secondary">All completed hardware orders have been successfully routed.</span>
            </div>
          <?php else: ?>
            <form method="post" action="<?= BASE_URL ?>/?r=service/service/create" class="m-0" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Target Purchase Invoice</label>
                <select name="order_id" class="form-select form-select-sm" required>
                  <option value="">— Select eligible reference —</option>
                  <?php foreach ($orders as $o): ?>
                    <option value="<?= htmlspecialchars((string) $o['Order_Id']) ?>">
                      Invoice #<?= htmlspecialchars((string) $o['Order_InvoiceNo']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Assign Hardware Specialist</label>
                <select name="emp_id" class="form-select form-select-sm" required>
                  <option value="">— Select a technician —</option>
                  <?php foreach ($technicians as $tech): ?>
                    <option value="<?= htmlspecialchars((string) $tech['Emp_Id']) ?>">
                      <?= htmlspecialchars(trim(($tech['Emp_Fname'] ?? '') . ' ' . ($tech['Emp_Lname'] ?? ''))) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Intake Diagnosis & Symptoms</label>
                <textarea name="problem_info" class="form-control form-control-sm" rows="4" required maxlength="255" placeholder="Specify specific hardware errors, diagnostics, or parts requirements..."></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Upload Hardware Fault Photo (Optional)</label>
                <input type="file" name="attachment" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
              </div>
              <button type="submit" class="btn btn-sm btn-primary w-100 rounded-2 py-2">
                <i class="bi bi-plus-circle me-1"></i>Deploy Service Ticket
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="col-12 <?= $isAdmin ? 'col-xl-8' : '' ?>">
    <div class="card border-0 shadow-sm bg-white">
      <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <span class="card-title fw-bold text-dark mb-0">
          <i class="bi bi-shield-check text-blue me-2"></i><?= $isTech ? 'Assigned Maintenance Registry' : 'Global Diagnostic Registers' ?>
        </span>
        <span class="badge bg-light text-secondary border"><?= count($tickets) ?> Registered</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size: .875rem;">
            <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
              <tr>
                <th class="ps-4 py-3">Ticket ID</th>
                <th>Client Intake</th>
                <th>Invoice</th>
                <?php if ($isAdmin): ?>
                  <th>Assigned Tech</th>
                <?php endif; ?>
                <th>Status Mapping</th>
                <th class="pe-4 <?= $isTech ? 'text-end' : '' ?>">Diagnostics / Technical Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($tickets)): ?>
                <tr>
                  <td colspan="<?= 5 + ($isAdmin ? 1 : 0) ?>" class="text-center text-muted py-5">
                    <i class="bi bi-headset fs-3 d-block mb-2"></i>
                    <span class="small fw-medium text-secondary">No active technical service tickets located in storage.</span>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($tickets as $t): ?>
                  <tr>
                    <td class="ps-4">
                      <span class="fw-bold text-dark d-block">#<?= htmlspecialchars((string) $t['Tix_Id']) ?></span>
                      <span class="text-muted small" style="font-size: 0.75rem;"><?= htmlspecialchars(date('M d, Y', strtotime((string)$t['Tix_CreatedAt']))) ?></span>
                    </td>
                    <td>
                      <span class="fw-bold text-dark d-block"><?= htmlspecialchars(trim(($t['Cus_Fname'] ?? '') . ' ' . ($t['Cus_Lname'] ?? ''))) ?></span>

                      <!-- Inline Image Viewer Update -->
                      <?php if (!empty($t['Tix_Attachment'])): ?>
                        <div class="mt-2 d-flex flex-column gap-1">
                          <a href="<?= BASE_URL ?>/assets/uploads/reports/<?= htmlspecialchars((string) $t['Tix_Attachment']) ?>"
                            target="_blank"
                            class="d-inline-block border rounded bg-light p-0.5 overflow-hidden shadow-sm"
                            style="width: 54px; height: 54px;"
                            title="Click to view full resolution fault photo">
                            <img src="<?= BASE_URL ?>/assets/uploads/reports/<?= htmlspecialchars((string) $t['Tix_Attachment']) ?>"
                              alt="Hardware fault confirmation graphic"
                              class="w-100 h-100 object-fit-cover rounded-1">
                          </a>
                          <small class="text-muted" style="font-size: 0.7rem;">
                            <i class="bi bi-image me-1"></i>Inspect Asset Photo
                          </small>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="text-muted font-monospace small">INV-<?= htmlspecialchars((string) ($t['Order_InvoiceNo'] ?? 'N/A')) ?></span>
                    </td>
                    <?php if ($isAdmin): ?>
                      <td>
                        <span class="fw-medium text-secondary small">
                          <i class="bi bi-person-badge me-1"></i><?= htmlspecialchars(trim(($t['Emp_Fname'] ?? '') . ' ' . ($t['Emp_Lname'] ?? ''))) ?>
                        </span>
                      </td>
                    <?php endif; ?>
                    <td>
                      <?php
                      $status = trim((string)$t['Tix_Status']);
                      if ($status === 'Completed') {
                        echo '<span class="badge bg-light text-success border border-success-subtle px-2 py-1">Completed</span>';
                      } elseif ($status === 'In Progress') {
                        echo '<span class="badge bg-blue-light text-blue px-2 py-1 border border-primary-subtle">In Progress</span>';
                      } else {
                        echo '<span class="badge bg-light text-warning border border-warning-subtle px-2 py-1">Pending</span>';
                      }
                      ?>
                    </td>

                    <?php if ($isTech): ?>
                      <td class="text-end pe-4 align-middle" style="min-width: 320px;">

                        <?php if (!empty($t['Tix_Attachment'])): ?>
                          <div class="mb-2 d-flex align-items-center justify-content-end gap-2">
                            <span class="text-muted" style="font-size: 0.75rem;">
                              <i class="bi bi-camera me-1"></i>Fault Asset:
                            </span>
                            <a href="<?= BASE_URL ?>/assets/uploads/reports/<?= htmlspecialchars((string) $t['Tix_Attachment']) ?>"
                              target="_blank"
                              class="d-inline-block border rounded bg-light p-0.5 overflow-hidden shadow-sm img-thumbnail-link"
                              style="width: 40px; height: 40px;"
                              title="Click to expand hardware fault photo in new tab">
                              <img src="<?= BASE_URL ?>/assets/uploads/reports/<?= htmlspecialchars((string) $t['Tix_Attachment']) ?>"
                                alt="Hardware Fault Confirmation Log"
                                class="w-100 h-100 object-fit-cover rounded-1">
                            </a>
                          </div>
                        <?php endif; ?>

                        <form method="post" action="<?= BASE_URL ?>/?r=service/service/technicianUpdate" class="d-flex flex-column gap-2 m-0 align-items-end">
                          <input type="hidden" name="tix_id" value="<?= htmlspecialchars((string) $t['Tix_Id']) ?>">
                          <div class="input-group input-group-sm">
                            <input name="problem_info" class="form-control form-control-sm" value="<?= htmlspecialchars((string) $t['Tix_ProblemInfo']) ?>" maxlength="255" placeholder="Add diagnostic updates...">
                            <select name="status" class="form-select form-select-sm bg-light text-secondary" style="max-width: 120px;">
                              <?php foreach (['Pending', 'In Progress', 'Completed'] as $st): ?>
                                <option value="<?= $st ?>" <?= ($t['Tix_Status'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <button class="btn btn-sm btn-outline-primary px-3 rounded-2" type="submit">
                            <i class="bi bi-save me-1"></i>Save Log
                          </button>
                        </form>
                      </td>
                    <?php else: ?>
                      <td class="pe-4 align-middle">
                        <div class="bg-light p-2 rounded border border-light-subtle small text-secondary" style="max-width: 300px; min-width: 200px;">
                          <i class="bi bi-file-earmark-text me-1 text-primary"></i>
                          <span title="<?= htmlspecialchars((string) $t['Tix_ProblemInfo']) ?>">
                            <?= htmlspecialchars((string) $t['Tix_ProblemInfo']) ?: '<em class="text-muted">No diagnostic notes provided yet.</em>' ?>
                          </span>
                        </div>
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
  </div>
</div>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>