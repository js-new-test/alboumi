<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeDetails extends Model
{
    protected $table = 'attribute_details';

    protected $fillable = ['attribute_id','language_id','attribute_group_id','name','display_name'];

	public function globalLanguage()
	{
		return $this->hasOne('App\Models\GlobalLanguage', 'id', 'language_id')->with('language');
	}

}