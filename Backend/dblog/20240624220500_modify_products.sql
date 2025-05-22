DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE products ADD approved TINYINT(1);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE products DROP approved;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
