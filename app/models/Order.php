<?php

    declare(strict_types=1);

    use Illuminate\Database\Eloquent\Casts\Attribute;
    use Illuminate\Database\Eloquent\Model;

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
            return $this->belongsToMany(Product::class, 'Order_Contains_Product', 'order_id', 'product_id')->withPivot('quantity', 'employee_id', 'state');
        }

        public function table()
        {
            return $this->belongsTo(Table::class, 'table_id', 'id');
        }
    }

?>