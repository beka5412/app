DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		CREATE TABLE IF NOT EXISTS app_cademi_integrations (
			`id` BIGINT unsigned PRIMARY KEY AUTO_INCREMENT,
			`user_id` BIGINT UNSIGNED,
			`subdomain` VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`token` VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`product_id` VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'this column can contain multiple IDs separated by semicolons, each ID represents the product ID on this platform and also on "cademi"',
			`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`status` TINYINT(1) DEFAULT NULL
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE IF EXISTS app_cademi_integrations;
    END;

	CALL _rollback();
	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
