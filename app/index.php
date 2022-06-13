<?php

  // Error Handling
  error_reporting(-1);
  ini_set('display_errors', 1);

  use DI\Container;
  use Slim\Factory\AppFactory;

  require __DIR__ . '/../vendor/autoload.php';

  // require_once './db/DBAccess.php';
  // require_once __DIR__ . './db/eloquentDatabase.php';
  require_once __DIR__ . './db/database.php';

  require_once './middlewares/JWTAuthenticator.php';
  require_once './middlewares/JWTAuthenticatorMW.php';

  require_once './controllers/EmployeeController.php';
  require_once './controllers/ProductController.php';
  require_once './controllers/TableController.php';
  require_once './controllers/OrderController.php';

  // Load ENV
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->safeLoad();

  // Set Container on app
  $container = new Container();

  // Set settings on container
  $settings = require __DIR__ . '/../app/settings.php';
  $settings($container);

  AppFactory::setContainer($container);

  // Instantiate App
  $app = AppFactory::create();

  // Add error middleware
  $middleware  = require __DIR__ . '/../app/middlewares/ErrorDisplayMW.php';
  $middleware($app);

  $routes = require __DIR__ . '/../app/routes.php';
  $routes($app);

  $app->run();

?>