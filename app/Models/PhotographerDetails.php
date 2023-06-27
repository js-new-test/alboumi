<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotographerDetails extends Model
{
    protected $table = 'photographer_details';

    protected $fillable = ['photographer_id','language_id','name','about','location','experience',
                            'deleted_at'];

	public function globalLanguage()
	{
		return $this->hasOne('App\Models\GlobalLanguage', 'id', 'language_id')->with('language');
	}
}
