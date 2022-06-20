<?php

    declare(strict_types=1);

    require_once './controllers/EmployeeController.php';
    require_once './controllers/ProductController.php';
    require_once './controllers/TableController.php';
    require_once './controllers/OrderController.php';
    require_once './controllers/TokenController.php';

    require_once './middlewares/JWTAuthenticator.php';
    require_once './middlewares/JWTAuthenticatorMW.php';
    require_once './middlewares/OrderAuthenticatorMW.php';

    use Slim\App;
    use Slim\Routing\RouteCollectorProxy;

    return function(App $app)
    {
        $app->group('/employees', function (RouteCollectorProxy $group) {
            $group->get('[/]', \EmployeeController::class . ':GetEmployees');
            $group->post('[/]', \EmployeeController::class . ':AddEmployee');
          })->add(JWTAuthenticatorMW::class);
        
          $app->group('/products', function (RouteCollectorProxy $group) {
            $group->get('[/]', \ProductController::class . ':GetProducts');
            $group->post('[/]', \ProductController::class . ':AddProduct');
          })->add(JWTAuthenticatorMW::class);;
        
          $app->group('/tables', function (RouteCollectorProxy $group) {
            $group->get('[/]', \TableController::class . ':GetTables');
            $group->post('[/]', \TableController::class . ':AddTable');
          })->add(JWTAuthenticatorMW::class);;
        
          $app->group('/order', function (RouteCollectorProxy $group) {
            $group->get('[/]', \OrderController::class . ':GetOrders');
            $group->post('[/]', \OrderController::class . ':AddOrder');
            $group->post('/update', \OrderController::class . ':UpdateState');
          })->add(OrderAuthenticatorMW::class);;

          $app->group('/token', function(RouteCollectorProxy $group){
            $group->post('[/]', \TokenController::class . ':GetToken');
          });
    }

?>