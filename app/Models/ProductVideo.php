<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVideo extends Model
{
    protected $table = 'product_videos';
    protected $fillable = [
        'product_id',
        'title',
        'type',
        'created_at',
        'updated_at'

    ];
}
