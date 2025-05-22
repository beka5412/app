DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE products MODIFY delivery ENUM('download', 'memberkit', 'astronmembers', 'external', 'nothing');
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE products MODIFY delivery ENUM('download', 'memberkit', 'external', 'nothing');
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
