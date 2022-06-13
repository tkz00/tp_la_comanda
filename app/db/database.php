<?php

    use Illuminate\Database\Capsule\Manager as Database;

    $database = new Database;

    // $database->addConnection([
    //     'driver' => 'mysql',
    //     'host' => $_ENV['MYSQL_HOST'],
    //     'database' => $_ENV['MYSQL_DB'],
    //     'username' => $_ENV['MYSQL_USER'],
    //     'password' => $_ENV['MYSQL_PASS'],
    //     'charset' => 'utf-8',
    //     'collation' => 'utf8_unicode_ci',
    //     'prefix' => ''
    // ]);

    $database->addConnection([
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'la_comanda',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf-8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    ]);

    $database->setAsGlobal();

    $database->bootEloquent();

?>