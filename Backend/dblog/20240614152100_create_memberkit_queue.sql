DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		CREATE TABLE IF NOT EXISTS memberkit_queue (
			`id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			`data` TEXT COLLATE utf8mb4_unicode_ci,
			`response` JSON DEFAULT NULL,
			`scheduled_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`current_attempt` SMALLINT DEFAULT '1',
			`status` enum('waiting', 'executed', 'sent', 'error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'waiting',
			`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP()
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE memberkit_queue;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
