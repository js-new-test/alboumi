<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CurrencyController extends Controller
{
    public function getCurrSpecificData(Request $request)
    {
        $langCode=$request->currency_symbol;
        Session::put('currency_id',$request->currId);
        Session::put('currency_symbol',$langCode);        
        return response()->json(['status' => true]);
    }
}
