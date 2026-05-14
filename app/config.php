<?php
// app/config.php
declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'pcx_db');
define('DB_USER', 'root');
define('DB_PASS', 'L1nm@rrr'); // change if you use a password
define('BASE_URL', '/pcx/public'); // adjust if different

/** Project root (directory containing /app and /modules) */
define('PCX_ROOT', dirname(__DIR__));

// PDO options
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
