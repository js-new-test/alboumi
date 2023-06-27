<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Auth;
use DB;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use Exception;

class BillingAddressController extends Controller
{
    public function getCustomerBillingAddressList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [            
                'language_id' => 'required|numeric',                                    
            ]);
                
            if ($validator->fails()) {
                return response()->json([
                  'statusCode' => '300',
                  'message' => $validator->errors(),
                ], 300);
            }
            
            //Localization
            $codes = ['CUSTOMERIDREQ','ADDRESSNOTFOUND','OK','SUCCESS','addressType1','addressType2'
            ,'CUSTOMERNOTFOUND'];
            $myAddressLabels = getCodesMsg($request->language_id, $codes);

            $lang_id = $request->language_id;            
            $msg = [            
                'customerId.required' => $myAddressLabels["CUSTOMERIDREQ"],                       
            ];                
    
            $validator = Validator::make($request->all(), [                
                'customerId' => 'required',                
            ],$msg);
                
            if ($validator->fails()) {
                return response()->json([
                    'statusCode' => '300',
                    'message' => $validator->errors(),
                ], 300);
            }

            // $customer = Auth::guard('customer')->user();
            // $customer_id = Auth::guard('api')->user()->token()->user_id;
            $customer = \App\Models\Customer::where('id', $request->customerId)->where('is_deleted', 1)->first();            
            if($customer)
            {
                $result['statusCode'] = '300';           
                $result['message'] = $myAddressLabels['CUSTOMERNOTFOUND'];            
                return response()->json($result, 300);
            }

            $customer_addresses = \App\Models\BillingAddress::where('customer_id', $request->customerId)
            ->where('is_deleted', 0)->get();                        
            if(count($customer_addresses) == 0)
            {                
                $result['statusCode'] = '300';                                      
                $result['message'] = $myAddressLabels["ADDRESSNOTFOUND"];                                 
                return response()->json($result, 300);
            }
            
            $i = 0;
            $customer_addresses_arr = [];            
            foreach ($customer_addresses as $customer_address) {
                $customer_addresses_arr[$i]['addressId'] = (String) $customer_address->id;
                $customer_addresses_arr[$i]['fullName'] = $customer_address->fullname;
                $customer_addresses_arr[$i]['addressLine1'] = $customer_address->address_1;
                $customer_addresses_arr[$i]['addressLine2'] = $customer_address->address_2;
                $customer_addresses_arr[$i]['state'] = $customer_address->state;
                $customer_addresses_arr[$i]['city'] = $customer_address->city;
                $countries = DB::table('countries')->where('id', $customer_address->country)->first();
                $customer_addresses_arr[$i]['countryId'] = (String) $customer_address->country;
                $customer_addresses_arr[$i]['country'] = $countries->name;
                $customer_addresses_arr[$i]['postCode'] = $customer_address->pincode;
                $customer_addresses_arr[$i]['mobile'] = $customer_address->phone1;
                $customer_addresses_arr[$i]['addressType'] = (String) $customer_address->address_type;
                $customer_addresses_arr[$i]['addressTypeName'] = ($customer_address->address_type == 1) ? $myAddressLabels['addressType1'] : $myAddressLabels['addressType2'];
                $customer_addresses_arr[$i]['isSelected'] = (String) $customer_address->is_default;
                $i++;
            }
            
            $result['status'] = $myAddressLabels["OK"];
            $result['statusCode'] = '200';                                      
            $result['message'] = $myAddressLabels["SUCCESS"]; 
            $result['language_id'] = $lang_id;
            $result['addressList'] = $customer_addresses_arr;
            return response()->json($result);            
        } catch (\Exception $th) {
            return handleServerError($lang_id);
        }
                
    } 

    public function addBillingAddress(Request $request)
    {
        //Localization
        $codes = ['FULLNAMEREQ', 'ADDRESS1REQ', 'MOBILEREQ','MOBILENUM','COUNTRYREQ'
        ,'PINCODEREQ','ADDRESSADDEDSUCC','ADDRESSNOTADDED','CUSTOMERIDREQ','ADDRESS2REQ',
        'STATEREQ','CITYREQ','MOBILEMUSTBE8DIGIT','ADDRESSTYPEREQ','ADDRESSTYPEINVLID'];
        $addressLabels = getCodesMsg($request->language_id, $codes);
        
        $msg = [   
            'customerId.required' => $addressLabels["CUSTOMERIDREQ"],                      
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
            'address_type.required' => $addressLabels["ADDRESSTYPEREQ"],  
            'address_type.in' => $addressLabels["ADDRESSTYPEINVLID"],
        ]; 

        $validator = Validator::make($request->all(), [   
            'language_id' => "required|numeric",            
            'customerId' => 'required|numeric',
            'full_name' => 'required',         
            'address_1' => 'required',     
            'address_2' => 'required',             
            'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',              
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',              
            'pincode' => 'required',
            'address_type' => 'required|in:1,2'         
        ],$msg);
            
        if($validator->fails()) {            
            return response()->json([
                'statusCode' => '300',
                'message' => $validator->errors(),
              ], 300);
        }          
        
        $lang_id = $request->language_id;
        $customer_address = \App\Models\BillingAddress::saveCustomerAddress($request);                       
        if($customer_address['status'] == 'true')
        {            
            $result['statusCode'] = '200';                                                              
            $result['message'] = $addressLabels["ADDRESSADDEDSUCC"];
            return response()->json($result); 
        }
        else
        {
            $result['statusCode'] = '300';                                                              
            $result['message'] = $addressLabels["ADDRESSNOTADDED"];
            return response()->json($result, 300); 
        }        
    }

    public function updateBillingAddress(Request $request)
    {
        //Localization
        $codes = ['FULLNAMEREQ', 'ADDRESS1REQ', 'MOBILEREQ','MOBILENUM','COUNTRYREQ'
        ,'PINCODEREQ','ADDRESSUPDATEDSUCC','ADDRESSNOTFOUND','CUSTOMERIDREQ','ADDRESSIDREQ'
        ,'STATEREQ','CITYREQ','ADDRESS2REQ','MOBILEMUSTBE8DIGIT', 'ADDRESSTYPEREQ','ADDRESSTYPEINVLID'];
        $addressLabels = getCodesMsg($request->language_id, $codes);
        
        $msg = [   
            'customerId.required' => $addressLabels["CUSTOMERIDREQ"],
            'address_id.required' => $addressLabels["ADDRESSIDREQ"],                       
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
            'address_type.required' => $addressLabels["ADDRESSTYPEREQ"],  
            'address_type.in' => $addressLabels["ADDRESSTYPEINVLID"],
        ]; 

        $validator = Validator::make($request->all(), [   
            'language_id' => "required|numeric",
            'address_id' => "required|numeric",            
            'customerId' => 'required|numeric',
            'full_name' => 'required',         
            'address_1' => 'required',
            'address_2' => 'required',                  
            'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',              
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',                
            'pincode' => 'required',
            'address_type' => 'required|in:1,2'        
        ],$msg);
            
        if($validator->fails()) {            
            return response()->json([
                'statusCode' => '300',
                'message' => $validator->errors(),
              ], 300);
        }          
        
        $lang_id = $request->language_id;
        $customer_address = \App\Models\BillingAddress::updateCustomerAddress($request);                      
        if($customer_address['status'] == 'true')
        {            
            $result['statusCode'] = '200';                                                              
            $result['message'] = $addressLabels["ADDRESSUPDATEDSUCC"];
            return response()->json($result); 
        }
        else
        {
            $result['statusCode'] = '300';                                                              
            $result['message'] = $addressLabels["ADDRESSNOTFOUND"];
            return response()->json($result, 300); 
        }        
    }

    public function deleteBillingAddress(Request $request)
    {
        //Localization
        $codes = ['ADDRESSIDREQ','ADDRESSDELETEDSUCC', 'ADDRESSNOTFOUND'];
        $addressLabels = getCodesMsg($request->language_id, $codes);
        
        $msg = [               
            'address_id.required' => $addressLabels["ADDRESSIDREQ"],                                   
        ]; 

        $validator = Validator::make($request->all(), [   
            'language_id' => "required|numeric",
            'address_id' => "required|numeric",                                  
        ],$msg);
            
        if($validator->fails()) {            
            return response()->json([
                'statusCode' => '300',
                'message' => $validator->errors(),
              ], 300);
        }

        $customer_id = Auth::guard('api')->user()->token()->user_id;
        $cust_address = \App\Models\BillingAddress::where('id', $request->address_id)
        ->where('customer_id', $customer_id)->where('is_deleted', 0)->first();
        if($cust_address)
        {
            $cust_address->is_deleted = 1;
            $cust_address->save();
            $result['statusCode'] = '200';                                                              
            $result['message'] = $addressLabels["ADDRESSDELETEDSUCC"];
            return response()->json($result);
        }
        else
        {
            $result['statusCode'] = '300';                                                              
            $result['message'] = $addressLabels["ADDRESSNOTFOUND"];
            return response()->json($result, 300);
        }

    }
}
