<?php
// public/index.php
declare(strict_types=1);
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/BaseModel.php';
require_once __DIR__ . '/../app/core/BaseController.php';
require_once __DIR__ . '/../app/core/View.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload modules (simple)
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../modules/',
        __DIR__ . '/../app/core/',
    ];
    foreach ($paths as $p) {
        $file = $p . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) require_once $file;
    }
});

// PDO connection
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);

// Basic routing: ?r=module/controller/action
$route = $_GET['r'] ?? 'catalog/product/home';
list($module, $controller, $action) = explode('/', $route) + [null,null,null];
$controllerClass = ucfirst($controller) . 'Controller';
$controllerFile = __DIR__ . "/../modules/{$module}/controllers/{$controllerClass}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $ctrl = new $controllerClass($pdo);
    if (method_exists($ctrl, $action)) {
        $ctrl->{$action}();
    } else {
        http_response_code(404);
        echo "Action not found";
    }
} else {
    http_response_code(404);
    echo "Controller not found";
}