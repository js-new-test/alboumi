<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\FooterGenerator;
use App\Models\CmsPagesDetails;
use App\Models\CmsPages;
use App\Models\FooterLinks;
use App\Models\Locale;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\MegaMenu;
use DB;
use App\Traits\ReuseFunctionTrait;

class FooterController extends Controller
{
    use ReuseFunctionTrait;

    protected $megamenu;

	public function __construct(MegaMenu $megamenu) {
        $this->megamenu = $megamenu;
    }
    public function getFooterLinks()
    {
        $language_id = Session::get('language_id');
        $footerData = FooterGenerator::getFooterData($language_id);
        $socialLinks = FooterLinks::getSocialLinks();

        $codes = ['FOLLOWUS', 'DOWNLOADAPP'];
        $footerLabels = getCodesMsg($language_id, $codes);
        $baseUrl = $this->getBaseUrl();
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $setSessionforLang=setSessionforLang($defaultLanguageData['language_id']);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        return response()->json(['status' => true,'footerData' => $footerData,'socialLinks' => $socialLinks, 'footerLabels' => $footerLabels]);
    }
    public function getAboutUsPageContents()
    {
        $slug = request()->segment(count(request()->segments()));

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultLangId = $defaultLanguageData['id'];

        $setSessionforLang=setSessionforLang($defaultLanguageData['language_id']);
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $lang_id = Session::get('language_id');
        
        $langCode = Session::get('language_code');
        $langName = Session::get('language_name');

        $codes = ['APPNAME','ABOUTUS'];
        $footerLabels = getCodesMsg($lang_id, $codes);

        $pageName = $footerLabels['ABOUTUS'];
        $projectName = $footerLabels['APPNAME'];

        $aboutData = CmsPages::select('banner_text','title','description','seo_title','seo_description','seo_keyword','cms_pages.banner_image as cms_banner'
                                    ,'cms_pages.mobile_banner_image as cms_mobile_banner','cd.banner_image','cd.mobile_banner')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug',$slug)
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
                                    ->where('slug',$slug)
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('status',1)
                                    ->where('language_id',$defaultLanguageData['id'])
                                    ->first();
        }
        if($aboutData == null)
        {
            return redirect('/');
        }
        $baseUrl = $this->getBaseUrl();
        return view('frontend/footerPages/about',compact('pageName','projectName','aboutData','megamenuFileName','lang_id','langCode','langName','baseUrl','mobileMegamenuFileName','defaultLangId'));
    }

}

?>
