<?php
/**
 * Database Setup Script - Run this ONCE to initialize the database
 * Place this file in the public folder and access via: http://localhost/pcx/public/setup.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/config.php';

echo "<h1>PCX Database Setup</h1>";

try {
    // Create database connection without selecting a database
    $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
    
    echo "<h2>Step 1: Dropping existing database...</h2>";
    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
    echo "✓ Dropped existing database<br>";
    
    echo "<h2>Step 2: Creating database...</h2>";
    $pdo->exec("CREATE DATABASE " . DB_NAME);
    echo "✓ Created database<br>";
    
    // Now select the new database
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
    
    echo "<h2>Step 3: Creating tables...</h2>";
    $sqlFile = __DIR__ . '/../sql/001_create_tables.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        // Execute SQL statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement . ';');
            }
        }
        echo "✓ Tables created successfully<br>";
    } else {
        echo "✗ SQL file not found: $sqlFile<br>";
    }
    
    echo "<h2>Step 4: Inserting seed data...</h2>";
    
    // Insert Branches
    $branches = [
        ['BR-00001', 'Main Branch', '123 Main Street, Downtown', '555-0001'],
        ['BR-00002', 'North Branch', '456 North Ave, North District', '555-0002'],
        ['BR-00003', 'South Branch', '789 South St, South Side', '555-0003'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO Branch (Branch_Id, Branch_Name, Branch_Location, Branch_ContactNo) VALUES (?, ?, ?, ?)");
    foreach ($branches as $branch) {
        $stmt->execute($branch);
    }
    echo "✓ Branches inserted<br>";
    
    // Insert Categories
    $categories = [
        ['CAT-0001', 'Electronics', 'Electronic devices and gadgets'],
        ['CAT-0002', 'Computers', 'Desktop and laptop computers'],
        ['CAT-0003', 'Accessories', 'Computer and electronic accessories'],
        ['CAT-0004', 'Software', 'Operating systems and applications'],
        ['CAT-0005', 'Peripherals', 'Printers, scanners, and other peripherals'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO Category (Cat_Id, Cat_Name, Cat_Description) VALUES (?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "✓ Categories inserted<br>";
    
    // Insert Products
    $products = [
        ['PROD-0001', 'Laptop Pro 15', 'TechBrand', 1299.99, 24, 'CAT-0002', 'https://via.placeholder.com/300?text=Laptop', 1, 'High-performance laptop with 15-inch display', 'Active'],
        ['PROD-0002', 'USB-C Cable', 'AccessoryPro', 15.99, 12, 'CAT-0003', 'https://via.placeholder.com/300?text=Cable', 0, 'Fast charging USB-C cable 2 meters', 'Active'],
        ['PROD-0003', 'Wireless Mouse', 'PeripheralMax', 45.99, 12, 'CAT-0003', 'https://via.placeholder.com/300?text=Mouse', 1, 'Ergonomic wireless mouse with 2.4GHz', 'Active'],
        ['PROD-0004', 'Mechanical Keyboard', 'KeyMaster', 89.99, 24, 'CAT-0003', 'https://via.placeholder.com/300?text=Keyboard', 0, 'RGB Mechanical keyboard', 'Active'],
        ['PROD-0005', 'Monitor 27"', 'DisplayPro', 349.99, 36, 'CAT-0001', 'https://via.placeholder.com/300?text=Monitor', 1, '4K IPS monitor with USB-C', 'Active'],
        ['PROD-0006', 'Webcam HD', 'CamTech', 79.99, 12, 'CAT-0001', 'https://via.placeholder.com/300?text=Webcam', 0, '1080p HD webcam with auto-focus', 'Active'],
        ['PROD-0007', 'Printer MultiFunc', 'PrintMax', 199.99, 24, 'CAT-0005', 'https://via.placeholder.com/300?text=Printer', 0, 'All-in-one printer with wireless', 'Active'],
        ['PROD-0008', 'External SSD 1TB', 'StoragePro', 129.99, 36, 'CAT-0001', 'https://via.placeholder.com/300?text=SSD', 1, 'Portable SSD with 1TB capacity', 'Active'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO Product (Prod_Id, Prod_Name, Prod_Brand, Prod_Price, Prod_Warranty, Prod_CatId, Prod_Image, Prod_Featured, Prod_Description, Prod_Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $prod) {
        $stmt->execute($prod);
    }
    echo "✓ Products inserted<br>";
    
    // Insert Promotions
    $promos = [
        ['Summer Sale', '20% off on all electronics', 'https://via.placeholder.com/800?text=Summer+Sale', 'Active', '2026-05-01', '2026-06-30'],
        ['Back to School', '15% discount for students', 'https://via.placeholder.com/800?text=Back+to+School', 'Active', '2026-07-01', '2026-09-30'],
        ['Black Friday', 'Up to 50% off on selected items', 'https://via.placeholder.com/800?text=Black+Friday', 'Inactive', '2026-11-01', '2026-11-30'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO Promotion (Promo_Title, Promo_Description, Promo_Banner, Promo_Status, Promo_Start, Promo_End) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($promos as $promo) {
        $stmt->execute($promo);
    }
    echo "✓ Promotions inserted<br>";
    
    // Insert Employees with test password 'admin123'
    // Hash: $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.
    $employees = [
        ['EMP-00001', 'Admin', 'User', 'Administrator', 'BR-00001', 'admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.'],
        ['EMP-00002', 'John', 'Sales', 'Sales Representative', 'BR-00001', 'john', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.'],
        ['EMP-00003', 'Tech', 'Support', 'Technician', 'BR-00002', 'tech', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO Employee (Emp_Id, Emp_Fname, Emp_Lname, Emp_Role, Emp_BranchId, Emp_Email, Emp_PasswordHash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($employees as $emp) {
        $stmt->execute($emp);
    }
    echo "✓ Employees inserted<br>";
    
    // Insert Customers with test password 'customer123'
    $customers = [
        ['CUST-00001', 'Test', 'Customer', 'customer@test.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.', '555-1234', '100 Test Street, Test City'],
        ['CUST-00002', 'Jane', 'Doe', 'jane@test.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.', '555-5678', '200 Doe Avenue, Test City'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO Customer (Cus_Id, Cus_Fname, Cus_Lname, Cus_Email, Cus_Password, Cus_ContactNo, Cus_Address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($customers as $cust) {
        $stmt->execute($cust);
    }
    echo "✓ Customers inserted<br>";
    
    // Insert Inventory
    $inventory = [
        ['INV-00001', 'PROD-0001', 'BR-00001', 15, 5],
        ['INV-00002', 'PROD-0002', 'BR-00001', 100, 20],
        ['INV-00003', 'PROD-0003', 'BR-00001', 50, 10],
        ['INV-00004', 'PROD-0004', 'BR-00002', 25, 5],
        ['INV-00005', 'PROD-0005', 'BR-00001', 8, 3],
        ['INV-00006', 'PROD-0006', 'BR-00002', 30, 10],
        ['INV-00007', 'PROD-0007', 'BR-00003', 12, 4],
        ['INV-00008', 'PROD-0008', 'BR-00001', 20, 5],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO Inventory (Inv_Id, Inv_ProdId, Inv_BranchId, Inv_StockQty, Inv_ReorderLevel) VALUES (?, ?, ?, ?, ?)");
    foreach ($inventory as $inv) {
        $stmt->execute($inv);
    }
    echo "✓ Inventory inserted<br>";
    
    echo "<h2 style='color: green;'>Setup Complete!</h2>";
    echo "<h3>Test Credentials:</h3>";
    echo "<p><strong>Employee Login:</strong><br>";
    echo "Username: <code>admin</code>, Password: <code>admin123</code> (Administrator)<br>";
    echo "Username: <code>john</code>, Password: <code>admin123</code> (Sales Representative)<br>";
    echo "Username: <code>tech</code>, Password: <code>admin123</code> (Technician)</p>";
    echo "<p><strong>Customer Login/Register:</strong><br>";
    echo "Email: <code>customer@test.local</code>, Password: <code>customer123</code><br>";
    echo "Or register a new account</p>";
    echo "<p><a href='" . BASE_URL . "/?r=auth/auth/login' class='btn btn-primary'>Go to Login</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>