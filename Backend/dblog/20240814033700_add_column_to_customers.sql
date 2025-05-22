DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE customers 
            ADD iugu_customer_id CHAR(32);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE customers 
            DROP iugu_customer_id;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
