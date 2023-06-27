<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOptions extends Model
{
    protected $table = 'attribute_options';
    protected $fillable = [
        'id',
        'attribute_id',
        'display_name',
        'title',
        'multicolor',
        'sort_order',
        'status'
    ];

    public static function saveAttributeOptions($data)
    {
        foreach($data->display_name as $k => $v)
        {
            $multicolor = array_values($data['multicolor']);

            AttributeOptions::create([
                'attribute_id' => $data['attribute_id'],
                'display_name' => $v,
                'title' => $data['title'][$k],
                'multicolor' => $multicolor[$k],
                'sort_order' => 0,
                'status' => '1'
            ]);
        }
        return true;
    }
}