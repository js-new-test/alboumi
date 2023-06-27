<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use DataTables;
use Auth;
use DB;
use Image;
use App\Traits\ReuseFunctionTrait;

class BannerController extends Controller
{
    use ReuseFunctionTrait;
    /* ###########################################
    // Function: showAddBannerFomr
    // Description: Show banner add form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function listBanners(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)
            ->where('is_default', 1)
            ->get();
            if(count($global_languages_count) >= 2)
            {
                $global_languages_ids = $global_languages_count->pluck('id');
                $banners = \App\Models\Banners::select('id', 'status', 'text as banner',
                DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as bnr_created_at"))
                ->where('is_deleted', 0)
                ->whereIn('language_id', $global_languages_ids)
                ->get();
            }
            else
            {
                $global_language = \App\Models\GlobalLanguage::where('status',1)
                ->where('is_deleted', 0)
                ->where('is_default', 1)
                ->first();
                $banners = \App\Models\Banners::select('homepage_banners.id', 'homepage_banners.status', 'homepage_banners.text as banner',
                DB::raw("date_format(homepage_banners.created_at,'%Y-%m-%d %h:%i:%s') as bnr_created_at"))
                ->leftJoin('global_language','global_language.id','=','homepage_banners.language_id')
                ->where('homepage_banners.is_deleted', 0)
                ->where('homepage_banners.language_id', $global_language->id)
                ->get();
            }

            return Datatables::of($banners)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true);
        }

        $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        ->select('global_language.id','alpha2','langEN','is_default')
        ->where('status',1)
        ->where('is_deleted', 0)
        ->get();

        return view('admin.banner.list',compact('languages'));
    }

    /* ###########################################
    // Function: showAddBannerFomr
    // Description: Show banner add form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function showAddBannerFomr()
    {
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang);                
        if($global_languages_count >= 2)
        {
            $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status',1)
            ->where('global_language.is_deleted', 0)
            ->get();
            return view('admin.banner.add', compact('global_languages','categories'));
        }
        else
        {
            $global_language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->where('global_language.is_deleted', 0)
            ->first();
            return view('admin.banner.add', compact('global_language','categories'));
        }

    }

    /* ###########################################
    // Function: addBanner
    // Description: Add banner information
    // Parameter: banner_image: String, language: Int, banner_text: String, act_deact_chk_val: Int, banner_image: File
    // ReturnType: view
    */ ###########################################
    public function addBanner(Request $request)
    {
        try {
            $msg = [
                'banner_image_desktop.required' => "The desktop image is required.",
                'banner_image_mobile.required' => "The mobile image is required.",
                'language.required' => "The language is required.",
                'title.required' => "The title is required.",
                'link.required' => "The link is required.",
            ];
            $validator = Validator::make($request->all(), [
                'banner_image_desktop' => 'required',
                'banner_image_mobile' => 'required',
                'language' => 'required',
                'title' => 'required',
                'link' => 'required',
            ],$msg);
            if($validator->fails()) {
                return redirect('/admin/banner/add')
                            ->withErrors($validator)
                            ->withInput();
            }
            $banner = new \App\Models\Banners;
            $banner->language_id = $request->language;
            if($request->hasFile('banner_image_desktop'))
            {
                $max_height = config('app.banner_image_desktop.height');
                $max_width = config('app.banner_image_desktop.width');
                $loaded_image_height = $request->loaded_desktop_image_height;
                $loaded_image_width = $request->loaded_desktop_image_width;
                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {
                    $image = $request->file('banner_image_desktop');
                    $ext = $request->file('banner_image_desktop')->extension();
                    $filename = "desktop_banner_".rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.banner_image_desktop.width'), config('app.banner_image_desktop.height'));
                    $image->move(public_path('/assets/images/banners/desktop/'), $filename);
                    $image_resize->save(public_path('/assets/images/banners/desktop/' .$filename));

                }
                else
                {
                    $photo = $request->file('banner_image_desktop');
                    $ext = $request->file('banner_image_desktop')->extension();
                    $filename = "desktop_banner_".rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/banners/desktop/', $filename);
                }
            }
            $banner->image = $filename;
            if($request->hasFile('banner_image_mobile'))
            {
                $max_height = config('app.banner_image_mobile.height');
                $max_width = config('app.banner_image_mobile.width');
                $loaded_image_height = $request->loaded_mobile_image_height;
                $loaded_image_width = $request->loaded_mobile_image_width;

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {
                    $image = $request->file('banner_image_mobile');
                    $ext = $request->file('banner_image_mobile')->extension();
                    $filename = "mobile_banner_".rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.banner_image_mobile.width'), config('app.banner_image_mobile.height'));
                    $image->move(public_path().'/assets/images/banners/mobile/', $filename);
                    $image_resize->save(public_path('/assets/images/banners/mobile/' .$filename));
                }
                else
                {
                    $photo = $request->file('banner_image_mobile');
                    $ext = $request->file('banner_image_mobile')->extension();
                    $filename = "mobile_banner_".rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/banners/mobile/', $filename);
                }
            }
            $banner->mobile_image = $filename;
            $banner->text = $request->banner_text;
            $banner->title = $request->title;
            $banner->category_id = $request->link;
            // $banner->status = $request->act_deact_chk_val != '' ? $request->act_deact_chk_val : 0;
            // $banner->status = $request->banner_act_deact;
            $banner->created_at = date('Y-m-d H:i:s');
            $banners = \App\Models\Banners::where('language_id', $request->language)->where('status', 1)
            ->where('is_deleted', 0)->get();
            if($request->banner_act_deact == 1)
            {                
                foreach ($banners as $banner_data) {
                    $bannersData = \App\Models\Banners::where('id', $banner_data->id)
                    ->where('status', 1)->first();                
                    if($bannersData)
                    {
                        $bannersData->status = 0;
                        $bannersData->save();
                    }
                }
                $status = 1;                                                              
            }
            else
            {
                $status = 0;                                
            }            
            $banner->status = $status;                                                              
            $banner->save();  
            $notification = array(
                'message' => config('message.Banners.BanAddSuccess'),
                'alert-type' => 'success'
            );
            return redirect('/admin/banner')->with($notification);
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: editBanner
    // Description: Edit banner information
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editBanner($id)
    {
        $banners = \App\Models\Banners::where('id', $id)->first();
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang);       
        if($global_languages_count >= 2)
        {
            $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status',1)
            ->where('global_language.is_deleted', 0)
            ->get();
            return view('admin.banner.edit',compact('banners','global_languages','categories'));
        }
        else
        {
            $global_language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->where('global_language.is_deleted', 0)
            ->first();
            return view('admin.banner.edit',compact('banners','global_language','categories'));
        }
    }

    /* ###########################################
    // Function: updateBanner
    // Description: Update banner information
    // Parameter: banner_image: String, language: Int, banner_text: String, act_deact_chk_val: Int, banner_image: File
    // ReturnType: view
    */ ###########################################
    public function updateBanner(Request $request)
    {
        // return $request;
        try {
            $banner = \App\Models\Banners::where('id', $request->banner_id)->first();
            $banner->language_id = $request->language;
            if($request->hasFile('banner_image_desktop'))
            {
                $max_height = config('app.banner_image_desktop.height');
                $max_width = config('app.banner_image_desktop.width');
                $loaded_image_height = $request->loaded_image_height;
                $loaded_image_width = $request->loaded_image_width;

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {
                    $image = $request->file('banner_image_desktop');
                    $ext = $request->file('banner_image_desktop')->extension();
                    $filename = "desktop_banner_".rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.banner_image_desktop.width'), config('app.banner_image_desktop.height'));
                    $image->move(public_path().'/assets/images/banners/desktop/', $filename);
                    $image_resize->save(public_path('/assets/images/banners/desktop/' .$filename));
                }
                else
                {
                    $photo = $request->file('banner_image_desktop');
                    $ext = $request->file('banner_image_desktop')->extension();
                    $filename = "desktop_banner_".rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/banners/desktop/', $filename);
                }
            }

            if(!empty($filename))
            {
                $path = public_path('/assets/images/banners/desktop').'/'.$banner->image;
                if(file_exists($path))
                {
                    unlink($path);
                }
                $banner->image = $filename;
            }

            if($request->hasFile('banner_image_mobile'))
            {
                $max_height = config('app.banner_image_mobile.height');
                $max_width = config('app.banner_image_mobile.width');
                $loaded_image_height = $request->loaded_mobile_image_height;
                $loaded_image_width = $request->loaded_mobile_image_width;

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {
                    $image = $request->file('banner_image_mobile');
                    $ext = $request->file('banner_image_mobile')->extension();
                    $filename = "mobile_banner_".rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.banner_image_mobile.width'), config('app.banner_image_mobile.height'));
                    $image->move(public_path().'/assets/images/banners/mobile/', $filename);
                    $image_resize->save(public_path('/assets/images/banners/mobile/' .$filename));
                }
                else
                {
                    $photo = $request->file('banner_image_mobile');
                    $ext = $request->file('banner_image_mobile')->extension();
                    $filename = "mobile_banner_".rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/banners/mobile/', $filename);
                }
            }

            if(!empty($filename))
            {
                $path = public_path('/assets/images/banners/mobile').'/'.$banner->mobile_image;
                if(file_exists($path))
                {
                    unlink($path);
                }
                $banner->mobile_image = $filename;
            }

            $banner->text = $request->banner_text;
            $banner->title = $request->title;
            $banner->category_id = $request->link;
            // $banner->status = $request->act_deact_chk_val != '' ? $request->act_deact_chk_val : 0;
            // $banner->status = $request->banner_act_deact;
            if($request->banner_act_deact == 1)
            {
                $banners = \App\Models\Banners::where('id', '<>', $request->banner_id)
                ->where('language_id', $request->language)->where('status', 1)
                ->where('is_deleted', 0)->get();
                foreach ($banners as $banner_data) {
                    $bannersData = \App\Models\Banners::where('id',$banner_data->id)->first();
                    if($bannersData->status == 1)
                    {
                        $bannersData->status = 0;
                        $bannersData->save();
                    }
                }                
                $banner->status = 1;
                $banner->save();
            }
            else
            {
                $banner->status = 0;
                $banner->save();
            }            
            $notification = array(
                'message' => config('message.Banners.BanUpdateSuccess'),
                'alert-type' => 'success'
            );
            return redirect('/admin/banner')->with($notification);
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: deleteBanner
    // Description: Delete banner information
    // Parameter: banner_id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteBanner(Request $request)
    {
        if($request->ajax())
        {
            $banner = \App\Models\Banners::where('id', $request->banner_id)->first();
            if($banner)
            {
                $banner->is_deleted = 1;
                $banner->save();
                $result['status'] = 'true';
                $result['msg'] = config('message.Banners.BanDeleteSuccess');
                return $result;
            }
            else
            {
                $result['status'] = 'false';
                $result['msg'] = 'Something went wrong!';
                return $result;
            }
        }
    }

    /* ###########################################
    // Function: bannerActDeact
    // Description: Delete banner information
    // Parameter: banner_id: Int
    // ReturnType: array
    */ ###########################################
    public function bannerActDeact(Request $request)
    {
        // return $request;
        if($request->ajax())
        {                  
            $banner = \App\Models\Banners::where('id', $request->banner_id)->first();
            if($banner)
            {
                // $banner->status = ($request->status == 0) ? 1 : 0;
                // $banner->save();
                if($request->status == 0)
                {
                    $banners = \App\Models\Banners::where('id', '<>', $request->banner_id)
                    ->where('language_id', $banner->language_id)->where('status', 1)
                    ->where('is_deleted', 0)->get();
                    foreach ($banners as $banner_data) {
                        $bannersData = \App\Models\Banners::where('id',$banner_data->id)->first();
                        if($bannersData->status == 1)
                        {
                            $bannersData->status = 0;
                            $bannersData->save();
                        }
                    }                
                    $banner->status = 1;
                    $banner->save();
                }                

                $result['status'] = 'true';
                if($request->status == 0)
                {
                    $result['msg'] = config('message.Banners.BanActSuccess');
                }
                else
                {
                    $result['msg'] = config('message.Banners.BanDeactSuccess');
                }
                return $result;
            }
            else
            {
                $result['status'] = 'false';
                $result['msg'] = 'Something went wrong!';
                return $result;
            }
        }
    }

    public function filterBanner(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $banners = \App\Models\Banners::select('id', 'status', 'text as banner',
        DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as bnr_created_at"));
        if($request->filter_banner_lang == 'all')
        {
            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)->get();
            $global_languages_ids = $global_languages_count->pluck('id');
            $banners->where(function($query) use($global_languages_ids){
                return $query->where('is_deleted', 0)->whereIn('language_id', $global_languages_ids);
            });
        }
        else
        {
            $banners->where(function($query) use($request){
                return $query->where('language_id', $request->filter_banner_lang)->where('is_deleted', 0);
            });
        }
        $banners = $banners->get();
        return Datatables::of($banners)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }
}
