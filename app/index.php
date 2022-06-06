<?php

  // Error Handling
  error_reporting(-1);
  ini_set('display_errors', 1);

  use DI\Container;
  use Slim\Psr7\Response;
  use Psr\Http\Message\ResponseInterface;
  use Psr\Http\Message\ServerRequestInterface as Request;
  use Psr\Http\Server\RequestHandlerInterface;
  use Slim\Factory\AppFactory;
  use Slim\Routing\RouteCollectorProxy;
  use Slim\Routing\RouteContext;

  require __DIR__ . '/../vendor/autoload.php';

  // require_once './db/DBAccess.php';
  // require_once './middlewares/Logger.php';

  // require_once './controllers/UsuarioController.php';
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

  // Add middleware
  $middleware  = require __DIR__ . '/../app/middleware.php';
  $middleware($app);

  $app->group('/employees', function (RouteCollectorProxy $group) {
    $group->get('[/]', \EmployeeController::class . ':GetEmployees');
    $group->post('[/]', \EmployeeController::class . ':AddEmployee');
  });

  $app->group('/products', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductController::class . ':GetProducts');
    $group->post('[/]', \ProductController::class . ':AddProduct');
  });

  $app->group('/tables', function (RouteCollectorProxy $group) {
    $group->get('[/]', \TableController::class . ':GetTables');
    $group->post('[/]', \TableController::class . ':AddTable');
  });

  $app->group('/order', function (RouteCollectorProxy $group) {
    $group->get('[/]', \OrderController::class . ':GetOrders');
    $group->post('[/]', \OrderController::class . ':AddOrder');
  });

  // // Routes
  // $app->group('/usuarios', function (RouteCollectorProxy $group) {
  //     $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  //     $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  //     $group->post('[/]', \UsuarioController::class . ':CargarUno');
  //   });

  // $app->get('[/]', function (Request $request, ResponseInterface $response) {    
  //     $response->getBody()->write("Slim Framework 4 PHP");
  //     return $response;

  // });

  $app->run();

?>