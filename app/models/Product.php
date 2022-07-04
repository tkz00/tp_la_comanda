<?php

    declare(strict_types=1);

    use Illuminate\Database\Eloquent\Model;

    require_once __DIR__ . '/../enums/ProductTypeEnum.php';
    require_once __DIR__ . '/../db/eloquentDatabase.php';

    class Product extends Model implements JsonSerializable
    {
        protected $table = 'product';
        public $timestamps = false;

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