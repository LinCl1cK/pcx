<?php
/**
 * PCX Corporate Console - Structural Initialization
 */
$employee = $employee ?? ($_SESSION['employee'] ?? []);
require __DIR__ . '/header.php';

// Launch the core multi-pane application container framework
require __DIR__ . '/nav.php';   