<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Agent;
use Auth;
use DB;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;

class MyAccountController extends Controller
{
    public function showMyAccount()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        //Localization
        $codes = ['MYACCOUNTLABEL', 'MYACCOUNTLABEL1', 'MYACCOUNTLABEL2','MYACCOUNTLABEL3','MYACCOUNTLABEL4'
        ,'MYACCOUNTLABEL5', 'LOGINLABEL2','MYACCOUNTLABEL','APPNAME', 'MYACCOUNTSEODESC', 'MYACCOUNTSEOKEYWORD'
        ,'HOME','EMAILREQ','NOTVALIDEMAIL','FIRSTNAMEREQ','LASTNAMEREQ','MOBILEMUSTBE8DIGIT','MOBILENUM',
        'error505','MOBILEREQ','REGISTERLABEL11'];
        $myAccountLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $myAccountLabels["MYACCOUNTLABEL"];
        $projectName = $myAccountLabels["APPNAME"];
        $baseUrl = $this->getBaseUrl();       
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $customer_id = Auth::guard('customer')->user()->id;
        $customer = \App\Models\Customer::find($customer_id);
        return view('frontend.my-account', compact('customer','pageName','projectName',
        'megamenuFileName','myAccountLabels','baseUrl','mobileMegamenuFileName'));
    }

    public function saveMyAccount(Request $request)
    {
        //Localization
        $codes = ['FIRSTNAMEREQ', 'LASTNAMEREQ', 'EMAILREQ','NOTVALIDEMAIL','MOBILEREQ'
        ,'MOBILENUM', 'MYACCOUNTLABEL5', 'MYACCOUNTLABEL6', 'MYACCOUNTLABEL7','MOBILEMUSTBE8DIGIT'];
        $myAccountLabels = getCodesMsg(Session::get('language_id'), $codes);

        $language_id = Session::get('language_id');
        $msg = [
            'firstName.required' => $myAccountLabels["FIRSTNAMEREQ"],
            'lastName.required' => $myAccountLabels["LASTNAMEREQ"],
            'email.required' => $myAccountLabels["EMAILREQ"],
            'email.email' => $myAccountLabels["NOTVALIDEMAIL"],
            'mobile.required' => $myAccountLabels["MOBILEREQ"],
            'mobile.numeric' => $myAccountLabels["MOBILENUM"],
            'mobile.min' => $myAccountLabels["MOBILEMUSTBE8DIGIT"],
            // 'dateOfBirth.required' => $myAccountLabels["MYACCOUNTLABEL5"],
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric|min:8',
            // 'dateOfBirth' => 'required',
        ],$msg);

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer_id = Auth::guard('customer')->user()->id;
        $my_account = \App\Models\Customer::updateMyAccount($request, $customer_id);
        if($my_account == "true")
        {
            return redirect()->back()->with('msg', $myAccountLabels["MYACCOUNTLABEL6"])->with('alert_type', true);
        }
        else
        {
            return redirect()->back()->with('msg', $myAccountLabels["MYACCOUNTLABEL7"])->with('alert_type', false);
        }
    }
}
