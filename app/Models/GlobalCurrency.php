<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalCurrency extends Model
{
    use HasFactory;

    protected $table = 'global_currency';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency_id',        
    ];

    public function currency()
    {
        return $this->hasOne('App\Models\Currency', 'id', 'name','code','currency_name','currency_symbol');
    }

    public static function getAllCurrency()
    {
        $currency = GlobalCurrency::select('global_currency.id','currency.name',
        'currency.code','currency.currency_symbol')
        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
        ->where('global_currency.is_deleted', 0)->get();
        return $currency;
    }
}
