#!/bin/bash

composer update
composer install

exec php-fpm8.1 -F