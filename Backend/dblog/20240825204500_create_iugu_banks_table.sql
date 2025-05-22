DELIMITER ~
    CREATE PROCEDURE _transaction()
    BEGIN
		CREATE TABLE IF NOT EXISTS iugu_banks (
			`id` BIGINT unsigned PRIMARY KEY AUTO_INCREMENT,
			`name` VARCHAR(255) NULL COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP()
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    END;

    CREATE PROCEDURE _rollback()
    BEGIN
        DROP TABLE IF EXISTS iugu_banks;
    END;

	CREATE PROCEDURE _seed()
	BEGIN
		INSERT INTO iugu_banks (name)
		VALUES 
			('Banco do Brasil'),
			('Santander'),
			('Caixa Econômica'),
			('Bradesco/Next'),
			('Itaú'),
			('Agibank'),
			('Banpará'),
			('Banrisul'),
			('Sicoob'),
			('Inter'),
			('BRB'),
			('Via Credi/Civia Cooperativa'),
			('Neon/Votorantim'),
			('Nubank'),
			('Pagseguro'),
			('Banco Original'),
			('Safra'),
			('Modal'),
			('Banestes'),
			('Unicred'),
			('Money Plus'),
			('Mercantil do Brasil'),
			('JP Morgan'),
			('Gerencianet Pagamentos do Brasil'),
			('Banco C6'),
			('BS2'),
			('Banco Topazio'),
			('Uniprime'),
			('Stone'),
			('Rendimento'),
			('Banco Daycoval'),
			('Banco do Nordeste'),
			('Citibank'),
			('DOCK IP S.A.'),
			('Cooperativa Central de Credito Noroeste Brasileiro'),
			('Uniprime Norte do Paraná'),
			('Global SCM'),
			('Cora'),
			('Mercado Pago'),
			('Banco da Amazonia'),
			('BNP Paribas BrasilDD'),
			('Juno'),
			('Cresol'),
			('BRL Trust DTVM'),
			('Banco Banese'),
			('Banco BTG Pactual'),
			('Banco Omni'),
			('Acesso Soluções de pagamento'),
			('CCR de São Miguel do Oeste'),
			('Polocred'),
			('Ótimo'),
			('Picpay'),
			('Banco Genial'),
			('Banco Capital S.A'),
			('SicrediD'),
			('Banco Ribeirão Preto'),
			('ASAAS IP'),
			('Banco Pan'),
			('Neon'),
			('VORTX DTVM LTDA-D'),
			('Banco BMG'),
			('Fitbank-'),
			('Pefisa'),
			('J17 - SCD S/A'),
			('Credisan'),
			('Pinbank'),
			('XP Investimentos'),
			('Crefisa'),
			('Singulare'),
			('SUMUP SCD S.A.'),
			('Banco ABC Brasil'),
			('Banco Letsbank S.A'),
			('HR Digital Sociedade de Crédito Direto S.A'),
			('BANCO XP S.A.'),
			('Neon Pagamentos S.A. IP'),
			('CLOUD WALK MEIOS DE PAGAMENTOS E SERVICOS LTDA'),
			('BCO SOFISA S.A.'),
			('BANCO BV S.A.'),
			('CELCOIN INSTITUICAO DE PAGAMENTO S.A.'),
			('SUPERDIGITAL I.P. S.A.'),
			('BCO FIBRA S.A.'),
			('UY3 SCD S/A'),
			('QI Sociedade de Crédito'),
			('LISTO SCD S.A'),
			('ID CORRETORA DE TÍTULOS E VALORES MOBILIÁRIOS S.A'),
			('CARTOS SCD S.A.')
		;
	END;

	CALL _rollback();
	CALL _transaction();
	CALL _seed();

	DROP PROCEDURE _transaction;
	DROP PROCEDURE _rollback;
	DROP PROCEDURE _seed;
	
~ 
DELIMITER ;
