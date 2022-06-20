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
            if($_SERVER['REQUEST_METHOD'] === 'GET')
            {
                $response = $handler->handle($request);
            }
            else
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
                                    $response = $handler->handle($request);
                                }

                                $response = new Response();
                                $response->getBody()->write(json_encode(array("mensaje" => "El usuario " . $payload->name . " no está autorizado para realizar esta acción.")));
                            }
                        }
                    }
                    else
                    {
                        $response = new Response();
                        $response->getBody()->write(json_encode(array("mensaje" => "No se tiene un token válido para realizar esta acción.")));
                    }
                }
            }

            return $response;
        }
    }
?>