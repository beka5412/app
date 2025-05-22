# Etapas para Instalação e configuração VPS

```
timedatectl set-timezone America/Sao_Paulo

apt install nginx

apt install -y software-properties-common
add-apt-repository ppa:ondrej/php
apt install -y php8.1-fpm
apt install -y php-mysql
apt install -y php8.1-mysqli
apt install -y php8.1-curl
apt install -y php8.1-mbstring
apt install -y php8.1-intl
#apt install php8.1-ext-curl
phpenmod mysqli
phpenmod curl
phpenmod mbstring
phpenmod intl
php --ri mbstring
apt install zip unzip php-zip

apt install mysql-server

systemctl restart php8.1-fpm; systemctl status php8.1-fpm

curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

apt install certbot python3-certbot-nginx

apt install nodejs
apt install npm
npm i pm2 -g
apt install nmon

SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET PERSIST sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

pm2 --name Email_Queue start /bin/bash -- /var/www/html/src/scripts/emailQueue.sh
pm2 --name Utmify_Queue start /bin/bash -- /var/www/html/src/scripts/utmifyQueue.sh
pm2 --name Webhook_Queue start /bin/bash --npm i pm2 -g /var/www/html/src/scripts/webhookQueue.sh
pm2 --name Memberkit_Queue start /bin/bash -- /var/www/html/src/scripts/memberkitQueue.sh
pm2 --name Astronmembers_Queue start /bin/bash -- /var/www/html/src/scripts/astronmembersQueue.sh
pm2 --name Sellflux_Queue start /bin/bash -- /var/www/html/src/scripts/sellfluxQueue.sh

pm2 --name Email_Queue__Test start /bin/bash -- /var/www/html_test/src/scripts/test/emailQueue.sh
pm2 --name Utmify_Queue__Test start /bin/bash -- /var/www/html_test/src/scripts/test/utmifyQueue.sh
pm2 --name Webhook_Queue__Test start /bin/bash -- /var/www/html_test/src/scripts/test/webhookQueue.sh
pm2 --name Memberkit_Queue__Test start /bin/bash -- /var/www/html_test/src/scripts/test/memberkitQueue.sh
pm2 --name Astronmembers_Queue__Test start /bin/bash -- /var/www/html_test/src/scripts/test/astronmembersQueue.sh
pm2 --name Sellflux_Queue__Test start /bin/bash -- /var/www/html_test/src/scripts/test/sellfluxQueue.sh


apt install cron -y
```

cronjob root:
```0 0 * * * /bin/bash /root/bkdb/run.sh ```

cronjob ubuntu: 
```
<<<EOF
* * * * * curl -X GET https://app.lyzardpay.online/api/cronjob/expire-user
* * * * * curl -X GET https://app.lyzardpay.online/api/cronjob/public-suffix-list/update
* * * * * curl -X GET https://app.lyzardpay.online/api/cronjob/upsell/token/delete
* * * * * curl -X DELETE https://app.lyzardpay.online/api/cronjob/customer/password-reset-tokens/delete
* * * * * curl -X GET https://app.lyzardpay.online/api/cronjob/abandoned-cart/alive
          0 * * * * curl -X GET https://app.lyzardpay.online/api/cronjob/currencies-rate/update
          #* * * * * curl -X PATCH https://app.lyzardpay.online/api/cronjob/pay-seller
          #* * * * * curl -X GET https://app.lyzardpay.online/api/cronjob/sendmail
          0 0 * * * curl -X PATCH https://app.lyzardpay.online/api/cronjob/users/chargeback-percent/update
          0 0 * * * curl -X PATCH https://app.lyzardpay.online/api/cronjob/pay-seller/scheduled
          0 0 * * * curl -X POST https://app.lyzardpay.online/api/cronjob/users/award/create-request

# teste
* * * * * curl -X GET https://app-test.lyzardpay.online/api/cronjob/expire-user
* * * * * curl -X GET https://app-test.lyzardpay.online/api/cronjob/public-suffix-list/update
* * * * * curl -X GET https://app-test.lyzardpay.online/api/cronjob/upsell/token/delete
* * * * * curl -X DELETE https://app-test.lyzardpay.online/api/cronjob/customer/password-reset-tokens/delete
* * * * * curl -X GET https://app-test.lyzardpay.online/api/cronjob/abandoned-cart/alive
          0 * * * * curl -X GET https://app-test.lyzardpay.online/api/cronjob/currencies-rate/update
          0 0 * * * curl -X PATCH https://app-test.lyzardpay.online/api/cronjob/users/chargeback-percent/update
          0 0 * * * curl -X PATCH https://app-test.lyzardpay.online/api/cronjob/pay-seller/scheduled
          0 0 * * * curl -X POST https://app-test.lyzardpay.online/api/cronjob/users/award/create-request
          EOF
```

nginx:
``` client_max_body_size 4096M; ```


php:
```
/etc/php/8.1/fpm/php.ini

php_value upload_max_filesize 256M
php_value post_max_size 256M
php_value max_execution_time 120
php_value max_input_time 120

systemctl restart php8.1-fpm
```

--------------------------
banco:
```
nano /etc/mysql/my.cnf

[mysqld]
bind-address = 0.0.0.0

/etc/init.d/mysql restart
```

amazon:
editar grupo de regras de seguranças e adicionar aurora/mysql 0.0.0.0/0

--------------------------
permissoes:
```
chmod -R 777 /var/www/html/src/frontend/public/upload
chmod -R 777 /var/www/html/src/safe_upload

chmod -R 777 /var/www/html_test/src/frontend/public/upload
chmod -R 777 /var/www/html_test/src/safe_upload
```



--------------------------
NGINX:
--------------------------
# app.lyzardpay.online.conf
```
server {
listen 80;
listen [::]:80;

	root /var/www/html/src/frontend/public;
	server_name app.lyzardpay.online;


	location / {
		index index.php;
		try_files $uri $uri/ /index.php$is_args$args;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock;
		include         fastcgi_params;
		fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
		fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
	}

	location ~ /\.ht {
		deny all;
	}
}
```

# checkout.lyzardpay.online.conf
```
server {
listen 80;
listen [::]:80;

	root /var/www/html/src/frontend/public;
	server_name checkout.lyzardpay.online;


	location / {
		index index.php;
		try_files $uri $uri/ /index.php$is_args$args;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock;
		include         fastcgi_params;
		fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
		fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
	}

	location ~ /\.ht {
		deny all;
	}
}
```

# purchase.lyzardpay.online.conf
```
server {
	listen 80;
	listen [::]:80;

	root /var/www/html/src/frontend/public;
	server_name purchase.lyzardpay.online;


	location / {
		index index.php;
		try_files $uri $uri/ /index.php$is_args$args;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock;
		include         fastcgi_params;
		fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
		fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
	}

	location ~ /\.ht {
		deny all;
	}
}
```

```
ln -s /etc/nginx/sites-available/app.lyzardpay.online.conf /etc/nginx/sites-enabled/app.lyzardpay.online.conf
ln -s /etc/nginx/sites-available/checkout.lyzardpay.online.conf /etc/nginx/sites-enabled/checkout.lyzardpay.online.conf
ln -s /etc/nginx/sites-available/purchase.lyzardpay.online.conf /etc/nginx/sites-enabled/purchase.lyzardpay.online.conf
```
--------------------------
NGINX (TEST):
--------------------------
# app-test.lyzardpay.online.conf
```
server {
listen 80;
listen [::]:80;

	root /var/www/html_test/src/frontend/public;
	server_name app-test.lyzardpay.online;


	location / {
		index index.php;
		try_files $uri $uri/ /index.php$is_args$args;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock;
		include         fastcgi_params;
		fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
		fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
	}

	location ~ /\.ht {
		deny all;
	}
}
```

# checkout-test.lyzardpay.online.conf
```
server {
listen 80;
listen [::]:80;

	root /var/www/html_test/src/frontend/public;
	server_name checkout-test.lyzardpay.online;


	location / {
		index index.php;
		try_files $uri $uri/ /index.php$is_args$args;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock;
		include         fastcgi_params;
		fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
		fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
	}

	location ~ /\.ht {
		deny all;
	}
}
```

# purchase-test.lyzardpay.online.conf
```
server {
listen 80;
listen [::]:80;

	root /var/www/html_test/src/frontend/public;
	server_name purchase-test.lyzardpay.online;


	location / {
		index index.php;
		try_files $uri $uri/ /index.php$is_args$args;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock;
		include         fastcgi_params;
		fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
		fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
	}

	location ~ /\.ht {
		deny all;
	}
}
```

```
ln -s /etc/nginx/sites-available/app-test.lyzardpay.online.conf /etc/nginx/sites-enabled/app-test.lyzardpay.online.conf
ln -s /etc/nginx/sites-available/checkout-test.lyzardpay.online.conf /etc/nginx/sites-enabled/checkout-test.lyzardpay.online.conf
ln -s /etc/nginx/sites-available/purchase-test.lyzardpay.online.conf /etc/nginx/sites-enabled/purchase-test.lyzardpay.online.conf



certbot --nginx -d app.lyzardpay.online -d checkout.lyzardpay.online -d purchase.lyzardpay.online
certbot --nginx -d app-test.lyzardpay.online -d checkout-test.lyzardpay.online -d purchase-test.lyzardpay.online
```
