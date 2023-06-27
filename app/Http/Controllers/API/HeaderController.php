<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\GlobalLanguage;

class HeaderController extends Controller
{
    public function getAllLanguages()
    {
        $result = GlobalLanguage::getAllLanguages();
        $i = 0;
        foreach($result as $lang)
        {
            $language['id'] = "".$lang->id."";
            $language['text'] = $lang->text;
            $language['image'] = $this->getBaseUrl().'/public/assets/images/languages/'.$lang->image;
            $language['defaultSelected'] = "".$lang->defaultSelected."";
            $language['isUIFlip'] = "".$lang->isUIFlip."";
            $languages[$i++] = $language;
        }
        return response()->json(['status' => "OK","statusCode" => 200,'languageData' => $languages]);
    }

    public function getAllCurrency()
    {
        $currency = \App\Models\GlobalCurrency::getAllCurrency();
        $i = 0;
        $curr_arr = [];
        foreach($currency as $curr)
        {
            $curr_arr[$i]['id'] = $curr->id;
            $curr_arr[$i]['currency_symbol'] = $curr->currency_symbol;                   
            $i++;
        }
        return response()->json(['status' => "OK","statusCode" => 200,'currencyData' => $curr_arr]);
    }
}

?>
