<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class ShippindAddressController extends Controller
{
    public function getShippingAddress()
    {
        if(Auth::guard('customer')->check())
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
            'COUNTRYREQ','PINCODEREQ','CHECKOUT','CONTINUE','PICKFROMHERE','MYADDRESSES3','MYADDRESSES10'
            ,'STATEREQ','CITYREQ','ADD_ADDRESS','SELECTADDRESSERR','SELECTDELADDR','SELECTSTOREADDR',
            'MOBILEMUSTBE8DIGIT','FULLNAMEREQ','ADDRESS1REQ','MOBILEREQ','MOBILENUM','MOBILEMUSTBE8DIGIT','COUNTRYREQ','STATEREQ',
            'CITYREQ','PINCODEREQ','ADDRESSTYPEREQ','ADDRESS2REQ'];
            $shippingAddressLabels = getCodesMsg($lang_id, $codes);
            
            $baseUrl = $this->getBaseUrl();
            $pageName = $shippingAddressLabels["SHIPPINGADDRESS"];
            $projectName = $shippingAddressLabels["APPNAME"];        

            $megamenuFileName = "megamenu_".Session::get('language_code');
            $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

            $customer = Auth::guard('customer')->user()->id;
            $c_first_name = Auth::guard('customer')->user()->first_name;
            $c_last_name = Auth::guard('customer')->user()->last_name;
            $c_mobile = Auth::guard('customer')->user()->mobile;
            $customer_id = $customer;

            $addresses = \App\Models\CustomerAddress::where('customer_id', $customer)
            ->where('is_deleted', 0)->get()->toArray();
            $addresses_arr = array_chunk($addresses, 2);            
            $countries = DB::table('countries')->where('id', 17)->get();  
            $store_locations = \App\Models\StoreLocation::where('language_id', $lang_id)->where('is_deleted', 0)->get()->toArray();          
            $store_locations_arr = array_chunk($store_locations, 2);
            $cust_default_address = \App\Models\CustomerAddress::where('customer_id', $customer)->where('is_default', 1)->where('is_deleted', 0)->first();
            return view('frontend.shipping-address',compact('baseUrl','pageName','projectName','megamenuFileName',
            'mobileMegamenuFileName','shippingAddressLabels','addresses','countries','addresses_arr',
            'store_locations_arr','cart_master_id','cust_default_address','c_first_name', 'c_last_name', 'c_mobile','customer_id'));
        }   
        else
        {
            return redirect('/login?flagCheckout=1');
        }     
    }   

    public function saveCustomerShipAddress(Request $request)
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
            // 'states.required' => getCodesMsg($lang_id, $code = "STATEREQ"),  
            // 'cities.required' => getCodesMsg($lang_id, $code = "CITYREQ"),  
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
            return redirect('/customer/shipping-address')
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
            $notification = array(
                'message' => "address addedd successfulyy",
                'alert-type' => 'success'
            );
            return redirect('/customer/payment-method')->with($notification);
            // return redirect()->back()->with('msg', $msg)->with('alert_type', true);
        }        
        else
        {
            return redirect()->back()->with('msg', $addressLabels["ADDRESSNOTADDED"])->with('alert_type', true);
        }
    }

    public function saveDeliveryType(Request $request)
    {
        //Localization
        $codes = ['ADDRESSSELECTSUCC','SOMETHINGWRONG'];
        $deliveryTypeLabels = getCodesMsg(Session::get('language_id'), $codes);

        $user_id = Auth::guard('customer')->user()->id;
        $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
        if($cart_master)
        {
            $cart_master->checkout_type = $request->checkout_type;
            $cart_master->address_id = $request->address_id;
            $cart_master->store_location_id = $request->s_location_id;
            $cart_master->save();
            $result['status'] = 'true';
            $result['msg'] = $deliveryTypeLabels['ADDRESSSELECTSUCC'];
            return $result;
        } 
        else
        {
            $result['status'] = 'false';
            $result['msg'] = $deliveryTypeLabels['SOMETHINGWRONG'];
            return $result;
        }
    }

    public function editShippingAddress(Request $request)
    {
        $customer_address = \App\Models\CustomerAddress::where('id',$request->address_id)
        ->where('is_deleted', 0)->first();
        if($customer_address)
        {
            $countries = \App\Models\Country::whereNull('deleted_at')->get();
            $result['status'] = 'true';
            $result['customer_address'] = $customer_address;
            $result['country'] = $countries;
            return $result;
        }        
    }

    public function updateShippingAddress(Request $request)
    {
        if($request->method('post') && $request->ajax())
        {        
            $lang_id = Session::get('language_id');        
            
            //Localization
            $codes = ['ADDRESSUPDATEDSUCC','ADDRESSADDEDSUCC','SOMETHINGWRONG'];
            $shippingAddressLabels = getCodesMsg($lang_id, $codes);

            if(isset($request->address_id))
            {            
                $customer_address = \App\Models\CustomerAddress::updateCustomerAddress($request);
                $msg = $shippingAddressLabels["ADDRESSUPDATEDSUCC"];
            }
            else
            {            
                $customer_address = \App\Models\CustomerAddress::saveCustomerAddress($request);              
                $msg = $shippingAddressLabels["ADDRESSADDEDSUCC"];
            }
            
            if($customer_address['status'] == 'true')
            {                                   
                $address = \App\Models\CustomerAddress::where('customer_id', $request->customer_id)
                ->where('is_deleted', 0)->where('id', $request->address_id)->first();                                                            
                $country = \App\Models\Country::where('id', $address->country)->first();

                $result['status'] = 'true';
                $result['msg'] = $msg;
                $result['address'] = $address;
                $result['country'] = $country->name;
                return $result;
            }        
            else
            {
                $result['status'] = 'false';
                $result['msg'] = $shippingAddressLabels["SOMETHINGWRONG"];
                return $result;
            }
        }
    }

    public function addNewShippingAddress(Request $request)
    {        
        //Localization
        $codes = ['FULLNAMEREQ', 'ADDRESS1REQ', 'ADDRESS2REQ','MOBILEREQ','MOBILENUM','COUNTRYREQ'
        ,'PINCODEREQ', 'ADDRESSADDEDSUCC', 'ADDRESSUPDATEDSUCC', 'ADDRESSNOTADDED','STATEREQ','CITYREQ'];
        $addressLabels = getCodesMsg(Session::get('language_id'), $codes);

        $lang_id = Session::get('language_id');
        $msg = [                        
            'full_name.required' => $addressLabels["FULLNAMEREQ"],            
            'address_1.required' => $addressLabels["ADDRESS1REQ"],
            'address_2.required' => $addressLabels["ADDRESS2REQ"],                      
            'mobile.required' => $addressLabels["MOBILEREQ"],
            'mobile.numeric' => $addressLabels["MOBILENUM"],  
            'country.required' => $addressLabels["COUNTRYREQ"],  
            // 'states.required' => getCodesMsg($lang_id, $code = "STATEREQ"),  
            // 'cities.required' => getCodesMsg($lang_id, $code = "CITYREQ"),  
            'state.required' => $addressLabels["STATEREQ"],  
            'city.required' => $addressLabels["CITYREQ"],  
            'pincode.required' => $addressLabels["PINCODEREQ"],  
        ]; 

        $validator = Validator::make($request->all(), [   
            'full_name' => 'required',         
            'address_1' => 'required',
            'address_2' => 'required',               
            'mobile' => 'required|numeric',              
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',  
            'pincode' => 'required'          
        ],$msg);
            
        if($validator->fails()) {            
            return redirect('/customer/shipping-address')
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
            $result['status'] = 'true';
            return $result;            
        }        
        else
        {
            $result['status'] = 'false';
            return $result;
        }
    } 
}
