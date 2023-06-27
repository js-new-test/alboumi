<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Promotions;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*
    *
    	Define base url commonly to use anywhere in application
    *
    */
    // public $baseUrl = "";
    
    // function __construct()
    // {
    // 	$this->baseUrl = url('/');
    // }

    public function getBaseUrl()
    {
        return url('/');
    }

    //function to generate random unique key
    public function UniqueRandomNumbersWithinRange() {
        $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $uniquePin = substr(str_shuffle(str_repeat($pool, 3)), 0, 3);
        $uniquePin = "ALB".$uniquePin;
        return $this->checkUniqueFromTable($uniquePin);
    }

    public function checkUniqueFromTable($uniquePin) {
        $companyPin = Promotions::where('coupon_code', $uniquePin)->first();
        if (!empty($companyPin)) {
            return $this->UniqueRandomNumbersWithinRange();
        }else{
            return $uniquePin;
        }
    }
}
