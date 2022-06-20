<?php

    require_once __DIR__ . '/../models/Table.php';

    class TableController
    {
        public function GetTables($request, $response, $args)
        {
            $list = Table::all();

            $payload = json_encode(array("Product List" => $list));
  
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function AddTable($request, $response, $args)
        {
            try
            {
                $parameters = $request->getParsedBody();

                $code = $parameters['code'];
    
                // Create Table
                $table = new Table();
                $table->SetCode($code);

                $table->SaveToDB();
    
                $payload = json_encode(array("mensaje" => "Mesa creado con exito"));
    
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