<?php

use Illuminate\Database\Capsule\Manager as DB;

$manager = new DB;
$manager->addConnection([
   "driver" => env('DB_DRIVER'),
   "host" => env('DB_HOST'),
   "database" => env('DB_NAME'),
   "username" => env('DB_USER'),
   "password" => env('DB_PASS')
]);

$manager->setAsGlobal();
$manager->bootEloquent();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();