<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Agent;
use Auth;

class ApiController extends Controller
{
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric',
            'password' => 'required|min:6|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|min:6',
        ]);
            
        if ($validator->fails()) {
            $result['statusCode'] = 300;
            $result['message'] = $validator->errors();
            return response()->json($result);
        }
        
        $customer = \App\Models\Customer::where('email', $request->email)->first();
        if($customer)
        {
            $result['statusCode'] = '300';
            $result['message'] = 'Email is already registred.';            
            return response()->json($result);              
        }

        $customer_unique_id = \App\Models\Customer::getCustomerUniqueId();
        $customer = new \App\Models\Customer;
        $customer->customer_unique_id = $customer_unique_id;
        $customer->first_name =  $request->firstname;
        $customer->last_name = $request->lastname;                        
        $customer->mobile = $request->mobile;            
        $customer->email = $request->email; 
        //$customer->ip_address = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));  
        $customer->ip_address = "";
        $customer->os_name = Agent::platform();                    
        $customer->browser_name = Agent::browser();                    
        $customer->browser_version = Agent::version($customer->browser_name);
        $customer->password = Hash::make($request->password);            
        $customer->created_at = date("Y-m-d H:i:s");
        if($customer->save())
        {
            $result['statusCode'] = '200';
            $result['message'] = 'Registred successfully.';
            $result['data'] = [
                "customerId" => $customer->id,
                "email" => $customer->email, 
                "firstname" => $customer->first_name, 
                "lastname" => $customer->last_name, 
            ];
            return response()->json($result);
        }
        else
        {
            $result['statusCode'] = '500';
            $result['message'] = 'Server error.';            
            return response()->json($result);
        }                
    }

    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [            
            'email' => 'required|email',            
            'password' => 'required',            
        ]);
            
        if ($validator->fails()) {
            $result['statusCode'] = 300;
            $result['message'] = $validator->errors();
            return response()->json($result);
        }

        $customer = \App\Models\Customer::where('email', $request['email'])->first();
        if ($customer->is_deleted == 1) {
            $result['statusCode'] = '200';
            $result['message'] = config('message.AuthMessages.AccountDelete');            
            return response()->json($result);            
        }elseif ($customer->is_verify == 0) {
            $result['statusCode'] = '200';
            $result['message'] = config('message.AuthMessages.NotVerified');            
            return response()->json($result);            
        }elseif ($customer->is_active == 0) {
            $result['statusCode'] = '200';
            $result['message'] = config('message.AuthMessages.NotActive');            
            return response()->json($result);
        }

        if(Auth::guard('customer')->attempt(array('email' => $request['email'], 'password' => $request['password']), true))
        {
            $customer = Auth::guard('customer')->user();
            $result['statusCode'] = '200';
            $result['message'] = 'Login Successfully';   
            $result['data'] = [
                "customerId" => $customer->id,
                "email" => $customer->email, 
                "firstname" => $customer->first_name, 
                "lastname" => $customer->last_name, 
            ];         
            return response()->json($result);             
        }
        else
        {                   
            $result['statusCode'] = '200';
            $result['message'] = config('message.AuthMessages.InvalidCredentials');            
            return response()->json($result);                                   
        }        
    }

    public function logOut(Request $request)
    {
        Auth::guard('customer')->logout();
        $result['statusCode'] = '200';
        $result['message'] = 'Logout successfully.';            
        return response()->json($result);
    }

    public function getPolicies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'statusCode' => 300,
            'message' => $validator->errors(),
            ], 300);
        }

        $lang_id = $request->language_id;
        
        //Localization
        $codes = ['SUCCESS'];
        $policiesLabels = getCodesMsg($lang_id, $codes);

        $policies = \App\Models\CmsPages::leftJoin('cms_details', 
        'cms_details.cms_id', '=', 'cms_pages.id')
        ->whereIn('cms_pages.slug', ['privacy-policy', 'refund-policy', 'shipping-policy','terms-of-use'])
        ->where('cms_details.language_id', $lang_id)
        ->where('cms_pages.status', 1)
        ->whereNull('cms_pages.deleted_at')
        ->whereNull('cms_details.deleted_at')
        ->get();

        $baseUrl = $this->getBaseUrl();
        $policy_list = [];
        $i = 0;
        $terms_of_use_arr = [];
        foreach($policies as $policy)
        {
            if($policy->slug == "terms-of-use")
            {
                $terms_of_use_arr['name'] = $policy->title;
                $terms_of_use_arr['url'] = $baseUrl."/page/".$policy->slug."?language_id=".$lang_id;    
                continue;
            }
            $policy_list[$i]['name'] = $policy->title;
            $policy_list[$i]['url'] = $baseUrl."/page/".$policy->slug."?language_id=".$lang_id;
            $i++;
        }
        if(!empty($terms_of_use_arr)){
            array_push($policy_list, $terms_of_use_arr);
        }            
        $result['status'] = $policiesLabels['SUCCESS'];
        $result['statusCode'] = '200';
        $result['message'] = $policiesLabels["SUCCESS"];
        $result['list'] = $policy_list;        
        return response()->json($result);
    }
}
