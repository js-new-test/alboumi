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

class MyAddressController extends Controller
{
    public function showMyaddress()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        //Localization
        $codes = ['MYADDRESSES', 'MYADDRESSES1', 'MYADDRESSES2','MYADDRESSES3','MYADDRESSES4','MYADDRESSES6'
        ,'MYADDRESSES7','MYADDRESSES8','MYADDRESSES9','MYADDRESSES10','MYACCOUNTLABEL3','MYACCOUNTLABEL5',
        'CONFIRMATION','AREYOUSURE','NO','YES','MYADDRESSES11', 'MYADDRESSES12','APPNAME','MYADDRESSES',
        'MYADDRESSSEODESC','MYADDRESSSEOKEYWORD','MYADDRESSES5','HOME','addressType1','addressType2',
        'FULLNAMEREQ','ADDRESS1REQ','MOBILEREQ','MOBILENUM','MOBILEMUSTBE8DIGIT','COUNTRYREQ','STATEREQ',
        'CITYREQ','PINCODEREQ','ADDRESSTYPEREQ','ADDRESS2REQ'];
        $myAddressLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $myAddressLabels["MYADDRESSES"];
        $projectName = $myAddressLabels["APPNAME"];        

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $baseUrl = $this->getBaseUrl();
        $countries = DB::table('countries')->where('id', 17)->get();
        $customer_id = Session::get('customer_id');
        $cust_addresses = \App\Models\CustomerAddress::where('customer_id', $customer_id)->where('is_deleted', 0)->get();
        $cust_default_address = \App\Models\CustomerAddress::where('customer_id', $customer_id)->where('is_default', 1)->where('is_deleted', 0)->first();
        return view('frontend.my-address', compact('baseUrl','countries','cust_addresses','pageName',
        'projectName','megamenuFileName','myAddressLabels','mobileMegamenuFileName','cust_default_address'));
    }

    public function saveMyaddress(Request $request)
    {           
        //Localization
        $codes = ['FULLNAMEREQ', 'ADDRESS1REQ', 'ADDRESS2REQ','MOBILEREQ','MOBILENUM','COUNTRYREQ'
        ,'PINCODEREQ', 'ADDRESSADDEDSUCC', 'ADDRESSUPDATEDSUCC', 'ADDRESSNOTADDED','STATEREQ','CITYREQ'
        ,'MOBILEMUSTBE8DIGIT'];
        $addressLabels = getCodesMsg(Session::get('language_id'), $codes);

        $lang_id = Session::get('language_id');
        $msg = [                        
            'full_name.required' => $addressLabels["FULLNAMEREQ"],            
            'address_1.required' => $addressLabels["ADDRESS1REQ"],
            'address_2.required' => $addressLabels["ADDRESS2REQ"],                      
            'mobile.required' => $addressLabels["MOBILEREQ"],
            'mobile.regex' => $addressLabels["MOBILENUM"],
            'mobile.min' => $addressLabels["MOBILEMUSTBE8DIGIT"],  
            'country.required' => $addressLabels["COUNTRYREQ"],  
            'state.required' => $addressLabels["STATEREQ"],  
            'city.required' => $addressLabels["CITYREQ"],  
            'pincode.required' => $addressLabels["PINCODEREQ"],  
        ]; 

        $validator = Validator::make($request->all(), [   
            'full_name' => 'required',         
            'address_1' => 'required',
            'address_2' => 'required',               
            'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',              
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',  
            'pincode' => 'required'          
        ],$msg);
            
        if($validator->fails()) {            
            return redirect('/customer/my-address')
            ->withErrors($validator)
            ->withInput();
        }

        if(isset($request->address_id))
        {            
            $customer_address = \App\Models\CustomerAddress::updateCustomerAddress($request);
            $msg = $addressLabels["ADDRESSUPDATEDSUCC"];
        }
        else
        {            
            $customer_address = \App\Models\CustomerAddress::saveCustomerAddress($request);              
            $msg = $addressLabels["ADDRESSADDEDSUCC"];
        }
        
        if($customer_address['status'] == 'true')
        {            
            return redirect()->back()->with('msg', $msg)->with('alert_type', true);
        }        
        else
        {
            return redirect()->back()->with('msg', $addressLabels["ADDRESSNOTADDED"])->with('alert_type', true);
        }
    }

    public function changeDefaultAddress(Request $request)
    {
        $lang_id = Session::get('language_id');
        $customer_id = Session::get('customer_id');
        $customer_address = \App\Models\CustomerAddress::where('id', $request->address_id)->first();
        if($customer_address)
        {
            if($request->is_default == 1)
            {
                $is_default = \App\Models\CustomerAddress::where('id', $request->address_id)->where('is_default', 1)->first();            
                if(!$is_default)
                {
                    \App\Models\CustomerAddress::where('id', '<>',$request->address_id)->where('customer_id', $customer_id)->update(['is_default' => 0]);            
                    $customer_address->is_default = 1;
                }                        
            }
            else
            {
                $customer_address->is_default = 0;
            }  
            $customer_address->save();
            return 'true';
        }
        else
        {
            return 'false';
        }
    }

    public function deleteAddress(Request $request)
    {
        $customer_address = \App\Models\CustomerAddress::where('id', $request->address_id)->first();
        if($customer_address)
        {
            $customer_address->is_deleted = 1;
            $customer_address->save();
            return 'true';
        }    
        else
        {
            return 'false';
        }
    }

    public function getAjaxAddress(Request $request)
    {
        $customer_address = \App\Models\CustomerAddress::where('id', $request->address_id)->first();
        $countries = DB::table('countries')->where('id', $customer_address->country)->first();
        $states = DB::table('states')->where('id', $customer_address->state)->first();        
        $cities = DB::table('cities')->where('id', $customer_address->city)->first();                
        $result['customer_address'] = $customer_address;
        $result['country'] = $countries; 
        $result['states'] = $states; 
        $result['city'] = $cities; 
        return $result;
    }

    /* ###########################################
    // Function: getStates
    // Description: Get states/territory data from database
    // Parameter: country_id: Int
    // ReturnType: object
    */ ###########################################
    public function getStates($id, Request $request)
    {
        if($request->ajax())
        {
            $states = DB::table('states')->where('country_id',$id)->get();
            return $states;
        }        
    }

    /* ###########################################
    // Function: getCities
    // Description: Get city from state id from database
    // Parameter: state_id: Int
    // ReturnType: object
    */ ###########################################
    public function getCities($id, Request $request)
    {
        if($request->ajax())
        {
            $cities = DB::table('cities')->where('state_id',$id)->get();
            return $cities;
        }        
    }
}
