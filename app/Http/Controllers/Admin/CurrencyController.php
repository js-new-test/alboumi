<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use DataTables;
use App\Models\GlobalCurrency;
use App\Models\Currency;
use App\Models\CurrencyConversionRate;

class CurrencyController extends Controller
{
	protected $projectName = "";
	protected $pageTitle = "";
	public function __construct()
    {
        $this->projectName = "Alboumi";
        $this->pageTitle = "Currency Conversion";
    }

	public function getCurrencyConversion()
	{
		$projectName = $this->projectName;
		$pageTitle = $this->pageTitle;
		$baseUrl = $this->getBaseUrl();
		$currency = GlobalCurrency::select('global_currency.id', 'currency_name', 'code')
									->join('currency', 'currency.id', '=', 'global_currency.currency_id')->get()->toArray();

		// $currency = Currency::where('status',0)->get();

		return view('admin.currencyConversion.currencyConversion', compact('currency', 'projectName', 'pageTitle', 'baseUrl'));
	}

	public function postCurrencyConversion(Request $request)
	{
		$res = array_combine($request['currencyId'],$request['currencyCode']);
		foreach ($request['currencyId'] as $key) {
			$currencyConversion = CurrencyConversionRate::where(['from_currency_id'=>$request['selectedCurrencyId'], 'to_currency_id'=>$key])->first();
			if (!empty($currencyConversion)) {
				$currencyConversion->rate = array_key_exists($key,$res) ? $res[$key] : 0;
				$currencyConversion->save();
			} else {
				$currencyConversion = new CurrencyConversionRate;
				$currencyConversion->from_currency_id = $request['selectedCurrencyId'];
				$currencyConversion->to_currency_id = $key;
				$currencyConversion->rate = isset($res[$key]) ? $res[$key] : 0;
				$currencyConversion->save();
			}
		}

		$notification = array(
            'message' => 'Currency Conversion added successfully!', 
            'alert-type' => 'success'
        );

        return redirect('admin/currency/list')->with($notification);
	}

	public function getRemainingCurrencies(Request $request)
	{
		$currencies = GlobalCurrency::select('global_currency.id', 'currency_name', 'currency_code', 'code')
					->join('currency', 'currency.id', '=', 'global_currency.currency_id')
					// ->join('currency_conversion_rate', 'currency_conversion_rate.to_currency_id', '=', 'global_currency.id')
					->where('global_currency.id', "!=",$request['selectCurrency'])
					->get();

									
		// dd($currencies);die;

		//$currencies = Currency::
		/*join('currency_conversion_rate', 'currency_conversion_rate.to_currency_id', '=', 'currency.id')
									->*/
									// where('currency.id', "!=",$request['selectCurrency'])
									// ->where('status',0)
									// ->get();


		$currencyConversion = CurrencyConversionRate::where('from_currency_id', $request['selectCurrency'])->get();

		$resArr = [];
		$index = 0;
		foreach ($currencies as $currency) {
			$resArr[$index]['currencyId'] = $currency->id;
			$resArr[$index]['code'] = $currency->currency_code;
			$resArr[$index]['rate'] = 0;
			foreach ($currencyConversion as $data) {
				if (in_array($currency->id, $data->toArray())) {
					$resArr[$index]['conversionId'] = $data['id'];
					$resArr[$index]['rate'] = $data['rate'];
				}
			}
			$resArr[$index++]['name'] = $currency->code;
		}
		return $resArr;
	}
}
