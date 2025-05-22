DELIMITER ~

    CREATE PROCEDURE _transaction()
    BEGIN
        UPDATE products SET delivery = null WHERE delivery = 'rocketmember';
        ALTER TABLE products MODIFY delivery ENUM('download', 'memberkit', 'external', 'nothing');
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        UPDATE products SET delivery = null WHERE delivery = 'memberkit';
        ALTER TABLE products MODIFY delivery ENUM('download', 'rocketmember', 'external', 'nothing');
        UPDATE products SET delivery = 'rocketmember' WHERE delivery IS NULL;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
    
~ 
DELIMITER ;
