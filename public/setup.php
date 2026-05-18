<?php
/**
 * Database Setup Script - Full CSV Integration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/config.php';

echo "<h1>PCX Database Setup: Full Integration</h1>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "<h2>Step 1: Reinitializing Database...</h2>";
    $pdo->exec("DROP DATABASE IF EXISTS `pcx_db` ");
    $pdo->exec("CREATE DATABASE `pcx_db` ");
    
    $dsn = "mysql:host=" . DB_HOST . ";dbname=pcx_db;charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "<h2>Step 2: Creating Tables...</h2>";
    $sql = file_get_contents(__DIR__ . '/../sql/001_create_tables.sql');
    $pdo->exec($sql);

    // 1. BRANCHES (Cebu)
    echo "<h3>Populating Branches...</h3>";
    $branches = [
        ['BRAN-001', 'PC Express - SM Seaside Cebu', 'SM Seaside City, SRP, Cebu City', '032-2556543'],
        ['BRAN-002', 'PCX Gaming - SM City Cebu', 'SM City Cebu, Cyberzone, 2F', '032-3439343'],
        ['BRAN-003', 'PC Express - Ayala Center Cebu', 'Ayala Center Cebu, Cebu Business Park', '032-2311234'],
        ['BRAN-004', 'PC Express - Robinsons Galleria Cebu', 'Robinsons Galleria, Gen. Maxilom Ave, Cebu City', '032-2687654']
    ];
    $stmt = $pdo->prepare("INSERT INTO branch (Branch_Id, Branch_Name, Branch_Location, Branch_ContactNo) VALUES (?, ?, ?, ?)");
    foreach ($branches as $b) $stmt->execute($b);

    // 2. EMPLOYEES (Covering all roles per schema)
    echo "<h3>Populating Employees...</h3>";
    $employees = [
        ['EMP-001', 'Juan', 'Dela Cruz', 'juan.dc@pcx.com.ph', 'Administrator', 'BRAN-001', password_hash('admin123', PASSWORD_DEFAULT), '09171234567', '123 Main St, Cebu City'],
        ['EMP-002', 'Maria', 'Santos', 'm.santos@pcx.com.ph', 'Sales Representative', 'BRAN-001', password_hash('sales123', PASSWORD_DEFAULT), '0917589579', '456 Oak Ave, Cebu City'],
        ['EMP-003', 'Rico', 'Blanco', 'r.blanco@pcx.com.ph', 'Technician', 'BRAN-002', password_hash('tech123', PASSWORD_DEFAULT), '09172768142', '789 Pine Rd, Cebu City'],
        ['EMP-004', 'Elena', 'Adarna', 'e.adarna@pcx.com.ph', 'Manager', 'BRAN-003', password_hash('manager123', PASSWORD_DEFAULT), '09179385672', '321 Elm St, Cebu City'],
        ['EMP-005', 'Kevin', 'Alas', 'k.alas@pcx.com.ph', 'Sales Representative', 'BRAN-004', password_hash('sales123', PASSWORD_DEFAULT), '09172548571', '654 Maple Dr, Cebu City']
    ];
    $stmt = $pdo->prepare("INSERT INTO employee (Emp_Id, Emp_Fname, Emp_Lname, Emp_Email, Emp_Position, Emp_BranchId, Emp_Password, Emp_ContactNo, Emp_Address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($employees as $e) $stmt->execute($e);

    // 3. CATEGORIES (Full Cat.csv)
    echo "<h3>Populating All Categories...</h3>";
    $categories = [
        ['CAT-001', 'Laptops', 'Portable computers for work and gaming'],
        ['CAT-002', 'PC Components', 'Motherboards, CPUs, GPUs, RAM, Storage'],
        ['CAT-003', 'Monitors', 'LCD, LED, Gaming Displays'],
        ['CAT-004', 'Smartphones', 'Mobile phones and accessories'],
        ['CAT-005', 'Peripherals', 'Keyboards, Mice, Headsets, Accessories'],
        ['CAT-006', 'Software', 'Operating systems and productivity tools'],
        ['CAT-007', 'Desktop PCs', 'Custom and prebuilt desktop computers for work and gaming'],
        ['CAT-008', 'Gaming Devices & Accessories', 'Consoles, gaming peripherals, and accessories']
    ];
    $stmt = $pdo->prepare("INSERT INTO category (Cat_Id, Cat_Name, Cat_Description) VALUES (?, ?, ?)");
    foreach ($categories as $c) $stmt->execute($c);

    // 4. SUBCATEGORIES (Full Subc.csv)
    echo "<h3>Populating All Subcategories...</h3>";
    $subc = [
        ['SUBC-001', 'Gaming', 'High performance gaming products'],
        ['SUBC-002', 'Powered by ASUS', 'ASUS certified builds and devices'],
        ['SUBC-003', 'Intel i7', 'Intel Core i7 processors'],
        ['SUBC-004', 'NVIDIA', 'NVIDIA graphics powered devices'],
        ['SUBC-005', 'Business Solutions', 'Enterprise and productivity systems'],
        ['SUBC-006', 'AMD Ryzen', 'AMD Ryzen processors'],
        ['SUBC-007', 'Samsung Monitors', 'Samsung branded displays'],
        ['SUBC-008', 'Microsoft', 'Microsoft software and solutions'],
        ['SUBC-009', 'Mechanical Keyboards', 'Tactile and high-durability input devices'],
        ['SUBC-010', 'Gaming Headsets', 'Immersive audio solutions for gamers'],
        ['SUBC-011', 'Solid State Drives (SSD)', 'High-speed internal and external storage'],
        ['SUBC-012', 'Liquid Cooling', 'AIO and custom loop cooling solutions'],
        ['SUBC-013', 'Productivity Monitors', 'Color-accurate and large-format displays for work'],
        ['SUBC-014', 'Motherboards', 'Intel and AMD socket compatible boards'],
        ['SUBC-015', 'External Storage', 'Portable hard drives and high-speed flash storage']
    ];
    $stmt = $pdo->prepare("INSERT INTO subcategory (Subc_Id, Subc_Name, Subc_Description) VALUES (?, ?, ?)");
    foreach ($subc as $s) $stmt->execute($s);

// 5. POPULATE ALL PRODUCTS (MUST BE DEFINED BEFORE INVENTORY)
    echo "<h3>Populating All Products...</h3>";
    $products = [
        ['PROD-001', 'ASUS ROG Strix G16', 'ASUS', 89995, 24, 'CAT-001', 'rog_g16.png', 0],
        ['PROD-002', 'Lenovo IdeaPad Slim 3', 'Lenovo', 54995, 12, 'CAT-001', 'ideapad_slim3.png', 0],
        ['PROD-003', 'Intel Core i7-14700K', 'Intel', 24995, 36, 'CAT-002', 'intel_i7.png', 0],
        ['PROD-004', 'NVIDIA GeForce RTX 4060', 'NVIDIA', 19995, 24, 'CAT-002', 'rtx4060.png', 0],
        ['PROD-005', 'Samsung Odyssey G5 Monitor', 'Samsung', 15995, 12, 'CAT-003', 'odyssey_g5.png', 0],
        ['PROD-006', 'AMD Ryzen 7 7800X3D', 'AMD', 22995, 36, 'CAT-002', 'ryzen7800.png', 0],
        ['PROD-007', 'Microsoft Office 365', 'Microsoft', 3995, 12, 'CAT-006', 'office365.png', 0],
        ['PROD-008', 'MSI Raider GE78 HX', 'MSI', 124995, 24, 'CAT-001', 'msi_raider.webp', 1],
        ['PROD-009', 'Gigabyte AORUS Master Z790', 'Gigabyte', 28995, 36, 'CAT-002', 'aorus_z790.png', 1],
        ['PROD-010', 'NVIDIA GeForce RTX 4070 Super', 'NVIDIA', 34995, 24, 'CAT-002', 'rtx4070super.png', 1],
        ['PROD-011', 'LG UltraGear 32GP850', 'LG', 22995, 12, 'CAT-003', 'lg_ultragear.webp', 1],
        ['PROD-012', 'Razer BlackShark V2 Pro', 'Razer', 9995, 12, 'CAT-005', 'razer_blackshark.png', 1],
        ['PROD-013', 'Corsair iCUE H150i Elite', 'Corsair', 12995, 36, 'CAT-002', 'corsair_h150i.png', 1],
        ['PROD-014', 'Apple MacBook Pro M3', 'Apple', 129995, 24, 'CAT-001', 'macbook_pro_m3.png', 1],
        ['PROD-015', 'Seagate FireCuda 530 2TB SSD', 'Seagate', 15995, 36, 'CAT-002', 'firecuda_530.jpg', 1]
    ];
    $stmt = $pdo->prepare("INSERT INTO product (Prod_Id, Prod_Name, Prod_Brand, Prod_Price, Prod_Warranty, Prod_CatId, Prod_Image, Prod_Featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) $stmt->execute($p);

    // 6. ENHANCED PRODUCT-SUBCATEGORY MAPPING (Keep this, delete your old Step 6)
    echo "<h3>Mapping Products to Appropriate Subcategories...</h3>";
    $links = [
        ['PROD-001', 'SUBC-001'], ['PROD-001', 'SUBC-002'], ['PROD-001', 'SUBC-004'],
        ['PROD-002', 'SUBC-005'], ['PROD-008', 'SUBC-001'], ['PROD-014', 'SUBC-005'],
        ['PROD-003', 'SUBC-003'], ['PROD-006', 'SUBC-006'], ['PROD-004', 'SUBC-004'],
        ['PROD-010', 'SUBC-004'], ['PROD-005', 'SUBC-007'], ['PROD-011', 'SUBC-007'],
        ['PROD-015', 'SUBC-011'], ['PROD-015', 'SUBC-015'], ['PROD-012', 'SUBC-010'],
        ['PROD-013', 'SUBC-012'], ['PROD-009', 'SUBC-014'], ['PROD-007', 'SUBC-008']
    ];
    $stmt = $pdo->prepare("INSERT INTO product_subcategory (Prod_Id, Subc_Id) VALUES (?, ?)");
    foreach ($links as $l) $stmt->execute($l);

    // 7. PROMOTIONS (Promo.csv)
    echo "<h3>Populating Promotions...</h3>";
    $promos = [
        [1, 'Kingston Banner', 'High-performance memory and storage solutions.', 'Kingston-Banner-1800x600.webp', 'Active', '2026-05-01', '2026-06-30'],
        [2, 'PCX Promo', 'Exclusive PCX deals and bundles.', 'PCX_1800x600_b2923a14-4d61-449c-ab4b-c3a509c0a10f.webp', 'Active', '2026-05-01', '2026-06-30'],
        [3, 'Business Solutions', 'Reliable systems for productivity and enterprise.', '1800p_x_600p_Business_solutions.webp', 'Active', '2026-05-01', '2026-06-30'],
        [4, 'Samsung AI Monitor', 'Next-gen AI-powered displays.', '1800x600-Samsung-AI-Monitor.webp', 'Active', '2026-05-01', '2026-06-30'],
        [5, 'AMD Aorus Family', 'Gaming hardware powered by AMD.', 'AMD_Aorus_Family_-_1800x600_f451b045-c395-43b5-8f79-9a58d00b5f69.webp', 'Active', '2026-05-01', '2026-06-30']
    ];
    $stmt = $pdo->prepare("INSERT INTO promotion (Promo_Id, Promo_Title, Promo_Description, Promo_Banner, Promo_Status, Promo_Start, Promo_End) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($promos as $pr) $stmt->execute($pr);

    // 8. INVENTORY (Distributed with partial stocks)
    echo "<h3>Distributing Inventory to Branches...</h3>";
    // Distributing all products with logical branch variations
    $inventory = [];
    $invCount = 1;
    $branchIds = ['BRAN-001', 'BRAN-002', 'BRAN-003', 'BRAN-004'];
    
    foreach ($products as $idx => $p) {
        // Distribute to 2-3 branches only to ensure incomplete stock per branch
        $assignedBranches = array_rand(array_flip($branchIds), rand(2, 3));
        foreach ($assignedBranches as $bId) {
            $idStr = "INV-" . str_pad($invCount++, 3, "0", STR_PAD_LEFT);
            $qty = rand(5, 50);
            $reorder = 10;
            $inventory[] = [$idStr, $p[0], $bId, $qty, $reorder];
        }
    }
    $stmt = $pdo->prepare("INSERT INTO inventory (Inv_Id, Inv_ProdId, Inv_BranchId, Inv_StockQty, Inv_ReorderLevel) VALUES (?, ?, ?, ?, ?)");
    foreach ($inventory as $inv) $stmt->execute($inv);

    // 9. CUSTOMER (Distributed with partial stocks)
    echo "<h3>Populating Customers...</h3>";
    $customer = [
        ['CUS26179', 'Lin', 'Mar', 'lin.mar@gmail.com', password_hash('L1nm@rrr', PASSWORD_DEFAULT), '09638730869', 'Hernan Cortes, Mandaue City']
    ];
    $stmt = $pdo->prepare("INSERT INTO customer (Cus_Id, Cus_Fname, Cus_Lname, Cus_Email, Cus_Password, Cus_ContactNo, Cus_Address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($customer as $c) $stmt->execute($c);

    // 10. TRIGGERS AND STORED PROCEDURES
    echo "<h2>Step 3: Creating Triggers and Stored Procedures...</h2>";
    $sqlPath = __DIR__ . '/../sql/002_triggers_procedures.sql';

    if (file_exists($sqlPath)) {
        $sqlContent = file_get_contents($sqlPath);
        
        // 1. Strip out client-side DELIMITER lines entirely
        $sqlContent = preg_replace('/DELIMITER\s+\S+/i', '', $sqlContent);
        
        // 2. Break statements apart using the custom '$$' delimiter
        $statements = explode('$$', $sqlContent);
        
        foreach ($statements as $query) {
            $trimmedQuery = trim($query);
            if (!empty($trimmedQuery)) {
                // Execute each individual trigger or procedure definition
                $pdo->exec($trimmedQuery);
            }
        }
        echo "<p style='color:green;'>Triggers and procedures successfully injected!</p>";
    } else {
        die("Setup failed: Triggers file not found at " . htmlspecialchars($sqlPath));
    }

    echo "<h2 style='color: green;'>Full Integration Success!</h2>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());

}