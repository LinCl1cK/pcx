<?php
/**
 * PCX Corporate Console - Structural Termination 
 */
?>
        </main>
    </div> </div>

<?php
/**
 * PCX Admin — Integrated Footer Component
 */
$footerRawRole = strtolower((string)($_SESSION['employee']['Emp_Position'] ?? $_SESSION['employee']['role'] ?? ''));
$footerRole    = str_replace('_', ' ', $footerRawRole);
?>

<?php require __DIR__ . '/footer.php'; ?>