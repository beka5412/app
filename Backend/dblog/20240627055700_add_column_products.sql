DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE products 
            ADD has_upsell_rejection TINYINT(1) DEFAULT 0,
            ADD upsell_text VARCHAR(500),
            ADD upsell_rejection_link VARCHAR(500),
            ADD upsell_rejection_text VARCHAR(500);
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE products 
            DROP has_upsell_rejection,
            DROP upsell_text,
            DROP upsell_rejection_link,
            DROP upsell_rejection_text;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
