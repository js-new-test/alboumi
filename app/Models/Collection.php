<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $table = 'collections';

    protected $fillable = ['language_id','collection_title','collection_image','collection_link','sort_order','status','deleted_at'];
}
