<?php

    use Psr7Middlewares\Middleware\Payload;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Psr\Http\Message\ResponseInterface;
    use Slim\Psr7\Response;

    class OrderAuthenticatorMW
    {
        public function __invoke(Request $request, RequestHandler $handler) : ResponseInterface
        {
            $header = $request->getHeaderLine('Authorization');

            if($header != "")
            {
                $token = trim(explode("Bearer", $header)[1]);
                $valid = false;

                try
                {
                    JWTAuthenticator::VerifyToken($token);
                    $valid = true;
                }
                catch (Exception $e) {
                    $valid = false;
                }

                if($valid)
                {
                    // Verify if employee can perform action based on it's employeeType
                    $payload = JWTAuthenticator::GetData($token);
                    
                    if($payload->type == "partner")
                    {
                        $response = $handler->handle($request);
                    }
                    else
                    {
                        $uri = $request->getUri();
                        $action = trim(end(explode("/", $uri->getPath())));

                        if($action == "order" && $payload->type == "waiter")
                        {
                            $response = $handler->handle($request);
                        }
                        else
                        {
                            if($action == "update")
                            {
                                // Check if order has employee and if this employee is the one sending the request.
                                $order = Order_Contains_Product::find($request->getParsedBody()['id']);
                                $tokenEmployee = JWTAuthenticator::GetData($token);

                                if(!isset($order->employee_id) || $order->employee_id == $tokenEmployee->id)
                                {
                                    $response = $handler->handle($request);
                                }
                                else
                                {
                                    $response = new Response();
                                    $response->getBody()->write(json_encode(array("mensaje" => "El usuario " . $payload->name . " no es el responsabe de esta orden.")));    
                                }
                            }
                            else if($_SERVER['REQUEST_METHOD'] === 'GET' && ($payload->type == "bartender" || $payload->type == "cook" || $payload->type == "brewer"))
                            {
                                $response = $handler->handle($request);
                            }
                            else
                            {
                                $response = new Response();
                                $response->getBody()->write(json_encode(array("mensaje" => "El usuario " . $payload->name . " no está autorizado para realizar esta acción.")));
                            }
                        }
                    }
                }
                else
                {
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("mensaje" => "No se tiene un token válido para realizar esta acción.")));
                }
            }

            return $response;
        }
    }
?>