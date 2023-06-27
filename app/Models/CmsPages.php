<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CmsPages extends Model
{
    protected $table = 'cms_pages';

    protected $fillable = ['slug','status','deleted_at'];

    public static function getCmsPages($cmsId,$langId)
    {
        $allCmsPages = CmsPages::select('cms_pages.id','slug','cd.title')
                                        ->join('cms_details as cd','cd.cms_id','cms_pages.id')
                                        ->where('cd.language_id',$langId)
                                        ->where('cms_pages.id',$cmsId)
                                        ->whereNull('cms_pages.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('slug','!=','contact-us')
                                        ->where('status',1)
                                        ->get()->toArray();
        if(count($allCmsPages) == 0)
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            
            DB::enableQueryLog();
            $allCmsPages = CmsPages::select('cms_pages.id','slug','cd.title')
                                        ->join('cms_details as cd','cd.cms_id','cms_pages.id')
                                        ->where('cd.language_id',$defaultLanguageId)
                                        ->where('cms_pages.id',$cmsId)
                                        ->whereNull('cms_pages.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('slug','!=','contact-us')
                                        ->where('status',1)
                                        ->get()->toArray();
        }
        // dd($allCmsPages);
        return $allCmsPages;
    }

    public static function getContactUsCmsPage($langId)
    {
        $contactUsPage = CmsPages::select('cms_pages.id','slug','cd.title')
                                        ->join('cms_details as cd','cd.cms_id','cms_pages.id')
                                        ->where('cd.language_id',$langId)
                                        ->whereNull('cms_pages.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('slug','=','contact-us')
                                        ->where('status',1)
                                        ->first();
                                        // dd($contactUsPage);
        if($contactUsPage == null)
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            
            DB::enableQueryLog();
            $contactUsPage = CmsPages::select('cms_pages.id','slug','cd.title')
                                        ->join('cms_details as cd','cd.cms_id','cms_pages.id')
                                        ->where('cd.language_id',$defaultLanguageId)
                                        ->whereNull('cms_pages.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('slug','!=','contact-us')
                                        ->where('status',1)
                                        ->first()->toArray();
        }
        // dd($contactUsPage);
        return $contactUsPage;
    }

    public static function getCmsPagesForMobile($cmsId,$langId)
    {
        $allCmsPages = CmsPages::select('cms_pages.id','slug','cd.title')
                                        ->join('cms_details as cd','cd.cms_id','cms_pages.id')
                                        ->where('cd.language_id',$langId)
                                        ->where('cms_pages.id',$cmsId)
                                        ->whereNull('cms_pages.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('status',1)
                                        ->get()->toArray();
        if(count($allCmsPages) == 0)
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            
            DB::enableQueryLog();
            $allCmsPages = CmsPages::select('cms_pages.id','slug','cd.title')
                                        ->join('cms_details as cd','cd.cms_id','cms_pages.id')
                                        ->where('cd.language_id',$defaultLanguageId)
                                        ->where('cms_pages.id',$cmsId)
                                        ->whereNull('cms_pages.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('status',1)
                                        ->get()->toArray();
        }
        // print_r($allCmsPages);
        return $allCmsPages;
    }

}
