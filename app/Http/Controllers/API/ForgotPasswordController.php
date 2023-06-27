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

class ForgotPasswordController extends Controller
{
    use ReuseFunctionTrait;

    public function forgotPassword(Request $request)
    {
        try {
            //Localization
            $codes = ['EMAILREQ','NOTVALIDEMAIL','CUSTOMERNOTFOUND','EMAILSENTSUCC'];
            $forgotPasswordLabels = getCodesMsg($request->language_id, $codes);

            $msg = [                        
                'email.required' => $forgotPasswordLabels["EMAILREQ"],            
                'email.email' => $forgotPasswordLabels["NOTVALIDEMAIL"],            
            ];  

            $validator = Validator::make($request->all(), [   
                'language_id' => 'required|numeric',                    
                'email' => 'required|email',                              
            ],$msg);
                
            if ($validator->fails()) {
                return response()->json([
                'statusCode' => '300',
                'message' => $validator->errors(),
                ],300);
            }

            $lang_id = $request->language_id;
            $customer = \App\Models\Customer::where('email', $request->email)
            ->where('is_deleted', 0)->first();                    
            if($customer)
            {
                $forgot_password = \App\Models\CustomerPasswordReset::savePasswordResetData($request);

                // Send email start              
                $temp_arr = []; 
                $new_user = $this->getEmailTemp($lang_id);
                foreach($new_user as $code )
                {            
                    if($code->code == 'FRGPS')
                    {
                        array_push($temp_arr, $code);
                    }
                }         

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }
                
                $link = url('/reset-password').'/'.$forgot_password->token;

                $replace_data = array(
                    '{{name}}' => $customer->first_name,
                    '{{link}}' => $link,
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);            
                $data = [                
                    'html' => $html_value,                
                ]; 
                $subject = $temp_arr[0]['subject'];
                $email = $customer->email;
                Mail::send('frontend.emails.forgot-password-email', $data, function ($message) use ($email,$subject) {                
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over                        
                $result['statusCode'] = '200';        
                $result['message'] = $forgotPasswordLabels['EMAILSENTSUCC'];            
                return response()->json($result);
            }
            else
            {            
                $result['statusCode'] = '300';           
                $result['message'] = $forgotPasswordLabels['CUSTOMERNOTFOUND'];            
                return response()->json($result, 300);
            }
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }        
    }    
}
