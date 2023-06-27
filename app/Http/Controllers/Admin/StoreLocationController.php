<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use DataTables;
use Auth;
use DB;

class StoreLocationController extends Controller
{
    /* ###########################################
    // Function: listStoreLoc
    // Description: Display list of store location
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function listStoreLoc(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

            $StoreLocation = \App\Models\StoreLocation::select('id', 'title', 'address_1','address_2', 
            'phone','map_url', DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as sl_created_at"));
            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)->where('is_default', 1)->get();
            $global_languages_ids = $global_languages_count->pluck('id');        
            if(count($global_languages_count) >= 2)
            {                
                $StoreLocation->where(function($query) use($global_languages_ids){
                    return $query->where('is_deleted', 0)->whereIn('language_id', $global_languages_ids)->get();
                });
            }
            else
            {
                $StoreLocation->where(function($query) use($global_languages_ids){
                    return $query->where('is_deleted', 0)->whereIn('language_id', $global_languages_ids)->get();
                });
            }
            return Datatables::of($StoreLocation)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true); 
        }

        $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        ->select('global_language.id','alpha2','langEN','is_default')
        ->where('status',1)
        ->where('is_deleted', 0)
        ->get();
        return view('admin.settings.store-location.list', compact('languages'));
    }

    /* ###########################################
    // Function: showStoreLocForm
    // Description: Show store location form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function showStoreLocForm()
    {
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();

        $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
        ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
        ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id');
        if($global_languages_count >= 2)
        {   
            $global_languages->where(function($query){
                return $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });                     
            $languages = $global_languages->get();
            return view('admin.settings.store-location.add', compact('languages'));
        }
        else
        {
            $global_languages->where(function($query){
                return $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });                     
            $language = $global_languages->first();
            return view('admin.settings.store-location.add', compact('language'));
        }                
    }

    /* ###########################################
    // Function: addStoreLoc
    // Description: Add store location to database
    // Parameter: language: Int, title: String, address_1: String, address_2: String, phone: Int, map_url: String
    // ReturnType: view
    */ ###########################################
    public function addStoreLoc(Request $request)
    {
        try {
            $msg = [
                'title.required' => "Title is required.",
                'address_1.required' => "Address 1 is required.",  
                'phone.required' => 'Phone number is required',                  
            ];        
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'address_1' => 'required',   
                'phone' => 'required',         
            ],$msg);
                
            if($validator->fails()) {
                return redirect('/admin/store-location/add')
                            ->withErrors($validator)
                            ->withInput();
            }

            $StoreLocation = new \App\Models\StoreLocation;
            $StoreLocation->language_id = $request->language;
            $StoreLocation->title = $request->title; 
            $StoreLocation->address_1 = $request->address_1;
            $StoreLocation->address_2 = $request->address_2;
            $StoreLocation->phone = $request->phone;
            $StoreLocation->map_url = $request->map_url;
            $StoreLocation->latitude = $request->latitude;        
            $StoreLocation->longitude = $request->longitude;        
            $StoreLocation->save();
            $notification = array(
                'message' => config('message.StoreLocation.StoreLocAddSuccess'), 
                'alert-type' => 'success'
            );
            return redirect('/admin/store-location')->with($notification);            
        } catch (\Exception $e) {
            return view('errors.500');
        }        
    }

    /* ###########################################
    // Function: editStoreLoc
    // Description: Show store location edit form 
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editStoreLoc($id)
    {
        $StoreLocation = \App\Models\StoreLocation::where('id', $id)->first();
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();

        $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
        ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
        ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id');
        if($global_languages_count >= 2)
        {   
            $global_languages->where(function($query){
                return $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });                     
            $languages = $global_languages->get();
            return view('admin.settings.store-location.edit', compact('StoreLocation','languages'));
        }
        else
        {
            $global_languages->where(function($query){
                return $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });                     
            $language = $global_languages->first();
            return view('admin.settings.store-location.edit', compact('StoreLocation','language'));
        }

    }

    /* ###########################################
    // Function: updateStoreLoc
    // Description: Update store location information 
    // Parameter: sl_id: Int, language: Int, title: String, address_1: String, address_2: String, phone: Int, map_url: String
    // ReturnType: view
    */ ###########################################
    public function updateStoreLoc(Request $request)
    {
        try {
            $msg = [
                'title.required' => "Title is required.",
                'address_1.required' => "Address 1 is required.",  
                'phone.required' => 'Phone number is required',                  
            ];        
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'address_1' => 'required',   
                'phone' => 'required',         
            ],$msg);
                
            if($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $StoreLocation = \App\Models\StoreLocation::where('id', $request->sl_id)->first();
            $StoreLocation->language_id = $request->language;
            $StoreLocation->title = $request->title; 
            $StoreLocation->address_1 = $request->address_1;
            $StoreLocation->address_2 = $request->address_2;
            $StoreLocation->phone = $request->phone;
            $StoreLocation->map_url = $request->map_url;  
            $StoreLocation->latitude = $request->latitude;        
            $StoreLocation->longitude = $request->longitude;      
            $StoreLocation->save();
            $notification = array(
                'message' => config('message.StoreLocation.StoreLocUpdateSuccess'), 
                'alert-type' => 'success'
            );
            return redirect('/admin/store-location')->with($notification);
        } catch (\Exception $e) {
            return view('errors.500');
        }        
    }

    /* ###########################################
    // Function: deleteStoreLoc
    // Description: Delete store location data 
    // Parameter: sl_id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteStoreLoc(Request $request)
    {
        if($request->ajax())
        {
            $StoreLocation = \App\Models\StoreLocation::where('id', $request->sl_id)->first();
            if($StoreLocation)
            {
                $StoreLocation->is_deleted = 1;
                $StoreLocation->save();
                $result['status'] = 'true';
                $result['msg'] = config('message.StoreLocation.StoreLocDeleteSuccess');
                return $result;
            }
            else
            {
                $result['status'] = 'false';
                $result['msg'] = config('message.500.SomeThingWrong');
                return $result;
            }
        }   
    }
   
    /* ###########################################
    // Function: filterStoreLoc
    // Description: Filter store location data 
    // Parameter: filter_SL_lang: Int
    // ReturnType: array
    */ ###########################################
    public function filterStoreLoc(Request $request)
    {        
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $StoreLocation = \App\Models\StoreLocation::select('store_location.id', 'store_location.title', 'store_location.address_1','store_location.address_2', 
        'store_location.phone','world_languages.langEN as langName','map_url', DB::raw("date_format(store_location.created_at,'%Y-%m-%d %h:%i:%s') as sl_created_at"))
        ->join('global_language','global_language.id','=','store_location.language_id')
        ->join('world_languages','world_languages.id','=','global_language.language_id');         
        if($request->filter_SL_lang == 'all')
        {
            $global_languages_ids = \App\Models\GlobalLanguage::where('is_deleted', 0)->pluck('id');            
            $StoreLocation->where(function($query) use($global_languages_ids){            
                return $query->where('store_location.is_deleted', 0)->whereIn('store_location.language_id', $global_languages_ids);
            });
        }
        else
        {
            $StoreLocation->where(function($query) use($request){               
                return $query->where('store_location.language_id', $request->filter_SL_lang)->where('store_location.is_deleted', 0);
            });
        }
        $StoreLocation = $StoreLocation->get();
        return Datatables::of($StoreLocation)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);             
    }
}
