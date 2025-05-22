DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
        CREATE TABLE IF NOT EXISTS iugu_charge_queue (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            data TEXT COLLATE utf8mb4_unicode_ci,
            response LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
            scheduled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            current_attempt SMALLINT DEFAULT '1',
            status ENUM('waiting', 'executed', 'sent', 'error') COLLATE utf8mb4_unicode_ci DEFAULT 'waiting',
            order_id BIGINT UNSIGNED,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            CONSTRAINT IuguChargeQueue_Check_Response CHECK (JSON_VALID(response)),
            CONSTRAINT Fk_IuguChargeQueue_BelongsTo_Order FOREIGN KEY iugu_charge_queue (order_id) 
                REFERENCES orders (id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE IF EXISTS iugu_charge_queue;
    END;

	CALL _rollback();
	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
