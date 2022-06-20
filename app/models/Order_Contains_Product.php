<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../enums/OrderStateEnum.php';

    use Illuminate\Database\Eloquent\Model;

    class Order_Contains_Product extends Model
    {
        protected $table = 'order_contains_product';
        protected $fillable = ['state', 'quantity'];
        public $timestamps = false;
    }
?>