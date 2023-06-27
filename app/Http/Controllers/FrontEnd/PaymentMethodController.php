<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class PaymentMethodController extends Controller
{
    public function getPaymentMethod()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->where('is_deleted',0)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->where('is_deleted',0)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);
        $lang_id = Session::get('language_id');
        $cart_master_id = Session::get('cart_master_id');
                
        //Localization
        $codes = ['APPNAME','HOME','SHIPPINGADDRESS','PAYMENMETHOD','REVIEWORDER','LOGINEMAIL',
        'MYADDRESSES5','DELIVERY','STOREPICKUPFREE','CONTINUESHOPPING','EDIT_ADDRESS_TITLE',
        'DELIVERYHERE','MYADDRESSES1','SET_AS_DEFAULT_ADDRESS','MYADDRESSES11','MYADDRESSES12',
        'PINCODE_HINT','ADDRESS_LINE1_HINT','ADDRESS_LINE2_HINT','MYACCOUNTLABEL3','FULL_NAME',
        'addressType1','addressType2','SELECTCOUNTRY','CONFIRMATION','YES','NO','AREYOUSURE','CANCEL'
        ,'EDIT_ADDRESS_TITLE','FULLNAMEREQ', 'ADDRESS1REQ', 'ADDRESS2REQ','MOBILEREQ','MOBILENUM',
        'COUNTRYREQ','PINCODEREQ','CHECKOUT','PAYONLINECREDIT','PAYONLINEDEBIT','PLACEORDER','CONTINUE'];
        $paymentMethodLabels = getCodesMsg($lang_id, $codes);
        
        $baseUrl = $this->getBaseUrl();
        $pageName = $paymentMethodLabels["PAYMENMETHOD"];
        $projectName = $paymentMethodLabels["APPNAME"];        

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        
        return view('frontend.payment-method', compact('baseUrl','pageName','projectName','megamenuFileName',
        'mobileMegamenuFileName','paymentMethodLabels','cart_master_id'));
    }

    public function savePaymentMethod(Request $request)
    {
        //Localization
        $codes = ['PAYMENTMETHODSLCTSUCC','SOMETHINGWRONG'];
        $paymentMethodLabels = getCodesMsg(Session::get('language_id'), $codes);
        
        $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
        if($cart_master)
        {
            $cart_master->payment_method = $request->cart_method;            
            $cart_master->save();
            $result['status'] = 'true';
            $result['msg'] = $paymentMethodLabels['PAYMENTMETHODSLCTSUCC'];
            return $result;
        } 
        else
        {
            $result['status'] = 'false';
            $result['msg'] = $paymentMethodLabels['SOMETHINGWRONG'];
            return $result;
        }
    }
}
