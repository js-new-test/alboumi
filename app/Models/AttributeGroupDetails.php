<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AttributeGroupDetails extends Model
{
    protected $table = 'attribute_group_details';

    protected $fillable = ['language_id','attr_group_id','name','display_name'];

    public function attributes()
	{
		return $this->hasMany('App\Models\AttributeGroup', 'id', 'attr_group_id');
	}

	public function globalLanguage()
	{
		return $this->hasOne('App\Models\GlobalLanguage', 'id', 'language_id')->with('language');
	}

	public static function getAttributeGroups($defaultLanguageId)
    {
		$attr_groups = AttributeGroupDetails::select('ag.id','display_name')
										->join('attribute_groups as ag','ag.id','=','attribute_group_details.attr_group_id')
                                        ->where('ag.status','=',1)
										->whereNull('attribute_group_details.deleted_at')
										->whereNull('ag.deleted_at')
										->where('language_id',$defaultLanguageId)
										->get();
        return $attr_groups;
    }
}
