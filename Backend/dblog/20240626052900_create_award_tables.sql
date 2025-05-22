DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
        CREATE TABLE IF NOT EXISTS user_addresses (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED,
            `street` VARCHAR(255),
            `number` CHAR(20),
            `neighborhood` VARCHAR(255),
            `city` VARCHAR(255),
            `state` VARCHAR(255),
            `zipcode` CHAR(20),
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
            CONSTRAINT Fk_UserAddress_BelongsTo_User FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) engine=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

		CREATE TABLE IF NOT EXISTS awards (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(255),
            `description` VARCHAR(500),
            `amount` double(30, 2) NOT NULL DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP()
        ) engine=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

		CREATE TABLE IF NOT EXISTS award_requests (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED,
            `award_id` BIGINT UNSIGNED,
            `user_address_id` BIGINT UNSIGNED,
            `status` ENUM('pending', 'sent', 'canceled') DEFAULT 'pending',
            `answered_at` DATETIME,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
            CONSTRAINT Fk_AwardRequest_BelongsTo_User FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT Fk_AwardRequest_BelongsTo_Award FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT Fk_AwardRequest_BelongsTo_UserAddress FOREIGN KEY (user_address_id) REFERENCES user_addresses(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) engine=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

        INSERT INTO awards (name, description, amount)
        VALUES 
            (NULL, NULL, 10000),
            (NULL, NULL, 100000),
            (NULL, NULL, 500000),
            (NULL, NULL, 1000000),
            (NULL, NULL, 10000000),
            (NULL, NULL, 100000000);

        ALTER TABLE balance_available_history 
            MODIFY `current` DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY `old` DOUBLE(30, 2) DEFAULT 0.00;
            
        ALTER TABLE balances 
            MODIFY available DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY blocked DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY withdrawn DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY pending DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY amount DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY withdrawal_requested DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY future_releases DOUBLE(30, 2) DEFAULT 0.00,
            MODIFY reserved_as_guarantee DOUBLE(30, 2) DEFAULT 0.00;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE award_requests;
        TRUNCATE TABLE awards;
        DROP TABLE awards;
        DROP TABLE user_addresses;

        ALTER TABLE balance_available_history 
            MODIFY `current` DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY `old` DOUBLE(10, 2) DEFAULT 0.00;

        ALTER TABLE balances 
            MODIFY available DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY blocked DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY withdrawn DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY pending DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY amount DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY withdrawal_requested DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY future_releases DOUBLE(10, 2) DEFAULT 0.00,
            MODIFY reserved_as_guarantee DOUBLE(10, 2) DEFAULT 0.00;        
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
