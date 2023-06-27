<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use HasFactory;

    protected $table = 'locale';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'title', 'is_deleted'];

    public static function getFooterLabels($language_id)
    {
        $footerLabels = Locale::select('value')
                                ->join('locale_details as ld','ld.locale_id','=','locale.id')
                                ->where([
                                    ['ld.language_id', '=', $language_id],
                                    ['locale.is_active', '=', 0]
                                ])
                                ->where(function($q) {
                                    $q->where('code','=','FOLLOWUS')
                                    ->orWhere('code','=','DOWNLOADAPP');
                                })
                                ->get();
        return $footerLabels;
    }

    public static function getFreeDeliveryMsg($language_id)
    {
        $freeDeliveryMsg = Locale::select('value')
                                    ->join('locale_details as ld','ld.locale_id','=','locale.id')
                                    ->where([
                                        ['ld.language_id', '=', $language_id],
                                        ['locale.is_active', '=', 0],
                                        ['code','=','FREEDELIVERY']
                                    ])
                                   ->first();

        return $freeDeliveryMsg;                           
    }
}
