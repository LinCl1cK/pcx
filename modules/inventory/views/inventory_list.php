<?php
$stocks = $stocks ?? [];
$branches = $branches ?? [];
$products = $products ?? [];
$flash = $flash ?? null;
$readOnly = !empty($readOnly);
$isBranchAdmin = !empty($employee['Emp_BranchId']);
$myBranchId = $employee['Emp_BranchId'] ?? '';
$employee = $employee ?? ($_SESSION['employee'] ?? []);
$navActive = $navActive ?? 'inventory';
$pageTitle = $pageTitle ?? 'Inventory — PCX Admin';
$pageHeading = $pageHeading ?? 'Branch Inventory';
$pageSubtitle = 'Monitor hardware stock levels and execute branch transfers.';
require dirname(__DIR__, 3) . '/app/views/layouts/employee_begin.php';
?>

<?php if ($readOnly): ?>
  <div class="alert alert-secondary border-0 bg-light text-secondary d-flex align-items-center mb-4 rounded-3 p-3 shadow-sm">
    <i class="bi bi-eye fs-5 me-3"></i>
    <span class="small fw-medium">View-only inventory mode. Stock modifications and transfers are restricted to branch administrators.</span>
  </div>
<?php endif; ?>

<div class="row g-4">
  <?php if (!$readOnly): ?>
    <div class="col-12 col-xl-4">
      <div class="card border-0 shadow-sm mb-4 sticky-xl-top" style="top: 1.5rem; z-index: 10;">
        <div class="card-header bg-white py-3 fw-bold text-dark">
          <i class="bi bi-plus-circle text-blue me-2"></i>Register Stock Row
        </div>
        <div class="card-body">
          <form method="post" action="<?= BASE_URL ?>/?r=inventory/inventory/create" class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold text-secondary">Product</label>
              <select class="form-select" name="Inv_ProdId" required>
                <option value="">— Select Product —</option>
                <?php foreach ($products as $p): ?>
                  <option value="<?= htmlspecialchars((string) $p['Prod_Id']) ?>"><?= htmlspecialchars((string) $p['Prod_Name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold text-secondary">Target Branch</label>
              <?php if ($isBranchAdmin): ?>
                <input type="hidden" name="Inv_BranchId" value="<?= htmlspecialchars($myBranchId) ?>">
                <input class="form-control bg-light text-muted" value="Current Location (<?= htmlspecialchars($myBranchId) ?>)" disabled>
              <?php else: ?>
                <select class="form-select" name="Inv_BranchId" required>
                  <option value="">— Select Branch —</option>
                  <?php foreach ($branches as $b): ?>
                    <option value="<?= htmlspecialchars((string) $b['Branch_Id']) ?>"><?= htmlspecialchars((string) $b['Branch_Name']) ?></option>
                  <?php endforeach; ?>
                </select>
              <?php endif; ?>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold text-secondary">Initial Qty</label>
              <input class="form-control" type="number" min="0" name="Inv_StockQty" value="0" required>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold text-secondary">Reorder At</label>
              <input class="form-control" type="number" min="0" name="Inv_ReorderLevel" value="10" required>
            </div>
            <div class="col-12 mt-4">
              <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-down me-1"></i>Add Stock Row</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 fw-bold text-dark">
          <i class="bi bi-arrow-left-right text-blue me-2"></i>Execute Transfer
        </div>
        <div class="card-body">
          <form method="post" action="<?= BASE_URL ?>/?r=inventory/inventory/transfer" class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold text-secondary">Product ID</label>
              <input class="form-control" name="Inv_ProdId" placeholder="e.g. 1042" required>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold text-secondary">Source Branch (Sending From)</label>
              <?php if ($isBranchAdmin): ?>
                <input type="hidden" name="from_branch" value="<?= htmlspecialchars($myBranchId) ?>">
                <input class="form-control bg-light text-muted" value="My Inventory (<?= htmlspecialchars($myBranchId) ?>)" disabled>
              <?php else: ?>
                <select class="form-select" name="from_branch" required>
                  <?php foreach ($branches as $b): ?><option value="<?= htmlspecialchars((string) $b['Branch_Id']) ?>"><?= htmlspecialchars((string) $b['Branch_Name']) ?></option><?php endforeach; ?>
                </select>
              <?php endif; ?>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold text-secondary">Destination Branch</label>
              <select class="form-select" name="to_branch" required>
                <?php foreach ($branches as $b): ?><option value="<?= htmlspecialchars((string) $b['Branch_Id']) ?>"><?= htmlspecialchars((string) $b['Branch_Name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold text-secondary">Transfer Amount</label>
              <input class="form-control" type="number" min="1" name="qty" placeholder="0" required>
            </div>
            <div class="col-12 mt-4">
              <button class="btn btn-dark w-100" type="submit"><i class="bi bi-truck me-1"></i>Dispatch Transfer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="col-12 <?= $readOnly ? 'col-xl-12' : 'col-xl-8' ?>">
    <div class="card border-0 shadow-sm bg-white overflow-hidden">

      <div class="card-header bg-white py-3">
        <div class="row align-items-center g-3">
          <div class="col-12 col-md-4">
            <span class="card-title fw-bold text-dark mb-0 d-block">
              <i class="bi bi-inboxes text-blue me-2"></i>Active Inventory Matrix
            </span>
          </div>
          <div class="col-12 col-sm-6 col-md-4">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-search"></i></span>
              <input type="text" id="tableSearch" class="form-control bg-light border-start-0" placeholder="Search product name...">
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-4 <?= $isBranchAdmin ? 'd-none' : '' ?>">
            <select id="branchFilter" class="form-select form-select-sm bg-light">
              <option value="">All Branches</option>
              <?php
              $uniqueBranches = array_unique(array_column($stocks, 'Branch_Name'));
              foreach ($uniqueBranches as $branchName):
              ?>
                <option value="<?= htmlspecialchars((string) $branchName) ?>"><?= htmlspecialchars((string) $branchName) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="matrixTable" style="font-size: .875rem;">
            <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
              <tr>
                <th class="ps-4 py-3 text-start align-middle user-select-none" style="cursor: pointer;" onclick="sortTable(0)">
                  <div class="d-inline-flex align-items-center">
                    Product Name <i class="bi bi-arrow-down-up ms-1 text-muted extra-small"></i>
                  </div>
                </th>

                <th class="text-start align-middle user-select-none" style="cursor: pointer;" onclick="sortTable(1)">
                  <div class="d-inline-flex align-items-center">
                    Branch Location <i class="bi bi-arrow-down-up ms-1 text-muted extra-small"></i>
                  </div>
                </th>

                <th class="text-start align-middle user-select-none" style="cursor: pointer;" onclick="sortTable(2, true)">
                  <div class="d-inline-flex align-items-center">
                    Current Stock <i class="bi bi-arrow-down-up ms-1 text-muted extra-small"></i>
                  </div>
                </th>

                <th class="text-start align-middle">Reorder Alert</th>

                <th class="text-start align-middle">Last Audit</th>

                <?php if (!$readOnly): ?>
                  <th class="text-end align-middle pe-4">Action</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody id="matrixTableBody">
              <?php if (empty($stocks)): ?>
                <tr class="no-data-row">
                  <td colspan="<?= $readOnly ? '5' : '6' ?>" class="text-center text-muted py-5"><i class="bi bi-box fs-3 d-block mb-2"></i>No inventory records found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($stocks as $s): ?>
                  <tr class="inventory-row">
                    <td class="ps-4 fw-bold text-dark product-cell"><?= htmlspecialchars((string) $s['Prod_Name']) ?></td>
                    <td class="text-secondary branch-cell"><?= htmlspecialchars((string) $s['Branch_Name']) ?></td>
                    <td data-qty="<?= (int) $s['Inv_StockQty'] ?>">
                      <?php if ((int) $s['Inv_StockQty'] <= (int) $s['Inv_ReorderLevel']): ?>
                        <span class="badge bg-red-light text-red px-2 py-1 border border-danger-subtle"><?= (int) $s['Inv_StockQty'] ?> Units</span>
                      <?php else: ?>
                        <span class="badge bg-light text-secondary border px-2 py-1"><?= (int) $s['Inv_StockQty'] ?> Units</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-secondary"><?= (int) $s['Inv_ReorderLevel'] ?></td>
                    <td class="text-muted small"><?= htmlspecialchars((string) $s['Inv_LastUpdated']) ?></td>
                    <?php if (!$readOnly): ?>
                      <td class="text-end pe-4">
                        <a href="<?= BASE_URL ?>/?r=inventory/inventory/edit&id=<?= urlencode((string) $s['Inv_Id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Inventory">
                          <i class="bi bi-pencil"></i>
                        </a>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
              <tr id="emptyFilterRow" class="d-none">
                <td colspan="<?= $readOnly ? '5' : '6' ?>" class="text-center text-muted py-5"><i class="bi bi-search fs-3 d-block mb-2"></i>No matching records match your filter parameters.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tableSearch');
    const branchFilter = document.getElementById('branchFilter');
    const tableRows = document.querySelectorAll('.inventory-row');
    const emptyFilterRow = document.getElementById('emptyFilterRow');

    function filterTable() {
      const query = searchInput.value.toLowerCase().trim();
      const branchValue = branchFilter.value.toLowerCase();
      let visibleCount = 0;

      tableRows.forEach(row => {
        const productName = row.querySelector('.product-cell').textContent.toLowerCase();
        const branchName = row.querySelector('.branch-cell').textContent.toLowerCase();

        const matchesSearch = productName.includes(query);
        const matchesBranch = branchValue === "" || branchName === branchValue;

        if (matchesSearch && matchesBranch) {
          row.classList.remove('d-none');
          visibleCount++;
        } else {
          row.classList.add('d-none');
        }
      });

      if (visibleCount === 0 && tableRows.length > 0) {
        emptyFilterRow.classList.remove('d-none');
      } else {
        emptyFilterRow.classList.add('d-none');
      }
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (branchFilter) branchFilter.addEventListener('change', filterTable);
  });

  // High-speed structural DOM sorting script
  let sortDirection = false;

  function sortTable(columnIndex, isNumeric = false) {
    const tbody = document.getElementById('matrixTableBody');
    const rows = Array.from(tbody.querySelectorAll('.inventory-row'));
    sortDirection = !sortDirection;

    rows.sort((rowA, rowB) => {
      let cellA, cellB;

      if (isNumeric) {
        // Read direct structural data attributes instead of scraping dirty text badges
        cellA = parseInt(rowA.children[columnIndex].getAttribute('data-qty') || '0', 10);
        cellB = parseInt(rowB.children[columnIndex].getAttribute('data-qty') || '0', 10);
        return sortDirection ? cellA - cellB : cellB - cellA;
      } else {
        cellA = rowA.children[columnIndex].textContent.toLowerCase().trim();
        cellB = rowB.children[columnIndex].textContent.toLowerCase().trim();
        return sortDirection ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
      }
    });

    // Re-append rows smoothly in sorted order
    rows.forEach(row => tbody.appendChild(row));
  }
</script>

<?php require dirname(__DIR__, 3) . '/app/views/layouts/employee_end.php'; ?>