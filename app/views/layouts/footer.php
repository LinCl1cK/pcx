    </body>
    <footer class="bg-dark text-light py-5 mt-auto border-top border-secondary">
        <div class="container-fluid px-4">
            <div class="row g-4">
                
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-uppercase fw-bold mb-3 text-info">
                        <i class="bi bi-cpu-fill me-2"></i>PCX Enterprise
                    </h5>
                    <p class="text-secondary small pe-lg-4 mb-3">
                        Internal administration, fulfillment, and operations console. Ensure active sessions are locked when leaving workstations.
                    </p>
                    <div class="d-flex gap-3 small text-secondary">
                        <span><i class="bi bi-building me-1"></i> HQ Network</span>
                        <span class="text-muted">|</span>
                        <span><i class="bi bi-shield-check me-1"></i> Secure Connection</span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="text-uppercase fw-bold mb-3 text-secondary">Operations</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>/?r=catalog/product/list" class="text-secondary text-decoration-none hover-white">
                                <i class="bi bi-box-seam me-1"></i> Catalog Admin
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-secondary text-decoration-none hover-white">
                                <i class="bi bi-graph-up-arrow me-1"></i> Inventory Stock
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-secondary text-decoration-none hover-white">
                                <i class="bi bi-receipt me-1"></i> Orders Hub
                            </a>
                        </li>
                        
                        <?php if (isset($_SESSION['employee']['role']) && in_array($_SESSION['employee']['role'], ['admin', 'manager'])): ?>
                            <li class="mb-2">
                                <a href="#" class="text-secondary text-decoration-none hover-white">
                                    <i class="bi bi-graph-up me-1"></i> Business Reporting
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase fw-bold mb-3 text-secondary">
                        <?= (isset($_SESSION['employee']['role']) && $_SESSION['employee']['role'] === 'admin') ? 'System Control' : 'Staff Resources'; ?>
                    </h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>/?r=service/service/index" class="text-secondary text-decoration-none hover-white">
                                <i class="bi bi-ticket-perforated me-1"></i> Ticket Queue
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-secondary text-decoration-none hover-white">
                                <i class="bi bi-book me-1"></i> Knowledge Base (SOP)
                            </a>
                        </li>
                        
                        <?php if (isset($_SESSION['employee']['role']) && $_SESSION['employee']['role'] === 'admin'): ?>
                            <li class="mb-2">
                                <a href="#" class="text-info text-decoration-none hover-white">
                                    <i class="bi bi-people-fill me-1"></i> Manage Access Roles
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-info text-decoration-none hover-white">
                                    <i class="bi bi-gear-fill me-1"></i> Core Configurations
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a href="#" class="text-secondary text-decoration-none hover-white">
                                    <i class="bi bi-calendar-event me-1"></i> Shift Schedule
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase fw-bold mb-3 text-secondary">Session</h6>
                    
                    <?php if (empty($_SESSION['employee'])): ?>
                        <p class="text-secondary small mb-3">No active corporate session identified.</p>
                        <button type="button" class="btn btn-outline-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#authModal">
                            <i class="bi bi-lock-fill me-1"></i> Portal Sign In
                        </button>
                    <?php else: ?>
                        <div class="p-2 bg-dark-subtle rounded border border-secondary mb-2 small">
                            <span class="text-muted d-block small">Active User:</span>
                            <strong class="text-light"><?= htmlspecialchars($_SESSION['employee']['name'] ?? 'Staff Member') ?></strong>
                            <span class="badge bg-info text-dark ms-1 text-uppercase text-xs" style="font-size: 0.7rem;">
                                <?= htmlspecialchars($_SESSION['employee']['role'] ?? 'Staff') ?>
                            </span>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="<?= BASE_URL ?>/?r=auth/employee/dashboard" class="btn btn-outline-light btn-sm w-100 text-nowrap">
                                    <i class="bi bi-speedometer2 me-1"></i> Portal
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= BASE_URL ?>/?r=auth/employee/logout" class="btn btn-outline-danger btn-sm w-100 text-nowrap">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            
            <hr class="border-secondary mt-4 mb-3">
            
            <div class="row align-items-center small text-secondary">
                <div class="col-md-6 text-center text-md-start">
                    &copy; <?= date('Y') ?> <strong>PCX Store</strong> · Internal Use Only
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <span class="me-3 text-warning small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> 
                        Confidential (<?= htmlspecialchars(strtoupper($_SESSION['employee']['role'] ?? 'RESTRICTED')) ?>)
                    </span>
                    <a href="#" class="text-secondary text-decoration-none me-3">Infosec Guidelines</a>
                    <a href="#" class="text-secondary text-decoration-none">Data Privacy</a>
                </div>
            </div>
        </div>
    
    
        <script>
            window.BASE_URL = "<?= BASE_URL ?>";
        </script>
        <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <style>
        /* Optional Footer Custom CSS to add to your style.css */
        footer a.hover-white:hover {
            color: #ffffff !important;
            text-decoration: underline !important;
        }
        </style>
    </footer>
</html>