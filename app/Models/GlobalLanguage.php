<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalLanguage extends Model
{
    use HasFactory;

    public $table = 'global_language';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_id',
    ];


    public function language()
    {
        return $this->hasOne('App\Models\WorldLanguage', 'id', 'language_id');
    }

    public static function getAllLanguages()
    {
        $allLanguages = GlobalLanguage::join('world_languages as wl','wl.id','=','global_language.language_id')
                                    ->select('global_language.id','global_language.is_default','langEN as text','is_default as defaultSelected','visibility as isUIFlip','alpha2 as Code','lang_flag as image')
                                    ->where('status',1)
                                    ->where('is_deleted',0)
                                    ->get();
        return $allLanguages;
    }

    public static function checkVisibility($langId)
    {
        $langVisibility = GlobalLanguage::join('world_languages as wl','wl.id','=','global_language.language_id')
                                    ->select('visibility')
                                    ->where('status',1)
                                    ->where('is_deleted',0)
                                    ->where('global_language.id',$langId)
                                    ->first();
        return $langVisibility;
    }
}
