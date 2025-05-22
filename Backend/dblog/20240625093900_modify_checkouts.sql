DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE checkouts 
        DROP countdown_color,
        ADD header_bg_color VARCHAR(20) AFTER countdown_time,
        ADD header_text_color VARCHAR(20) AFTER header_bg_color;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE checkouts 
        ADD countdown_color VARCHAR(20) AFTER countdown_time,
        DROP header_bg_color,
        DROP header_text_color;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
