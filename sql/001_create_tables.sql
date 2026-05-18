-- ==========================================================
-- PCX_DB COMPREHENSIVE SCHEMA COMPILATION
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `pcx_db`;
USE `pcx_db`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */; -- Add this line here
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- ---------------------------------------------------------
-- 1. INDEPENDENT TABLES (Level 0)
-- ---------------------------------------------------------

-- From: pcx_db_branch.sql
DROP TABLE IF EXISTS `branch`;
CREATE TABLE `branch` (
  `Branch_Id` char(10) NOT NULL,
  `Branch_Name` varchar(100) NOT NULL,
  `Branch_Location` varchar(150) NOT NULL,
  `Branch_ContactNo` varchar(15) NOT NULL,
  `Branch_CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Branch_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_category.sql
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `Cat_Id` char(10) NOT NULL,
  `Cat_Name` varchar(50) NOT NULL,
  `Cat_Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Cat_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_subcategory.sql
DROP TABLE IF EXISTS `subcategory`;
CREATE TABLE `subcategory` (
  `Subc_Id` char(10) NOT NULL,
  `Subc_Name` varchar(50) NOT NULL,
  `Subc_Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Subc_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_customer.sql
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `Cus_Id` char(10) NOT NULL,
  `Cus_Fname` varchar(50) NOT NULL,
  `Cus_Lname` varchar(50) NOT NULL,
  `Cus_Email` varchar(255) NOT NULL,
  `Cus_Password` varchar(255) NOT NULL,
  `Cus_ContactNo` varchar(15) NOT NULL,
  `Cus_Address` varchar(255) NOT NULL,
  `Cus_IdAttachment` VARCHAR(255) NULL,
  `Cus_IsVerified` ENUM('Unverified', 'Pending', 'Verified', 'Rejected') NOT NULL DEFAULT 'Unverified',
  `Cus_CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Cus_Id`),
  UNIQUE KEY `Cus_Email` (`Cus_Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_promotion.sql
DROP TABLE IF EXISTS `promotion`;
CREATE TABLE `promotion` (
  `Promo_Id` int NOT NULL AUTO_INCREMENT,
  `Promo_Title` varchar(100) NOT NULL,
  `Promo_Description` text,
  `Promo_Banner` varchar(255) NOT NULL,
  `Promo_Status` enum('Active','Inactive') NOT NULL,
  `Promo_Start` date DEFAULT NULL,
  `Promo_End` date DEFAULT NULL,
  PRIMARY KEY (`Promo_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ---------------------------------------------------------
-- 2. PRIMARY DEPENDENT TABLES (Level 1)
-- ---------------------------------------------------------

-- From: pcx_db_employee.sql
DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `Emp_Id` char(10) NOT NULL,
  `Emp_Fname` varchar(50) NOT NULL,
  `Emp_Lname` varchar(50) NOT NULL,
  `Emp_Email` VARCHAR(255) NOT NULL,
  `Emp_Position` varchar(50) NOT NULL,
  `Emp_BranchId` char(10) NOT NULL,
  `Emp_Password` varchar(255) NOT NULL,
  `Emp_ContactNo` varchar(15) NOT NULL,
  `Emp_Address` varchar(255) NOT NULL,
  `Emp_CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Emp_Id`),
  KEY `Emp_BranchId` (`Emp_BranchId`),
  CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`Emp_BranchId`) REFERENCES `branch` (`Branch_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_product.sql
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `Prod_Id` char(10) NOT NULL,
  `Prod_Name` varchar(100) NOT NULL,
  `Prod_Brand` varchar(50) NOT NULL,
  `Prod_Price` decimal(10,2) NOT NULL,
  `Prod_Warranty` int DEFAULT '0',
  `Prod_CatId` char(10) NOT NULL,
  `Prod_Image` varchar(255) DEFAULT NULL,
  `Prod_CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Prod_UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Prod_Featured` tinyint(1) DEFAULT '0',
  `Prod_Description` text,
  `Prod_Status` enum('Active','Inactive','Discontinued') DEFAULT 'Active',
  PRIMARY KEY (`Prod_Id`),
  KEY `Prod_CatId` (`Prod_CatId`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Prod_CatId`) REFERENCES `category` (`Cat_Id`),
  CONSTRAINT `product_chk_1` CHECK ((`Prod_Price` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_cart.sql
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `Cart_Id` char(10) NOT NULL,
  `Cart_CusId` char(10) NOT NULL,
  `Cart_CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Cart_LastUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Cart_Id`),
  KEY `Cart_CusId` (`Cart_CusId`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`Cart_CusId`) REFERENCES `customer` (`Cus_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ---------------------------------------------------------
-- 3. SECONDARY DEPENDENT TABLES (Level 2)
-- ---------------------------------------------------------

-- From: pcx_db_inventory.sql
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `Inv_Id` char(10) NOT NULL,
  `Inv_ProdId` char(10) NOT NULL,
  `Inv_BranchId` char(10) NOT NULL,
  `Inv_StockQty` int NOT NULL DEFAULT '0',
  `Inv_ReorderLevel` int NOT NULL DEFAULT '10',
  `Inv_LastUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Inv_Id`),
  KEY `Inv_ProdId` (`Inv_ProdId`),
  KEY `Inv_BranchId` (`Inv_BranchId`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`Inv_ProdId`) REFERENCES `product` (`Prod_Id`),
  CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`Inv_BranchId`) REFERENCES `branch` (`Branch_Id`),
  CONSTRAINT `inventory_chk_1` CHECK ((`Inv_StockQty` >= 0)),
  CONSTRAINT `inventory_chk_2` CHECK ((`Inv_ReorderLevel` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_orders.sql
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `Order_Id` char(10) NOT NULL,
  `Order_Date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Order_Status` enum('Pending','Confirmed','Paid','Completed','Cancelled') NOT NULL,
  `Order_Shipping` enum('Delivery','Pickup') NOT NULL,
  `Order_DestinationAddress` TEXT NOT NULL,
  `Order_ContactNo` VARCHAR(15) NULL,
  `Order_CusId` char(10) NOT NULL,
  `Order_VerifiedBy` char(10) DEFAULT NULL,
  `Order_InvoiceNo` varchar(20) NOT NULL,
  `Order_InvoiceDate` timestamp NULL DEFAULT NULL,
  `Order_VAT` decimal(10,2) DEFAULT '0.00',
  `Order_TotalAmount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Order_Id`),
  KEY `Order_CusId` (`Order_CusId`),
  KEY `Order_VerifiedBy` (`Order_VerifiedBy`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`Order_CusId`) REFERENCES `customer` (`Cus_Id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`Order_VerifiedBy`) REFERENCES `employee` (`Emp_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_wishlist.sql
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `Wish_Id` char(10) NOT NULL,
  `Wish_CusId` char(10) NOT NULL,
  `Wish_ProdId` char(10) NOT NULL,
  `Wish_AddedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Wish_Id`),
  KEY `Wish_CusId` (`Wish_CusId`),
  KEY `Wish_ProdId` (`Wish_ProdId`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`Wish_CusId`) REFERENCES `customer` (`Cus_Id`),
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`Wish_ProdId`) REFERENCES `product` (`Prod_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_product_subcategory.sql
DROP TABLE IF EXISTS `product_subcategory`;
CREATE TABLE `product_subcategory` (
  `Prod_Id` char(10) NOT NULL,
  `Subc_Id` char(10) NOT NULL,
  PRIMARY KEY (`Prod_Id`,`Subc_Id`),
  KEY `Subc_Id` (`Subc_Id`),
  CONSTRAINT `product_subcategory_ibfk_1` FOREIGN KEY (`Prod_Id`) REFERENCES `product` (`Prod_Id`),
  CONSTRAINT `product_subcategory_ibfk_2` FOREIGN KEY (`Subc_Id`) REFERENCES `subcategory` (`Subc_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_cart_item.sql
DROP TABLE IF EXISTS `cart_item`;
CREATE TABLE `cart_item` (
  `Cait_Id` char(10) NOT NULL,
  `Cait_CartId` char(10) NOT NULL,
  `Cait_ProdId` char(10) NOT NULL,
  `Cait_Quantity` int NOT NULL,
  `Cait_Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Cait_Id`),
  KEY `Cait_CartId` (`Cait_CartId`),
  KEY `Cait_ProdId` (`Cait_ProdId`),
  CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`Cait_CartId`) REFERENCES `cart` (`Cart_Id`),
  CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`Cait_ProdId`) REFERENCES `product` (`Prod_Id`),
  CONSTRAINT `cart_item_chk_1` CHECK ((`Cait_Quantity` > 0)),
  CONSTRAINT `cart_item_chk_2` CHECK ((`Cait_Price` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ---------------------------------------------------------
-- 4. TERTIARY DEPENDENT TABLES (Level 3)
-- ---------------------------------------------------------

-- From: pcx_db_order_item.sql
DROP TABLE IF EXISTS `order_item`;
CREATE TABLE `order_item` (
  `Item_Id` char(10) NOT NULL,
  `Item_OrderID` char(10) NOT NULL,
  `Item_ProdId` char(10) NOT NULL,
  `Item_Quantity` int NOT NULL,
  `Item_Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Item_Id`),
  KEY `Item_OrderID` (`Item_OrderID`),
  KEY `Item_ProdId` (`Item_ProdId`),
  CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`Item_OrderID`) REFERENCES `orders` (`Order_Id`),
  CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`Item_ProdId`) REFERENCES `product` (`Prod_Id`),
  CONSTRAINT `order_item_chk_1` CHECK ((`Item_Quantity` > 0)),
  CONSTRAINT `order_item_chk_2` CHECK ((`Item_Price` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_payment.sql
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `Pay_Id` char(10) NOT NULL,
  `Pay_OrderID` char(10) NOT NULL,
  `Pay_CusId` char(10) NOT NULL,
  `Pay_Method` enum('COD','Cashless') NOT NULL,
  `Pay_PaidAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Pay_Amount` decimal(10,2) NOT NULL,
  `Pay_Status` enum('Pending','Verified','Rejected','Cancelled') NOT NULL,
  `Pay_GatewayRef` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Pay_Id`),
  KEY `Pay_OrderID` (`Pay_OrderID`),
  KEY `Pay_CusId` (`Pay_CusId`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Pay_OrderID`) REFERENCES `orders` (`Order_Id`),
  CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`Pay_CusId`) REFERENCES `customer` (`Cus_Id`),
  CONSTRAINT `payment_chk_1` CHECK ((`Pay_Amount` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- From: pcx_db_service_ticket.sql
DROP TABLE IF EXISTS `service_ticket`;
CREATE TABLE `service_ticket` (
  `Tix_Id` char(10) NOT NULL,
  `Tix_EmpId` char(10) DEFAULT NULL,
  `Tix_CusId` char(10) NOT NULL,
  `Tix_OrderID` char(10) DEFAULT NULL,
  `Tix_ProblemInfo` varchar(255) NOT NULL,
  `Tix_Status` enum('Pending','In Progress','Completed') NOT NULL,
  `Tix_CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Tix_DateCompleted` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Tix_Id`),
  KEY `Tix_EmpId` (`Tix_EmpId`),
  KEY `Tix_CusId` (`Tix_CusId`),
  KEY `Tix_OrderID` (`Tix_OrderID`),
  CONSTRAINT `service_ticket_ibfk_1` FOREIGN KEY (`Tix_EmpId`) REFERENCES `employee` (`Emp_Id`),
  CONSTRAINT `service_ticket_ibfk_2` FOREIGN KEY (`Tix_CusId`) REFERENCES `customer` (`Cus_Id`),
  CONSTRAINT `service_ticket_ibfk_3` FOREIGN KEY (`Tix_OrderID`) REFERENCES `orders` (`Order_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ---------------------------------------------------------
-- 5. ROUTINES AND TRIGGERS
-- ---------------------------------------------------------

-- From: pcx_db_routines.sql
-- Note: This section usually contains Stored Procedures and Functions.
-- Added as placeholder for the logic found in your specific routines file.

-- DELIMITER ;;
-- Example Routine Placeholder:
-- CREATE PROCEDURE `YourProcedure`() ... ;;
-- DELIMITER ;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
