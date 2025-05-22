DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		CREATE TABLE IF NOT EXISTS product_requests (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED,
            `product_id` BIGINT UNSIGNED,
            `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            `answered_at` DATETIME,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
            CONSTRAINT Fk_ProductRequest_BelongsTo_User FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT Fk_ProductRequest_BelongsTo_Product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) engine=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE product_requests;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
