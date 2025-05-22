DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		CREATE TABLE IF NOT EXISTS iugu_sellers (
			`id` BIGINT unsigned PRIMARY KEY AUTO_INCREMENT,
			`user_id` BIGINT UNSIGNED,
			`account_id` VARCHAR(255) NULL COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`live_api_token` VARCHAR(500) NULL COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`test_api_token` VARCHAR(500) NULL COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`user_token` VARCHAR(500) NULL COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`response_create` TEXT NULL COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`response_verification` TEXT NULL COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP()
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE IF EXISTS iugu_sellers;
    END;

	CALL _rollback();
	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
