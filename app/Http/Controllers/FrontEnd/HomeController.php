<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\Locale;
use Exception;
use Auth;
use Mail;
use Socialite;
use Agent;
use Illuminate\Support\Facades\Session;
use App\Traits\ReuseFunctionTrait;
use DB;
use App\Models\CmsPages;
use URL;
class HomeController extends Controller
{
    use ReuseFunctionTrait;

    /* ###########################################
    // Function: home
    // Description: Display front end home page
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function home($slug = null)
    {
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();

        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang = setSessionforLang($defaultLanguageId);
        $decimalNumber=Session::get('decimal_number');
        $decimalSeparator=Session::get('decimal_separator');
        $thousandSeparator=Session::get('thousand_separator');

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName')
                                ->join('world_languages as wl','wl.id','=','language_id')
                                ->where('is_default',1)
                                ->where('status',1)
                                ->where('is_deleted',0)
                                ->first();

        //Localization
        $codes = ['PERSONALISENOW', 'OURPRINTSERVICES', 'WHATWEDO', 'OURAMAZINGCOLLECTION','PHOTOGIFTSANDCARDS',
        'SHOPNOW','WEUNDERTAKEALL','EVENTOCCASIONS','HOWITWORKS','BOOKYOUREVENT','SHOPFROM', 'OURCOLLECTION',
        'SOMEOFAMAZING','BAHRIANPHOTOGRAPHERPHOTO','SOMEOFOUR', 'BESTSELLERS', 'EXPLORE', 'FROM', 'BHD','HOME',
        'APPNAME'];
        $homePageLabels = getCodesMsg(Session::get('language_id'), $codes);
        $pageName = $homePageLabels["HOME"];
        $projectName = $homePageLabels["APPNAME"];

        //Localization
        $codes = ['PERSONALISENOW', 'OURPRINTSERVICES', 'WHATWEDO', 'OURAMAZINGCOLLECTION','PHOTOGIFTSANDCARDS',
        'SHOPNOW','WEUNDERTAKEALL','EVENTOCCASIONS','HOWITWORKS','BOOKYOUREVENT','SHOPFROM', 'OURCOLLECTION',
        'SOMEOFAMAZING','BAHRIANPHOTOGRAPHERPHOTO','SOMEOFOUR', 'BESTSELLERS', 'EXPLORE', 'FROM', 'BHD'];
        $homePageLabels = getCodesMsg(Session::get('language_id'), $codes);

        $language = GlobalLanguage::where('id', Session::get('language_id'))->where('status',1)
        ->where('is_deleted', 0)->first();
        if($language)
        {
            $lang_id = $language->id;
        }
        else
        {
            $lang_id = $this->getDefaultLanguage();
        }

        $langCode = session('language_code');
        $langName = session('language_name');
        $defaultLangId = session('default_lang_id');

        $freeDeliveryMsg = Locale::getFreeDeliveryMsg(Session::get('language_id'));

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $baseUrl = $this->getBaseUrl();


        if(empty($slug))
        {
            //code commented & changed for lang code (last code updated 19 Jan 2021)
            // if($langCode == null)
            // {
                // echo "in if"; die;
                // $defaultLanguageId = $defaultLanguageData['id'];

                // $languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName')
                //                         ->join('world_languages as wl','wl.id','=','language_id')
                //                         ->where('is_default',1)
                //                         ->where('status',1)
                //                         ->where('is_deleted',0)
                //                         ->first();
                // Session::put('language_id',$defaultLanguageId);

                // $freeDeliveryMsg = Locale::getFreeDeliveryMsg($defaultLanguageId);
                // $defaultLanguageCode = WorldLanguage::select('alpha2 as Code')->where('id',$defaultLanguageData['language_id'])->first();
                // $megamenuFileName = "megamenu_".$defaultLanguageCode->Code;
            // }
            // else
            // {
            //     // echo "in else"; die;
            //     $languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName')
            //                             ->join('world_languages as wl','wl.id','=','language_id')
            //                             ->where('wl.alpha2',$langCode)
            //                             ->where('status',1)
            //                             ->where('is_deleted',0)
            //                             ->first();
            //     $defaultLanguageId = $languageData['id'];
            //     Session::put('language_id',$defaultLanguageId);

            //     $freeDeliveryMsg = Locale::getFreeDeliveryMsg($defaultLanguageId);
            //     $megamenuFileName = "megamenu_".$languageData['Code'];
            // }

            //Banner
            $home_page_banner = \App\Models\Banners::where('language_id', $lang_id)->where('status', 1)
            ->where('is_deleted', 0)->first();

            //Home Page Text
            $home_page_text_is_active = $this->homePageCompIsActiveDeactive(1);
            $home_page_text = \App\Models\HomePageText::where('language_id', $lang_id)->where('is_deleted', 0)->first();

            //Our Services
            $our_services_is_active = $this->homePageCompIsActiveDeactive(2);
            $our_services = \App\Models\Services::where('language_id', $lang_id)->where('status', 1)
            ->whereNull('deleted_at')->orderBy('sort_order', 'asc')->get();

            //Home page content
            $home_page_content_is_active = $this->homePageCompIsActiveDeactive(3);
            $home_page_content = \App\Models\HomePageContent::where('language_id', $lang_id)
            ->where('is_deleted', 0)->first();

            //Our Collection
            $our_collection_is_active = $this->homePageCompIsActiveDeactive(5);
            $our_collections = \App\Models\Collection::where('language_id', $lang_id)->where('status', 1)
            ->orderBy('sort_order', 'asc')->whereNull('deleted_at')->get();

            //Behrain Photographer
            $behrain_photo_is_active = $this->homePageCompIsActiveDeactive(6);
            $behrain_photo_arr = [];
            $behrain_photo = \App\Models\HomePagePhotographer::select('home_page_photographer.id',
            'home_page_photographer.big_image', 'photographers.web','home_page_photographer.small_image','photographers.id as photographer_id','photographer_details.name')
            ->Join('photographers', function($join) {
                $join->on('photographers.id', '=' , 'home_page_photographer.photographer_id');
                $join->where('photographers.status','=',1);
                $join->whereNull('photographers.deleted_at');
            })
            ->Join('photographer_details', function($join) use($lang_id) {
                $join->on('photographer_details.photographer_id', '=' , 'photographers.id');
                $join->where('photographer_details.language_id','=',$lang_id);
                $join->whereNull('photographer_details.deleted_at');
            })
            // ->leftjoin('photographers', 'photographers.id', '=', 'home_page_photographer.photographer_id')
            // ->leftjoin('photographer_details', 'photographer_details.photographer_id', '=', 'photographers.id')
            // ->where('photographer_details.language_id', $lang_id)
            // ->where('photographers.status', 1)
            // ->whereNull('photographers.deleted_at')
            ->where('home_page_photographer.status', 1)
            ->where('home_page_photographer.is_deleted', 0)
            ->orderBy('home_page_photographer.sort_order', 'asc')
            ->get()
            ->toArray();

            $chunkedarray = array_chunk($behrain_photo, 3, true);

            //Best Sellers
            $best_seller_is_active = $this->homePageCompIsActiveDeactive(7);
            $best_sellers = \App\Models\BestSellers::where('language_id', $lang_id)->where('status', 'Active')
            ->whereNull('deleted_at')->orderBy('sort_order', 'asc')->get();

            //How it works
            $how_it_works_is_active = $this->homePageCompIsActiveDeactive(4);
            $how_it_works = \App\Models\HowItWorks::where('language_id', $lang_id)->whereNull('deleted_at')
            ->orderBy('sort_order', 'asc')->get();

            //How it works banners
            $how_it_works_banners = \App\Models\HowItWorksBanners::where('language_id', $lang_id)
            ->whereNull('deleted_at')->first();
            if(empty($how_it_works_banners))
            {
                $defaultLang = $this->getDefaultLanguage();
                $how_it_works_banners = \App\Models\HowItWorksBanners::where('language_id', $defaultLang)
                ->whereNull('deleted_at')->first();
            }

            //Currency Symbol
            $Currency_symbol = getCurrSymBasedOnLangId($lang_id);

            //Currency Rate
            $get_curr = GlobalLanguage::select('currency_id')->where('id', $lang_id)->first();
            $Currency_rate = getCurrencyRates($get_curr->currency_id);

            $langVisibility = GlobalLanguage::checkVisibility($lang_id);

            return view('frontend.home',compact('pageName','projectName','freeDeliveryMsg','home_page_banner',
            'home_page_text', 'our_services', 'home_page_content', 'our_collections', 'chunkedarray', 'best_sellers',
            'how_it_works','how_it_works_banners','home_page_text_is_active', 'our_services_is_active','home_page_content_is_active',
            'our_collection_is_active','behrain_photo_is_active', 'best_seller_is_active', 'how_it_works_is_active',
            'megamenuFileName','lang_id','langCode','langName','defaultLangId','homePageLabels','baseUrl',
            'Currency_symbol','Currency_rate','mobileMegamenuFileName','langVisibility',
            'decimalNumber','decimalSeparator','thousandSeparator'));
        }
        if(!empty($slug))
        {
            // $slug = request()->segment(count(request()->segments()));
            //if($slug == "privacy-policy" || $slug == "shipping-policy" || $slug == "refund-policy")
            //{
                $lang_id = Session::get('language_id');
                $pageName = '';
                // if($slug == "privacy-policy")
                //     $pageName = "Privacy Policy";
                // if($slug == "shipping-policy")
                //     $pageName = "Shipping Policy";
                // if($slug == "refund-policy")
                //     $pageName = "Refund Policy";
                // else
                //     $pageName = $slug;

                $projectName = "Alboumi";
                $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
                $defaultLanguageId = $defaultLanguageData['id'];
                $setSessionforLang=setSessionforLang($defaultLanguageId);

                $policyData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword')
                                            ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                            ->where('slug',$slug)
                                            ->where('status',1)
                                            ->where('language_id',$lang_id)
                                            ->whereNull('cms_pages.deleted_at')
                                            ->whereNull('cd.deleted_at')
                                            ->first();
                if($policyData == null)
                {
                    $policyData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword')
                                            ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                            ->where('slug',$slug)
                                            ->where('status',1)
                                            ->where('language_id',$defaultLanguageData['id'])
                                            ->whereNull('cms_pages.deleted_at')
                                            ->whereNull('cd.deleted_at')
                                            ->first();
                }
                // dd($policyData);
                if($policyData == null)
                {
                    return redirect('/');
                }
                $pageName = $policyData['title'];
                return view('frontend/footerPages/policy',compact('pageName','projectName','policyData','megamenuFileName','lang_id','langCode','langName','defaultLangId','baseUrl','mobileMegamenuFileName'));
            //}
        }
    }


    /* ###########################################
    // Function: showLogin
    // Description: Display customer login page
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showLogin()
    {
        //Localization
        // Session::put('url.intended',URL::previous());
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $codes = ['LOGINLABEL', 'LOGINLABEL1', 'LOGINLABEL2', 'LOGINLABEL3','LOGINLABEL4',
        'LOGINLABEL5','LOGINLABEL6','LOGINLABEL7','LOGINLABEL8','LOGINLABEL9','LOGINLABEL10',
        'APPNAME', 'SIGNIN','LOGINSEODESC','LOGINSEOKEYWORD','EMAILREQ','NOTVALIDEMAIL','PASSWORDREQ'];
        $loginLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $loginLabels["SIGNIN"];
        $projectName = $loginLabels["APPNAME"];
        $baseUrl = $this->getBaseUrl();

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        $defaultLanguageId = $defaultLanguageData['id'];
        return view('frontend.login',compact('pageName','projectName','megamenuFileName','loginLabels','baseUrl','mobileMegamenuFileName'));
    }

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /* ###########################################
    // Function: customerLogin
    // Description: Customer can login to access their account
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function customerLogin(Request $request,$provider = null)
    {
        // dd($request);
        $language_id = session('language_id');

        // Login with Social media
        if(!empty($provider))
        {
            $user = Socialite::driver($provider)->user();

            $finduser = \App\Models\Customer::where('provider',$provider)
                                            ->where('provider_id', $user->id)->first();

            if($finduser)
            {
                $customer_timezone = \App\Models\CustomerTimezone::where('customer_id', $finduser->id)->first();
                if($customer_timezone)
                {
                    $customer_timezone->timezone = $request->timezone ? $request->timezone : "Asia/Bahrain";;
                    $customer_timezone->zone = $request->zone_time ? $request->zone_time : "+03:00";
                    $customer_timezone->save();
                }
                else
                {
                    $customer_timezone = new \App\Models\CustomerTimezone;
                    $customer_timezone->customer_id = $finduser->id;
                    $customer_timezone->timezone = $request->timezone ? $request->timezone : "Asia/Bahrain";;
                    $customer_timezone->zone = $request->zone_time ? $request->zone_time : "+03:00";
                    $customer_timezone->save();
                }

                Auth::guard('customer')->login($finduser);
                if($finduser->first_time_login == 0)
                {
                    $finduser->first_time_login = 1;
                    //$finduser->ip_address = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
                    $finduser->ip_address = "";
                    $finduser->os_name = Agent::platform();
                    $finduser->browser_name = Agent::browser();
                    $finduser->browser_version = Agent::version($customer->browser_name);
                    $finduser->save();

                    // $email = $finduser->email;
                    // $email_encoded = rtrim(strtr(base64_encode($finduser->email), '+/', '-_'), '=');
                    // $link = url('/verification-success').'/'.$email_encoded;
                    // Mail::send('frontend.emails.welcome', ['link' => $link, 'email' => $email_encoded], function ($message) use ($email) {
                    //     $message->from('no.reply.magneto123@gmail.com', 'Alboumi');
                    //     $message->to($email)->subject('Welcome to Alboumi');
                    // });

                    // Send email start
                    Session::put('language_id', 1);
                    $temp_arr = [];
                    $lang_id = Session::get('language_id');
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
                        '{{name}}' => $finduser->first_name,
                    );
                    $html_value = $this->replaceHtmlContent($replace_data,$value);
                    $data = [
                        'html' => $html_value,
                    ];
                    $subject = $temp_arr[0]['subject'];
                    $email = $finduser->email;
                    Mail::send('frontend.emails.customer-welcome-email', $data, function ($message) use ($email,$subject) {
                        $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                        $message->to($email)->subject($subject);
                    });
                    // Send email over
                }
                $cart_master_id=Session::get('cart_master_id');
                setCartMasterId($finduser->id,$cart_master_id);
                return redirect('/customer/my-account');
                // return redirect(Session::get('url.intended'));

            }
            else
            {
                $customer = \App\Models\Customer::saveCustomerLoginObj($user, $provider);
                Auth::guard('customer')->login($customer);

                $customer_timezone = \App\Models\CustomerTimezone::where('customer_id', $customer->id)->first();
                if($customer_timezone)
                {
                    $customer_timezone->timezone = $request->timezone ? $request->timezone : "Asia/Bahrain";;
                    $customer_timezone->zone = $request->zone_time ? $request->zone_time : "+03:00";
                    $customer_timezone->save();
                }
                else
                {
                    $customer_timezone = new \App\Models\CustomerTimezone;
                    $customer_timezone->customer_id = $customer->id;
                    $customer_timezone->timezone = $request->timezone ? $request->timezone : "Asia/Bahrain";;
                    $customer_timezone->zone = $request->zone_time ? $request->zone_time : "+03:00";
                    $customer_timezone->save();
                }

                // Send email start
                Session::put('language_id', 1);
                $temp_arr = [];
                $lang_id = Session::get('language_id');
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
                $cart_master_id=Session::get('cart_master_id');
                setCartMasterId($customer->id,$cart_master_id);
                return redirect('/customer/my-account');
                // return redirect(Session::get('url.intended'));

            }
        }
        else
        {
            //Localization
            $codes = ['EMAILREQ', 'NOTVALIDEMAIL', 'PASSWORDREQ','LOGINACCDELETE',
            'LOGINACCNOTVRFYD','LOGINACCNOTACTIVATE','CUSTOMERNOTFOUND'];
            $loginLabels = getCodesMsg(Session::get('language_id'), $codes);

            $msg = [
                'email.required' => $loginLabels['EMAILREQ'],
                'email.email' => $loginLabels['NOTVALIDEMAIL'],
                'password.required' => $loginLabels['PASSWORDREQ'],
            ];

            // Login without FB or Google
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ],$msg);

            if($validator->fails()) {
                return redirect('/login')
                            ->withErrors($validator)
                            ->withInput();
            }

            $customer = \App\Models\Customer::where('email', $request['email'])->first();
            if(empty($customer))
            {
                return redirect()->back()->with('invalid_credentials', $loginLabels['CUSTOMERNOTFOUND']);
            }

            $customer_timezone = \App\Models\CustomerTimezone::where('customer_id', $customer->id)->first();
            if($customer_timezone)
            {
                $customer_timezone->timezone = $request->timezone ? $request->timezone : "Asia/Bahrain";;
                $customer_timezone->zone = $request->zone_time ? $request->zone_time : "+03:00";
                $customer_timezone->save();
            }
            else
            {
                $customer_timezone = new \App\Models\CustomerTimezone;
                $customer_timezone->customer_id = $customer->id;
                $customer_timezone->timezone = $request->timezone ? $request->timezone : "Asia/Bahrain";
                $customer_timezone->zone = $request->zone_time ? $request->zone_time : "+03:00";
                $customer_timezone->save();
            }

            if($customer->first_time_login == 0)
            {
                $customer->first_time_login = 1;
                //$customer->ip_address = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
                $customer->ip_address = "";
                $customer->os_name = Agent::platform();
                $customer->browser_name = Agent::browser();
                $customer->browser_version = Agent::version($customer->browser_name);
                $customer->save();

                // $email = $customer->email;
                // $email_encoded = rtrim(strtr(base64_encode($customer->email), '+/', '-_'), '=');

                // $link = url('/verification-success').'/'.$email_encoded;
                // Mail::send('frontend.emails.welcome', ['link' => $link, 'email' => $email_encoded], function ($message) use ($email) {
                //     $message->from('no.reply.magneto123@gmail.com', 'Alboumi');
                //     $message->to($email)->subject('Welcome to Alboumi');
                // });

                // Send email start
                Session::put('language_id', 1);
                $temp_arr = [];
                $lang_id = Session::get('language_id');
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
            // dd($customer);
            if ($customer->is_deleted == 1) {
                return redirect()->back()->with('invalid_credentials', $loginLabels['LOGINACCDELETE']);
            }elseif ($customer->is_verify == 0) {
                return redirect()->back()->with('invalid_credentials', $loginLabels['LOGINACCNOTVRFYD']);
            }elseif ($customer->is_active == 0) {
                return redirect()->back()->with('invalid_credentials', $loginLabels['LOGINACCNOTACTIVATE']);
            }

            if($request->has('remember_me'))
            {
                if(Auth::guard('customer')->attempt(array('email' => $request['email'], 'password' => $request['password']), true))
                {
                    $customer_id = Auth::guard('customer')->user()->id;
                    Session::put('customer_id', $customer_id);
                    $cart_master_id=Session::get('cart_master_id');
                    setCartMasterId($customer_id,$cart_master_id);

                    // added by Pallavi (for redirecting back to event enq)
                    if($request->flagLogin == "1")
                    {
                        return redirect('events/'.$request->eventId);
                    }
                    if($request->flagCheckout == '1')
                    {
                        return redirect('/customer/shipping-address');
                    }
                    else
                    {
                        return redirect('/customer/my-account')->withCookie("customer_email", $request->email)
                        ->withCookie("customer_password", $request->password)->withCookie("customer_remember", "checked");
                    }
                }
                else
                {
                    return redirect()->back()->withInput()->with('invalid_credentials', config('message.AuthMessages.InvalidCredentials'));
                }
            }
            else
            {
                if(Auth::guard('customer')->attempt(array('email' => $request['email'], 'password' => $request['password']), false))
                {
                    $customer_id = Auth::guard('customer')->user()->id;
                    Session::put('customer_id', $customer_id);
                    $cart_master_id=Session::get('cart_master_id');
                    setCartMasterId($customer_id,$cart_master_id);
                     // added by Pallavi (for redirecting back to event enq)
                    if($request->flagLogin == "1")
                    {
                        return redirect('events/'.$request->eventId);
                    }

                    if($request->flagCheckout == '1')
                    {
                        return redirect('/customer/shipping-address');
                    }
                    else
                    {
                        return redirect('/customer/my-account')->withCookie("customer_email", "")
                        ->withCookie("customer_password", "")->withCookie("customer_remember", "");
                    }

                }
                else
                {
                    return redirect()->back()->withInput()->with('invalid_credentials', config('message.AuthMessages.InvalidCredentials'));
                }
            }
        }

    }

    /* ###########################################
    // Function: showSignup
    // Description: Display customer registration page
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showSignup()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];

        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);


        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        $defaultLanguageId = $defaultLanguageData['id'];

        //Localization
        $codes = ['REGISTERLABEL1', 'REGISTERLABEL2', 'REGISTERLABEL3', 'REGISTERLABEL4','REGISTERLABEL5',
        'REGISTERLABEL6','REGISTERLABEL7','REGISTERLABEL8','REGISTERLABEL9','REGISTERLABEL10','LOGINLABEL'
        , 'APPNAME','REGISTER','REGISTERSEODESC','REGISTERSEOKEYWORD','REGISTERLABEL11','REGISTERLABEL12',
        'EMAILREQ','NOTVALIDEMAIL','PASSWORDREQ','PASSCONFREQ','error516','FIRSTNAMEREQ','MOBILEMUSTBE8DIGIT',
        'MOBILENUM','MOBILEREQ'];
        $registerLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $registerLabels["REGISTER"];
        $projectName = $registerLabels["APPNAME"];
        $baseUrl = $this->getBaseUrl();

        return view('frontend.signup',compact('pageName','projectName','megamenuFileName','registerLabels','baseUrl','mobileMegamenuFileName'));
    }

    /* ###########################################
    // Function: signup
    // Description: Get customer information and store into database
    // Parameter: firstname: String, lastname: String, emial: String, mobile: Int, password: Int, confirm_password: Int
    // ReturnType: view
    */ ###########################################
    public function signup(Request $request)
    {
        // $ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));

        // dd("Public IP: ".$ip);
        // $ip = Agent::ip();
        // dd($ip);
        // $platform = Agent::platform();
        // dd($platform);
        // $browser = Agent::browser();
        // print_r($browser);
        // $version = Agent::version($browser);
        // dd($version);

        //Localization
        $codes = ['FIRSTNAMEREQ', 'LASTNAMEREQ', 'EMAILREQ', 'NOTVALIDEMAIL','MOBILEREQ',
        'MOBILENUM','PASSWORDREQ','PASSCONFREQ','PASSWORDCONFMSG','CUSTOMEREMAILALRDYEXIST',
        'REGISTRATIONMAILSENTMSG','CAPTCHAREQ','CAPTCHVERIFREQ','MOBILEMUSTBE8DIGIT'];
        $registerLabels = getCodesMsg(Session::get('language_id'), $codes);

        // try {
            $msg = [
                'firstName.required' => $registerLabels['FIRSTNAMEREQ'],
                'lastName.required' => $registerLabels['LASTNAMEREQ'],
                'email.required' => $registerLabels['EMAILREQ'],
                'email.email' => $registerLabels['NOTVALIDEMAIL'],
                'mobile.required' => $registerLabels['MOBILEREQ'],
                'mobile.regex' => $registerLabels['MOBILENUM'],
                'mobile.min' => $registerLabels["MOBILEMUSTBE8DIGIT"],
                'password.required' => $registerLabels['PASSWORDREQ'],
                'confirm_password.required' => $registerLabels['PASSCONFREQ'],
                'password.same' => $registerLabels['PASSWORDCONFMSG'],
                'g-recaptcha-response.recaptcha' => $registerLabels['CAPTCHVERIFREQ'],
                'g-recaptcha-response.required' => $registerLabels['CAPTCHAREQ'],
            ];

            $validator = Validator::make($request->all(), [
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
                'password' => 'required|min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required|min:6',
                'g-recaptcha-response' => 'required|recaptcha'
            ],$msg);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $language_id = Session::get('language_id');
            $customer_email = \App\Models\Customer::where('email', $request->email)->where('is_deleted', 0)->first();
            if($customer_email)
            {
                return redirect()->back()->withInput()->with('msg', $registerLabels['CUSTOMEREMAILALRDYEXIST']);
            }

            $save_customer = \App\Models\Customer::saveCustomerRegisterObj($request);
            if($save_customer)
            {
                $email = $request->email;

                // Send email start
                $temp_arr = [];
                $new_user = $this->getEmailTemp($language_id);
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
                // Send email over

                return redirect()->back()->with('mail_sent_msg', $registerLabels["REGISTRATIONMAILSENTMSG"]);
            }
        // } catch ALTER TABLE `customers` ADD `os_name` VARCHAR(100) NOT NULL AFTER `ip_address`, ADD `browser_name` VARCHAR(100) NOT NULL AFTER `os_name`, ADD `browser_version` VARCHAR(100) NOT NULL AFTER `browser_name`

    }

    /* ###########################################
    // Function: logout
    // Description: Destroy customer current session
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function logout()
    {
        //Localization
        $codes = ['LOGOUT'];
        $logoutLabels = getCodesMsg(Session::get('language_id'), $codes);

        Auth::guard('customer')->logout();
        // Session::flush();
        $notification = array(
            'message' => $logoutLabels['LOGOUT'],
            'alert-type' => 'success'
        );
        return redirect('/login')->with($notification);
    }

    /* ###########################################
    // Function: showForgotPassForm
    // Description: Show forgot password form
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showForgotPassForm()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        //Localization
        $codes = ['FORGOTPASSLABEL', 'FORGOTPASSLABEL1', 'FORGOTPASSLABEL2', 'FORGOTPASSLABEL3'
        ,'FORGOTPASSLABEL4','LOGINLABEL', 'FORGOTPASSLABEL','APPNAME','FPASSWORDSEODESC','FPASSWORDSEOKEYWORD'
        ,'EMAILREQ','NOTVALIDEMAIL'];
        $forgotPasswordLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $forgotPasswordLabels["FORGOTPASSLABEL"];
        $projectName = $forgotPasswordLabels["APPNAME"];
        $baseUrl = $this->getBaseUrl();

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        return view('frontend.forgot-password', compact('pageName', 'projectName','megamenuFileName',
        'forgotPasswordLabels','baseUrl','mobileMegamenuFileName'));
    }

    /* ###########################################
    // Function: forgotPassword
    // Description: Send forgot password email to customer
    // Parameter: email: String
    // ReturnType: view
    */ ###########################################
    public function forgotPassword(Request $request)
    {
        //Localization
        $codes = ['EMAILREQ', 'NOTVALIDEMAIL', 'EMAILSENTSUCC', 'EMAILNOTFOUND'];
        $forgotPasswordLabels = getCodesMsg(Session::get('language_id'), $codes);

        $msg = [
            'email.required' => $forgotPasswordLabels['EMAILREQ'],
            'email.email' => $forgotPasswordLabels['NOTVALIDEMAIL'],
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ],$msg);

        if($validator->fails()) {
            return redirect('/forgot-password')->withErrors($validator)->withInput();
        }

        $customer = \App\Models\Customer::where('email', $request->email)->first();
        if($customer)
        {
            $forgot_password = \App\Models\CustomerPasswordReset::savePasswordResetData($request);

            // Send email start
            $temp_arr = [];
            $lang_id = session('language_id');
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
            $msg = $forgotPasswordLabels['EMAILSENTSUCC'];
            return redirect()->back()->with('msg', $msg)->with('msg_class', true);
        }
        else
        {
            $msg = $forgotPasswordLabels['EMAILNOTFOUND'];
            return redirect()->back()->with('msg', $msg)->with('msg_class', false);
        }
    }

    /* ###########################################
    // Function: emailVerificationSuccess
    // Description: Display email verification success page
    // Parameter: email: String
    // ReturnType: view
    */ ###########################################
    public function emailVerificationSuccess($code)
    {
        try {
            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            $setSessionforLang=setSessionforLang($defaultLanguageId);

            $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
            $defaultCurrId = $defaultCurrData['id'];
            $setSessionforCurr = setSessionforCurr($defaultCurrId);

            //Localization
            $codes = ['APPNAME','EMAILVERIFICATION','EMAILVERIFIEDSUCC','EMAILVERIFICATIONDESC'
            ,'EMAILVERIFICATIONKEY'];
            $emailVerificationLabels = getCodesMsg(Session::get('language_id'), $codes);

            $pageName = $emailVerificationLabels['EMAILVERIFICATION'];
            $projectName = $emailVerificationLabels['APPNAME'];

            $baseUrl = $this->getBaseUrl();
            $email_decoded = base64_decode(strtr($code, '-_', '+/'));
            $customer = \App\Models\Customer::where('email', $email_decoded)->first();
            if($customer)
            {
                $customer->is_verify = 1;
                $customer->save();
                $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;

                $megamenuFileName = "megamenu_".Session::get('language_code');
                $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
                return view('frontend.emails.email-verify-success', compact('megamenuFileName',
                'mobileMegamenuFileName','pageName','projectName','emailVerificationLabels','baseUrl'));
            }
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: showResetPassForm
    // Description: Show reset password form to customer
    // Parameter: token: String
    // ReturnType: view
    */ ###########################################
    public function showResetPassForm($token)
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        //Localization
        $codes = ['RESETPASSWORD','APPNAME','RESTEPASSSEODEC','RESTEPASSSEOKEYWORD',
        'PASSWORD','RESETPASSWORD','CONFIRM_PASSWORD','FORGOTPASSLABEL3','LOGINLABEL',
        'PASSWORDREQ','PASSCONFREQ','error516'];
        $resetPasswordLabels = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $resetPasswordLabels["RESETPASSWORD"];
        $projectName = $resetPasswordLabels["APPNAME"];
        $baseUrl = $this->getBaseUrl();

        $current_time = date('Y-m-d H:i:s');
        $timeOut = \Carbon\Carbon::parse($current_time);
        $forgot_password = \App\Models\CustomerPasswordReset::where('token', $token)->first();
        $diffInHours = $timeOut->diffInMinutes($forgot_password->created_at);

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        if($diffInHours < 60)
        {
            return view('frontend.reset-password',compact('baseUrl','pageName','projectName','megamenuFileName','resetPasswordLabels','mobileMegamenuFileName'))->with('email', $forgot_password->email);
            // return view('frontend.reset-password')->with('token', $token);
        }
        else
        {
            return view('frontend.link-expire');
        }

    }

    /* ###########################################
    // Function: resetPassword
    // Description: Reset password functionality for customer
    // Parameter: password: String, confirm_password: String
    // ReturnType: view
    */ ###########################################
    public function resetPassword(Request $request)
    {
        try {
            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            $setSessionforLang=setSessionforLang($defaultLanguageId);

            //Localization
            $codes = ['PASSWORDREQ','PASSCONFREQ','PASSWORDCONFMSG','PASSWORDMINLENGTH',
            'CHANGEPASSSUCC','SOMETHINGWRONG'];
            $resetPasswordLabels = getCodesMsg(Session::get('language_id'), $codes);

            $msg = [
                'password.required' => $resetPasswordLabels['PASSWORDREQ'],
                'confirm_password.required' => $resetPasswordLabels['PASSCONFREQ'],
                'password.same' => $resetPasswordLabels['PASSWORDCONFMSG'],
                'password.min' => $resetPasswordLabels['PASSWORDMINLENGTH'],
            ];

            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required|min:6',
            ],$msg);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required|min:6',
            ]);

            if($validator->fails()) {
                return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
            }

            $reset_password = \App\Models\Customer::where('email', $request->reset_pass_email)->first();
            $reset_password->password = Hash::make($request->password);
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;

            $megamenuFileName = "megamenu_".Session::get('language_code');
            $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
            if($reset_password->save())
            {
                return redirect()->back()->with('msg', $resetPasswordLabels['CHANGEPASSSUCC'])->with('alert_type', true);
                // return redirect('/reset-password-success',compact('megamenuFileName'));
            }
            else
            {
                return redirect()->back()->with('msg', $resetPasswordLabels['SOMETHINGWRONG'])->with('alert_type', false);
            }
        } catch (Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: resetPasswordSuccess
    // Description: Display reset password success page after reseting password
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function resetPasswordSuccess()
    {
        $pageName = "Login";
        $projectName = "Alboumi";
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        return view('frontend.reset-password-success', compact('pageName', 'projectName','megamenuFileName','mobileMegamenuFileName'));
    }

    public function homePageCompIsActiveDeactive($id)
    {
        $home_page_component = \App\Models\HomePageComponent::where('id', $id)->first();
        return $home_page_component;
    }

    public function getPrivacyPolicyContents(Request $request)
    {
        // dd(request()->get('language_id'));
        $langId = $request->get('language_id');
        $policyData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword')
                                ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                ->where('slug','privacy-policy')
                                ->where('status',1)
                                ->where('language_id',request()->get('language_id'))
                                ->whereNull('cms_pages.deleted_at')
                                ->whereNull('cd.deleted_at')
                                ->first();

        $codes = ['APPNAME','PRIVACYPOLICY'];
        $policyLabels = getCodesMsg(request()->get('language_id'), $codes);

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultLanguageId = $defaultLanguageData['id'];

        if($langId == null)
            $langId = $defaultLanguageId;
        if($policyData == null)
        {
            $policyData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug','privacy-policy')
                                    ->where('status',1)
                                    ->where('language_id',$defaultLanguageData['id'])
                                    ->whereNull('cms_pages.deleted_at')
                                    ->whereNull('cd.deleted_at')
                                    ->first();
        }
        // dd($policyData);
        $pageName = $policyLabels['PRIVACYPOLICY'];
        $projectName = $policyLabels['APPNAME'];

        return view('frontend/footerPages/privacyPolicy',compact('policyData','pageName','projectName','langId'));
    }

    public function getPolicyPagesContents(Request $request)
    {
        $langId = $request->get('language_id');
        $refPolicyData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword')
                                ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                ->where('slug',$request->slug)
                                ->where('status',1)
                                ->where('language_id',request()->get('language_id'))
                                ->whereNull('cms_pages.deleted_at')
                                ->whereNull('cd.deleted_at')
                                ->first();

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultLanguageId = $defaultLanguageData['id'];

        if($langId == null)
            $langId = $defaultLanguageId;
        if($refPolicyData == null)
        {
            $refPolicyData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug',$request->slug)
                                    ->where('status',1)
                                    ->where('language_id',$defaultLanguageData['id'])
                                    ->whereNull('cms_pages.deleted_at')
                                    ->whereNull('cd.deleted_at')
                                    ->first();
        }

        $lang_id = $langId ? $langId : $defaultLanguageId;
        $codes = ['APPNAME','REFUNDPOLICY', 'SHIPPINGPOLICY', 'TERMSOFSERVICE', 'ABOUTUS'];
        $policyLabels = getCodesMsg($lang_id, $codes);

        if($request->slug == 'refund-policy'){
            $pageName = $policyLabels['REFUNDPOLICY'];
        }elseif ($request->slug == 'shipping-policy') {
            $pageName = $policyLabels['SHIPPINGPOLICY'];
        }elseif ($request->slug == 'terms-of-use') {
            $pageName = $policyLabels['TERMSOFSERVICE'];
        }elseif ($request->slug == 'about-us') {
            $pageName = $policyLabels['ABOUTUS'];
        }
        $projectName = $policyLabels['APPNAME'];

        return view('frontend/footerPages/policyPages',compact('refPolicyData','pageName','projectName','langId'));
    }

    public function getAboutUsPageContents(Request $request)
    {
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultLangId = $defaultLanguageData['id'];

        $lang_id = $request->get('language_id');

        $codes = ['APPNAME','ABOUTUS'];
        $footerLabels = getCodesMsg($lang_id, $codes);

        $pageName = $footerLabels['ABOUTUS'];
        $projectName = $footerLabels['APPNAME'];

        $aboutData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword','cms_pages.banner_image as cms_banner'
                                    ,'cms_pages.mobile_banner_image as cms_mobile_banner','cd.banner_image','cd.mobile_banner')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug',"about-us")
                                    ->where('language_id',$lang_id)
                                    ->where('status',1)
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->first();

        if($aboutData == null)
        {
            $aboutData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword','cms_pages.banner_image as cms_banner'
            ,'cms_pages.mobile_banner_image as cms_mobile_banner','cd.banner_image','cd.mobile_banner')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug',"about-us")
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('status',1)
                                    ->where('language_id',$defaultLanguageData['id'])
                                    ->first();
        }
        if($lang_id == null)
            $lang_id = $defaultLanguageData['id'];
        $baseUrl = $this->getBaseUrl();
        return view('frontend/footerPages/plainAboutUsPage',compact('pageName','projectName','aboutData','lang_id','baseUrl'));
    }
}
