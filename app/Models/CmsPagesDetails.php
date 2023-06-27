<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPagesDetails extends Model
{
    protected $table = 'cms_details';

    protected $fillable = ['cms_id','language_id','title','description','seo_title','seo_description','seo_keyword',
                            'deleted_at'];

	public function globalLanguage()
	{
		return $this->hasOne('App\Models\GlobalLanguage', 'id', 'language_id')->with('language');
	}
}
