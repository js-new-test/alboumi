<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;
use Auth;

class LumiseProducts extends Model
{
    use CommonTrait;
    public $table = 'lumise_products';

    // public function category()
    // {
    //     return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    // }


}
