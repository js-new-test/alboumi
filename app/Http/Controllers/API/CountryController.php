<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Country;
use Config;

class CountryController extends Controller
{
    public function getCountrylist(Request $request)
    {
        $countryData = Country::select('id','name','phonecode')
                                ->where('is_active',1)
                                ->whereNull('deleted_at')
                                ->get();
        $countryList = array();
        $k = 0;
        foreach($countryData as $ctr)
        {
            $countryList[$k]['country'] = $ctr['name'];
            $countryList[$k]['flagIcon'] = '';
            $countryList[$k++]['countryCode'] = '+'.$ctr['phonecode'];
        }

        $result['status'] = "OK";
        $result['statusCode'] = 200;
        $result['message'] = "Country list received.";
        $result['countryList'][] = $countryList;
        return response()->json($result);
    }
}
