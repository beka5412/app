DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE orders 
            ADD gateway CHAR(255);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE orders 
            DROP gateway;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
