<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedProduct extends Model
{
    protected $table = 'related_products';

    protected $fillable = ['product_id', 'related_id'];

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
