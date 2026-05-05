DELIMITER //

CREATE TRIGGER before_admin_insert
BEFORE INSERT ON admin_user
FOR EACH ROW
BEGIN
    -- This generates a random number between 1000 and 9999
    SET NEW.pin = FLOOR(1000 + RAND() * 9000);
END;

//
DELIMITER ;
