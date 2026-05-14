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
            <?php $flash = $flash ?? null; if ($flash): ?>
              <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?>"><?= htmlspecialchars((string) $flash['message']) ?></div>
            <?php endif; ?>
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
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/../../../app/core/footer.php'; ?>
