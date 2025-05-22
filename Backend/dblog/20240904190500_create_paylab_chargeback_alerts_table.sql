DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
        CREATE TABLE IF NOT EXISTS paylab_chargeback_alerts (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            alert_id CHAR(50),
            api_alert_id CHAR(25),
            api_transaction_date DATE,
            api_amount DOUBLE(10, 2),
            api_auth_code CHAR(25),
            api_card_number CHAR(20),
            api_merchant CHAR(128),
            api_merchant_descriptor CHAR(255),
            api_received_date DATETIME,
            api_issuer CHAR(255),
            api_transaction_type CHAR(50),
            api_source CHAR(50),
            api_status CHAR(50),
            api_type CHAR(50),
            paylab_result_status ENUM('ACCOUNT_SUSPENDED', 'NOTFOUND', 'OTHER'),
            order_id BIGINT UNSIGNED,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP()
        ) engine=InnoDB AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE IF EXISTS paylab_chargeback_alerts;
    END;

	CALL _rollback();
	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
