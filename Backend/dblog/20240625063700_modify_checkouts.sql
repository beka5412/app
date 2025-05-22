DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE checkouts 
        ADD backredirect_enabled TINYINT(1),
        ADD backredirect_url VARCHAR(500);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE checkouts 
        DROP backredirect_enabled,
        DROP backredirect_url;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
