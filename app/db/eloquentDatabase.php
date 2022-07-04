<?php

    use Illuminate\Database\Capsule\Manager as Database;

    $database = new Database;

    $database->addConnection([
        'driver' => 'mysql',
        'host' => $_ENV['MYSQL_HOST'],
        'database' => $_ENV['MYSQL_DB'],
        'username' => $_ENV['MYSQL_USER'],
        'password' => $_ENV['MYSQL_PASS'],
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix' => ''
    ]);

    $database->setAsGlobal();
    $database->bootEloquent();

?>