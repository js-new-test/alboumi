<?php

namespace App\Http\Controllers\Frontend;

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

class ContactUsController extends Controller
{
    use ReuseFunctionTrait;

    public function showContactUs()
    {      
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        //Localization
        $codes = ['CONTACTUS', 'CONTACTUSPAGELABEL', 'CONTACTUSPAGELABEL1', 'CONTACTUSPAGELABEL2', 
        'CONTACTUSPAGELABEL3','MYADDRESSES9', 'FORGOTPASSLABEL2','APPNAME','FULLNAMEREQ','EMAILREQ'
        ,'MESSAGEREQ','NOTVALIDEMAIL'];
        $contactUsLabels = getCodesMsg(Session::get('language_id'), $codes);        
        $lang_id = Session::get('language_id');
        $pageName = "Contact Us";
        $projectName = $contactUsLabels["APPNAME"];
        $baseUrl = $this->getBaseUrl();
        // $cms_contact_us = \App\Models\CmsPages::select('cms_details.title', 'cms_details.description')
        // ->leftJoin('cms_details', 'cms_details.cms_id','=', 'cms_pages.id')
        // ->where('cms_pages.slug', 'contact-us')->first();
        
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');        

        $cmsPageDetails = \App\Models\CmsPages::select('title','description','cms_pages.banner_image as cms_banner'
        ,'cms_pages.mobile_banner_image as cms_mobile_banner','cd.banner_image','cd.mobile_banner','seo_title','seo_description','seo_keyword')
        ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
        ->where('slug','contact-us')
        ->whereNull('cd.deleted_at')
        ->whereNull('cms_pages.deleted_at')
        ->where('status',1)
        ->where('language_id', $lang_id)
        ->first();

        if($cmsPageDetails == null)
        {
            return redirect('/');
        }

        return view('frontend.contact-us', compact('pageName','projectName',
        'megamenuFileName', 'contactUsLabels','baseUrl','cmsPageDetails','mobileMegamenuFileName'));
    }

    public function showContactUsWithLangCode($lang_code)
    {
        //Localization
        $codes = ['CONTACTUS', 'CONTACTUSPAGELABEL', 'CONTACTUSPAGELABEL1', 'CONTACTUSPAGELABEL2', 
        'CONTACTUSPAGELABEL3','MYADDRESSES9', 'FORGOTPASSLABEL2'];
        $contactUsLabels = getCodesMsg(Session::get('language_id'), $codes); 

        $pageName = "Login";
        $projectName = $contactUsLabels["APPNAME"]; 
        $cms_contact_us = \App\Models\CmsPages::select('cms_details.title', 'cms_details.description')
        ->leftJoin('cms_details', 'cms_details.cms_id','=', 'cms_pages.id')
        ->where('cms_pages.slug', 'contact-us')
        ->where('cms_details.language_id', $lang_code)
        ->first();

        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        return view('frontend.contact-us', compact('cms_contact_us', 'pageName','projectName',
        'megamenuFileName','contactUsLabels'));
    }

    public function responseInquery(Request $request)
    {       
        //Localization
        $codes = ['FULLNAMEREQ', 'EMAILREQ', 'NOTVALIDEMAIL', 'MESSAGEREQ','EMAILSENTSUCC',
        'CAPTCHVERIFREQ','CAPTCHAREQ'];
        $contactUsLabels = getCodesMsg(Session::get('language_id'), $codes);

        $msg = [                                
            'fullname.required' => $contactUsLabels['FULLNAMEREQ'],
            'email.required' => $contactUsLabels['EMAILREQ'],
            'email.email' => $contactUsLabels['NOTVALIDEMAIL'],                            
            'text_message.required' => $contactUsLabels['MESSAGEREQ'],
            'g-recaptcha-response.recaptcha' => $contactUsLabels['CAPTCHVERIFREQ'],
            'g-recaptcha-response.required' => $contactUsLabels['CAPTCHAREQ'],
        ];
        
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email' => 'required|email',
            'text_message' => 'required',
            // 'confirm_password' => 'required|min:6',
            'g-recaptcha-response' => 'required|recaptcha'
        ],$msg);
                
        if($validator->fails()) {
            return redirect('/contact-us')
                ->withErrors($validator)
                ->withInput();
        }                

        $contact_us_inquiry = \App\Models\ContactUsInquiry::saveContactUsInquiry($request);            
        // Send email start                                               
        $temp_arr = []; 
        $lang_id = session('language_id');
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

        $notification = array(
            'message' => $contactUsLabels['EMAILSENTSUCC'] , 
            'alert-type' => 'success'
        );         
        return redirect('/contact-us')->with($notification); 
    }

    public function responseInqueryWithLangCode(Request $request)
    {
        //Localization
        $codes = ['FULLNAMEREQ', 'EMAILREQ', 'NOTVALIDEMAIL', 'MESSAGEREQ'];
        $contactUsLabels = getCodesMsg(Session::get('language_id'), $codes);

        $msg = [                                
            'fullname.required' => $contactUsLabels['FULLNAMEREQ'],  
            'email.required' => $contactUsLabels['EMAILREQ'],            
            'email.email' => $contactUsLabels['NOTVALIDEMAIL'],                            
            'text_message.required' => $contactUsLabels['MESSAGEREQ'],                 
        ];

        // Login without FB or Google
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email' => 'required|email',
            'text_message' => 'required'
        ],$msg);
                
        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }        

        $contact_us_inquiry = new \App\Models\ContactUsInquiry;
        $contact_us_inquiry->name = $request->fullname;
        $contact_us_inquiry->email = $request->email;
        $contact_us_inquiry->message = $request->text_message;
        $contact_us_inquiry->save();

        // Send email start                                               
        $temp_arr = []; 
        $lang_id = session('language_id');
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

        $notification = array(
            'message' => "Email Sent", 
            'alert-type' => 'success'
        );         
        return redirect()->back()->with($notification); 
    }
}
