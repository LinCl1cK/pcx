<?php
$user = $user ?? [];
$customer = $customer ?? null;
$orders = $orders ?? [];
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? 'My Account - PCX Store';
require_once __DIR__ . '/../../../app/core/header.php';
?>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">My Account Dashboard</h1>
  </div>
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body p-4">
          <h2 class="h5 mb-3">Profile Information</h2>
          <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/updateProfile" class="mb-3">
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required maxlength="255"
                value="<?= htmlspecialchars((string) ($customer['Cus_Email'] ?? $user['email'] ?? '')) ?>">
            </div>
            <div class="row g-2 mb-2">
              <div class="col-md-6">
                <label class="form-label">First name</label>
                <input type="text" name="fname" class="form-control" required maxlength="50"
                  value="<?= htmlspecialchars((string) ($customer['Cus_Fname'] ?? '')) ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Last name</label>
                <input type="text" name="lname" class="form-control" required maxlength="50"
                  value="<?= htmlspecialchars((string) ($customer['Cus_Lname'] ?? '')) ?>">
              </div>
            </div>
            <div class="mb-2">
              <label class="form-label">Contact no.</label>
              <input type="text" name="contact" class="form-control" required maxlength="15"
                value="<?= htmlspecialchars((string) ($customer['Cus_ContactNo'] ?? '')) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" required maxlength="255"
                value="<?= htmlspecialchars((string) ($customer['Cus_Address'] ?? '')) ?>">
            </div>
            <div class="mb-2">
              <label class="form-label">New password (optional)</label>
              <input type="password" name="new_password" class="form-control" minlength="8" autocomplete="new-password">
              <div class="form-text">Leave blank to keep current password.</div>
            </div>
            <button type="submit" class="btn btn-dark btn-sm">Save profile</button>
          </form>
          <p class="mb-2 small text-muted"><strong>Customer ID:</strong> <?= htmlspecialchars((string) ($customer['Cus_Id'] ?? $user['id'] ?? '')) ?></p>
          <a href="<?= BASE_URL ?>/?r=auth/auth/logout" class="btn btn-dark btn-sm">Logout</a>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h2 class="h5 mb-3">Transaction Records</h2>
          <?php if (empty($orders)): ?>
            <div class="alert alert-info mb-0">No transaction records yet.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped align-middle mb-0">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td><?= htmlspecialchars((string) $order['Order_Id']) ?></td>
                      <td><?= htmlspecialchars((string) $order['Order_Date']) ?></td>
                      <td><?= htmlspecialchars((string) $order['Order_InvoiceNo']) ?></td>
                      <td><?= (int) $order['item_count'] ?></td>
                      <td><?= htmlspecialchars((string) $order['Order_Status']) ?></td>
                      <td><?= htmlspecialchars((string) $order['payment_status']) ?></td>
                      <td>PHP <?= number_format((float) $order['Order_TotalAmount'], 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm border-0 mt-3">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Service & Warranty Tickets</h2>
            <a href="<?= BASE_URL ?>/?r=service/customerTicket/request" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-plus-lg"></i> Open New Ticket
            </a>
          </div>
          
          <?php if (empty($serviceTickets)): ?>
            <div class="alert alert-light border mb-0 text-muted">You have no active or past service requests.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped align-middle mb-0">
                <thead>
                  <tr>
                    <th>Ticket ID</th>
                    <th>Date Opened</th>
                    <th>Linked Invoice</th>
                    <th>Status</th>
                    <th>Latest Update</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($serviceTickets as $tix): ?>
                    <tr>
                      <td class="fw-medium text-dark">#<?= htmlspecialchars((string) $tix['Tix_Id']) ?></td>
                      <td class="text-muted small"><?= htmlspecialchars(date('M d, Y', strtotime((string)$tix['Tix_CreatedAt']))) ?></td>
                      <td>INV-<?= htmlspecialchars((string) ($tix['Order_InvoiceNo'] ?? 'N/A')) ?></td>
                      <td>
                        <?php
                        $status = trim((string)$tix['Tix_Status']);
                        if ($status === 'Completed') {
                          echo '<span class="badge bg-light text-success border border-success-subtle px-2 py-1">Completed</span>';
                        } elseif ($status === 'In Progress') {
                          echo '<span class="badge bg-blue-light text-primary border border-primary-subtle px-2 py-1">In Progress</span>';
                        } else {
                          echo '<span class="badge bg-light text-warning border border-warning-subtle px-2 py-1">Pending</span>';
                        }
                        ?>
                      </td>
                      <td class="small text-secondary" style="max-width: 200px;">
                         <div class="text-truncate" title="<?= htmlspecialchars((string) $tix['Tix_ProblemInfo']) ?>">
                           <?= htmlspecialchars((string) $tix['Tix_ProblemInfo']) ?>
                         </div>
                      </td>
                      <td class="text-end">
                        <a href="<?= BASE_URL ?>/?r=service/customerTicket/details&id=<?= htmlspecialchars((string) $tix['Tix_Id']) ?>" class="btn btn-sm btn-dark">
                          View Details
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
    </div>
  </div>
</div>
<?php require dirname(__DIR__, 3) . '/app/views/layouts/customer_footer.php'; ?>