<?php

    use Psr7Middlewares\Middleware\Payload;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Psr\Http\Message\ResponseInterface;
    use Slim\Psr7\Response;

    class TableAuthenticatorMW
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

                            switch($action)
                            {
                                case "tables":
                                    $response = $handler->handle($request);
                                    break;
                                case "update":
                                    if($request->getParsedBody()['state'] == 'closed')
                                    {
                                        $response = new Response();
                                        $response->getBody()->write(json_encode(array("mensaje" => "El usuario " . $payload->name . " no esta autorizado para cerrar una mesa ya que no es un socio.")));
                                    }
                                    else if($payload->type == "waiter")
                                    {
                                        $response = $handler->handle($request);
                                    }
                                    else
                                    {
                                        $response = new Response();
                                        $response->getBody()->write(json_encode(array("mensaje" => "El usuario " . $payload->name . " no esta autorizado para cambiar el estado de una mesa ya que no es ni mozo ni socio.")));
                                    }
                                    break;
                                default:
                                    $response = $handler->handle($request);
                                    break;
                            }
                        }
                    }
                    else
                    {
                        $response = new Response();
                        $response->getBody()->write(json_encode(array("mensaje" => "No se tiene un token válido para realizar esta acción.")));
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