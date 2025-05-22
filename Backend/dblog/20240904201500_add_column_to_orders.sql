DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE orders 
            ADD alert_id CHAR(50);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE orders 
            DROP COLUMN alert_id;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
