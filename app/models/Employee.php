<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../enums/EmployeeTypeEnum.php';
    require_once __DIR__ . '/../db/DBAccess.php';

    class Employee implements JsonSerializable
    {
        private $id;
        private $active;
        private $deleted;
        private $type;

        public function SetType($newType)
        {
            if(!EmployeeTypeEnum::isValidName($newType))
            {
                throw new InvalidArgumentException("The type assigned is not valid.");
            }
            
            $this->type = $newType;
        }

        // Cannot have a constructor bc the pdo fetch wants to use it without sending the two required parameters
        // public function __construct(int $newId, int $type)
        // {
        //     $this->id = $newId;
        //     $this->active = true;
        //     $this->deleted = false;
        //     $this->type = EmployeeTypeEnum::toString($type);
        // }

        // public static function NewEmployee(int $newId, int $type)
        // {
        //     $newEmployee = new Employee();
        //     $newEmployee->id = $newId;
        //     $newEmployee->active = true;
        //     $newEmployee->deleted = false;
        //     $newEmployee->type = EmployeeTypeEnum::toString($type);
        //     return $newEmployee;
        // }

        public static function GetEmployees()
        {
            $objDBAccess = DBAccess::GetInstance();
            $consulta = $objDBAccess->PrepareQuery("SELECT * FROM employee");
            $consulta->execute();
    
            $test = $consulta->fetchAll(PDO::FETCH_CLASS, 'Employee');
            return $test;
        }

        public function SaveToDB()
        {
            $DBAccessObj = DBAccess::GetInstance();
            $consulta = $DBAccessObj->PrepareQuery("INSERT INTO employee (type) VALUES (:type)");
            $consulta->bindValue(':type', $this->type, PDO::PARAM_STR);
            $consulta->execute();
    
            return $DBAccessObj->GetLastId();
        }

        public function jsonSerialize()
        {
            return [
                'id' => $this->id,
                'active' => $this->active,
                'deleted' => $this->deleted,
                'type' => $this->type
            ];
        }
    }

?>