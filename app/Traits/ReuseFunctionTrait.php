<?php
/**
 * Created by PhpStorm.
 * User: Rima Panchal
 * Date: 3/19/2015
 * Time: 12:38 PM
 */
namespace App\Traits;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use App\Models\Locale;
use App\Models\LocaleDetails;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateDetails;
use DB;

trait ReuseFunctionTrait
{

    /**
     * Generate a random string
     * @param $col
     * @param null $title
     * @return string
     */
    public static function generateRandomString($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }

    /**
     * Display short description
     * @param $fullDescription
     * @return string
     */
    public static function shortDescription($fullDescription, $initialCount = 125)
    {
        $shortDescription = "";
        $fullDescription = trim(strip_tags($fullDescription));
        if ($fullDescription) {

            if (strlen($fullDescription) > $initialCount) {
                $shortDescription = substr($fullDescription, 0, $initialCount) . "...";
                return $shortDescription;
            } else {
                return $fullDescription;
            }
        }
    }

    /** Developed by : Pallavi */
    // public static function getLocaleDetailsForLang($codes, $language_id)
    // {
    //     // echo "in trait"; die;
    //     // $codes = $request->codes;
    //     // $request->language_id = 1;
    //     // DB::enableQueryLog();
    //     $locals = Locale::select('id','code');
    //                             foreach ($codes as $code)
    //                             {
    //                                 $locals->orWhere('code',$code);
    //                             }
    //     $locals = $locals->get();
    //     // dd(DB::getQueryLog());
    //     // DB::enableQueryLog();

    //     foreach($locals as $key => $locale)
    //     {
    //         $localsDetails[] = LocaleDetails::select('id','locale_id','value')
    //                                 ->where('language_id',$language_id)
    //                                 ->where('locale_id',$locale->id)
    //                                 ->first();
    //         $localsDetails[$key]['code'] = $locale->code;
    //     }
    //     // dd(DB::getQueryLog());
    //     // dd($localsDetails);
    //     return $localsDetails;

    // }

    /** Developed by : Pallavi */
    public static function getLocaleDetailsForLang($codes, $language_id)
    {
        $locals = Locale::select('locale.id','locale.code','locale.is_active','locale_details.value')
        ->leftJoin('locale_details', function($join) use($language_id)
         {
             $join->on('locale_details.locale_id', '=', 'locale.id');
             $join->where('locale_details.language_id','=', $language_id);
         })
        ->whereIn('code', $codes)
        ->get()
        ->toArray();

        $localsDetails = [];
        foreach($locals as $key => $locale)
        {
            if(!empty($locale['value']))
            {
                $localsDetails[$locale['code']] = $locale['value'];
            }
            else
            {
                $localsDetails[$locale['code']] = $locale['code'];
            }
        }
        return $localsDetails;
    }

    public function getEmailTemplatesForLang($request)
    {
                // echo "in trait"; die;

        // $request->code = 'forget-pwd';
        // $request->language_id = 1;

        $templateDetails = EmailTemplate::select('email_template.id','code','etd.value')
                                ->join('email_template_details as etd','etd.email_template_id','=','email_template.id')
                                ->where('code',$request->code)
                                ->where('language_id',$request->language_id)
                                ->first();
        // dd($templateDetails);
        return $templateDetails;

    }

    /** Developed by : Jignesh **/
    public function getEmailTemp($language_id = null)
    {
        if($language_id == null || $language_id == '' || !isset($language_id))
        {
            $lang = \App\Models\GlobalLanguage::where('is_default', 1)->first();
            if($lang)
            {
                $selected_lang = $lang->id;
            }
        }
        else
        {
            $lang = \App\Models\GlobalLanguage::where('id', $language_id)->first();
            if($lang)
            {
                $selected_lang = $lang->id;
            }
        }

        $email_template = \App\Models\EmailTemplate::select('email_template.code','email_template.title','email_template_details.value','email_template_details.email_template_id','email_template_details.language_id','email_template_details.subject')
        ->leftJoin('email_template_details','email_template_details.email_template_id','=','email_template.id')
        ->where('email_template_details.language_id',$selected_lang)
        ->get();
        return $email_template;
    }

    public function replaceHtmlContent($data,$html_value)
    {
        $html = $html_value;
        foreach ($data as $key => $value) {
            $html = str_replace($key, $value, $html);
        }
        return $html;
    }

    public function getDefaultLanguage()
    {
        $global_language = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_default', 1)
        ->where('is_deleted', 0)
        ->first();
        return $global_language->id;
    }

    /** Developed by : Jignesh **/
    public function getAllLocale($language_id)
    {
        $locale = \App\Models\Locale::leftJoin('locale_details','locale_details.locale_id','=','locale.id')
        ->where('locale_details.language_id', $language_id)->pluck('code');
        return $locale;
    }
}
