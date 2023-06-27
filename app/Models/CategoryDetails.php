<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryDetails extends Model
{
    protected $table = 'category_details';

    protected $fillable = ['category_id','language_id','title','description','meta_title',
                            'meta_keywords','meta_description','deleted_at'];

    public function globalLanguage()
    {
        return $this->hasOne('App\Models\GlobalLanguage', 'id', 'language_id')->with('language');
    }
}
