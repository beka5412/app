DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		CREATE TABLE IF NOT EXISTS app_memberkit_integrations (
			`id` BIGINT unsigned PRIMARY KEY AUTO_INCREMENT,
			`user_id` BIGINT UNSIGNED,
			`apikey` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`status` TINYINT(1) DEFAULT NULL,
			`product_id` BIGINT UNSIGNED,
			`classroomids` JSON,
			`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			CONSTRAINT Fk_AppMemberkitIntegration_BelongsTo_User FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
			CONSTRAINT Fk_AppMemberkitIntegration_BelongsTo_Product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE app_memberkit_integrations;
    END;

	-- CALL _rollback();
	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
