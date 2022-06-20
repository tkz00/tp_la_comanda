<?php

    declare(strict_types=1);

    use Illuminate\Database\Eloquent\Model;

    require_once __DIR__ . '/../enums/ProductTypeEnum.php';
    require_once __DIR__ . '/../db/DBAccess.php';
    require_once __DIR__ . '/../db/eloquentDatabase.php';

    class Product extends Model implements JsonSerializable
    {
        protected $table = 'product';
        // private $id;
        // private $title;
        // private $type;
        // private $price;

        // public static function GetProducts()
        // {
        //     return Product::all();
        // }

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

        public function orders()
        {
            return $this->belongsToMany(Order::class, 'Order_Contains_Product', 'product_id', 'order_id');
        }

        // public static function GetProducts()
        // {
        //     $objDBAccess = DBAccess::GetInstance();
        //     $consulta = $objDBAccess->PrepareQuery("SELECT * FROM product");
        //     $consulta->execute();
    
        //     $test = $consulta->fetchAll(PDO::FETCH_CLASS, 'product');
        //     return $test;
        // }


        // public function SaveToDB()
        // {
        //     $DBAccessObj = DBAccess::GetInstance();
        //     $consulta = $DBAccessObj->PrepareQuery("INSERT INTO product (title, type, price) VALUES (:title, :type, :price)");
        //     $consulta->bindValue(':title', $this->title, PDO::PARAM_STR);
        //     $consulta->bindValue(':type', $this->type, PDO::PARAM_STR);
        //     $consulta->bindValue(':price', $this->price);
        //     $consulta->execute();
    
        //     return $DBAccessObj->GetLastId();
        // }

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