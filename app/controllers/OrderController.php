<?php

    require_once __DIR__ . '/../models/Order.php';

    class OrderController
    {
        public function GetOrders($request, $response, $args)
        {
            $list = Order::GetOrders();
            $payload = json_encode(array("Order List" => $list));
  
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function AddOrder($request, $response, $args)
        {
            try
            {
                $parameters = $request->getParsedBody();

                $product_id = $parameters['product_id'];
                $quantity = $parameters['quantity'];
                $table_id = $parameters['table_id'];
                $client_name = $parameters['client_name'];
                $order_code = $parameters['order_code'];
                
                // These three fields are set not at the start of the object, but when it is asigned an employee and when the order is delivered
                // $state = $parameters['state'];
                // $photo_path = $parameters['photo_path'];
                // $estimated_completion_on = $parameters['estimated_completion_on'];
                // $completed_on = $parameters['completed_on'];
                // $employee_id = $parameters['employee_id'];
    
                // Create Order
                $order = new Order();
                $order->product_id = $product_id;
                $order->quantity = $quantity;
                $order->table_id = $table_id;
                $order->client_name = $client_name;
                $order->order_code = $order_code;
                
                // These three fields are set not at the start of the object, but when it is asigned an employee and when the order is delivered
                // $order->SetState($state);
                // $order->photo_path = $photo_path;
                // $order->estimated_completion_on = $estimated_completion_on;
                // $order->completed_on = $completed_on;
                // $employee_id = $employee_id;

                $order->SaveToDB();
    
                $payload = json_encode(array("mensaje" => "Order creado con exito"));
    
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }
            
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

?>