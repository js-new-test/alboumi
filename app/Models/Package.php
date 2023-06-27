<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';

    protected $fillable = ['event_id','package_name','discouunted_price',
    'price','is_active','deleted_at'];
}

