DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		ALTER TABLE refund_requests 
            ADD user_id BIGINT UNSIGNED AFTER customer_id,
            ADD CONSTRAINT Fk_RefundRequest_BelongsTo_User FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD refunded_at DATETIME AFTER status,
            MODIFY reason TEXT AFTER status;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        ALTER TABLE refund_requests 
        DROP COLUMN user_id,
        DROP CONSTRAINT Fk_RefundRequest_BelongsTo_User,
        DROP COLUMN refunded_at,
        MODIFY reason TEXT AFTER updated_at;
    END;

	CALL _transaction();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	
~ 
DELIMITER ;
