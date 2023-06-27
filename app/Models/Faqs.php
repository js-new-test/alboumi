<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    protected $table = 'faqs';

    protected $fillable = ['language_id','question','answer','sort_order','is_active','deleted_at'];
}
