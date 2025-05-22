DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE checkouts 
        ADD countdown_color VARCHAR(20) AFTER countdown_time;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE checkouts DROP countdown_color;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
