<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Events;
use App\Models\Package;
use Auth;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;
use Session;
use App\Traits\ExportTrait;
use Image;
use Config;

class EventsController extends Controller
{
    use ExportTrait;

    public function getListOfEvents(Request $request)
    {
        if($request->ajax()) 
        {            
            try 
            {
                $id = Auth::guard('admin')->user()->id;
                $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

                $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)
                ->where('is_default', 1)
                ->get();        
                if(count($global_languages_count) >= 2)
                {
                    $global_languages_ids = $global_languages_count->pluck('id');    
                    DB::statement(DB::raw('set @rownum=0'));
                    $events = Events::select('id', 'event_name', 'event_image', 'event_desc','is_active',
                    DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                    DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as e_created_at"))
                    ->whereIn('language_id', $global_languages_ids)
                    ->whereNull('deleted_at')
                    ->orderBy('updated_at','desc')
                    ->get();                
                }
                else
                {
                    $global_language = \App\Models\GlobalLanguage::where('status',1)
                    ->where('is_deleted', 0)
                    ->where('is_default', 1)
                    ->first();
                    DB::statement(DB::raw('set @rownum=0'));
                    $events = Events::select('id', 'event_name', 'event_image', 'event_desc','is_active',
                    DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                    DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as e_created_at"))
                    ->where('language_id', $global_language->id)
                    ->whereNull('deleted_at')
                    ->orderBy('updated_at','desc')
                    ->get();
                }
                  
                // dd($events);
                return Datatables::of($events)->rawColumns(['event_desc'])->editColumn('user_zone', function () use($timezone){
                    return $timezone;
                })->make(true);            
            } 
            catch (\Exception $e) 
            {
                return view('errors.500');
            }            
        }
        $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        ->select('global_language.id','alpha2','langEN','is_default')
        ->where('status',1)
        ->where('is_deleted',0)
        ->get();      
        $baseUrl = $this->getBaseUrl();
 
        return view('admin.events.event.index',compact('languages','baseUrl'));                
    }

    public function eventsAddView()
    {
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        if($global_languages_count >= 2)
        {
            $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status',1)
            ->where('global_language.is_deleted',0)
            ->get();
            return view('admin.events.event.add',compact('global_languages'));
        }
        else
        {
            $global_language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status',1)
            ->where('global_language.is_deleted',0)
            ->first();
            return view('admin.events.event.add',compact('global_language'));
        }        
    }

    public function addEvent(Request $request)
    {
        // dd($request->all());
        try
        {
            $event = new Events;
            $event->language_id = $request->language;
            $event->event_name = $request->event_name;
            $event->event_desc = $request->event_desc;
            $event->is_active = $request->is_active;
            $event->sort_order = $request->sort_order;
            // Image
            $reqdImgWidth =Config::get('app.event_image.width');
            $reqdImgHeight =Config::get('app.event_image.height');
            if($request->hasFile('event_image')) 
            {
                if($request->width != $reqdImgWidth || $request->height != $reqdImgHeight)
                {
                    $image       = $request->file('event_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                    $image->move(public_path('assets/images/events/'), $filename);
                    $image_resize->save(public_path('assets/images/events/' .$filename));
                    $event->event_image = $filename;
                   
                }
                else
                {
                    $image = $request->file('event_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/events/'), $filename);
                    $event->event_image = $request->file('event_image')->getClientOriginalName();
                }
            }

            // Desktop banner
            $reqdBannerImgWidth =Config::get('app.event_banner_image.width');
            $reqdBannerImgHeight =Config::get('app.event_banner_image.height');
            if($request->hasFile('banner_image')) 
            {
                if($request->bannerwidth != $reqdBannerImgWidth || $request->bannerheight != $reqdBannerImgHeight)
                {
                    $image       = $request->file('banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($reqdBannerImgWidth, $reqdBannerImgHeight);
                    $image->move(public_path('assets/images/events/banner/'), $filename);
                    $image_resize->save(public_path('assets/images/events/banner/' .$filename));
                    $event->banner_image = $filename;
                   
                }
                else
                {
                    $image = $request->file('banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/events/banner/'), $filename);
                    $event->banner_image = $request->file('banner_image')->getClientOriginalName();
                }
            }

            // Mobile banner
            $reqdMobileBannerImgWidth =Config::get('app.event_mobile_banner_image.width');
            $reqdMobileBannerImgHeight =Config::get('app.event_mobile_banner_image.height');
            if($request->hasFile('mobile_banner_image')) 
            {
                if($request->mobilebannerwidth != $reqdMobileBannerImgWidth || $request->mobilebannerheight != $reqdMobileBannerImgHeight)
                {
                    $image       = $request->file('mobile_banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($reqdMobileBannerImgWidth, $reqdMobileBannerImgHeight);
                    $image->move(public_path('assets/images/events/mobile_banner/'), $filename);
                    $image_resize->save(public_path('assets/images/events/mobile_banner/' .$filename));
                    $event->mobile_banner_image = $filename;
                
                }
                else
                {
                    $image = $request->file('mobile_banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/events/mobile_banner/'), $filename);
                    $event->mobile_banner_image = $request->file('mobile_banner_image')->getClientOriginalName();
                }
            }
            $event->save();
            if(!empty($request->event_feature))
            {
                foreach ($request->event_feature as $event_f) {
                    $event_features = new \App\Models\EventFeatures;
                    $event_features->event_id = $event->id;
                    $event_features->feature_name = $event_f;
                    $event_features->created_at = date('Y-m-d H:i:s');
                    $event_features->save();
                }
            }
            $notification = array(
                'message' => 'Event added successfully!', 
                'alert-type' => 'success'
            );
            return redirect('admin/event/list')->with($notification); 
        }
        catch (\Exception $e) 
        {
            Session::flash('error', $e->getMessage());            
            return redirect('admin/event/list');
        }
    }

    public function eventEditView($id)
    {
        $event = Events::findOrFail($id);
        if(!empty($event))
        {
            $event_features = \App\Models\EventFeatures::where('event_id', $id)->get();
            $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id');

            $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
            ->where('is_deleted', 0)
            ->count();
            if($global_languages_count >= 2)
            {
                $global_languages->where(function($query){
                    return $query->where('global_language.status', 1)->where('global_language.is_deleted', 0);
                });
                $global_languages = $global_languages->get();
                $baseUrl = $this->getBaseUrl();
                return view('admin.events.event.edit',compact('event','event_features','global_languages','baseUrl'));
            }
            else
            {
                $global_languages->where(function($query){
                    return $query->where('global_language.status', 1)->where('global_language.is_deleted', 0);
                });
                $global_language = $global_languages->first();
                $baseUrl = $this->getBaseUrl();
                return view('admin.events.event.edit',compact('event','event_features','global_language','baseUrl'));
            }                        
        }
    }

    public function updateEvent(Request $request)
    {
        // dd($request->all());
        $event = Events::findOrFail($request->id);
        if(!empty($event))
        {
            $event->language_id = $request->language;
            $event->event_name = $request->event_name;
            $event->event_desc = $request->event_desc;
            $event->is_active = $request->is_active;
            $event->sort_order = $request->sort_order;

            $reqdImgWidth =Config::get('app.event_image.width');
            $reqdImgHeight =Config::get('app.event_image.height');
            if($request->hasFile('event_image')) 
            {
                if($request->width != $reqdImgWidth || $request->height != $reqdImgHeight)
                {
                    $image       = $request->file('event_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                    $image->move(public_path('assets/images/events/'), $filename);
                    $image_resize->save(public_path('assets/images/events/' .$filename));
                    $event->event_image = $filename;
                }
                else
                {
                    $image = $request->file('event_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/events/'), $filename);
                    $event->event_image = $request->file('event_image')->getClientOriginalName();
                }
            }
            // Desktop banner
            $reqdBannerImgWidth =Config::get('app.event_banner_image.width');
            $reqdBannerImgHeight =Config::get('app.event_banner_image.height');
            // dd($reqdBannerImgHeight);
            if($request->hasFile('banner_image')) 
            {
                if($request->loaded_banner_width != $reqdBannerImgWidth || $request->loaded_banner_height != $reqdBannerImgHeight)
                {
                    // dd("in if");
                    $image       = $request->file('banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    // dd($image_resize);
                    $image_resize->resize($reqdBannerImgWidth, $reqdBannerImgHeight);
                    $image->move(public_path('assets/images/events/banner/'), $filename);
                    $image_resize->save(public_path('assets/images/events/banner/' .$filename));
                    $event->banner_image = $filename;
                   
                }
                else
                {
                    $image = $request->file('banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/events/banner/'), $filename);
                    $event->banner_image = $request->file('banner_image')->getClientOriginalName();
                }
            }

            // Mobile banner
            $reqdMobileBannerImgWidth =Config::get('app.event_mobile_banner_image.width');
            $reqdMobileBannerImgHeight =Config::get('app.event_mobile_banner_image.height');
            if($request->hasFile('mobile_banner_image')) 
            {
                if($request->mobilebannerwidth != $reqdMobileBannerImgWidth || $request->mobilebannerheight != $reqdMobileBannerImgHeight)
                {
                    $image       = $request->file('mobile_banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($reqdMobileBannerImgWidth, $reqdMobileBannerImgHeight);
                    $image->move(public_path('assets/images/events/mobile_banner/'), $filename);
                    $image_resize->save(public_path('assets/images/events/mobile_banner/' .$filename));
                    $event->mobile_banner_image = $filename;
                
                }
                else
                {
                    $image = $request->file('mobile_banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/events/mobile_banner/'), $filename);
                    $event->mobile_banner_image = $request->file('mobile_banner_image')->getClientOriginalName();
                }
            }
            if($event->save())
            {
                if(!empty($request->event_feature))
                {
                    foreach ($request->event_feature as $key => $event_f) {
                        $event_features = \App\Models\EventFeatures::where('id', $key)->where('event_id', $event->id)->first();
                        if($event_features)
                        {
                            $event_features->feature_name = $event_f;
                            $event_features->save();
                        }
                        else
                        {
                            $event_features = new \App\Models\EventFeatures;
                            $event_features->event_id = $event->id;
                            $event_features->feature_name = $event_f;
                            $event_features->created_at = date('Y-m-d H:i:s');
                            $event_features->save();
                        }
                        
                    }
                }
                
                $notification = array(
                    'message' => 'Event updated successfully!', 
                    'alert-type' => 'success'
                );
                return redirect('admin/event/list')->with($notification); 
            }
        }
    }

    public function eventActiveInactive(Request $request)
    {
        try 
        {
            $event = Events::where('id',$request->event_id)->first();
            if($request->is_active == 1) 
            {
                $event->is_active = $request->is_active;
                $msg = "Event Activated Successfully!";
            }
            else
            {
                $event->is_active = $request->is_active;
                $msg = "Event Deactivated Successfully!";
            }            
            $event->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        } 
        catch(\Exception $ex) 
        {
            return view('errors.500');            
        }        
    }
    
    public function deleteEvent(Request $request)
    {
        $event = Events::where('id', $request->event_id)->first();
        if($event)
        {
            $event->deleted_at = Carbon::now();          
            $event->save();
            $packages = Package::where('event_id',$request->event_id)->get();
          
            foreach ($packages as $package)
            {
                $package->deleted_at = Carbon::now();
                $package->save();
            }
            $result['status'] = 'true';
            $result['msg'] = "Event Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong!!";
            return $result;
        }
    }

    public function getExportEvents(Request $request)
    {
        try
        {
            $global_languages_ids = \App\Models\GlobalLanguage::where('status',1)
            ->where('is_deleted', 0)
            ->pluck('id');
            $events = Events::select('id as Id', 'event_name as Event Name', 'event_desc as Event Description')
                ->whereIn('language_id', $global_languages_ids)
                ->orderBy('id','desc')
                ->get()
                ->toArray();
            $sheetTitle = 'Events';
            return $this->exportEvents($events, $sheetTitle);
        } 
        catch(\Exception $ex) 
        {
            return redirect($request->segment(1).'/event/list');
        }
    }

    public function deleteEventFeature(Request $request)
    {
        $event_features = \App\Models\EventFeatures::where('id', $request->event_feature_id)->first();
        if($event_features)
        {
            $event_features->delete();
            $result['status'] = 'true';
            $result['msg'] = 'Event feature deleted successfully!';
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = 'Something went wrong!';
            return $result;
        }
    }

    public function getEventFeature($id)
    {
        $event_features = \App\Models\EventFeatures::where('event_id', $id)->get();
        return $event_features;
    }

    public function filterEvent(Request $request)
    {        
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        DB::statement(DB::raw('set @rownum=0'));
        $events = Events::select('id', 'event_name', 'event_image', 'event_desc','is_active',
        DB::raw('@rownum  := @rownum  + 1 AS rownum'),DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as e_created_at"));
        if($request->filter_event_lang == 'all')
        {
            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)->get();                    
            $global_languages_ids = $global_languages_count->pluck('id'); 
            $events->where(function($query) use($global_languages_ids){            
                return $query->whereNull('deleted_at')->whereIn('language_id', $global_languages_ids);
            });
        }
        else
        {
            $events->where(function($query) use($request){               
                return $query->where('language_id', $request->filter_event_lang)->whereNull('deleted_at');
            });
        }
        $events = $events->get();
        return Datatables::of($events)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    public function uploadCKeditorEventImage(Request $request)
    {
        $folder_name = 'ckeditor-event-image';
        uploadCKeditorImage($request, $folder_name);
    }
}

