<?php

    class TokenController
    {
        public function GetToken($request, $response, $args)
        {
            $parameters = $request->getParsedBody();

            try
            {
                $employee = Employee::GetEmployeeByName($parameters['name']);

                if($employee)
                {
                    $token = JWTAuthenticator::CreateToken($employee);

                    $payload = json_encode(array("token" => $token));
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No se ha encontrado un empleado con ese nombre."));
                }

            } catch (Exception $e) {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }

            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }
    }

?>