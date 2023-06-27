<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BrandDetails extends Model
{
	public $table = 'brand_details';

	public function brands()
	{
		return $this->hasOne('App\Models\Manufacturer', 'id', 'brand_id');
	}

	public function globalLanguage()
	{
		return $this->hasOne('App\Models\GlobalLanguage', 'id', 'language_id')->with('language');
	}
}