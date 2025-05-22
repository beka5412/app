FROM ubuntu:22.04

ARG DEBIAN_FRONTEND=noninteractive

USER root

RUN apt update

RUN apt install -y php8.1-fpm \
    php-mysql \
    php8.1-mysqli \
    php8.1-curl \
    php8.1-mbstring \
    php8.1-intl \
    zip \
    unzip \
    php-zip 

RUN apt-get update && apt-get install -y \
    software-properties-common \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    libzip-dev

RUN apt-get update && apt-get install -y  \
    nodejs \
    npm \
    nmon \
    nginx \
    cron

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /root/.composer

WORKDIR /var/www/

RUN chown -R www-data:www-data /var/www/

RUN npm i pm2
RUN npx pm2 update

RUN pm2 --name Email_Queue start /bin/bash -- /var/www/scripts/emailQueue.sh
RUN pm2 --name Utmify_Queue start /bin/bash -- /var/www/scripts/utmifyQueue.sh
RUN pm2 --name Webhook_Queue start /bin/bash -- /var/www/scripts/webhookQueue.sh
RUN pm2 --name Memberkit_Queue start /bin/bash -- /var/www/scripts/memberkitQueue.sh
RUN pm2 --name Astronmembers_Queue start /bin/bash -- /var/www/scripts/astronmembersQueue.sh
RUN pm2 --name Sellflux_Queue start /bin/bash -- /var/www/scripts/sellfluxQueue.sh

RUN apt install nginx

EXPOSE 80 9000

# RUN chmod +x scripts/**/*.sh

CMD ["pm2-runtime", "ecosystem.config.js"]
