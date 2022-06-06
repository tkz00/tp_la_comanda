<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../enums/TableStateEnum.php';
    require_once __DIR__ . '/../db/DBAccess.php';

    class Table implements JsonSerializable
    {
        private $id;
        private $table_code;
        private $state;

        public function SetCode(string $newCode)
        {
            $this->table_code = $newCode;
        }

        public function SetState($newState)
        {
            if(!TableStateEnum::isValidName($newState))
            {
                throw new InvalidArgumentException("The state assigned is not valid.");
            }
            
            $this->state = $newState;
        }

        public static function GetTables()
        {
            $objDBAccess = DBAccess::GetInstance();
            $consulta = $objDBAccess->PrepareQuery("SELECT * FROM client_table");
            $consulta->execute();
    
            $test = $consulta->fetchAll(PDO::FETCH_CLASS, 'table');
            return $test;
        }

        public function SaveToDB()
        {
            $DBAccessObj = DBAccess::GetInstance();
            $consulta = $DBAccessObj->PrepareQuery("INSERT INTO client_table (table_code) VALUES (:table_code)");
            $consulta->bindValue(':table_code', $this->table_code, PDO::PARAM_STR);
            $consulta->execute();
    
            return $DBAccessObj->GetLastId();
        }

        public function jsonSerialize()
        {
            return [
                'id' => $this->id,
                'code' => $this->table_code,
                'state' => $this->state
            ];
        }
    }

?>