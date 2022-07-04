<?php

use function DI\get;

    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/Order_Contains_Product.php';
    require_once __DIR__ . '/../models/Product.php';

    class StatisticsController
    {
        public function GetStatistics($request, $response, $args)
        {
            $orders = Order_Contains_Product::all();
            $statistics = array();

            // Orders

            // Producto más vendido
            $order = Order_Contains_Product::groupBy("product_id")->orderByRaw('COUNT(*) DESC')->limit(1)->get();
            $product = $order[0]->product()->get()[0];
            $statistics["Most sold product"] = $product;

            // Producto menos vendido
            $order = Order_Contains_Product::groupBy("product_id")->orderByRaw('COUNT(*) ASC')->limit(1)->get();
            $product = $order[0]->product()->get()[0];
            $statistics["Least sold product"] = $product;

            // Ordenes no entregadas a timepo
            $newOrders = array();
            for($i = 0; $i < count($orders); $i++)
            {
                if($orders[$i]->estimated_completion_on && $orders[$i]->completed_on)
                {
                    if($orders[$i]->estimated_completion_on < $orders[$i]->completed_on)
                    {
                        array_push($newOrders, $orders[$i]);
                    }
                }
            }
            $statistics["Orders not completed on time"] = $newOrders;

            // Ordenes canceladas
            $newOrders = array();
            for($i = 0; $i < count($orders); $i++)
            {
                if($orders[$i]->state == 'cancelled')
                {
                    array_push($newOrders, $orders[$i]);
                }
            }
            $statistics["Orders cancelled"] = $newOrders;

            // Mesa mas usada
            $order = Order::groupBy("table_id")->orderByRaw('COUNT(*) DESC')->limit(1)->get();
            $table = $order[0]->table()->get()[0];
            $statistics["Most used table"] = $table;

            // Mesa menos usada
            $order = Order::groupBy("table_id")->orderByRaw('COUNT(*) ASC')->limit(1)->get();
            $table = $order[0]->table()->get()[0];
            $statistics["Least used table"] = $table;

            $payload = json_encode(array("Statistics" => $statistics));
  
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }
    }

?>