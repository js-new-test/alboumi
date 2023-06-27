<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustGroupPrice extends Model
{
    protected $table = 'customer_group_price';
    protected $fillable = ['product_id','customer_group_id','price'];

    public $timestamps = false;
}
