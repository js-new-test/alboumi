<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\CmsPages;
use App\Models\GlobalCurrency;
use App\Models\Currency;
use App\Models\PhotoBooks;
use DB;
use Validator;
use App\Traits\CommonTrait;
use Illuminate\Pagination\Paginator;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PhotoBookController extends Controller
{
    use CommonTrait;
	protected $photobooks;

	public function __construct(PhotoBooks $photobooks) {
        $this->photobooks = $photobooks;
    }

    public function getBookDetails()
    {

      $pageName = "Photo Books";
      $projectName = "Alboumi";

      $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
      $setSessionforLang=setSessionforLang($defaultLanguageData['language_id']);
        $defaultLangId = $defaultLanguageData['id']; 

      $megamenuFileName = "megamenu_".Session::get('language_code');
      $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

      $decimalNumber=Session::get('decimal_number');
      $decimalSeparator=Session::get('decimal_separator');
      $thousandSeparator=Session::get('thousand_separator');

      $langId = Session::get('language_id');

      $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$langId)->first();

      $currencyCode =getCurrSymBasedOnLangId($langId);

      $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
      $decimalNumber=$currenyIdFromLang->decimal_number;
      $decimalSeparator=$currenyIdFromLang->decimal_separator;
      $thousandSeparator=$currenyIdFromLang->thousand_separator;

      $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
      $defaultCurrId = $defaultCurrData['id'];
      $setSessionforCurr = setSessionforCurr($defaultCurrId);
      $langVisibility = GlobalLanguage::checkVisibility($langId);

      $booksData = PhotoBooks::getBooks($langId);

      $codes = ['AMZPHOTOBOOKS', 'RECOMMENDEDCAT', 'SELECTBOOK','FROM'];

      $bookDetailLabels = getCodesMsg($langId, $codes);

      $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner',
                                    'cd.banner_image','cd.mobile_banner','seo_title','seo_description','seo_keyword')
                                  ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                  ->where('slug','photo-book')
                                  ->whereNull('cd.deleted_at')
                                  ->whereNull('cms_pages.deleted_at')
                                  ->where('language_id',$langId)
                                  ->first();


      $baseUrl = $this->getBaseUrl();

      if($cmsPageDetails == null)
      {
          $cmsPageDetails = CmsPages::select('title','description','cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile_banner',
                                    'cd.banner_image','cd.mobile_banner','seo_title','seo_description','seo_keyword')
                                  ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                  ->where('slug','photo-book')
                                  ->whereNull('cd.deleted_at')
                                  ->whereNull('cms_pages.deleted_at')
                                  ->where('language_id',$defaultLanguageData['id'])
                                  ->first();
      }
      $pageName = $cmsPageDetails->seo_title;
      return view ('frontend/photobook/photoBookDetails',compact('booksData','bookDetailLabels','pageName','projectName','cmsPageDetails','megamenuFileName','baseUrl','mobileMegamenuFileName','langVisibility','conversionRate','currencyCode','decimalNumber','decimalSeparator','thousandSeparator','defaultLangId'));
    }
}
?>
