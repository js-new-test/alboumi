<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendedProduct extends Model
{
    protected $table = 'recommended_products';

    protected $fillable = ['product_id', 'recommended_id'];

    protected $timestamp = true;

    public function products()
    {
    	return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function categoryProducts()
    {
    	return $this->belongsTo('App\Models\Product', 'product_id', 'id')->with('category');
    }
}
