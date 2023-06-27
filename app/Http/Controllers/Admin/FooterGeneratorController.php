<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use DataTables;
use Auth;
use DB;

class FooterGeneratorController extends Controller
{
    public function listFooterGenerator(Request $request)
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
                $footer_generator = \App\Models\FooterGenerator::select('id','footer_group','sort_order',
                DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as fg_created_at"))
                ->where('is_deleted', 0)
                ->whereIn('language_id', $global_languages_ids)
                ->get();
            }
            else
            {
                $global_language = \App\Models\GlobalLanguage::where('status',1)
                ->where('is_deleted', 0)
                ->first();
                $footer_generator = \App\Models\FooterGenerator::select('id','footer_group','sort_order',
                DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as fg_created_at"))
                ->where('is_deleted', 0)
                ->where('language_id', $global_language->id)
                ->get();
            }            
            return Datatables::of($footer_generator)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true); 
        }

        $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        ->select('global_language.id','alpha2','langEN','is_default')
        ->where('status',1)
        ->where('is_deleted', 0)
        ->get();

        return view('admin.settings.footer-generator.list', compact('languages'));
    }

    public function showFooterGenForm()
    {
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        if($global_languages_count >= 2)
        {            
            $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN','is_default')
            ->where('status',1)
            ->where('is_deleted', 0)
            ->get();
            return view('admin.settings.footer-generator.add',compact('languages'));
        }
        else
        {
            $language = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN','is_default')
            ->where('status',1)
            ->where('is_deleted', 0)
            ->first();            
            return view('admin.settings.footer-generator.add',compact('language'));
        }                    
    }

    public function addFooterGen(Request $request)
    {
        try {
            $footer_generator = new \App\Models\FooterGenerator;
            $footer_generator->language_id = $request->language;
            $footer_generator->footer_group = $request->footer_group;
            $footer_generator->sort_order = $request->footer_sort_order;
            $footer_generator->created_at = date('Y-m-d H:i:s');
            if($footer_generator->save())
            {                        
                $arr = collect($request->add_common_arr);
                $footer_section_arr = $arr->split(ceil($arr->count()/3))->toArray();           
                foreach ($footer_section_arr as $value) {         
                    $footer_link_section = new \App\Models\FooterLinkSection;
                    $footer_link_section->footer_gen_id = $footer_generator->id;                    
                    foreach ($value as $key => $value) {                    
                        if($key == 0)
                        {
                            $footer_link_section->name = $value;                        
                        }
                        if($key == 1)
                        {
                            $footer_link_section->link = $value;                        
                        } 
                        if($key == 2)
                        {
                            $footer_link_section->sort_order = $value;                                    
                        }                                                                                         
                    }
                    $footer_link_section->save();
                } 
                $notification = array(
                    'message' => config('message.FooterGenerator.FooterGeneratorAddSuccess'), 
                    'alert-type' => 'success'
                );                                
                return redirect('/admin/footer-generator')->with($notification);                                                     
            }
        } catch (\Exception $e) {
            return view('errors.500');
        }                
    }

    public function editFooterGenForm($id)
    {
        $footer_generator = \App\Models\FooterGenerator::where('is_deleted', 0)
        ->where('id', $id)->first();
        $footer_link_section = \App\Models\FooterLinkSection::where('footer_gen_id', $footer_generator->id)->get();
        
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        if($global_languages_count >= 2)
        {
            $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN','is_default')
            ->where('status',1)
            ->where('is_deleted', 0)
            ->get();    
            return view('admin.settings.footer-generator.edit', compact('footer_generator','languages','footer_link_section'));
        }
        else
        {
            $language = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN','is_default')
            ->where('status',1)
            ->where('is_deleted', 0)
            ->first();
            return view('admin.settings.footer-generator.edit', compact('footer_generator','language','footer_link_section'));
        }        
    }

    public function updateFooterGen(Request $request)
    {                      
        try {
            $footer_generator = \App\Models\FooterGenerator::where('id', $request->foot_gen_id)->first();
            $footer_generator->language_id = $request->language;
            $footer_generator->footer_group = $request->footer_group;
            $footer_generator->sort_order = $request->footer_sort_order;            
            if($footer_generator->save())
            {                  
                if($request->common_arr)
                {                    
                    $footer_section_arr = array_chunk($request->common_arr, 4);           
                    foreach ($footer_section_arr as $value) { 
                        if(is_array($value))
                        {
                            $footer_link_section = \App\Models\FooterLinkSection::where('id', $value[0])->first();
                            if($footer_link_section)
                            {
                                $footer_link_section->footer_gen_id = $footer_generator->id;                    
                                foreach ($value as $key => $value) {                    
                                    if($key == 1)
                                    {
                                        $footer_link_section->name = $value;                        
                                    }
                                    if($key == 2)
                                    {
                                        $footer_link_section->link = $value;                        
                                    } 
                                    if($key == 3)
                                    {
                                        $footer_link_section->sort_order = $value;                                    
                                    }                                                                                         
                                }
                                $footer_link_section->save();
                            }                        
                        }                                                                  
                    } 
                }      
                
                if($request->add_common_arr)
                {
                    $arr = collect($request->add_common_arr);
                    $count = count($arr);
                    if($count > 3)
                    {                        
                        $footer_section_arr = array_chunk($request->add_common_arr, 3);  
                        foreach ($footer_section_arr as $key => $value) {         
                            $footer_link_section = new \App\Models\FooterLinkSection;
                            $footer_link_section->footer_gen_id = $footer_generator->id;                    
                            foreach ($value as $key => $value) {                    
                                if($key == 0)
                                {
                                    $footer_link_section->name = $value;                        
                                }
                                if($key == 1)
                                {
                                    $footer_link_section->link = $value;                        
                                } 
                                if($key == 2)
                                {
                                    $footer_link_section->sort_order = $value;                                    
                                }                                                                                         
                            }
                            $footer_link_section->save();
                        }          
                    }
                    else
                    {   
                        if($request->add_common_arr){       
                            $footer_link_section = new \App\Models\FooterLinkSection;
                            $footer_link_section->footer_gen_id = $footer_generator->id;               
                            $footer_link_section->name = $request->add_common_arr[0];
                            $footer_link_section->link = $request->add_common_arr[1];
                            $footer_link_section->sort_order = $request->add_common_arr[2];       
                            $footer_link_section->save();
                        }              
                    }                                        
                }                                  
                $notification = array(
                    'message' => config('message.FooterGenerator.FooterlinksUpdateSuccess'), 
                    'alert-type' => 'success'
                );                                
                return redirect('/admin/footer-generator')->with($notification);                                                     
            }
        } catch (\Exception $e) {
            return view('errors.500');
        }   
    }

    public function deleteFooterGen(Request $request)
    {
        $footer_link_section = \App\Models\FooterLinkSection::where('id', $request->data_f_link_s_id)->first();
        if($footer_link_section)
        {
            $footer_link_section->delete();
            $result['status'] = 'true';
            $result['msg'] = 'Footer link deleted successfully!';
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = config('message.500.SomeThingWrong');
            return $result;
        }
    }

    public function deleteParentFooterGen(Request $request)
    {
        $footer_generator = \App\Models\FooterGenerator::where('id', $request->footer_gen_id)->first();
        if($footer_generator)
        {
            $footer_generator->is_deleted = 1;
            $footer_generator->save();
            $result['status'] = 'true';
            $result['msg'] = config('message.FooterGenerator.FooterGenDeleteSuccess');
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = config('message.500.SomeThingWrong');
            return $result;
        }
    }

    public function filterFooterGen(Request $request)
    {        
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $footer_generator = \App\Models\FooterGenerator::select('id','footer_group','sort_order',
        DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as fg_created_at"));        
        if($request->filter_footer_gen_lang == 'all')
        {
            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)->get();                    
            $global_languages_ids = $global_languages_count->pluck('id');
            $footer_generator->where(function($query) use($global_languages_ids){            
                return $query->where('is_deleted', 0)->whereIn('language_id', $global_languages_ids);
            });
        }
        else
        {
            $footer_generator->where(function($query) use($request){               
                return $query->where('language_id', $request->filter_footer_gen_lang)->where('is_deleted', 0);
            });
        }        
        $footer_generator->get();

        return Datatables::of($footer_generator)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);         
        
        return view('admin.settings.footer-generator.list');
    }
}
