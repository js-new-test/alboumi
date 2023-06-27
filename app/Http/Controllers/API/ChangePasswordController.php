<?php

namespace App\Http\Controllers\API;

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

class ChangePasswordController extends Controller
{
    use ReuseFunctionTrait;

    public function changePassword(Request $request)
    {     
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',                        
            ]);
                
            if ($validator->fails()) {
                return response()->json([
                  'statusCode' => 300,
                  'message' => $validator->errors(),
                ], 300);
            }

            //Localization
            $codes = ['CURRENTPASSWORD', 'PASSWORDREQ', 'PASSWORDMINLENGTH', 'PASSCONFREQ', 'PASSWORDCONFMSG'
            ,'CHANGEPASSSUCC','PASSWORDNOTMATCH'];
            $changePasswordLabels = getCodesMsg($request->language_id, $codes);
    
            $lang_id = $request->language_id;            
            // $customer_id = Auth::guard('customer')->user()->id;
            $customer_id = Auth::guard('api')->user()->token()->user_id;
            $customer = \App\Models\Customer::findOrFail($customer_id);
            $lang_id = $request->language_id;
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
                
            if ($validator->fails()) {
                return response()->json([
                    'statusCode' => 300,
                    'message' => $validator->errors(),
                ], 300);
            }

            if(Hash::check($request->currentpassword, $customer->password)) { 
                $customer->fill(['password' => Hash::make($request->password)])->save();                             
                $result['statusCode'] = '200';
                $result['message'] = $changePasswordLabels['CHANGEPASSSUCC'];         
                return response()->json($result);                             
            } else {                 
                $result['statusCode'] = '300';  
                $result['message'] = $changePasswordLabels['PASSWORDNOTMATCH'];    
                return response()->json($result, 300);
            }            
        } catch (\Exception $th) {
            return handleServerError($lang_id);
        }                       
    }
}
