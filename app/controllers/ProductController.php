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

        public function ProductsCSV($request, $response, $args)
        {
            try
            {
                $list = Product::all();
                
                if(count($list) > 0)
                {
                    $filePointer = fopen("products.csv", "w");

                    for($i = 0; $i < count($list); $i++)
                    {
                        fputcsv($filePointer, $list[$i]->toArray());
                    }

                    fclose($filePointer);

                    $payload = json_encode(array("mensaje" => "Se ha creado el archivo products.csv con exito."));
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No hay productos en la base de datos para mostrar."));
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

        public function LoadCSV($request, $response, $args)
        {
            try
            {
                $uploadedFiles = $request->getUploadedFiles();

                $csvFile = $uploadedFiles['csvFile'];

                $list = array_map('str_getcsv', file($csvFile->getFilePath()));

                for($i = 0; $i < count($list); $i++)
                {
                    if(!Product::find($list[$i][0]))
                    {
                        $newProduct = new Product();
                        $newProduct->id = $list[$i][0];
                        $newProduct->title = $list[$i][1];
                        $newProduct->type = $list[$i][2];
                        $newProduct->price = $list[$i][3];
                        $newProduct->created_at = $list[$i][4];
                        $newProduct->updated_at = $list[$i][4];

                        $newProduct->save();
                    }
                }

                $payload = json_encode(array("mensaje" => "Archivo leido con exito."));
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