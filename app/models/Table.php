<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../enums/TableStateEnum.php';

    use Illuminate\Database\Eloquent\Model;

    class Table extends Model implements JsonSerializable
    {
        protected $table = 'client_table';
        public $timestamps = false;
        
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