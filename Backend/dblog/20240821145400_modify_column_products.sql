DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        ALTER TABLE products MODIFY delivery ENUM('download','memberkit','cademi','rocketmember','astronmembers','external','nothing');
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE products MODIFY delivery ENUM('download','memberkit','rocketmember','astronmembers','external','nothing');
    END;

	CALL _rollback();
	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
