<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    protected $table = 'attribute_groups';

    protected $fillable = ['sort_order','status','category_ids'];
}
