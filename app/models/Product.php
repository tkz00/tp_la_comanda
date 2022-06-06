<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../enums/ProductTypeEnum.php';
    require_once __DIR__ . '/../db/DBAccess.php';

    class Product implements JsonSerializable
    {
        private $id;
        private $title;
        private $type;
        private $price;

        public function SetTitle(string $newTitle)
        {
            $this->title = $newTitle;
        }

        public function SetType($newType)
        {
            if(!ProductTypeEnum::isValidName($newType))
            {
                throw new InvalidArgumentException("The type assigned is not valid.");
            }
            
            $this->type = $newType;
        }

        public function SetPrice($newPrice)
        {
            $this->price = $newPrice;
        }

        public static function GetProducts()
        {
            $objDBAccess = DBAccess::GetInstance();
            $consulta = $objDBAccess->PrepareQuery("SELECT * FROM product");
            $consulta->execute();
    
            $test = $consulta->fetchAll(PDO::FETCH_CLASS, 'product');
            return $test;
        }

        public function SaveToDB()
        {
            $DBAccessObj = DBAccess::GetInstance();
            $consulta = $DBAccessObj->PrepareQuery("INSERT INTO product (title, type, price) VALUES (:title, :type, :price)");
            $consulta->bindValue(':title', $this->title, PDO::PARAM_STR);
            $consulta->bindValue(':type', $this->type, PDO::PARAM_STR);
            $consulta->bindValue(':price', $this->price);
            $consulta->execute();
    
            return $DBAccessObj->GetLastId();
        }

        public function jsonSerialize()
        {
            return [
                'id' => $this->id,
                'title' => $this->title,
                'type' => $this->type,
                'price' => $this->price
            ];
        }
    }

?>