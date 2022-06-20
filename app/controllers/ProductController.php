<?php

    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../models/Product.php';

    class ProductController
    {
        public function GetProducts($request, $response, $args)
        {
            $list = Product::all();

            $payload = json_encode(array("Product List" => $list));
  
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function AddProduct($request, $response, $args)
        {
            try
            {
                $parameters = $request->getParsedBody();

                $title = $parameters['title'];
                $type = $parameters['type'];
                $price = $parameters['price'];
    
                // Create Product
                $product = new Product();
                $product->SetTitle($title);
                $product->SetType($type);
                $product->SetPrice($price);

                $product->save();
    
                $payload = json_encode(array("mensaje" => "Producto creado con exito"));
    
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