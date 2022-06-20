<?php

    declare(strict_types=1);

    use Illuminate\Database\Eloquent\Casts\Attribute;
    use Illuminate\Database\Eloquent\Model;

    // class Order extends Model implements JsonSerializable
    class Order extends Model
    {
        protected $table = 'order';
        public $timestamps = false;

        public function SetState($newState)
        {
            if(!OrderStateEnum::isValidName($newState))
            {
                throw new InvalidArgumentException("The state assigned is not valid.");
            }
            
            $this->state = $newState;
        }

        public function products()
        {
            // Check attach and sync methods from Eloquent
            return $this->belongsToMany(Product::class, 'Order_Contains_Product', 'order_id', 'product_id')->withPivot('quantity');
        }

        // public static function GetAllOrders()
        // {
        //     $orders = Order::all();

        //     for($i = 0; $i < count($orders); $i++)
        //     {
        //         $orders[$i]->products = array();
        //         for($j = 0; $j < count($orders[$i]->pivot); $i++)
        //         {
        //             $newProduct = new stdClass();
        //             $newProduct->quantity = $orders[$i]->pivot->quantity;
        //             array_push($orders[$i]->products, $newProduct);
        //         }
        //     }

        //     return $orders;
        // }

        // public function jsonSerialize()
        // {
        //     return [
        //         'id' => $this->id,
        //         'table_id' => $this->table_id,
        //         'client_name' => $this->client_name,
        //         'order_code' => $this->order_code,
        //         'photo_path' => $this->photo_path,
        //         'products' => $this->order_contains_products()
        //     ];
        // }
    }

?>