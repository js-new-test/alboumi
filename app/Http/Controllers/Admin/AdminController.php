<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use DB;
use DataTables;
use Mail;
use Session;
use Exception;
use App\Traits\ReuseFunctionTrait;
use Image;
use Config;

class AdminController extends Controller
{
    use ReuseFunctionTrait;

    /* ###########################################
    // Function: showLoginForm
    // Description: Display admin login page
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /* ###########################################
    // Function: login
    // Description: Authentical user for admin login
    // Parameter: email: String, password: Int
    // ReturnType: view
    */ ###########################################
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required|recaptcha'
        ]);

        if($validator->fails()) {
            return redirect('/admin/login')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = \App\Models\User::where('email', $request['email'])->first();
        if(!$user)
        {
            return redirect()->back()->with('msg', config('message.AuthMessages.AccountNotFound'));
        }

        if ($user->is_deleted == 1) {
            return redirect()->back()->with('msg', config('message.AuthMessages.AccountDelete'));
        }elseif ($user->is_verify == 0) {
            return redirect()->back()->with('msg', config('message.AuthMessages.NotVerified'));
        }elseif ($user->is_active == 0) {
            return redirect()->back()->with('msg', config('message.AuthMessages.NotActive'));
        }

        if($request->has('remember'))
        {
            if(Auth::guard('admin')->attempt(array('email' => $request['email'], 'password' => $request['password']), true))
            {
                $user = Auth::guard('admin')->user();
                Session::put('username', $user->firstname.' '.$user->lastname);

                return redirect('/admin/dashboard')->withCookie("email", $request->email)->withCookie("password", $request->password)->withCookie("remember", "checked");
            }
            else
            {
                return redirect()->back()->with('msg', 'Email & Password are incorrect.');
            }
        }
        else
        {
            if(Auth::guard('admin')->attempt(array('email' => $request['email'], 'password' => $request['password']), false))
            {
                $user = Auth::guard('admin')->user();
                Session::put('username', $user->firstname.' '.$user->lastname);
                return redirect('/admin/dashboard')->withCookie("email", "")->withCookie("password", "")->withCookie("remember", "");
            }
            else
            {
                return redirect()->back()->with(['msg' => 'Email & Password are incorrect.']);
            }
        }
    }

    /* ###########################################
    // Function: logout
    // Description: Terminate user current session
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }

    /* ###########################################
    // Function: listCurrency
    // Description: List all addedd currency
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function listCurrency(Request $request)
    {
        if($request->ajax())
        {
            try {
                $id = Auth::guard('admin')->user()->id;
                $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

                $currency = DB::table('global_currency')
                    ->select('global_currency.id','currency.name','currency.currency_code', 'currency.currency_symbol',
                    'global_currency.is_default',DB::raw("date_format(global_currency.created_at,'%Y-%m-%d %h:%i:%s') as curr_created_at"))
                    ->leftJoin('currency', 'currency.id', '=', 'global_currency.currency_id')
                    ->where('currency.currency_symbol', '!=', '')
                    ->where('global_currency.is_deleted', 0)
                    ->get();
                return Datatables::of($currency)->editColumn('user_zone', function () use($timezone){
                    return $timezone;
                })->make(true);
            } catch (\Throwable $th) {
                return view('errors.500');
            }
        }
        return view('admin.settings.currency.list');
    }

    /* ###########################################
    // Function: showAddCurrForm
    // Description: Show add new currency form
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showAddCurrForm()
    {
        $currency = DB::table('currency')->where('currency_code','!=', '')->get();
        return view('admin.settings.currency.add', compact('currency'));
    }

    /* ###########################################
    // Function: addCurrency
    // Description: Add new currency in database
    // Parameter: currency: Int
    // ReturnType: view
    */ ###########################################
    public function addCurrency(Request $request)
    {
        $currency = DB::table('global_currency')->where('currency_id','=', $request->currency)->first();
        if($currency)
        {
            return redirect()->back()->with('msg', "Currency already addedd!");
        }
        else
        {
            $gll_currency = new \App\Models\GlobalCurrency;
            $gll_currency->currency_id = $request->currency;
            if($gll_currency->save())
            {
                $notification = array(
                    'message' => 'Currency added successfully!',
                    'alert-type' => 'success'
                );
                return redirect('admin/currency/list')->with($notification);
            }
        }

    }

    /* ###########################################
    // Function: editCurrency
    // Description: Show currency edit form
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editCurrency($id)
    {
        $glb_curr_id = $id;
        $currencies = DB::table('currency')
            ->select('id','name','currency_symbol','currency_code')
            ->where('currency.currency_symbol', '!=', '')
            ->get();
        $selected_currency = \App\Models\GlobalCurrency::where('id', $id)->first();
        return view('admin.settings.currency.edit', compact('selected_currency','currencies', 'glb_curr_id'));
    }

    /* ###########################################
    // Function: updateCurrency
    // Description: Update existing currency
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function updateCurrency(Request $request)
    {
        try {
            $currency = \App\Models\GlobalCurrency::where('id', $request->glb_curr_id)->first();
            if($currency)
            {
                $currency->currency_id = $request->currency_id;

                if($currency->save())
                {
                    $notification = array(
                        'message' => 'Currency updated successfully!',
                        'alert-type' => 'success'
                    );
                    return redirect('/admin/currency/list')->with($notification);

                }
            }
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: deleteCurrency
    // Description: Delete existing currency
    // Parameter: id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteCurrency(Request $request)
    {
        if($request->ajax())
        {
            try {
                $currency = \App\Models\GlobalCurrency::where('id', $request->currency_id)->first();
                if($currency)
                {
                    $currency->is_deleted = 1;
                    $currency->save();
                    $result['status'] = 'true';
                    $result['msg'] = "Currency deleted successfully!";
                    return $result;
                }
                else
                {
                    $result['status'] = 'false';
                    $result['msg'] = "Something went swong. Please try again!";
                    return $result;
                }
            } catch (\Exception $th) {
                return view('errors.500');
            }
        }
    }

    /* ###########################################
    // Function: listLanguage
    // Description: Show list of all added language
    // Parameter: id: Int
    // ReturnType: array
    */ ###########################################
    public function listLanguage(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

            $language = \App\Models\GlobalLanguage::select('global_language.id','global_language.is_default','global_language.status','world_languages.langEN as lang_name','world_languages.alpha2 as sortcode',
                        'visibility',DB::raw("date_format(global_language.created_at,'%Y-%m-%d %h:%i:%s') as lng_created_at"))
                        ->leftJoin('world_languages', 'world_languages.id', '=', 'global_language.language_id')
                        ->where('global_language.is_deleted', 0)
                        ->get();
            return Datatables::of($language)->editColumn('user_zone', function () use($timezone){
                    return $timezone;
                })->make(true);
        }
        return view('admin.settings.language.list');
    }

    /* ###########################################
    // Function: showAddLanguageForm
    // Description: Show add new language form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function showAddLanguageForm()
    {
        $currencies = getAllCurrency();
        $languages = \App\Models\WorldLanguage::select('id','langEN as lang_name','world_languages.alpha2 as sortcode')
                    ->get();
        return view('admin.settings.language.add',compact('languages','currencies'));
    }

    /* ###########################################
    // Function: addLanguage
    // Description: Add new language for global language
    // Parameter: id: Int
    // ReturnType: array
    */ ###########################################
    public function addLanguage(Request $request)
    {
        $language = \App\Models\WorldLanguage::where('alpha2', $request->language_selector)->first();
        if($language)
        {
            $glb_Language = \App\Models\GlobalLanguage::where('language_id', $language->id)->whereNull('deleted_at')->first();
            if($glb_Language)
            {
                $notification = array(
                    'message' => 'Language already added.',
                    'alert-type' => 'error'
                );
                return redirect('admin/language/list')->with($notification);
            }
            else
            {
                $glb_Language = new \App\Models\GlobalLanguage;
                $glb_Language->language_id = $language->id;
                $glb_Language->currency_id = $request->currency;
                $glb_Language->visibility = $request->visibility;
                $glb_Language->decimal_number = $request->decimal_number;
                $glb_Language->decimal_separator = $request->decimal_separator;
                $glb_Language->thousand_separator = $request->thousand_separator;

                $reqdImgWidth =Config::get('app.language_image.width');
                $reqdImgHeight =Config::get('app.language_image.height');
                if($request->hasFile('lang_flag'))
                {
                    if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
                    {
                        $image       = $request->file('lang_flag');
                        $filename    = $image->getClientOriginalName();

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                        $image->move(public_path('assets/images/languages/'), $filename);
                        $image_resize->save(public_path('assets/images/languages/' .$filename));
                        $glb_Language->lang_flag = $filename;
                    }
                    else
                    {
                        $image = $request->file('lang_flag');
                        $filename = $image->getClientOriginalName();
                        $image->move(public_path('assets/images/languages/'), $filename);
                        $glb_Language->lang_flag = $request->file('lang_flag')->getClientOriginalName();
                    }
                }

                if($glb_Language->save())
                {
                    $notification = array(
                        'message' => 'Language added successfully!',
                        'alert-type' => 'success'
                    );
                    return redirect('admin/language/list')->with($notification);
                }
                else
                {
                    $notification = array(
                        'message' => 'Something went wrong. Please try again!',
                        'alert-type' => 'error'
                    );
                    return redirect('admin/language/list')->with($notification);
                }
            }
        }
        else
        {
            $notification = array(
                'message' => 'Sorry the country language you select is not available.',
                'alert-type' => 'error'
            );
            return redirect('admin/language/list')->with($notification);
        }
        return $result;
    }

    /* ###########################################
    // Function: editLanguage
    // Description: Edit existing language
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editLanguage($id)
    {
        $currencies = getAllCurrency();
        $language = \App\Models\GlobalLanguage::select('global_language.id', 'global_language.decimal_number','global_language.decimal_separator','global_language.thousand_separator','global_language.currency_id','global_language.language_id','world_languages.langEN as lang_name','world_languages.alpha2 as sortcode','visibility','lang_flag')
            ->leftJoin('world_languages', 'world_languages.id', '=', 'global_language.language_id')
            ->where('global_language.id', $id)
            ->first();
        $w_languages = \App\Models\WorldLanguage::select('id','langEN as lang_name','world_languages.alpha2 as sortcode')
            ->get();

        $baseUrl = $this->getBaseUrl();
        return view('admin.settings.language.edit',compact('language','w_languages','baseUrl','currencies'));
    }

    /* ###########################################
    // Function: updateLanguage
    // Description: Update existing language
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function updateLanguage(Request $request)
    {
        $language = \App\Models\WorldLanguage::where('alpha2', $request->language_selector)->first();
        if($language)
        {
            $glb_Language = \App\Models\GlobalLanguage::where('id',$request->glb_lang_id)->first();
            $glb_Language->language_id = $language->id;
            $glb_Language->currency_id = $request->currency;
            $glb_Language->visibility = $request->visibility;
            $glb_Language->decimal_number = $request->decimal_number;
            $glb_Language->decimal_separator = $request->decimal_separator;
            $glb_Language->thousand_separator = $request->thousand_separator;

            $reqdImgWidth =Config::get('app.language_image.width');
            $reqdImgHeight =Config::get('app.language_image.height');
            if($request->hasFile('lang_flag'))
            {
                if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
                {
                    $image       = $request->file('lang_flag');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                    $image->move(public_path('assets/images/languages/'), $filename);
                    $image_resize->save(public_path('assets/images/languages/' .$filename));
                    $glb_Language->lang_flag = $filename;
                }
                else
                {
                    $image = $request->file('lang_flag');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/languages/'), $filename);
                    $glb_Language->lang_flag = $request->file('lang_flag')->getClientOriginalName();
                }
            }

            if($glb_Language->save())
            {
                $notification = array(
                    'message' => 'Language updated successfully!',
                    'alert-type' => 'success'
                );
                return redirect('admin/language/list')->with($notification);
            }
            else
            {
                $notification = array(
                    'message' => 'Something went wrong. Please try again!',
                    'alert-type' => 'error'
                );
                return redirect('admin/language/list')->with($notification);
            }
        }
        else
        {
            $notification = array(
                'message' => 'Sorry the country language you select is not available.',
                'alert-type' => 'error'
            );
            return redirect('admin/language/list')->with($notification);
        }
    }

    /* ###########################################
    // Function: deleteLanguage
    // Description: Delete existing language
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function deleteLanguage(Request $request)
    {
        if($request->ajax())
        {
            try {
                $language = \App\Models\GlobalLanguage::where('id', $request->language_id)->first();
                if($language)
                {
                    // $language->delete();
                    $language->is_deleted = 1;
                    $language->deleted_at = Carbon::now();
                    $language->save();
                    $result['status'] = 'true';
                    $result['msg'] = "Language deleted successfully!";
                    return $result;
                }
                else
                {
                    $result['status'] = 'false';
                    $result['msg'] = "Something went swong. Please try again!";
                    return $result;
                }
            } catch (\Exception $th) {
                return view('errors.500');
            }
        }
    }

    /* ###########################################
    // Function: changePasswordForm
    // Description: Admin change password form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function changePasswordForm()
    {
        return view('admin.change-password');
    }

    /* ###########################################
    // Function: changePassword
    // Description: Admin can change their password
    // Parameter: password: String, confirm_password: String
    // ReturnType: view
    */ ###########################################
    public function changePassword(Request $request)
    {
        try {
            $id = Auth::guard('admin')->user()->id;
            $user = \App\Models\User::where('id', $id)->first();
            if($user)
            {
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect()->back()->with('msg', "Password changed successfully!")->with('alert_class', true);
            }
            else
            {
                return redirect()->back()->with('msg', "Something went wrong, Please Try again!")->with('alert_class', false);
            }
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: profile
    // Description: Show photographer profile page
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function profile()
    {
        $user_id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $user_id)->first();
        if($timezone)
        {
            $users = \App\Models\User::select('users.id','users.firstname','users.lastname','users.phone', 'users.email', 'user_timezone.zone')
            ->leftJoin('user_timezone','user_timezone.user_id','=','users.id')
            ->where('users.id', $user_id)
            ->first();
        }
        else
        {
            $users = \App\Models\User::where('id', $user_id)->first();
        }
        $user_profile = \App\Models\UserProfilePhoto::where('user_id', $user_id)->first();
        if($user_profile)
        {

            $photo = ($user_profile->image) ? url('public/assets/images/user_profile').'/'.$user_profile->image : url('public/assets/images/default-user.png');
        }
        else
        {
            $photo = url('public/assets/images/default-user.png');
        }
        return view('admin.profile', compact('users'))->with('photo', $photo);
    }

    /* ###########################################
    // Function: updateProfile
    // Description: Update user profile details
    // Parameter: id: Int, photographer_firstname: String, photographer_lastname: String, profile_photo: String
    // ReturnType: view
    */ ###########################################
    public function updateProfile(Request $request)
    {
        $msg = [
            'firstname.required' => "The firstname field is required.",
            'lastname.required' => "The lastname field is required.",
            // 'email.required' => "The email field is required.",
            // 'email.email' => "Please enter a valid email address",
            'mobile.required' => "The email field is required.",
            'mobile.required' => "The mobile number must be in digit.",
        ];
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            // 'email' => 'required|email',
            'mobile' => 'required|numeric'
        ],$msg);

        if($validator->fails()) {
            return redirect('/admin/profile')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = \App\Models\User::where('id',$request->id)->first();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        // $user->email = $request->email;
        $user->phone = $request->mobile;
        if($request->hasFile('profile_photo'))
        {
            $photo = $request->file('profile_photo');
            $ext = $request->file('profile_photo')->extension();
            $filename = rand().'_'.time().'.'.$ext;
            $photo->move(public_path().'/assets/images/user_profile', $filename);
        }

        if($user->save()){
            $timezone = \App\Models\UserTimezone::where('user_id', $user->id)->first();
            if($timezone)
            {
                $timezone->timezone = $request->timezone;
                $timezone->zone = $request->timezone_offset;
                $timezone->created_at = date("Y-m-d H:i:s");
                $timezone->save();
            }
            else
            {
                $timezone = new \App\Models\UserTimezone;
                $timezone->user_id = $user->id;
                $timezone->timezone = $request->timezone;
                $timezone->zone = $request->timezone_offset;
                $timezone->created_at = date("Y-m-d H:i:s");
                $timezone->save();
            }

            $user_profile = \App\Models\UserProfilePhoto::where('user_id', $request->id)->first();
            if($user_profile)
            {
                if(!empty($filename))
                {
                    $path = public_path('/assets/images/user_profile').'/'.$user_profile->image;
                    if(file_exists($path))
                    {
                        unlink($path);
                    }
                    $user_profile->user_id = $user->id;
                    $user_profile->image = $filename;
                    $user_profile->save();
                }
            }
            else
            {
                if(!empty($filename))
                {
                    $user_profile = new \App\Models\UserProfilePhoto;
                    $user_profile->user_id = $user->id;
                    $user_profile->image = $filename;
                    $user_profile->save();
                }
            }
            $notification = array(
                'message' => "Profile Updated Successfully!",
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
            // return redirect()->back()->with('msg', "Profile Updated Successfully!");
        }
        else{
            return redirect()->back();
        }
    }

    /* ###########################################
    // Function: showForgotPassForm
    // Description: Show photographer forgot password form
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showForgotPassForm()
    {
        return view('admin.forgot-password');
    }

    /* ###########################################
    // Function: forgotPassword
    // Description: Send email to user mail id
    // Parameter: forgot_email: String
    // ReturnType: view
    */ ###########################################
    public function forgotPassword(Request $request)
    {
        $msg = [
            'forgot_email.required' => "The email field is required.",
            'forgot_email.email' => "The email must be a valid email address..",
        ];
        $validator = Validator::make($request->all(), [
            'forgot_email' => 'required|email',
        ],$msg);

        if($validator->fails()) {
            return redirect('/admin/forgot-password')
                        ->withErrors($validator)
                        ->withInput();
        }
        $user = \App\Models\User::where('email', $request->forgot_email)->first();
        if($user)
        {
            $forgot_password = new \App\Models\ResetPassword;
            $forgot_password->email = $request->forgot_email;
            $forgot_password->token = Str::random(60);
            $forgot_password->save();

            // Send email start
            $email = $request->forgot_email;
            $link = url('/admin/reset-password').'/'.$forgot_password->token;

            $temp_arr = [];
            $forgot_pass = $this->getEmailTemp();
            foreach($forgot_pass as $code )
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

            $replace_data = array(
                '{{name}}' => $user->firstname,
                '{{link}}' => $link,
            );
            $html_value = $this->replaceHtmlContent($replace_data,$value);
            $data = [
                'html' => $html_value,
            ];
            $subject = $temp_arr[0]['subject'];
            Mail::send('admin.emails.forgot-password-email', $data, function ($message) use ($email,$subject) {
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($email)->subject($subject);
            });
            // Send email over

            // $email = $request->forgot_email;
            // $link = url('/admin/reset-password').'/'.$forgot_password->token;
            // Mail::send('admin.emails.forgot-password-email', ['link' => $link], function ($message) use ($email) {
            //     $message->from('no.reply.magneto123@gmail.com', 'Alboumi');
            //     $message->to($email)->subject('Forgot Password');
            // });

            return redirect()->back()->with('success_msg', config('message.AuthMessages.EmailSentSuccess'));
        }
        else
        {
            return redirect()->back()->with('msg', config('message.AuthMessages.EmailNotFound'));
        }
    }

    /* ###########################################
    // Function: showResetPassForm
    // Description: Show photographer forgot password form
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showResetPassForm($token)
    {
        $current_time = date('Y-m-d H:i:s');
        $timeOut = \Carbon\Carbon::parse($current_time);
        $forgot_password = \App\Models\ResetPassword::where('token', $token)->first();
        $diffInHours = $timeOut->diffInMinutes($forgot_password->created_at);
        if($diffInHours < 60)
        {
            return view('admin.reset-password')->with('email', $forgot_password->email);
        }
        else
        {
            return view('admin.link-expire');
        }
    }

    /* ###########################################
    // Function: resetPassword
    // Description: Reset existing password functionality
    // Parameter: password: String, confirm_password: String
    // ReturnType: view
    */ ###########################################
    public function resetPassword(Request $request)
    {
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

        $reset_password = \App\Models\User::where('email', $request->reset_pass_email)->first();
        $reset_password->password = Hash::make($request->password);
        if($reset_password->save())
        {
            return view('admin.reset-password-success');
        }
        else
        {
            return redirect()->back()->with('msg', "Something went wrong, Please Try again!");
        }
    }

    /* ###########################################
    // Function: defaultCurrency
    // Description: Make currency as default
    // Parameter: curr_id: Int
    // ReturnType: array
    */ ###########################################
    public function defaultCurrency(Request $request)
    {
        $globalCurrency = \App\Models\GlobalCurrency::where('id', '<>', $request->curr_id)->get();
        foreach ($globalCurrency as $gcurr) {
            $globalCurrency = \App\Models\GlobalCurrency::where('id',$gcurr->id)->first();
            if($globalCurrency->is_default == 1)
            {
                $globalCurrency->is_default = 0;
                $globalCurrency->save();
            }
        }
        $globalCurrency = \App\Models\GlobalCurrency::where('id',$request->curr_id)->first();
        $globalCurrency->is_default = 1;
        $globalCurrency->save();
        $result['status'] = 'true';
        $result['msg'] = "Default currency updated successfully!";
        return $result;
    }

    /* ###########################################
    // Function: defaultLanguage
    // Description: Make langauge as default
    // Parameter: lang_id: Int
    // ReturnType: array
    */ ###########################################
    public function defaultLanguage(Request $request)
    {
        $globalLanguage = \App\Models\GlobalLanguage::where('id', '<>', $request->lang_id)->get();
        foreach ($globalLanguage as $glang) {
            $globalLanguage = \App\Models\GlobalLanguage::where('id',$glang->id)->first();
            if($globalLanguage->is_default == 1)
            {
                $globalLanguage->is_default = 0;
                $globalLanguage->save();
            }
        }
        $globalLanguage = \App\Models\GlobalLanguage::where('id',$request->lang_id)->first();
        $globalLanguage->is_default = 1;
        $globalLanguage->save();
        $result['status'] = 'true';
        $result['msg'] = "Default language updated successfully!";
        return $result;
    }

    /* ###########################################
    // Function: changeStatus
    // Description: Make langauge active or inactive
    // Parameter: lang_id: Int
    // ReturnType: array
    // By Nivedita 21-05-2021
    */ ###########################################
    public function changeStatus(Request $request)
    {
        $globalLanguage = \App\Models\GlobalLanguage::where('id',$request->lang_id)->first();
        if($globalLanguage->status == 1)
        {
            $globalLanguage->status = 0;
            $globalLanguage->save();
            $msg = "Language Deactivated Successfully!";
        }
        else
        {
          $globalLanguage->status = 1;
          $globalLanguage->save();
          $msg = "Language Activated Successfully!";
        }
        $result['status'] = 'true';
        $result['msg'] = $msg;
        return $result;
    }

    /* ###########################################
    // Function: accessDenied
    // Description: Prevent unauthorized access
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function accessDenied()
    {
        return view('admin.errors.forbiddon');
    }
}
