<?php

    declare(strict_types=1);

use Illuminate\Contracts\Auth\StatefulGuard;

    require_once __DIR__ . '/../enums/OrderStateEnum.php';
    require_once __DIR__ . '/../db/DBAccess.php';

    class Order implements JsonSerializable
    {
        public $id;
        public $product_id;
        public $quantity;
        public $table_id;
        public $client_name;
        public $order_code;
        private $state;
        public $photo_path;
        public $estimated_completion_on;
        public $completed_on;
        public $employee_id;

        public function SetState($newState)
        {
            if(!OrderStateEnum::isValidName($newState))
            {
                throw new InvalidArgumentException("The state assigned is not valid.");
            }
            
            $this->state = $newState;
        }

        public static function GetOrders()
        {
            $objDBAccess = DBAccess::GetInstance();
            $consulta = $objDBAccess->PrepareQuery("SELECT * FROM la_comanda.order");
            $consulta->execute();
    
            $test = $consulta->fetchAll(PDO::FETCH_CLASS, 'order');
            return $test;
        }

        public function SaveToDB()
        {
            $DBAccessObj = DBAccess::GetInstance();
            $consulta = $DBAccessObj->PrepareQuery("INSERT INTO la_comanda.order (product_id, quantity, table_id, client_name, order_code) VALUES (:product_id, :quantity, :table_id, :client_name, :order_code)");
            // $consulta = $DBAccessObj->PrepareQuery("INSERT INTO la_comanda.order (product_id, quantity, table_id, client_name, order_code) VALUES (:product_id, :quantity, :table_id, :client_name, :order_code);");
            $consulta->bindValue(':product_id', $this->product_id, PDO::PARAM_INT);
            $consulta->bindValue(':quantity', $this->quantity, PDO::PARAM_INT);
            $consulta->bindValue(':table_id', $this->table_id, PDO::PARAM_INT);
            $consulta->bindValue(':client_name', $this->client_name, PDO::PARAM_STR);
            $consulta->bindValue(':order_code', $this->order_code, PDO::PARAM_STR);
            $consulta->execute();
    
            return $DBAccessObj->GetLastId();
        }

        public function jsonSerialize()
        {
            return [
                'id' => $this->id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'table_id' => $this->table_id,
                'client_name' => $this->client_name,
                'order_code' => $this->order_code,
                'state' => $this->state,
                'photo_path' => $this->photo_path,
                'estimated_completion_on' => $this->estimated_completion_on,
                'completed_on' => $this->completed_on,
                'employee_id' => $this->employee_id
            ];
        }
    }

?>