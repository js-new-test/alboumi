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
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;
use App\Traits\ReuseFunctionTrait;

class RegisterController extends Controller
{       
    use ReuseFunctionTrait;

    public function signUp(Request $request)
    {   
        try {
            //Localization
            $codes = ['FIRSTNAMEREQ', 'LASTNAMEREQ', 'EMAILREQ','NOTVALIDEMAIL','MOBILEREQ',
            'MOBILENUM','PASSWORDREQ','PASSCONFREQ','PASSWORDCONFMSG','CUSTOMEREMAILALRDYEXIST',
            'REGISTERSUCMSG','MOBILEMUSTBE8DIGIT'];
            $signUpLabels = getCodesMsg($request->language_id, $codes);

            $msg = [            
                'firstName.required' => $signUpLabels["FIRSTNAMEREQ"],
                'lastName.required' => $signUpLabels["LASTNAMEREQ"],
                'email.required' => $signUpLabels["EMAILREQ"],            
                'email.email' => $signUpLabels["NOTVALIDEMAIL"],                
                'mobile.regex' => $signUpLabels["MOBILENUM"],
                'mobile.min' => $signUpLabels["MOBILEMUSTBE8DIGIT"],  
                'password.required' => $signUpLabels["PASSWORDREQ"],
                'confirm_password.required' => $signUpLabels["PASSCONFREQ"],
                'password.same' => $signUpLabels["PASSWORDCONFMSG"],            
            ];                

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'mobile' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
                'password' => 'required|min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required|min:6',
            ],$msg);
                
            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }
            
            $lang_id = $request->language_id;
            $customer = \App\Models\Customer::where('email', $request->email)
            ->where('is_deleted', 0)->first();
            if($customer)
            {
                $result['statusCode'] = '300'; 
                $result['message'] = $signUpLabels["CUSTOMEREMAILALRDYEXIST"];
                return response()->json($result,300);              
            }

            $save_customer = \App\Models\Customer::saveCustomerRegisterObj($request);
            //Store Customer Timezone
            updateCustomerTimezone($request->ip_address, $save_customer->id);
            if($save_customer)
            {
                $customer = \App\Models\Customer::where('id', $save_customer->id)->first();
                $email = $request->email;                

                // Send email start                                                       
                $temp_arr = [];                 
                $new_user = $this->getEmailTemp($lang_id);
                foreach($new_user as $code )
                {            
                    if($code->code == 'UVRFY')
                    {
                        array_push($temp_arr, $code);
                    }
                }         

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }
                
                $email_encoded = rtrim(strtr(base64_encode($request->email), '+/', '-_'), '=');        
                $replace_data = array(
                    '{{name}}' => $request->firstname,
                    '{{link}}' => url('/verification-success').'/'.$email_encoded,
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);            
                $data = [                
                    'html' => $html_value,                
                ]; 
                $subject = $temp_arr[0]['subject'];;
                Mail::send('frontend.emails.welcome-and-email-verify', $data, function ($message) use ($email,$subject) {                
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });

                $result['statusCode'] = '200';                        
                $result['message'] = $signUpLabels["REGISTERSUCMSG"];
                $result['data'] = [
                    "customerId" => $customer->id,
                    "email" => $customer->email, 
                    "firstname" => $customer->first_name, 
                    "lastname" => $customer->last_name, 
                ];
                return response()->json($result);
            }            
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }                                     
    }
}
