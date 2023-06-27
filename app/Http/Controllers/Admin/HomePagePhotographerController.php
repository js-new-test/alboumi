<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use DataTables;
use Auth;
use DB;
use Image;
use App\Traits\ReuseFunctionTrait;

class HomePagePhotographerController extends Controller
{
    use ReuseFunctionTrait;
    /* ###########################################
    // Function: listHomePagePhotographer
    // Description: Display home page photographer data
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function listHomePagePhotographer(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

            // $global_languages_ids = \App\Models\GlobalLanguage::where('is_deleted', 0)->pluck('id');
            $sys_default_lang = $this->getDefaultLanguage();
            $HomePagePhotographer = \App\Models\HomePagePhotographer::select('home_page_photographer.id',
            'home_page_photographer.status','home_page_photographer.sort_order','photographers.profile_pic',
            'photographer_details.name',DB::raw("date_format(home_page_photographer.created_at,'%Y-%m-%d %h:%i:%s') as hpp_created_at"))
            ->join('photographers','photographers.id', '=', 'home_page_photographer.photographer_id')
            ->join('photographer_details','photographer_details.photographer_id', '=', 'photographers.id')
            ->where('home_page_photographer.is_deleted', 0)
            ->where('photographers.status', 1)
            ->whereNull('photographers.deleted_at')
            ->whereNull('photographer_details.deleted_at')
            ->where('photographer_details.language_id', $sys_default_lang)
            ->get();

            return Datatables::of($HomePagePhotographer)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true);
        }
        // $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        // ->select('global_language.id','alpha2','langEN','is_default')
        // ->where('status',1)
        // ->where('is_deleted', 0)
        // ->get();

        return view('admin.home-page-photographer.list');
    }

    /* ###########################################
    // Function: showHomePagePhotographerForm
    // Description: Show home page photographer form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function showHomePagePhotographerForm()
    {
        // $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        // ->where('is_deleted', 0)
        // ->count();
        // $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
        // ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
        // ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id');
        // if($global_languages_count >= 2)
        // {
        //     $global_languages->where(function($query) {
        //         $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
        //     });
        //     $global_languages = $global_languages->get();
        //     return view('admin.home-page-photographer.add', compact('global_languages'));
        // }
        // else
        // {
        //     $global_languages->where(function($query){
        //         $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
        //     });
        //     $global_language = $global_languages->first();
        //     return view('admin.home-page-photographer.add', compact('global_language'));
        // }

        $sys_default_lang = $this->getDefaultLanguage();
        $photographer = \App\Models\Photographers::select('photographers.id','photographer_details.name')
        ->join('photographer_details','photographer_details.photographer_id', '=', 'photographers.id')
        ->where('photographer_details.language_id', $sys_default_lang)
        ->where('photographers.status', 1)
        ->whereNull('photographers.deleted_at')
        ->whereNull('photographer_details.deleted_at')
        ->get();
        $baseUrl = $this->getBaseUrl();
        return view('admin.home-page-photographer.add', compact('photographer','baseUrl'));
    }

    /* ###########################################
    // Function: addHomePagePhotographer
    // Description: Add home page photographer
    // Parameter: language: Int, p_name: String, p_status: Int, link: String, p_sort_order: String, image: File
    // ReturnType: view
    */ ###########################################
    public function addHomePagePhotographer(Request $request)
    {
        try {
            $HomePagePhotographer = new \App\Models\HomePagePhotographer;
            // $HomePagePhotographer->language_id = $request->language;
            $HomePagePhotographer->photographer_id = $request->p_id;
            $HomePagePhotographer->status = $request->p_status;
            // $HomePagePhotographer->link = $request->link;
            $HomePagePhotographer->sort_order = $request->p_sort_order;
            if($request->hasFile('big_image'))
            {
                $max_height = config('app.home_page_photographer_bigimg.height');
                $max_width = config('app.home_page_photographer_bigimg.width');
                $loaded_bigimage_height = $request->loaded_bigimage_height;
                $loaded_bigimage_width = $request->loaded_bigimage_width;

                if($loaded_bigimage_width != $max_width || $loaded_bigimage_height != $max_height)
                {
                    $image = $request->file('big_image');
                    $ext = $request->file('big_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.home_page_photographer_bigimg.width'), config('app.home_page_photographer_bigimg.height'));
                    $image_resize->save(public_path('/assets/images/home-page-photographer/bigimage/' .$filename));
                }
                else
                {
                    $photo = $request->file('big_image');
                    $ext = $request->file('big_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/home-page-photographer/bigimage', $filename);
                }
            }
            $HomePagePhotographer->big_image = $filename;
            if($request->hasFile('small_image'))
            {
                $max_height = config('app.home_page_photographer_smallimg.height');
                $max_width = config('app.home_page_photographer_smallimg.width');
                $loaded_smallimage_height = $request->loaded_smallimage_height;
                $loaded_smallimage_width = $request->loaded_smallimage_width;

                if($loaded_smallimage_width != $max_width || $loaded_smallimage_height != $max_height)
                {
                    $image = $request->file('small_image');
                    $ext = $request->file('small_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.home_page_photographer_smallimg.width'), config('app.home_page_photographer_smallimg.height'));
                    $image_resize->save(public_path('/assets/images/home-page-photographer/smallimage/' .$filename));
                }
                else
                {
                    $photo = $request->file('small_image');
                    $ext = $request->file('small_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/home-page-photographer/smallimage', $filename);
                }
            }
            $HomePagePhotographer->small_image = $filename;
            $HomePagePhotographer->save();
            $notification = array(
                'message' => config('message.HomePagePhotographer.HomePagePhotographerAddSuccess'),
                'alert-type' => 'success'
            );
            return redirect('/admin/home-page-photographer')->with($notification);
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: editHomePagePhotographer
    // Description: Edit home page photographer
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editHomePagePhotographer($id)
    {
        // $HomePagePhotographer = \App\Models\HomePagePhotographer::where('id', $id)->first();
        // $global_languages_count = \App\Models\GlobalLanguage::where('status',1)->where('is_deleted', 0)->count();
        // $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
        // ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
        // ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id');
        // if($global_languages_count >= 2)
        // {
        //     $global_languages->where(function($query){
        //         $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
        //     });
        //     $global_languages = $global_languages->get();
        //     return view('admin.home-page-photographer.edit',compact('HomePagePhotographer', 'global_languages'));
        // }
        // else
        // {
        //     $global_languages->where(function($query){
        //         $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
        //     });
        //     $global_language = $global_languages->first();
        //     return view('admin.home-page-photographer.edit',compact('HomePagePhotographer', 'global_language'));
        // }
        $sys_default_lang = $this->getDefaultLanguage();
        $photographer = \App\Models\Photographers::select('photographers.id','photographer_details.name')
        ->join('photographer_details','photographer_details.photographer_id', '=', 'photographers.id')
        ->where('photographers.status', 1)
        ->whereNull('photographers.deleted_at')
        ->whereNull('photographer_details.deleted_at')
        ->where('photographer_details.language_id', $sys_default_lang)
        ->get();
        $HomePagePhotographer = \App\Models\HomePagePhotographer::where('id', $id)->first();
        $baseUrl = $this->getBaseUrl();
        return view('admin.home-page-photographer.edit',compact('HomePagePhotographer','photographer','baseUrl'));
    }

    /* ###########################################
    // Function: updateHomePagePhotographer
    // Description: Update home page photographer
    // Parameter: language: Int, p_name: String, p_status: Int, link: String, p_sort_order: String, image: File
    // ReturnType: view
    */ ###########################################
    public function updateHomePagePhotographer(Request $request)
    {
        try {
            $HomePagePhotographer = \App\Models\HomePagePhotographer::where('id', $request->hpp_id)->first();
            // $HomePagePhotographer->language_id = $request->language;
            $HomePagePhotographer->photographer_id = $request->p_id;
            $HomePagePhotographer->status = $request->p_status;
            // $HomePagePhotographer->link = $request->link;
            $HomePagePhotographer->sort_order = $request->p_sort_order;

            if($request->hasFile('big_image'))
            {
                $max_height = config('app.home_page_photographer_bigimg.height');
                $max_width = config('app.home_page_photographer_bigimg.width');
                $loaded_bigimage_height = $request->loaded_bigimage_height;
                $loaded_bigimage_width = $request->loaded_bigimage_width;

                if($loaded_bigimage_width != $max_width || $loaded_bigimage_height != $max_height)
                {
                    $image = $request->file('big_image');
                    $ext = $request->file('big_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.home_page_photographer_bigimg.width'), config('app.home_page_photographer_bigimg.height'));
                    $image_resize->save(public_path('/assets/images/home-page-photographer/bigimage/' .$filename));
                }
                else
                {
                    $photo = $request->file('big_image');
                    $ext = $request->file('big_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/home-page-photographer/bigimage', $filename);
                }
                $HomePagePhotographer->big_image = $filename;
            }
            if($request->hasFile('small_image'))
            {
                $max_height = config('app.home_page_photographer_smallimg.height');
                $max_width = config('app.home_page_photographer_smallimg.width');
                $loaded_smallimage_height = $request->loaded_smallimage_height;
                $loaded_smallimage_width = $request->loaded_smallimage_width;
                if($loaded_smallimage_width != $max_width || $loaded_smallimage_height != $max_height)
                {
                    $image = $request->file('small_image');
                    $ext = $request->file('small_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    echo $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize(config('app.home_page_photographer_smallimg.width'), config('app.home_page_photographer_smallimg.height'));
                    $image_resize->save(public_path('/assets/images/home-page-photographer/smallimage/' .$filename));

                }
                else
                {
                    $photo = $request->file('small_image');
                    $ext = $request->file('small_image')->extension();
                    $filename = rand().'_'.time().'.'.$ext;
                    $photo->move(public_path().'/assets/images/home-page-photographer/smallimage', $filename);
                }
                $HomePagePhotographer->small_image = $filename;
            }

            $HomePagePhotographer->save();
            $notification = array(
                'message' => config('message.HomePagePhotographer.HomePagePhotographerUpdateSuccess'),
                'alert-type' => 'success'
            );
            return redirect('/admin/home-page-photographer')->with($notification);
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: deleteHomePagePhotographer
    // Description: Delete home page photographer
    // Parameter: hpp_id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteHomePagePhotographer(Request $request)
    {
        $HomePagePhotographer = \App\Models\HomePagePhotographer::where('id', $request->hpp_id)->first();
        if($HomePagePhotographer)
        {
            $HomePagePhotographer->is_deleted = 1;
            $HomePagePhotographer->save();
            $result['status'] = 'true';
            $result['msg'] = config('message.HomePagePhotographer.HomePagePhotographerDeleteSuccess');
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = config('message.500.SomeThingWrong');
            return $result;
        }
    }

    /* ###########################################
    // Function: actInactHomePagePhotographer
    // Description: Active Inactive home page photographer
    // Parameter: hpp_id: Int
    // ReturnType: array
    */ ###########################################
    public function actInactHomePagePhotographer(Request $request)
    {
        $HomePagePhotographer = \App\Models\HomePagePhotographer::where('id', $request->hpp_id)->first();
        if($HomePagePhotographer)
        {
            if($request->is_active == 1)
            {
                $msg = config('message.HomePagePhotographer.HomePagePhotographerActSuccess');
            }
            else
            {
                $msg = config('message.HomePagePhotographer.HomePagePhotographerInactSuccess');
            }
            $HomePagePhotographer->status = $request->is_active;
            $HomePagePhotographer->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = config('message.500.SomeThingWrong');
            return $result;
        }
    }

    /* ###########################################
    // Function: filterHomePagePhotographer
    // Description: Apply filer on home page photographer
    // Parameter: filter_HPP_lang: Int
    // ReturnType: array
    */ ###########################################
    public function filterHomePagePhotographer(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $HomePagePhotographer = \App\Models\HomePagePhotographer::select('home_page_photographer.id',
        'home_page_photographer.name','home_page_photographer.image','home_page_photographer.status',
        'home_page_photographer.link','home_page_photographer.sort_order','world_languages.langEN as langName',
        DB::raw("date_format(home_page_photographer.created_at,'%Y-%m-%d %h:%i:%s') as hpp_created_at"))
        ->join('global_language','global_language.id','=','home_page_photographer.language_id')
        ->join('world_languages','world_languages.id','=','global_language.language_id');
        if($request->filter_HPP_lang == 'all')
        {
            $global_languages_ids = \App\Models\GlobalLanguage::where('is_deleted', 0)->pluck('id');
            $HomePagePhotographer->where(function($query) use($global_languages_ids){
                return $query->where('home_page_photographer.is_deleted', 0)->whereIn('home_page_photographer.language_id', $global_languages_ids);
            });
        }
        else
        {
            $HomePagePhotographer->where(function($query) use($request){
                return $query->where('home_page_photographer.language_id', $request->filter_HPP_lang)->where('home_page_photographer.is_deleted', 0);
            });
        }
        $HomePagePhotographer = $HomePagePhotographer->get();
        return Datatables::of($HomePagePhotographer)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }
}
