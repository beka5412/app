DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE products 
            ADD iugu_plan_id CHAR(255);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE products 
            DROP iugu_plan_id;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
