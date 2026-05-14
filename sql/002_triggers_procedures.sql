USE pcx_db;

DELIMITER $$

DROP TRIGGER IF EXISTS trg_Inventory_StockCheck$$
CREATE TRIGGER trg_Inventory_StockCheck
BEFORE UPDATE ON Inventory
FOR EACH ROW
BEGIN
    IF NEW.Inv_StockQty < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Inventory stock cannot be negative.';
    END IF;
END$$

DROP TRIGGER IF EXISTS trg_Order_StatusFlow$$
CREATE TRIGGER trg_Order_StatusFlow
BEFORE UPDATE ON Orders
FOR EACH ROW
BEGIN
    IF OLD.Order_Status <> NEW.Order_Status AND NOT (
        (OLD.Order_Status = 'Pending' AND NEW.Order_Status IN ('Confirmed', 'Cancelled')) OR
        (OLD.Order_Status = 'Confirmed' AND NEW.Order_Status IN ('Paid', 'Cancelled')) OR
        (OLD.Order_Status = 'Paid' AND NEW.Order_Status = 'Completed')
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid order status transition.';
    END IF;
END$$

DROP TRIGGER IF EXISTS trg_Order_CancelCheck$$
CREATE TRIGGER trg_Order_CancelCheck
BEFORE UPDATE ON Orders
FOR EACH ROW
BEGIN
    IF OLD.Order_Status IN ('Paid', 'Completed') AND NEW.Order_Status = 'Cancelled' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Paid/completed orders cannot be cancelled.';
    END IF;
END$$

DROP TRIGGER IF EXISTS trg_Payment_StatusUpdate$$
CREATE TRIGGER trg_Payment_StatusUpdate
AFTER UPDATE ON Payment
FOR EACH ROW
BEGIN
    IF NEW.Pay_Status = 'Verified' AND OLD.Pay_Status <> 'Verified' THEN
        UPDATE Orders
        SET Order_Status = 'Paid'
        WHERE Order_Id = NEW.Pay_OrderID
          AND Order_Status = 'Confirmed';
    END IF;
END$$

DROP TRIGGER IF EXISTS trg_Inventory_AutoDeduct$$
CREATE TRIGGER trg_Inventory_AutoDeduct
AFTER UPDATE ON Orders
FOR EACH ROW
BEGIN
    IF NEW.Order_Status = 'Confirmed' AND OLD.Order_Status <> 'Confirmed' THEN
        UPDATE Inventory i
        INNER JOIN Order_Item oi ON oi.Item_ProdId = i.Inv_ProdId
        SET i.Inv_StockQty = i.Inv_StockQty - oi.Item_Quantity,
            i.Inv_LastUpdated = NOW()
        WHERE oi.Item_OrderID = NEW.Order_Id
          AND i.Inv_BranchId = (
              SELECT Emp_BranchId FROM Employee WHERE Emp_Id = NEW.Order_VerifiedBy LIMIT 1
          );
    END IF;
END$$

DROP TRIGGER IF EXISTS trg_Inventory_RestockAlert$$
CREATE TRIGGER trg_Inventory_RestockAlert
BEFORE UPDATE ON Inventory
FOR EACH ROW
BEGIN
    IF NEW.Inv_StockQty <= NEW.Inv_ReorderLevel THEN
        SET NEW.Inv_LastUpdated = NOW();
    END IF;
END$$

DROP TRIGGER IF EXISTS trg_Ticket_StatusFlow$$
CREATE TRIGGER trg_Ticket_StatusFlow
BEFORE UPDATE ON Service_Ticket
FOR EACH ROW
BEGIN
    IF OLD.Tix_Status <> NEW.Tix_Status AND NOT (
        (OLD.Tix_Status = 'Pending' AND NEW.Tix_Status = 'In Progress') OR
        (OLD.Tix_Status = 'In Progress' AND NEW.Tix_Status = 'Completed')
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid ticket status transition.';
    END IF;

    IF NEW.Tix_Status = 'Completed' THEN
        SET NEW.Tix_DateCompleted = NOW();
    END IF;
END$$

DROP PROCEDURE IF EXISTS sp_PlaceOrder$$
CREATE PROCEDURE sp_PlaceOrder(
    IN p_OrderId CHAR(10),
    IN p_CusId CHAR(10),
    IN p_OrderShipping ENUM('Delivery','Pickup'),
    IN p_VAT DECIMAL(10,2),
    IN p_TotalAmount DECIMAL(10,2),
    IN p_InvoiceNo VARCHAR(20)
)
BEGIN
    INSERT INTO Orders (
        Order_Id, Order_Date, Order_Status, Order_Shipping,
        Order_CusId, Order_InvoiceNo, Order_InvoiceDate, Order_VAT, Order_TotalAmount
    ) VALUES (
        p_OrderId, NOW(), 'Pending', p_OrderShipping,
        p_CusId, p_InvoiceNo, NOW(), p_VAT, p_TotalAmount
    );
END$$

DROP PROCEDURE IF EXISTS sp_ConfirmPayment$$
CREATE PROCEDURE sp_ConfirmPayment(
    IN p_PayId CHAR(10),
    IN p_OrderId CHAR(10),
    IN p_Method ENUM('COD','Bank Transfer'),
    IN p_Amount DECIMAL(10,2),
    IN p_Status ENUM('Pending','Verified'),
    IN p_Details VARCHAR(255)
)
BEGIN
    INSERT INTO Payment (
        Pay_Id, Pay_OrderID, Pay_Method, Pay_PaidAt, Pay_Amount, Pay_Status, Pay_Details
    ) VALUES (
        p_PayId, p_OrderId, p_Method, NOW(), p_Amount, p_Status, p_Details
    );
END$$

DROP PROCEDURE IF EXISTS sp_CreateServiceTicket$$
CREATE PROCEDURE sp_CreateServiceTicket(
    IN p_TixId CHAR(10),
    IN p_EmpId CHAR(10),
    IN p_CusId CHAR(10),
    IN p_ProblemInfo VARCHAR(255)
)
BEGIN
    INSERT INTO Service_Ticket (
        Tix_Id, Tix_EmpId, Tix_CusId, Tix_ProblemInfo, Tix_Status, Tix_CreatedAt
    ) VALUES (
        p_TixId, p_EmpId, p_CusId, p_ProblemInfo, 'Pending', NOW()
    );
END$$

DROP PROCEDURE IF EXISTS sp_AssignEmployee$$
CREATE PROCEDURE sp_AssignEmployee(
    IN p_TixId CHAR(10),
    IN p_EmpId CHAR(10)
)
BEGIN
    UPDATE Service_Ticket
    SET Tix_EmpId = p_EmpId
    WHERE Tix_Id = p_TixId;
END$$

DELIMITER ;
