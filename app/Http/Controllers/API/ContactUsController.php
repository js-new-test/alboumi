<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Str;
use DB;
use Exception;
use Mail;


class ContactUsController extends Controller
{
    use ReuseFunctionTrait;

    public function contactUs(Request $request)
    {
        try {                    
            //Localization
            $codes = ['FULLNAMEREQ', 'EMAILREQ', 'NOTVALIDEMAIL', 'MESSAGEREQ','EMAILSENTSUCC'];
            $contactUsLabels = getCodesMsg($request->language_id, $codes);

            $msg = [                                
                'fullname.required' => $contactUsLabels['FULLNAMEREQ'],
                'email.required' => $contactUsLabels['EMAILREQ'],
                'email.email' => $contactUsLabels['NOTVALIDEMAIL'],                            
                'text_message.required' => $contactUsLabels['MESSAGEREQ'],
            ];
            
            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric', 
                'fullname' => 'required',
                'email' => 'required|email',
                'text_message' => 'required'
            ],$msg);
                    
            if($validator->fails()) {
                return response()->json([
                    'statusCode' => 300,
                    'message' => $validator->errors(),
                ],300);
            }        
            $lang_id = $request->language_id;
            $contact_us_inquiry = \App\Models\ContactUsInquiry::saveContactUsInquiry($request);
            if($contact_us_inquiry)
            {
                // Send email start                                               
                $temp_arr = [];                 
                $new_user = $this->getEmailTemp($lang_id);
                foreach($new_user as $code )
                {            
                    if($code->code == 'CONTUS')
                    {
                        array_push($temp_arr, $code);
                    }
                }         

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }
                            
                $replace_data = array(
                    '{{name}}' => $contact_us_inquiry->name,                    
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);            
                $data = [                
                    'html' => $html_value,                
                ]; 
                $subject = $temp_arr[0]['subject'];
                $email = $contact_us_inquiry->email;
                Mail::send('frontend.emails.customer-welcome-email', $data, function ($message) use ($email,$subject) {                
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over                

                $result['statusCode'] = '200';                
                $result['message'] = $contactUsLabels["EMAILSENTSUCC"];                
                return response()->json($result);
            }
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }        
    }
}
