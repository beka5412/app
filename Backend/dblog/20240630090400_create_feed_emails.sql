DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		CREATE TABLE IF NOT EXISTS feed_emails (
			`id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			`email` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
			`status` TINYINT(1) DEFAULT 0,
			`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP()
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE feed_emails;
    END;

	CALL _transaction();

    INSERT INTO feed_emails (email, status) 
    VALUES 
        ('quielbala@gmail.com', 1),
        ('zilz@allops.com.br', 1);

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
