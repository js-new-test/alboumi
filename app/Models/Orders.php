<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $table = "orders";

    public function orderProducts()
    {
        return $this
                    ->hasMany('App\Models\OrderProducts',"order_id")
                    ->select(
                        "id",
                        "order_id",
                        'details',
                        'quantity'
                    );
    }  
    public static function getOrderUniqueId()
    {
        $last_unique_id = \App\Models\Orders::orderBy('id', 'desc')->pluck('order_id')->first();
        $uniqueId = getIdByLastUniqueOrderId($last_unique_id, 'ALB');
        return $uniqueId;
    }
}
