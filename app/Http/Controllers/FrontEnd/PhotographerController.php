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
use App\Models\Photographers;
use App\Models\CmsPages;
use Exception;
use Auth;
use Mail;
use Socialite;
use Agent;
use Illuminate\Support\Facades\Session;
use App\Traits\ReuseFunctionTrait;

class PhotographerController extends Controller
{
  use ReuseFunctionTrait;
  public function photographerProfile(Request $request)
  {
      $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
      $defaultLanguageId = $defaultLanguageData['id'];
      //session(['language_id' => '1']);
      // Session::put('language_id',$defaultLanguageId);
      $setSessionforLang=setSessionforLang($defaultLanguageId);

      $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
      $defaultCurrId = $defaultCurrData['id'];
      $setSessionforCurr = setSessionforCurr($defaultCurrId);

      $codes = ['PORTFOLIO','APPNAME'];
      $profileLabels = getCodesMsg(Session::get('language_id'), $codes);
      $pageName = "Photographer";
      $projectName = $profileLabels['APPNAME'];
      $photgrapherID=$request->id;
      //$freeDeliveryMsg = Locale::getFreeDeliveryMsg($defaultLanguageId);

      $baseUrl = $this->getBaseUrl();
      $lang_id= Session::get('language_id');

      $photographerDetials = \App\Models\Photographers::getPhotographerById($photgrapherID,$lang_id);
      if(empty($photographerDetials)){
        $photographerDetials = \App\Models\Photographers::getPhotographerById($photgrapherID,$defaultLanguageId);
      }
      $portfolioCount=\App\Models\PhotographerPortfolio::getPortfolioCount($photgrapherID);
      $portfolioArr=\App\Models\PhotographerPortfolio::getPortfolioByPhotographer($photgrapherID);

      $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
      $megamenuFileName = "megamenu_".Session::get('language_code');
      $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
      if(!empty($photographerDetials)){
        return view('frontend.photographers.profile', compact('baseUrl','pageName','projectName','photographerDetials','portfolioArr','portfolioCount','megamenuFileName','profileLabels','mobileMegamenuFileName'));
      }
      else{
				return abort(404);
		  }
  }

    public function getPhotographersListing()
    {
        $codes = ['APPNAME','BAHRAINPHOTOGRAPHER'];
        $eventsLabels = getCodesMsg(Session::get('language_id'), $codes); 

        // $pageName = $eventsLabels['BAHRAINPHOTOGRAPHER'];
        $projectName = $eventsLabels['APPNAME'];

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $setSessionforLang=setSessionforLang($defaultLanguageData['language_id']);
      
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);
        $defaultLangId = $defaultLanguageData['id'];

        $langId = Session::get('language_id');
        $langVisibility = GlobalLanguage::checkVisibility($langId);

        $photographersData = Photographers::getPhotographers($langId);

        $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner',
                                    'seo_title','seo_description','seo_keyword','cd.banner_image','cd.mobile_banner')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug','made-in-bahrain')
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('language_id',$langId)
                                    ->first();


        $baseUrl = $this->getBaseUrl();

        if($cmsPageDetails == null)
        {
            $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner',
                                    'seo_title','seo_description','seo_keyword','cd.banner_image','cd.mobile_banner')
                                    ->where('slug','made-in-bahrain')
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('language_id',$defaultLanguageData['id'])
                                    ->first();
        }
        $pageName = $cmsPageDetails['seo_title'];
        return view ('frontend/photographers/listing',compact('photographersData','pageName','projectName','cmsPageDetails','megamenuFileName','baseUrl','mobileMegamenuFileName','langVisibility','defaultLangId'));
    }
}
