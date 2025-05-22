<?php

# TESTE

use Backend\App;
$composer_autoload = include 'vendor/autoload.php'; # composer dump-autoload --optimize

if (env('DEBUG'))
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

else
{
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

ini_set("log_errors", 1);
ini_set("error_log", base_path("logs/php-error.log")); 

include 'bootstrap.php';

new App;
