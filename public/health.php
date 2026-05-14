<?php
/**
 * Database Troubleshooting & Health Check
 * Access via: http://localhost/pcx/public/health.php
 */

require_once __DIR__ . '/../app/config.php';

echo "<!DOCTYPE html><html><head><title>PCX System Health Check</title>";
echo "<style>body { font-family: Arial; margin: 20px; } .ok { color: green; } .error { color: red; } .warning { color: orange; } h2 { border-bottom: 2px solid #ddd; padding-bottom: 10px; }</style>";
echo "</head><body><h1>PCX System Health Check</h1>";

echo "<h2>1. Database Connection</h2>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
    echo "<p class='ok'>✓ Database connection successful</p>";
    
    echo "<h2>2. Table Verification</h2>";
    $tables = [
        'Branch', 'Category', 'Product', 'Promotion', 'Employee', 
        'Customer', 'Orders', 'Order_Item', 'Payment', 'Service_Ticket', 
        'Cart', 'Cart_Item', 'Wishlist', 'Inventory', 'Subcategory', 'Product_Subcategory'
    ];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        if ($exists) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<p class='ok'>✓ $table exists ($count records)</p>";
        } else {
            echo "<p class='error'>✗ $table does NOT exist</p>";
        }
    }
    
    echo "<h2>3. Test Data Verification</h2>";
    
    // Check Branches
    $branches = $pdo->query("SELECT COUNT(*) FROM Branch")->fetchColumn();
    echo "<p" . ($branches > 0 ? " class='ok'" : " class='warning'") . ">Branches: $branches</p>";
    
    // Check Products
    $products = $pdo->query("SELECT COUNT(*) FROM Product")->fetchColumn();
    echo "<p" . ($products > 0 ? " class='ok'" : " class='warning'") . ">Products: $products</p>";
    
    // Check Categories
    $categories = $pdo->query("SELECT COUNT(*) FROM Category")->fetchColumn();
    echo "<p" . ($categories > 0 ? " class='ok'" : " class='warning'") . ">Categories: $categories</p>";
    
    // Check Employees
    $employees = $pdo->query("SELECT COUNT(*) FROM Employee")->fetchColumn();
    echo "<p" . ($employees > 0 ? " class='ok'" : " class='warning'") . ">Employees: $employees</p>";
    
    // Check Customers
    $customers = $pdo->query("SELECT COUNT(*) FROM Customer")->fetchColumn();
    echo "<p" . ($customers > 0 ? " class='ok'" : " class='warning'") . ">Customers: $customers</p>";
    
    // Check Promotions
    $promotions = $pdo->query("SELECT COUNT(*) FROM Promotion")->fetchColumn();
    echo "<p" . ($promotions > 0 ? " class='ok'" : " class='warning'") . ">Promotions: $promotions</p>";
    
    echo "<h2>4. Sample Data</h2>";
    
    echo "<h3>Employees:</h3>";
    $result = $pdo->query("SELECT Emp_Id, Emp_Email, Emp_Role FROM Employee");
    while ($row = $result->fetch()) {
        echo "<p>" . htmlspecialchars($row['Emp_Email']) . " (" . htmlspecialchars($row['Emp_Role']) . ")</p>";
    }
    
    echo "<h3>Customers:</h3>";
    $result = $pdo->query("SELECT Cus_Email FROM Customer");
    while ($row = $result->fetch()) {
        echo "<p>" . htmlspecialchars($row['Cus_Email']) . "</p>";
    }
    
    echo "<h3>Products:</h3>";
    $result = $pdo->query("SELECT Prod_Id, Prod_Name, Prod_Price FROM Product LIMIT 5");
    while ($row = $result->fetch()) {
        echo "<p>" . htmlspecialchars($row['Prod_Name']) . " - PHP " . htmlspecialchars($row['Prod_Price']) . "</p>";
    }
    
    echo "<h3>Promotions:</h3>";
    $result = $pdo->query("SELECT Promo_Title, Promo_Status FROM Promotion");
    while ($row = $result->fetch()) {
        echo "<p>" . htmlspecialchars($row['Promo_Title']) . " (" . htmlspecialchars($row['Promo_Status']) . ")</p>";
    }
    
    echo "<h2 style='color: green;'>System is Ready!</h2>";
    echo "<p><a href='" . BASE_URL . "/?r=auth/auth/login'>Go to Login</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p class='warning'>Run the setup script first: <a href='" . BASE_URL . "/setup.php'>Setup Database</a></p>";
}
?>
</body></html>