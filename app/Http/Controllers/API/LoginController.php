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
// use Laravel\Passport\TokenRepository;
// use Laravel\Passport\RefreshTokenRepository;
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;
use App\Traits\ReuseFunctionTrait;

class LoginController extends Controller
{
    use ReuseFunctionTrait;

    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
              'statusCode' => '300',
              'message' => $validator->errors(),
            ],300);
        }

        //Localization
        $codes = ['EMAILREQ','NOTVALIDEMAIL','PASSWORDREQ','CUSTOMERNOTFOUND','LOGINACCDELETE'
        ,'LOGINACCNOTVRFYD','LOGINACCNOTACTIVATE','LOGINSUCMSG','INVALIDCRED','REGISTERSUCMSG'];
        $loginLabels = getCodesMsg($request->language_id, $codes);
        $lang_id=$request->language_id;
        $cart_master_id=$request->cart_master_id;
        // Login with Social media
        if($request->provider != '')
        {
            $provider=   $request->provider;
            $provider_id=   $request->provider_id;
            $customer = \App\Models\Customer::where('provider', $provider)->where('provider_id', $provider_id)->first();
            if(empty($customer))
            {
              $save_customer = \App\Models\Customer::saveCustomerSignInObj($request, $provider);
              //Store Customer Timezone
              updateCustomerTimezone($request->ip_address, $save_customer->id);
              if($save_customer)
              {
                  $customer = \App\Models\Customer::where('id', $save_customer->id)->first();

                  $email = $request->email;
                  $cart_master_id=setCartMasterId($customer->id,$cart_master_id);
                  $result['statusCode'] = '200';
                  $result['isVerify'] = '1';
                  $result['message'] = $loginLabels["LOGINSUCMSG"];
                  $result['data'] = [
                      "customerId" => $customer->id,
                      "email" => $customer->email,
                      "firstname" => $customer->first_name,
                      "lastname" => $customer->last_name,
                      "provider" => $request->provider,
                      "loyalityNo" => "12",
                      "token_type" => 'Bearer',
                      "cart_master_id"=>"".$cart_master_id."",
                      "access_token" => $customer->createToken('Alboumi')->accessToken,
                  ];
                  return response()->json($result);
              }
            }
            else{

                if ($customer->is_deleted == 1) {
                    $result['statusCode'] = '300';
                    $result['message'] = $loginLabels["LOGINACCDELETE"];
                    return response()->json($result, 300);
                }elseif ($customer->is_verify == 0) {
                    $result['statusCode'] = '300';
                    $result['message'] = $loginLabels["LOGINACCNOTVRFYD"];
                    return response()->json($result, 300);
                }elseif ($customer->is_active == 0) {
                    $result['statusCode'] = '300';
                    $result['message'] = $loginLabels["LOGINACCNOTACTIVATE"];
                    return response()->json($result, 300);
                }

                if($customer->first_time_login == 0)
                {
                    $customer->first_time_login = 1;
                    $customer->save();

                    // Send email start
                    $temp_arr = [];

                    $lang_id = $request->language_id;
                    $new_user = $this->getEmailTemp($lang_id);
                    foreach($new_user as $code )
                    {
                        if($code->code == 'WELCM')
                        {
                            array_push($temp_arr, $code);
                        }
                    }

                    if(is_array($temp_arr))
                    {
                        $value = $temp_arr[0]['value'];
                    }

                    $replace_data = array(
                        '{{name}}' => $customer->first_name,
                    );
                    $html_value = $this->replaceHtmlContent($replace_data,$value);
                    $data = [
                        'html' => $html_value,
                    ];
                    $subject = $temp_arr[0]['subject'];
                    $email = $customer->email;
                    Mail::send('frontend.emails.customer-welcome-email', $data, function ($message) use ($email,$subject) {
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                        $message->to($email)->subject($subject);
                    });
                    // Send email over
                }
                //Store Customer Timezone
                updateCustomerTimezone($request->ip_address, $customer->id);
                
                $cart_master_id=setCartMasterId($customer->id,$cart_master_id);
                $result['statusCode'] = '200';
                $result['isVerify'] = '1';
                $result['message'] = $loginLabels["LOGINSUCMSG"];
                $result['data'] = [
                    "customerId" => (String) $customer->id,
                    "email" => $customer->email,
                    "firstname" => $customer->first_name,
                    "lastname" => $customer->last_name,
                    "provider" => $request->provider,
                    "loyalityNo" => "12",
                    "token_type" => 'Bearer',
                    "cart_master_id"=>"".$cart_master_id."",
                    "access_token" => $customer->createToken('Alboumi')->accessToken,
                ];
                return response()->json($result);
            }

        }
        else
        {
            $msg = [
                'email.required' => $loginLabels["EMAILREQ"],
                'email.email' => $loginLabels["NOTVALIDEMAIL"],
                'password.required' => $loginLabels["PASSWORDREQ"],
            ];

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                  'statusCode' => '300',
                  'message' => $validator->errors(),
                ], 300);
            }
                                              
            $customer = \App\Models\Customer::where('email', $request['email'])->first();
            if(empty($customer))
            {
                $result['statusCode'] = '300';
                $result['message'] = $loginLabels["CUSTOMERNOTFOUND"];
                return response()->json($result, 300);
            }
            
            //Store Customer Timezone
            updateCustomerTimezone($request->ip_address, $customer->id);          

            if ($customer->is_deleted == 1) {
                $result['statusCode'] = '300';
                $result['message'] = $loginLabels["LOGINACCDELETE"];
                return response()->json($result, 300);
            }elseif ($customer->is_verify == 0) {
                $result['statusCode'] = '300';
                $result['message'] = $loginLabels["LOGINACCNOTVRFYD"];
                return response()->json($result, 300);
            }elseif ($customer->is_active == 0) {
                $result['statusCode'] = '300';
                $result['message'] = $loginLabels["LOGINACCNOTACTIVATE"];
                return response()->json($result, 300);
            }

            if($customer->first_time_login == 0)
            {
                $customer->first_time_login = 1;
                $customer->save();

                // Send email start
                $temp_arr = [];

                $lang_id = $request->language_id;
                $new_user = $this->getEmailTemp($lang_id);
                foreach($new_user as $code )
                {
                    if($code->code == 'WELCM')
                    {
                        array_push($temp_arr, $code);
                    }
                }

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }

                $replace_data = array(
                    '{{name}}' => $customer->first_name,
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);
                $data = [
                    'html' => $html_value,
                ];
                $subject = $temp_arr[0]['subject'];
                $email = $customer->email;
                Mail::send('frontend.emails.customer-welcome-email', $data, function ($message) use ($email,$subject) {
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over
            }

            // if(Auth::guard('customer')->attempt(array('email' => $request['email'], 'password' => $request['password'])))
            // {
            if(Hash::check($request->password , $customer->password) )
            {
                // $customer = Auth::guard('customer')->user();
                $cart_master_id=setCartMasterId($customer->id,$cart_master_id);
                $result['statusCode'] = '200';
                $result['isVerify'] = '1';
                $result['message'] = $loginLabels["LOGINSUCMSG"];
                $result['data'] = [
                    "customerId" => (String) $customer->id,
                    "email" => $customer->email,
                    "firstname" => $customer->first_name,
                    "lastname" => $customer->last_name,
                    "provider" => "",
                    "loyalityNo" => "12",
                    "token_type" => 'Bearer',
                    "cart_master_id"=>"".$cart_master_id."",
                    "access_token" => $customer->createToken('Alboumi')->accessToken,
                ];
                return response()->json($result);
            }
            else
            {
                $result['statusCode'] = '300';
                $result['message'] = $loginLabels["INVALIDCRED"];
                return response()->json($result, 300);
            }
        }
    }

    // public function logOut(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'language_id' => 'required|numeric',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //           'statusCode' => 300,
    //           'message' => $validator->errors(),
    //         ]);
    //     }

    //     $lang_id = $request->language_id;
    //     if(Auth::guard('customer')->check())
    //     {
    //         // $tokenRepository = app(TokenRepository::class);
    //         // $refreshTokenRepository = app(RefreshTokenRepository::class);
    //         // $api_token_id = session('api_token');
    //         // $tokenRepository->revokeAccessToken($api_token_id);

    //         Auth::guard('customer')->logout();
    //         $result['statusCode'] = '200';
    //         $code = "LOGOUT";
    //         $msg = getCodesMsg($request->language_id, $code);
    //         $result['message'] = $msg;
    //         return response()->json($result);
    //     }
    //     else
    //     {
    //         return authResponse($lang_id);
    //     }
    // }

    public function logOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
              'statusCode' => '300',
              'message' => $validator->errors(),
            ],300);
        }

        //Localization
        $codes = ['LOGOUT'];
        $logoutLabels = getCodesMsg($request->language_id, $codes);

        if(auth('api')->user()->token()->revoke())
        {
            $result['statusCode'] = '200';
            $result['message'] = $logoutLabels['LOGOUT'];
            return response()->json($result);
        }else{
            return authResponse($lang_id);
        }
    }    
}
