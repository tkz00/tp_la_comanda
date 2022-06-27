<?php

    require_once __DIR__ . '/../models/Employee.php';

    class EmployeeController
    {
        public function GetEmployees($request, $response, $args)
        {
            $list = Employee::GetEmployees();

            $payload = json_encode(array("Product List" => $list));
  
            $response->getBody()->write($payload);
            return $response
              ->withHeader('Content-Type', 'application/json');
        }

        public function AddEmployee($request, $response, $args)
        {
            try
            {
                $parameters = $request->getParsedBody();

                $name = $parameters['name'];
                $type = $parameters['type'];
    
                // Create Employee
                $employee = new Employee();
                $employee->SetType($type);
                $employee->SetName($name);

                $employee->save();
    
                $payload = json_encode(array("mensaje" => "Empleado creado con exito"));
    
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("mensaje" => $e->getMessage()));
            }
            
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        }

        public function ChangeActive($request, $response, $args)
        {
            try
            {
                $parameters = $request->getParsedBody();

                $employeeId = $parameters['id'];
                $isActive = $parameters['active'];

                $employee = Employee::find($employeeId);
                $employee->active = $isActive;
                $employee->save();

                $payload = json_encode(array("mensaje" => "Empleado modificado con exito"));
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