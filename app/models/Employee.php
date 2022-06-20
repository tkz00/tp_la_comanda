<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../enums/EmployeeTypeEnum.php';

    use Illuminate\Database\Eloquent\Model;

    class Employee extends Model implements JsonSerializable
    {
        protected $table = 'Employee';
        public $timestamps = false;



        public function SetType($newType)
        {
            if(!EmployeeTypeEnum::isValidName($newType))
            {
                throw new InvalidArgumentException("The type assigned is not valid.");
            }
            
            $this->type = $newType;
        }

        public function SetName($name)
        {
            $this->name = $name;
        }

        public static function GetEmployees()
        {
            return Employee::all();;
        }

        public static function GetEmployeeByName($name)
        {
            $employee = Employee::where('name', $name)->first();
            return $employee;
        }

        public function SaveToDB()
        {
            $DBAccessObj = DBAccess::GetInstance();
            $consulta = $DBAccessObj->PrepareQuery("INSERT INTO employee (name, type) VALUES (:name, :type)");
            $consulta->bindValue(':name', $this->name, PDO::PARAM_STR);
            $consulta->bindValue(':type', $this->type, PDO::PARAM_STR);
            $consulta->execute();
    
            return $DBAccessObj->GetLastId();
        }

        public function jsonSerialize()
        {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'active' => $this->active,
                'deleted' => $this->deleted,
                'type' => $this->type
            ];
        }
    }

?>