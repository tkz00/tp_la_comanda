<?php

    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/Order_Contains_Product.php';
    require_once __DIR__ . '/../models/Product.php';

    use Mpdf\Mpdf;

    define("imagesDirectory", "./OrderPhotos/");

    class OrderController
    {
        public function GetOrders($request, $response, $args)
        {
            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
            $payload = JWTAuthenticator::GetData($token);

            switch($payload->type)
            {
                case "partner":
                    $list = Order::with('products')->get();
                    $payload = json_encode(array("Order list" => $list));
                    break;
                case "waiter":
                    $list = Order_Contains_Product::with('products')->where('state', 'ready')->get();
                    $payload = json_encode(array("Orders ready for delivery" => $list));
                    break;
                default:
                    $list = Order_Contains_Product::with('products')->where('state', 'pending')->get();
                    $payload = json_encode(array("Orders waiting for employee" => $list));
                    break;
            }


  
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

                
                $uploadedImage = $request->getUploadedFiles()['photo'];
                if(isset($uploadedImage))
                {
                    // Photo 
                    if(!is_dir(imagesDirectory) && !mkdir(imagesDirectory, 0777, true))
                    {
                        die("Error creating photos folder " . imagesDirectory);
                    }

                    $photoName = $order->id . '_' . $order->client_name . '.' . pathinfo($uploadedImage->getClientFilename(), PATHINFO_EXTENSION);
                    $photoPath = imagesDirectory . $photoName;
                    $uploadedImage->moveTo($photoPath);

                    $order->photo_path = $photoName;
                    $order->save();
                }

                // Create order_product relation table in DB
                $quantity = $parameters['quantity'];

                for($i = 0; $i < count($product_ids); $i++)
                {
                    $order_contains_product = new Order_Contains_Product();
                    $order_contains_product->order_id = $order->id;
                    $order_contains_product->product_id = $product_ids[$i];
                    $order_contains_product->quantity = (isset($quantity[$i])) ? $quantity[$i] : 1;
                    $order_contains_product->save();
                }

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

        public function GetPdf($request, $response, $args)
        {
            try
            {
                $list = Order::with('products')->get();
                
                $html = "";

                $html .= '<table style="border-collapse: collapse; width: 100%; border: 1px solid black;">';

                $html .= "<tr>";
                $html .= "<th>Id</th>";
                $html .= "<th>Table Id</th>";
                $html .= "<th>Client Name</th>";
                $html .= "<th>Order Code</th>";
                $html .= "<th>Products (quantity)</th>";
                $html .= "<th>Photo</th>";
                $html .= "</tr>";

                for($i = 0; $i < count($list); $i++)
                {
                    $html .= "<tr>";
                    $html .= "<td>" . $list[$i]->id . "</td>";
                    $html .= "<td>" . $list[$i]->table_id . "</td>";
                    $html .= "<td>" . $list[$i]->client_name . "</td>";
                    $html .= "<td>" . $list[$i]->order_code . "</td>";

                    if($list[$i]->products)
                    {
                        $html .= "<td>";

                        for($j = 0; $j < count($list[$i]->products); $j++)
                        {
                            $html .= $list[$i]->products[$j]->title . " (" . $list[$i]->products[$j]->pivot->quantity . ")" . "<br>";
                        }
                        
                        $html .= "</td>";
                    }

                    $html .= "<td>";

                    if($list[$i]->photo_path)
                    {
                        $html .= '<img src="' . imagesDirectory . $list[$i]->photo_path . '" alt="" height=100></img>';
                    }

                    $html .= "</td>";

                    $html .= "</tr>";
                }

                $html .= "</table>";

                $mpdf = new Mpdf();

                require_once __DIR__ . '/../resources/pdf.css';

                $styleSheet = file_get_contents(__DIR__ . '/../resources/pdf.css');

                $mpdf->WriteHTML($styleSheet, 1);
                
                $mpdf->WriteHTML($html, 2);
                
                $mpdf->Output();

                $payload = json_encode(array("mensaje" => "Se genero el PDF con exito"));
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