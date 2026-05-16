<?php
// Back-compat: storefront layout lives under app/views/layouts/
if (!defined('PCX_ROOT')) {
    define('PCX_ROOT', dirname(__DIR__, 1));
}
require PCX_ROOT . '/../app/views/layouts/customer_header.php';
