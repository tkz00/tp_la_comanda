<?php

    declare(strict_types=1);

    use Slim\App;
    use Slim\Routing\RouteCollectorProxy;

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    return function(App $app)
    {
        $app->group('/employees', function (RouteCollectorProxy $group) {
            $group->get('[/]', \EmployeeController::class . ':GetEmployees');
            $group->post('[/]', \EmployeeController::class . ':AddEmployee');
          })->add(JWTAuthenticatorMW::class);
        
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

          $app->group('/login', function(RouteCollectorProxy $group){
            $group->post('[/]', \LoginController::class . ':Login');
          });
    }

?>