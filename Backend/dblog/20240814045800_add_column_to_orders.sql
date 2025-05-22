DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE orders 
            ADD iugu_subscription_id CHAR(32);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE orders 
            DROP iugu_subscription_id;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
