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
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;

class ChangePasswordController extends Controller
{    
    public function showChangePassForm()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        //Localization
        $codes = ['CHANGEPASSWORDLABEL', 'CHANGEPASSWORDLABEL1', 'CHANGEPASSWORDLABEL2',
        'CHANGEPASSWORDLABEL3','REGISTERLABEL8','CHANGE_PASSWORD','APPNAME','CHANGEPASSSEODESC',
        'CHANGEPASSSEOKEYWORD','HOME','CURRENTPASSREQ','PASSWORDREQ','PASSCONFREQ','error516'];
        $changePasswordLabels = getCodesMsg(Session::get('language_id'), $codes);
        
        $baseUrl = $this->getBaseUrl();
        $pageName = $changePasswordLabels["CHANGE_PASSWORD"];
        $projectName = $changePasswordLabels["APPNAME"];        

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        
        return view('frontend.change-password', compact('pageName','projectName',
        'megamenuFileName','changePasswordLabels', 'baseUrl','mobileMegamenuFileName'));
    }

    public function changePassword(Request $request)
    {                             
        if(Auth::guard('customer')->check())
        {
            //Localization
            $codes = ['CURRENTPASSWORD', 'PASSWORDREQ', 'PASSWORDMINLENGTH',
            'PASSCONFREQ','PASSWORDCONFMSG', 'CHANGEPASSSUCC', 'PASSWORDNOTMATCH'];
            $changePasswordLabels = getCodesMsg(Session::get('language_id'), $codes);

            $customer_id = Auth::guard('customer')->user()->id;
            $customer = \App\Models\Customer::findOrFail($customer_id);
            $lang_id = Session::get('language_id');
            $msg = [
                'currentpassword.required' => $changePasswordLabels["CURRENTPASSWORD"],                        
                'password.required' => $changePasswordLabels["PASSWORDREQ"],
                'password.min' => $changePasswordLabels["PASSWORDMINLENGTH"],
                'confirm_password.required' => $changePasswordLabels["PASSCONFREQ"],
                'password.same' => $changePasswordLabels["PASSWORDCONFMSG"],            
            ];  

            $validator = Validator::make($request->all(), [
                'currentpassword' => 'required',            
                'password' => 'required|min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required',
            ],$msg);
                
            if($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if(Hash::check($request->currentpassword, $customer->password)) { 
                $customer->fill(['password' => Hash::make($request->password)])->save();                                                                                       
                return redirect()->back()->with('msg', $changePasswordLabels['CHANGEPASSSUCC'])->with('alert_type', true); 
            } else {                                                 
                return redirect()->back()->with('msg', $changePasswordLabels['PASSWORDNOTMATCH'])->with('alert_type', false); 
            }
        }
    }
}
