<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalLanguage;
use App\Models\GlobalCurrency;
use App\Models\Locale;
use Exception;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function getLangSpecificData(Request $request)
    {
        $langCode=$request->code;
        $result= GlobalLanguage::find($request->langId);
        Session::put('language_id',$request->langId);
        Session::put('language_code',$langCode);
        Session::put('language_name',$request->text);
        Session::put('decimal_number',$request->decimal_number);
        Session::put('decimal_separator',$request->decimal_separator);
        Session::put('thousand_separator',$request->thousand_separator);
        return response()->json(['status' => true]);
    }
}


?>
