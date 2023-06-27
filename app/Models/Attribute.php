<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attribute';

    protected $fillable = ['attribute_type_id','parent_id','name', 'internal_name','sort_order','is_variant','is_filterable',
                            'status'];
}
