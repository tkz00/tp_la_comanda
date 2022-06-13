<?php

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

    class JWTAuthenticatorMW
    {
        public function __invoke(Request $request, RequestHandler $handler) : Response
        {
            $header = $request->getHeaderLine('Authorization');
            // $token = trim(explode("Bearer", $header)[1]);
            $token = $header;
            $valid = false;

            try{
                JWTAuthenticator::VerifyToken($token);
                $valid = true;

                $payload = json_encode(array('valid' => $valid));
                
                
            } catch (Exception $e) {
                $payload = json_encode(array('error' => $e->getMessage()));
            }
            
            // Como hago para evitar que se ejecute esta linea y poder seguir retornando un Response?
            $response = $handler->handle($request);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

?>