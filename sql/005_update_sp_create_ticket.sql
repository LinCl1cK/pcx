USE pcx_db;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_CreateServiceTicket$$
CREATE PROCEDURE sp_CreateServiceTicket(
    IN p_TixId CHAR(10),
    IN p_EmpId CHAR(10),
    IN p_CusId CHAR(10),
    IN p_OrderId CHAR(10),
    IN p_ProblemInfo VARCHAR(255)
)
BEGIN
    INSERT INTO Service_Ticket (
        Tix_Id, Tix_EmpId, Tix_CusId, Tix_OrderID, Tix_ProblemInfo, Tix_Status, Tix_CreatedAt
    ) VALUES (
        p_TixId, p_EmpId, p_CusId, p_OrderId, p_ProblemInfo, 'Pending', NOW()
    );
END$$

DELIMITER ;
