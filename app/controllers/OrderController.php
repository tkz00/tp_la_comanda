<?php

    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/Order_Contains_Product.php';
    require_once __DIR__ . '/../models/Product.php';

    class OrderController
    {
        public function GetOrders($request, $response, $args)
        {
            // $list = Order::all();
            $list = Order::with('products')->get();

            $payload = json_encode(array("Product List" => $list));
  
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function AddOrder($request, $response, $args)
        {
            try
            {
                $parameters = $request->getParsedBody();

                $product_ids = $parameters['product_id'];

                // Check if products exist
                $productsExist = true;

                for($i = 0; $i < count($product_ids); $i++)
                {
                    if(Product::find($product_ids[$i]) == null)
                    {
                        $productsExist = false;
                        throw new Exception("Los productos ordenados no existen en la base de datos.");
                    }
                }

                // Create Order in DB
                $table_id = $parameters['table_id'];
                $client_name = $parameters['client_name'];
                $order_code = $parameters['order_code'];

                $order = new Order();
                $order->table_id = $table_id;
                $order->client_name = $client_name;
                $order->order_code = $order_code;
                $order->save();

                // Create order_product relation table in DB
                $quantity = $parameters['quantity'];

                for($i = 0; $i < count($product_ids); $i++)
                {
                    $order_contains_product = new Order_Contains_Product();
                    $order_contains_product->order_id = $order->id;
                    $order_contains_product->product_id = $product_ids[$i];
                    $order_contains_product->quantity = ($quantity[$i] != null) ? $quantity[$i] : 1;
                    $order_contains_product->save();
                }

                $test = $order->order_contains_products();

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

        public function UpdateState($request, $response, $args)
        {
            try
            {
                $parameters = $request->getParsedBody();
                $newState = $parameters['state'];

                if(OrderStateEnum::isValidName($newState))
                {
                    $order = Order_Contains_Product::find($parameters['id']);
                    
                    switch($newState)
                    {
                        case 'pending':
                            break;
                        case 'preparation':
                            if(empty($parameters['estimated_completion_on']))
                            {
                                throw new Exception("No se ha ingresado un tiempo estimado de finalizacion.");
                            }

                            if(empty($parameters['employee_id']))
                            {
                                throw new Exception("No se ha ingresado el id de un empleado.");
                            }

                            $order->estimated_completion_on = $parameters['estimated_completion_on'];
                            $order->employee_id = $parameters['employee_id'];
                            break;
                        case 'ready':
                            break;
                        case 'finished':
                            if(empty($parameters['completed_on']))
                            {
                                throw new Exception("No se ha ingresado un tiempo de entrega.");
                            }

                            $order->completed_on = $parameters['completed_on'];
                            break;
                    }
                        
                    $order->state = $newState;
                    $order->save();

                    $payload = json_encode(array("mensaje" => "Order modificado con exito."));
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No se ingreso un nuevo estado valido."));
                }
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