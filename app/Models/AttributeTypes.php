<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AttributeTypes extends Model
{
    protected $table = 'attribute_types';
    protected $fillable = [
        'id',
        'code',
        'name'
    ];
}

