<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Faqs;
use App\Models\GlobalLanguage;
use App\Models\Country;
use App\Models\FooterLinks;
use App\Models\FooterDetails;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;
use Session;
use Image;
use App\Traits\ReuseFunctionTrait;

class SettingsController extends Controller
{
    use ReuseFunctionTrait;

    /** FAQ functions */
    public function getFaqList()
    {
        $languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN','is_default')
                                    ->where('status',1)
                                    ->where('is_deleted',0)
                                    ->get();
        return view('admin.settings.faqs.index',compact('languages'));
    }

    public function getFaqData(Request $request)
    {
        // dd($request['lang_id']);
        DB::statement(DB::raw('set @rownum=0'));

        $faqs = Faqs::select('faqs.id', 'question', 'answer','sort_order', 'is_active',
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                        ->leftjoin('global_language as gl','gl.id','=','faqs.language_id')
                        ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                        ->where('gl.is_default',1)
                        ->where('gl.status',1)
                        ->whereNull('deleted_at')
                        ->orderBy('faqs.updated_at','desc')
                        ->get();
        if($request['lang_id'] != null)
        {
            $faqs = Faqs::select('id', 'question', 'answer','sort_order', 'is_active',
                                DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                                ->where('language_id',$request['lang_id'])
                                ->whereNull('deleted_at')
                                ->orderBy('updated_at','desc')
                                ->get();
        }
        return Datatables::of($faqs)->rawColumns(['answer'])->make(true);
    }

    public function getFilteredData(Request $request)
	{
        // dd($request['lang_id']);
        if($request->ajax())
        {
            try
            {
                DB::statement(DB::raw('set @rownum=0'));

                $faqs = Faqs::select('id', 'question', 'answer','sort_order', 'is_active',
                                DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                                ->where('language_id',$request['lang_id'])
                                ->whereNull('deleted_at')
                                ->orderBy('updated_at','desc')
                                ->get();
                                // dd($faqs);
                return Datatables::of($faqs)->rawColumns(['answer'])->make(true);
            }
            catch (\Exception $e)
            {
                return view('errors.500');
            }
        }
    }

    public function faqAddView()
    {
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        if($global_languages_count >= 2)
        {
            $languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN')
            ->where('status',1)
            ->where('is_deleted',0)
            ->get();
            return view('admin.settings.faqs.add',compact('languages'));
        }
        else
        {
            $language = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN')
            ->where('status',1)
            ->where('is_deleted',0)
            ->first();
            return view('admin.settings.faqs.add',compact('language'));
        }
    }


    public function addFaq(Request $request)
    {
        try
        {
            $messsages = array(
                'language_id.required' => 'Please select language',
                'question.required' => 'Please write question',
                'answer.required' => 'Please write answer',
            );

            $validator = Validator::make($request->all(), [
                'language_id'=>'required',
                'question'=>'required',
                'answer'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }

            $faq = new Faqs;
            $faq->language_id = $request->language_id;
            $faq->sort_order = $request->sort_order;
            $faq->question = $request->question;
            $faq->is_active = $request->is_active;
            $faq->answer = $request->answer;
            $faq->save();

            $notification = array(
                'message' => 'FAQ added successfully!',
                'alert-type' => 'success'
            );
            return redirect('admin/faq')->with($notification);
        }
        catch (\Exception $e)
        {
                Session::flash('error', $e->getMessage());
            return redirect('admin/faq');
        }
    }

    public function faqActiveInactive(Request $request)
    {
        try
        {
            $faq = Faqs::where('id',$request->faq_id)->first();
            if($request->is_active == 1)
            {
                $faq->is_active = $request->is_active;
                $msg = "FAQ Activated Successfully!";
            }
            else
            {
                $faq->is_active = $request->is_active;
                $msg = "FAQ Deactivated Successfully!";
            }
            $faq->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        catch(\Exception $ex)
        {
            return view('errors.500');
        }
    }

    public function deleteFaq(Request $request)
    {
        $faq = Faqs::select('id')
                        ->where('id', $request->faq_id)
                        ->first();

        if(!empty($faq))
        {
            $faq->deleted_at = Carbon::now();
            $faq->save();
            $result['status'] = 'true';
            $result['msg'] = "FAQ Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong!!";
            return $result;
        }
    }

    public function faqEditView($id)
    {
        $faq = Faqs::findOrFail($id);
        if(!empty($faq))
        {
            $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
            ->where('is_deleted', 0)
            ->count();
            if($global_languages_count >= 2)
            {
                $languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                ->select('global_language.id','alpha2','langEN')
                ->where('status',1)
                ->where('is_deleted',0)
                ->get();
                return view('admin.settings.faqs.edit',compact('faq','languages'));
            }
            else
            {
                $language = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                ->select('global_language.id','alpha2','langEN')
                ->where('status',1)
                ->where('is_deleted',0)
                ->first();
                return view('admin.settings.faqs.edit',compact('faq','language'));
            }
        }
    }

    public function updateFaq(Request $request)
    {
        $faq = Faqs::findOrFail($request->faq_id);

        if(!empty($faq))
        {
            $messsages = array(
                'language_id.required' => 'Please select language',
                'question.required' => 'Please write question',
                'answer.required' => 'Please write answer',
            );

            $validator = Validator::make($request->all(), [
                'language_id'=>'required',
                'question'=>'required',
                'answer'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }


            $faq->language_id = $request->language_id;
            $faq->sort_order = $request->sort_order;
            $faq->question = $request->question;
            $faq->is_active = $request->is_active;
            $faq->answer = $request->answer;
            $faq->save();

            $notification = array(
                'message' => 'FAQ updated successfully!',
                'alert-type' => 'success'
            );

            return redirect('admin/faq')->with($notification);
        }
    }

    /** Country functions */
    public function getCountries()
    {
        return view('admin.settings.country.index');
    }

    public function getCountryData(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $countries = Country::select('id', 'name', 'is_active',
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                        ->whereNull('deleted_at')
                        ->orderBy('updated_at','desc')
                        ->get();

        return Datatables::of($countries)->make(true);
    }

    public function deleteCountry(Request $request)
    {
        $country = Country::select('id')
                        ->where('id', $request->country_id)
                        ->first();

        if(!empty($country))
        {
            $country->deleted_at = Carbon::now();
            $country->save();
            $result['status'] = 'true';
            $result['msg'] = "Country Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong!!";
            return $result;
        }
    }

    public function countryActiveInactive(Request $request)
    {
        try
        {
            $country = Country::where('id',$request->country_id)->first();
            if($request->is_active == 1)
            {
                $country->is_active = $request->is_active;
                $msg = "Country Activated Successfully!";
            }
            else
            {
                $country->is_active = $request->is_active;
                $msg = "Country Deactivated Successfully!";
            }
            $country->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        catch(\Exception $ex)
        {
            return view('errors.500');
        }
    }

    public function countryAddView()
    {
        return view('admin.settings.country.add');
    }

    public function addCountry(Request $request)
    {
        try
        {
            $messsages = array(
                'name.required' => 'Please enter country name'
            );

            $validator = Validator::make($request->all(), [
                'name'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }

            $errors = $this->checkAvailableCountries($request);

            if (count($errors) > 0)
            {
                $notification = array(
                    'message' => 'Country already exists!',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification)->withInput();
            }

            $country = new Country;
            $country->name = $request->name;
            $country->is_active = $request->is_active;
            $country->save();

            $notification = array(
                'message' => 'Country added successfully!',
                'alert-type' => 'success'
            );
            return redirect('admin/countries')->with($notification);
        }
        catch (\Exception $e)
        {
                Session::flash('error', $e->getMessage());
            return redirect('admin/countries');
        }
    }

    protected function checkAvailableCountries($request)
    {
        $countries = Country::get()->whereNull('deleted_at')->toArray();

        $errors = array();
        if (count($countries) > 0)
        {
            $data = $request->all();
            $i = 1;
            foreach ($countries as $country)
            {
                if (strtolower($country['name']) == strtolower($data['name']))
                {
                    if(isset($data['country_id']))
                    {
                        if($country['id'] != $data['country_id'])
                        {
                            $errors[] = 'Country already added';
                        }
                    }
                    else
                    {
                        $errors[] = 'Country already added';
                    }
                }
            }
        }
        return $errors;
    }

    public function countryEditView($id)
    {
        $country = Country::findOrFail($id);
        if(!empty($country))
        {
            return view('admin.settings.country.edit',compact('country'));
        }
    }

    public function updateCountry(Request $request)
    {
        $country = Country::findOrFail($request->country_id);

        if(!empty($country))
        {
            $messsages = array(
                'name.required' => 'Please select language'
            );

            $validator = Validator::make($request->all(), [
                'name'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }

            $errors = $this->checkAvailableCountries($request);

            if (count($errors) > 0)
            {
                $notification = array(
                    'message' => 'Country already exists!',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification)->withInput();
            }

            $country->name = $request->name;
            $country->is_active = $request->is_active;
            $country->save();

            $notification = array(
                'message' => 'Country updated successfully!',
                'alert-type' => 'success'
            );

            return redirect('admin/countries')->with($notification);
        }
    }

    /** Footer details functions */
    public function footerDetailsView()
    {
        $social_links = FooterLinks::first();
        $total_languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                            ->select('global_language.id','alpha2','langEN')
                            ->where('is_deleted',0)
                            ->where('status',1)
                            ->get();
        $footerData = [];
        foreach ($total_languages as $key => $lang)
        {
            $fdetails = FooterDetails::where('language_id',$lang['id'])->first();
            if(!empty($fdetails))
            {
                $footerData[] = $fdetails;
            }
        }
        $baseUrl = $this->getBaseUrl();
        $home_page_component = \App\Models\HomePageComponent::get();
        $home_page_mobile_app = \App\Models\HomePageMobileApp::get();
        $aramex_config = \App\Models\AramexConfig::first();
        $defaultLang = $this->getDefaultLanguage();
        $events = \App\Models\Events::where('language_id', $defaultLang)->whereNull('deleted_at')->get();
        $defaultCurr = \App\Models\GlobalCurrency::select('currency.currency_code')
        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
        ->where('global_currency.is_default',1)->where('global_currency.is_deleted', 0)->first();
        $settings = \App\Models\Settings::first();
        return view('admin.settings.footerDetails.footerDetails',compact('social_links','footerData','total_languages','home_page_component',
        'home_page_mobile_app','baseUrl','defaultCurr','settings','aramex_config','events'));
    }

    public function updateFooterDetails(Request $request)
    {
        if($request->page_name == 'footer_details')
        {
            $this->updateFooterData($request);
            $notification = array(
                'message' => 'Footer details updated successfully!',
                'alert-type' => 'success'
            );
        }
        if($request->page_name == 'social_links')
        {
            $this->updateSocialLinks($request);
            $notification = array(
                'message' => 'Social links updated successfully!',
                'alert-type' => 'success'
            );
        }
        return redirect()->back()->with($notification);
    }

    public function updateFooterData($request)
    {
        // dd($request);
        if(isset($request->lang_id))
        {
            foreach ($request->lang_id as $key)
            {
                $footerDetails = FooterDetails::where('language_id',$key)->first();
                if(!empty($footerDetails))
                {
                    $footerDetails->language_id = $key;
                    $footerDetails->contact_email = $request->contact_email[$key];
                    $footerDetails->about_us = $request->about_us[$key];
                    $footerDetails->contact_number = $request->contact_number[$key];
                    $footerDetails->whatsapp_number = $request->whatsapp_number[$key];
                    $footerDetails->save();
                }
                else
                {
                    $footerDetails = new FooterDetails;
                    $footerDetails->language_id = $key;
                    $footerDetails->contact_email = $request->contact_email[$key];
                    $footerDetails->about_us = $request->about_us[$key];
                    $footerDetails->contact_number = $request->contact_number[$key];
                    $footerDetails->whatsapp_number = $request->whatsapp_number[$key];
                    $footerDetails->save();
                }
            }
        }

    }

    public function updateSocialLinks($request)
    {
        if(isset($request->social_id))
        {
            $social_link = FooterLinks::findOrFail($request->social_id);
            if(!empty($social_link))
            {
                $social_link->fb_link = $request->fb_link;
                $social_link->insta_link = $request->insta_link;
                $social_link->youtube_link = $request->youtube_link;
                $social_link->twitter_link = $request->twitter_link;
                $social_link->save();
            }
        }
        else
        {
            $social_link = new FooterLinks;
            $social_link->fb_link = $request->fb_link;
            $social_link->insta_link = $request->insta_link;
            $social_link->youtube_link = $request->youtube_link;
            $social_link->twitter_link = $request->twitter_link;
            $social_link->save();
        }
    }

    public function homePageCompActDeact(Request $request)
    {
        $home_page_comp = \App\Models\HomePageComponent::where('id', $request->home_page_com_id)->first();
        if($home_page_comp)
        {
            $home_page_comp->is_active = $request->home_page_act_deact;
            $home_page_comp->save();
            $dynamic_msg = ($home_page_comp->is_active == 1) ? "Home page component activated successfully" : "Home page component deactivated successfully";
            $dynamic_switch_change = ($home_page_comp->is_active == 1) ? "true" : "false";
            $result['status'] = 'true';
            $result['msg'] = $dynamic_msg;
            $result['switch_status'] = $dynamic_switch_change;
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = 'failed';
            return $result;
        }
    }

    public function homePageMobileAppActDeact(Request $request)
    {
        $home_page_mobile_app = \App\Models\HomePageMobileApp::where('id', $request->home_page_ma_id)->first();
        if($home_page_mobile_app)
        {
            $home_page_mobile_app->is_active = $request->home_page_ma_act_deact;
            $home_page_mobile_app->save();
            $dynamic_msg = ($home_page_mobile_app->is_active == 1) ? "Home page mobile app activated successfully" : "Home page mobile app deactivated successfully";
            $dynamic_switch_change = ($home_page_mobile_app->is_active == 1) ? "true" : "false";
            $result['status'] = 'true';
            $result['msg'] = $dynamic_msg;
            $result['switch_status'] = $dynamic_switch_change;
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = 'failed';
            return $result;
        }
    }

    public function addHomePageMobileAppImage(Request $request)
    {
        if($request->home_page_mobile_app_image)
        {
            $counter = 0;
            foreach ($request->home_page_mobile_app_image as $key => $value) {                
                $image = $request->file('home_page_mobile_app_image')[$key];
                $ext = $value->extension();
                $filename = "mobile_app_".rand().'_'.time().'.'.$ext;
    
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(config('app.home_page_mobile_app_image.width'), config('app.home_page_mobile_app_image.height'));
                $image_resize->save(public_path('/assets/images/general-settings/mobile-app/'.$filename));
    
                $home_page_mobile_app = \App\Models\HomePageMobileApp::where('id', $key)->first();
                if($home_page_mobile_app->image)
                {
                    $path = public_path('/assets/images/general-settings/mobile-app').'/'.$home_page_mobile_app->image;
                    if(file_exists($path))
                    {
                        unlink($path);
                    }
                }
                $home_page_mobile_app->image = $filename;
                $home_page_mobile_app->event_id = $request->event_id[$counter];
                $home_page_mobile_app->is_active = 1;
                $home_page_mobile_app->save();
                $counter++;
            }
        }
        else
        {
            foreach ($request->hpma_id as $key => $id) {                
                $home_page_mobile_app = \App\Models\HomePageMobileApp::where('id', $id)->first();                
                $home_page_mobile_app->event_id = $request->event_id[$key];
                $home_page_mobile_app->is_active = 1;
                $home_page_mobile_app->save();
            }                   
        }
        
        $notification = array(
            'message' => "Image store successfully",
            'alert-type' => 'success'
        );
        return redirect('/admin/footerDetails')->with($notification);
    }

    public function addShippingCost(Request $request)
    {
        $settings = \App\Models\Settings::where('id', $request->settings_id)->first();
        if($settings)
        {
            $settings->min_order_amt = $request->min_order_amt;
            $settings->shipping_cost = $request->shipping_cost;
            $settings->min_qty = $request->min_qty;
            $settings->delivery_days = $request->delivery_days;
            $settings->delivery_days_exceed_min_qty = $request->delivery_days_exceed_min_qty;
            $settings->save();
            $notification = array(
                'message' => config('message.ShippingCost.ShipCostAddSucc'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }

    public function saveAramexConfig(Request $request)
    {
        $aramex_config = \App\Models\AramexConfig::where('id', $request->aramex_cng_id)->first();
        if($aramex_config)
        {
            $aramex_config->contact_name = $request->contact_name;
            $aramex_config->company_name = $request->company_name;
            $aramex_config->line_1 = $request->line_1;
            $aramex_config->line_2 = $request->line_2;
            $aramex_config->city = $request->city;
            $aramex_config->country_code = $request->country_code;
            $aramex_config->phone_extension = $request->phone_ext;
            $aramex_config->phone = $request->phone_number;
            $aramex_config->email = $request->email;
            $aramex_config->save();
            $notification = array(
                'message' => config('message.AramexConfig.ConfigSetSucc'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }

    public function addMultipleAdminEmails(Request $request)
    {
        $settings = \App\Models\Settings::where('id', $request->settings_id)->first();
        if($settings)
        {
            $settings->email = $request->admin_emails;
            $settings->save();
            $notification = array(
                'message' => config('message.AdminEmail.EmailAddSucc'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }        
    }
}
?>
