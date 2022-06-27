<?php

    class ClientController
    {
        public function GetOrders($request, $response, $args)
        {
            $parameters = $request->getQueryParams();

            $order_code = $parameters['order_code'];
            $table_code = $parameters['table_code'];

            $order = Order::where('order_code', $order_code)->first();
            $table = $order->table;

            if($table->table_code == $table_code)
            {
                $orderDetails = Order_Contains_Product::where('order_id', $order->id)->get();
                $payload = json_encode(array("Tus pedidos" => $orderDetails));
            }

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }
    }

?>