<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPages;
use App\Models\CmsPagesDetails;
use App\Models\GlobalLanguage;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;
use Image;
use Config;

class CmsController extends Controller
{
    use CommonTrait;

    public function getCmsPages()
    {
        $languages = GlobalLanguage::getAllLanguages();
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        return view('admin.cmsPages.index',compact('otherLanguages','languages'));
    }

    public function getCmsPagesList(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        DB::statement(DB::raw('set @rownum=0'));

        $cmsPages = CmsPages::select('cms_pages.id','title','slug','status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(cd.created_at,'%Y-%m-%d %h:%i:%s') as a_created_at"))
                            ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                            ->where('cd.language_id', $defaultLanguageId)
                            ->whereNull('cms_pages.deleted_at')
                            ->whereNull('cd.deleted_at')
                            ->orderBy('cd.updated_at','desc')
                            ->get();

        if($request['lang_id'] != null)
        {
            DB::statement(DB::raw('set @rownum=0'));

            $cmsPages = CmsPages::select('cms_pages.id','title','slug','status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(cd.created_at,'%Y-%m-%d %h:%i:%s') as a_created_at"))
                            ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                            ->where('language_id',$request['lang_id'])
                            ->whereNull('cms_pages.deleted_at')
                            ->whereNull('cd.deleted_at')
                            ->orderBy('cd.updated_at','desc')
                            ->get();
        }
        return Datatables::of($cmsPages)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    public function pageAddView()
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];
        $formTitle = "Add CMS Page";

        $page = isset($_GET['page']) ? $_GET['page'] : "addPage";
        $pageId = isset($_GET['pageId']) ? $_GET['pageId'] : "";

        if (!empty($pageId))
        {
            $cmsPage = CmsPagesDetails::where(['cms_id'=> $pageId, 'language_id'=>$defaultLanguageId])
                                                ->whereNull('deleted_at')
                                                ->first();
            $pageName = $cmsPage->title;
        }

        $language = "";
        if ($page == "anotherLanguage")
        {
            $existingLanguageIds = CmsPagesDetails::where('cms_id', $pageId)
                                                        ->whereNull('deleted_at')
                                                        ->get()
                                                        ->pluck('language_id')
                                                        ->toArray();

            $language = GlobalLanguage::select('global_language.id as globalLanguageId', 'world_languages.langEN as languageName')
                                    ->Join('world_languages', 'world_languages.id', '=', 'global_language.language_id')
                                    ->whereNotIn('global_language.id', $existingLanguageIds)
                                    ->where('global_language.status', 1)
                                    ->where('is_deleted',0)
                                    ->get()->toArray();

            $formTitle = "Add CMS Page - Other Language ($pageName)";
        }
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $baseUrl = $this->getBaseUrl();
        return view('admin.cmsPages.add',compact('defaultLanguage','defaultLanguageId','pageId','language','formTitle','otherLanguages','page','baseUrl'));
    }

    public function addPage(Request $request)
    {
        if (isset($request->pageId))
        {
            $page_details = new CmsPagesDetails;
            $page_details->cms_id = $request->pageId;
            $page_details->language_id = $request->defaultLanguage;
            $page_details->title = $request->title;
            $page_details->description = $request->description;
            $page_details->seo_title = $request->seo_title;
            $page_details->seo_description = $request->seo_description;
            $page_details->seo_keyword = $request->seo_keyword;

            $reqdBannerWidth =Config::get('app.cms_banner_image.width');
            $reqdBannerHeight =Config::get('app.cms_banner_image.height');
            if($request->hasFile('banner_image'))
            {
                if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                {
                    $image       = $request->file('banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                    $image->move(public_path('assets/images/cms/banner/'), $filename);
                    $image_resize->save(public_path('assets/images/cms/banner/' .$filename));
                    $page_details->banner_image = $filename;
                }
                else
                {
                    $image = $request->file('banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/cms/banner/'), $filename);
                    $page_details->banner_image = $request->file('banner_image')->getClientOriginalName();
                }
            }
            $reqdMobBannerWidth =Config::get('app.cms_mobile_banner_image.width');
            $reqdMobBannerHeight =Config::get('app.cms_mobile_banner_image.height');
            if($request->hasFile('mobile_banner_image'))
            {
                if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                {
                    $image       = $request->file('mobile_banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                    $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                    $image_resize->save(public_path('assets/images/cms/mobile_banner/' .$filename));
                    $page_details->mobile_banner = $filename;
                }
                else
                {
                    $image = $request->file('mobile_banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                    $page_details->mobile_banner = $request->file('mobile_banner_image')->getClientOriginalName();
                }
            }
            
            $page_details->save();
        }
        else
        {
            $cms_page = new CmsPages;

            $validator = Validator::make(['slug' => $request->slug], [
                'slug' => "required|unique:cms_pages,slug,NULL,id,deleted_at,NULL,status,1",
            ]);

            // $validator->sometimes('external_id', 'required|unique:cms_pages,slug,NULL,id,deleted_at,NULL', function($input) {
            //     return $input->status == 0;
            // });

            if ($validator->fails())
            {
                $notification = array(
                    'message' => 'Slug already exists !!',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification)->withInput();
            }
            $cms_page->slug = $request->slug;
            /* Code for banner and mobile banner :Nivedita(11-01-2021)*/
            $reqdBannerWidth =Config::get('app.cms_banner_image.width');
            $reqdBannerHeight =Config::get('app.cms_banner_image.height');
            if($request->hasFile('banner_image'))
            {
                if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                {
                    $image       = $request->file('banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                    $image->move(public_path('assets/images/cms/banner/'), $filename);
                    $image_resize->save(public_path('assets/images/cms/banner/' .$filename));
                    $cms_page->banner_image = $filename;
                }
                else
                {
                    $image = $request->file('banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/cms/banner/'), $filename);
                    $cms_page->banner_image = $request->file('banner_image')->getClientOriginalName();
                }
            }
            $reqdMobBannerWidth =Config::get('app.cms_mobile_banner_image.width');
            $reqdMobBannerHeight =Config::get('app.cms_mobile_banner_image.height');
            if($request->hasFile('mobile_banner_image'))
            {
                if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                {
                    $image       = $request->file('mobile_banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                    $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                    $image_resize->save(public_path('assets/images/cms/mobile_banner/' .$filename));
                    $cms_page->mobile_banner_image = $filename;
                }
                else
                {
                    $image = $request->file('mobile_banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                    $cms_page->mobile_banner_image = $request->file('mobile_banner_image')->getClientOriginalName();
                }
            }
            /* Code end for banner and mobile banner :Nivedita(11-01-2021)*/
            $cms_page->status = $request->status;
            $cms_page->display_on = $request->display_on;
            if($cms_page->save())
            {
                $page_details = new CmsPagesDetails;
                $page_details->cms_id = $cms_page->id;
                $page_details->language_id = $request->defaultLanguageId;
                $page_details->title = $request->title;
                $page_details->description = $request->description;
                $page_details->seo_title = $request->seo_title;
                $page_details->seo_description = $request->seo_description;
                $page_details->seo_keyword = $request->seo_keyword;
                $page_details->save();
            }
        }

        $notification = array(
            'message' => 'Page added successfully!',
            'alert-type' => 'success'
        );

        return redirect('admin/cmsPages')->with($notification);
    }

    public function pageEditView($id)
    {

        $page = isset($_GET['page']) ? $_GET['page'] : "getDefaultLangData";
        $lang_id = isset($_GET['lang']) ? $_GET['lang'] : "defaultLang";

        $nonDefaultLanguage = CmsPagesDetails::select('cms_details.language_id','langEN')
                                            ->join('global_language as gl','gl.id','=','cms_details.language_id')
                                            ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                                            ->whereNull('cms_details.deleted_at')
                                            ->where('cms_id',$id)
                                            ->get()->toArray();

        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;
        $baseUrl = $this->getBaseUrl();
        $cmsDetails = [];

        if($page != 'otherLang')
        {
            $cmsDetails = CmsPages::select('cms_pages.id as id', 'slug as slug', 'cms_pages.status as status','display_on',
                                    'cd.title as title', 'cd.language_id as languageId','cd.description','cd.seo_title',
                                    'cd.seo_description','cd.seo_keyword','cms_pages.banner_image','cms_pages.mobile_banner_image')
                                    ->join('cms_details as cd', 'cd.cms_id', '=', 'cms_pages.id')
                                    ->findOrFail($id);

            if(!empty($cmsDetails))
            {
                $cmsDetails = $cmsDetails->toArray();
            }
        }
        else
        {
            $cmsDetails = CmsPages::select('cms_pages.id as id', 'slug as slug', 'cms_pages.status as status',
                                    'cd.title as title', 'cd.language_id as languageId','cd.description','cd.seo_title',
                                    'cd.seo_description','cd.seo_keyword','cd.banner_image','cd.mobile_banner',
                                    'cms_pages.banner_image as cms_banner','cms_pages.mobile_banner_image as cms_mobile')
                                    ->join('cms_details as cd', 'cd.cms_id', '=', 'cms_pages.id')
                                    ->where('language_id',$lang_id)
                                    ->whereNull('cd.deleted_at')
                                    ->where('cms_id',$id)
                                    ->first($id);

            if(!empty($cmsDetails))
            {
                $cmsDetails = $cmsDetails->toArray();
                return response()->json(['status' => true, 'cmsDetails' => $cmsDetails]);
            }
        }

        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $formTitle = "Edit Page";
        return view('admin.cmsPages.edit',compact('cmsDetails','nonDefaultLanguage','defaultLanguageId','otherLanguages','formTitle','baseUrl'));
    }

    public function updatePage(Request $request)
    {
        $page = CmsPages::findOrFail($request->page_id);
        if (isset($page))
        {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $defaultLanguageId = $defaultLanguageData['id'];

            $validator = Validator::make(['slug' => $request->slug],[
                'slug' => "required|unique:cms_pages,slug,".$request->page_id.",id,deleted_at,NULL"
                ]);

            if ($validator->fails())
            {
                $notification = array(
                    'message' => 'Slug already exists !!',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification)->withInput();
            }
            $page->slug = $request->slug;
            if($request->language_id == $defaultLanguageId)
            {
                /* Code for banner and mobile banner :Nivedita(11-01-2021)*/
                $reqdBannerWidth =Config::get('app.cms_banner_image.width');
                $reqdBannerHeight =Config::get('app.cms_banner_image.height');
                if($request->hasFile('banner_image'))
                {
                    if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                    {
                        $image       = $request->file('banner_image');
                        $filename    = $image->getClientOriginalName();

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                        $image->move(public_path('assets/images/cms/banner/'), $filename);
                        $image_resize->save(public_path('assets/images/cms/banner/' .$filename));
                        $page->banner_image = $filename;
                    }
                    else
                    {
                        $image = $request->file('banner_image');
                        $filename = $image->getClientOriginalName();
                        $image->move(public_path('assets/images/cms/banner/'), $filename);
                        $page->banner_image = $request->file('banner_image')->getClientOriginalName();
                    }
                }
                $reqdMobBannerWidth =Config::get('app.cms_mobile_banner_image.width');
                $reqdMobBannerHeight =Config::get('app.cms_mobile_banner_image.height');
                if($request->hasFile('mobile_banner_image'))
                {
                    if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                    {
                        $image       = $request->file('mobile_banner_image');
                        $filename    = $image->getClientOriginalName();

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                        $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                        $image_resize->save(public_path('assets/images/cms/mobile_banner/' .$filename));
                        $page->mobile_banner_image = $filename;
                    }
                    else
                    {
                        $image = $request->file('mobile_banner_image');
                        $filename = $image->getClientOriginalName();
                        $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                        $page->mobile_banner_image = $request->file('mobile_banner_image')->getClientOriginalName();
                    }
                }
                /* Code end for banner and mobile banner :Nivedita(11-01-2021)*/
            }

            $page->status = $request->status;
            $page->display_on = $request->display_on;
            $page->save();
            
            $pageDetails = CmsPagesDetails::where(['cms_id'=>$request->page_id, 'language_id'=>$request->language_id])->whereNull('deleted_at')->first();
            if (!empty($pageDetails))
            {
                $pageDetails->cms_id = $request->page_id;
                $pageDetails->language_id = $request->language_id;
                $pageDetails->title = $request->title;
                $pageDetails->description = $request->description;
                $pageDetails->seo_title = $request->seo_title;
                $pageDetails->seo_description = $request->seo_description;
                $pageDetails->seo_keyword = $request->seo_keyword;

                if($request->language_id != $defaultLanguageId)
                {
                    $reqdBannerWidth =Config::get('app.cms_banner_image.width');
                    $reqdBannerHeight =Config::get('app.cms_banner_image.height');
                    if($request->hasFile('banner_image'))
                    {
                        if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                        {
                            $image       = $request->file('banner_image');
                            $filename    = $image->getClientOriginalName();
    
                            $image_resize = Image::make($image->getRealPath());
                            $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                            $image->move(public_path('assets/images/cms/banner/'), $filename);
                            $image_resize->save(public_path('assets/images/cms/banner/' .$filename));
                            $pageDetails->banner_image = $filename;
                        }
                        else
                        {
                            $image = $request->file('banner_image');
                            $filename = $image->getClientOriginalName();
                            $image->move(public_path('assets/images/cms/banner/'), $filename);
                            $pageDetails->banner_image = $request->file('banner_image')->getClientOriginalName();
                        }
                    }
                    $reqdMobBannerWidth =Config::get('app.cms_mobile_banner_image.width');
                    $reqdMobBannerHeight =Config::get('app.cms_mobile_banner_image.height');
                    if($request->hasFile('mobile_banner_image'))
                    {
                        if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                        {
                            $image       = $request->file('mobile_banner_image');
                            $filename    = $image->getClientOriginalName();
        
                            $image_resize = Image::make($image->getRealPath());
                            $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                            $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                            $image_resize->save(public_path('assets/images/cms/mobile_banner/' .$filename));
                            $pageDetails->mobile_banner = $filename;
                        }
                        else
                        {
                            $image = $request->file('mobile_banner_image');
                            $filename = $image->getClientOriginalName();
                            $image->move(public_path('assets/images/cms/mobile_banner/'), $filename);
                            $pageDetails->mobile_banner = $request->file('mobile_banner_image')->getClientOriginalName();
                        }
                    }
                }
                $pageDetails->save();
                // dd($pageDetails);
            }

            $notification = array(
                'message' => 'Page updated successfully!',
                'alert-type' => 'success'
            );
            return redirect('admin/cmsPages')->with($notification);
        }
    }

    public function pageActiveInactive(Request $request)
    {
        try
        {
            $page = CmsPages::where('id',$request->pageId)->first();
            if($request->is_active == 1)
            {
                $page->status = $request->is_active;
                $msg = "Page Activated Successfully!";
            }
            else
            {
                $page->status = $request->is_active;
                $msg = "Page Deactivated Successfully!";
            }
            $page->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        catch(\Exception $ex)
        {
            return view('errors.500');
        }
    }

    public function getLanguageWisePage(Request $request)
    {
        $arrResult = [];

        $pageDetails = CmsPagesDetails::with('globalLanguage')
                                            ->where('cms_id',$request['pageId'])
                                            ->whereNull('cms_details.deleted_at')
                                            ->get();
        $index = 0;
        foreach ($pageDetails as $key)
        {
            $arrResult[$index]['id'] = $key->id;
            $arrResult[$index]['pageName'] = $key->title;
            $arrResult[$index]['languageName'] = $key['globalLanguage']['language']->langEN;
            $arrResult[$index++]['isDefault'] = $key['globalLanguage']->is_default;
        }

        return DataTables::of($arrResult)->make();
    }

    public function deletePage(Request $request)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);

        $page = CmsPagesDetails::find($request->pageDetailId);
        if ($page->language_id == $defaultLanguageData['id'])
        {
            $temp = ['deleted_at'=>now()];
            $updatePage = CmsPagesDetails::where('cms_id', $page->cms_id)->update($temp);
            $pageDelete = CmsPages::find($page->cms_id);
            $pageDelete->deleted_at = now();
            if ($pageDelete->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Page Deleted Successfully!!')
                );
            }
        }
        else
        {
            $page->deleted_at = now();
            if ($page->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Page Deleted Successfully!!')
                );
            }
        }
    }

    public function uploadCMSPageImage(Request $request)
    {
        $folder_name = 'ckeditor-cms-page-image';
        uploadCKeditorImage($request, $folder_name);
    }
}
?>
