-- =========================
-- SEED DATA FOR PCX DATABASE
-- =========================
USE pcx_db;

-- Insert Branches (required for employees)
INSERT INTO Branch (Branch_Id, Branch_Name, Branch_Location, Branch_ContactNo) VALUES
('BR-00001', 'Main Branch', '123 Main Street, Downtown', '555-0001'),
('BR-00002', 'North Branch', '456 North Ave, North District', '555-0002'),
('BR-00003', 'South Branch', '789 South St, South Side', '555-0003');

-- Insert Categories
INSERT INTO Category (Cat_Id, Cat_Name, Cat_Description) VALUES
('CAT-0001', 'Electronics', 'Electronic devices and gadgets'),
('CAT-0002', 'Computers', 'Desktop and laptop computers'),
('CAT-0003', 'Accessories', 'Computer and electronic accessories'),
('CAT-0004', 'Software', 'Operating systems and applications'),
('CAT-0005', 'Peripherals', 'Printers, scanners, and other peripherals');

-- Insert Sample Products
INSERT INTO Product (Prod_Id, Prod_Name, Prod_Brand, Prod_Price, Prod_Warranty, Prod_CatId, Prod_Image, Prod_Featured, Prod_Description, Prod_Status) VALUES
('PROD-0001', 'Laptop Pro 15', 'TechBrand', 1299.99, 24, 'CAT-0002', 'https://via.placeholder.com/300?text=Laptop', 1, 'High-performance laptop with 15-inch display', 'Active'),
('PROD-0002', 'USB-C Cable', 'AccessoryPro', 15.99, 12, 'CAT-0003', 'https://via.placeholder.com/300?text=Cable', 0, 'Fast charging USB-C cable 2 meters', 'Active'),
('PROD-0003', 'Wireless Mouse', 'PeripheralMax', 45.99, 12, 'CAT-0003', 'https://via.placeholder.com/300?text=Mouse', 1, 'Ergonomic wireless mouse with 2.4GHz', 'Active'),
('PROD-0004', 'Mechanical Keyboard', 'KeyMaster', 89.99, 24, 'CAT-0003', 'https://via.placeholder.com/300?text=Keyboard', 0, 'RGB Mechanical keyboard with custom switches', 'Active'),
('PROD-0005', 'Monitor 27"', 'DisplayPro', 349.99, 36, 'CAT-0001', 'https://via.placeholder.com/300?text=Monitor', 1, '4K IPS monitor with USB-C', 'Active'),
('PROD-0006', 'Webcam HD', 'CamTech', 79.99, 12, 'CAT-0001', 'https://via.placeholder.com/300?text=Webcam', 0, '1080p HD webcam with auto-focus', 'Active'),
('PROD-0007', 'Printer MultiFunc', 'PrintMax', 199.99, 24, 'CAT-0005', 'https://via.placeholder.com/300?text=Printer', 0, 'All-in-one printer with wireless', 'Active'),
('PROD-0008', 'External SSD 1TB', 'StoragePro', 129.99, 36, 'CAT-0001', 'https://via.placeholder.com/300?text=SSD', 1, 'Portable SSD with 1TB capacity', 'Active');

-- Insert Promotions
INSERT INTO Promotion (Promo_Title, Promo_Description, Promo_Banner, Promo_Status, Promo_Start, Promo_End) VALUES
('Summer Sale', '20% off on all electronics', 'https://via.placeholder.com/800?text=Summer+Sale', 'Active', '2026-05-01', '2026-06-30'),
('Back to School', '15% discount for students', 'https://via.placeholder.com/800?text=Back+to+School', 'Active', '2026-07-01', '2026-09-30'),
('Black Friday', 'Up to 50% off on selected items', 'https://via.placeholder.com/800?text=Black+Friday', 'Inactive', '2026-11-01', '2026-11-30');

-- Insert Admin Employee (password: admin123)
INSERT INTO Employee (Emp_Id, Emp_Fname, Emp_Lname, Emp_Role, Emp_BranchId, Emp_Email, Emp_PasswordHash) VALUES
('EMP-00001', 'Admin', 'User', 'Administrator', 'BR-00001', 'admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.'),
('EMP-00002', 'John', 'Sales', 'Sales Representative', 'BR-00001', 'john', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.'),
('EMP-00003', 'Tech', 'Support', 'Technician', 'BR-00002', 'tech', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.');

-- Insert Sample Customers (password: customer123)
INSERT INTO Customer (Cus_Id, Cus_Fname, Cus_Lname, Cus_Email, Cus_Password, Cus_ContactNo, Cus_Address) VALUES
('CUST-00001', 'Test', 'Customer', 'customer@test.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.', '555-1234', '100 Test Street, Test City'),
('CUST-00002', 'Jane', 'Doe', 'jane@test.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/TVm.', '555-5678', '200 Doe Avenue, Test City');

-- Insert Inventory
INSERT INTO Inventory (Inv_Id, Inv_ProdId, Inv_BranchId, Inv_StockQty, Inv_ReorderLevel) VALUES
('INV-00001', 'PROD-0001', 'BR-00001', 15, 5),
('INV-00002', 'PROD-0002', 'BR-00001', 100, 20),
('INV-00003', 'PROD-0003', 'BR-00001', 50, 10),
('INV-00004', 'PROD-0004', 'BR-00002', 25, 5),
('INV-00005', 'PROD-0005', 'BR-00001', 8, 3),
('INV-00006', 'PROD-0006', 'BR-00002', 30, 10),
('INV-00007', 'PROD-0007', 'BR-00003', 12, 4),
('INV-00008', 'PROD-0008', 'BR-00001', 20, 5);
