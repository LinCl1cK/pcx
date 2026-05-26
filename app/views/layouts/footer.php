<footer class="bg-white border-top py-3 mt-auto" style="border-color: var(--border-color) !important;">
    <div class="container-fluid px-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 small text-muted">
        <div>
            &copy; <?= date('Y') ?> <span class="fw-bold text-dark">PCX Enterprises</span> &middot; Operational Ecosystem Engine
        </div>
        <div class="d-flex align-items-center gap-3">
            <?php if (!empty($_SESSION['employee'])): ?>
                <span class="text-danger small fw-semibold bg-red-light px-2 py-0.5 rounded border border-danger-subtle text-uppercase" style="font-size:0.7rem;">
                    <i class="bi bi-shield-fill-exclamation me-1"></i> Level Check: <?= htmlspecialchars($_SESSION['employee']['role'] ?? 'STAFF') ?>
                </span>
            <?php endif; ?>
            <a href="#" class="text-decoration-none text-secondary">Operational Protocols</a>
            <a href="#" class="text-decoration-none text-secondary">Data Audit</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('pcxSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (toggleBtn && sidebar && overlay) {
            function toggleViewLayout() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('d-none');
                overlay.classList.toggle('show');
            }
            toggleBtn.addEventListener('click', toggleViewLayout);
            overlay.addEventListener('click', toggleViewLayout);
        }
        window.BASE_URL = "<?= BASE_URL ?>";
    });
</script>
</body>

</html>