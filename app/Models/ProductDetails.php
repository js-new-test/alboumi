<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductDetails extends Model
{
    public $table = 'product_details';

    public function product()
    {
    	return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public function productVideo()
    {
    	return $this->belongsTo('App\Models\ProductVideo', 'product_id', 'product_id');
    }
}
