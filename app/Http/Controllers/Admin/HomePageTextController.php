<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use DataTables;
use Auth;
use DB;

class HomePageTextController extends Controller
{
    public function listHomePageText(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

            $HomePageText = \App\Models\HomePageText::select('home_page_text.id',
            'home_page_text.content_1','home_page_text.content_2','world_languages.langEN as langName', 
            DB::raw("date_format(home_page_text.created_at,'%Y-%m-%d %h:%i:%s') as hpt_created_at"))
            ->join('global_language','global_language.id','=','home_page_text.language_id')
            ->join('world_languages','world_languages.id','=','global_language.language_id')    
            ->where('home_page_text.is_deleted', 0)        
            ->get();            

            return Datatables::of($HomePageText)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true);
        }        
        return view('admin.home-page-text.list');
    }

    public function showHomePageTextForm()
    {            
        $HomePageText = \App\Models\HomePageText::get();        
        if(count($HomePageText) > 0)
        {                                    
            $used_id = array();            
            foreach ($HomePageText as $hptext) {
                if($hptext->is_deleted == 0)
                {                    
                    array_push($used_id, $hptext->language_id);
                }                              
            }            
            $languages = \App\Models\GlobalLanguage::select('global_language.id',
            'wl.alpha2','wl.langEN','global_language.is_default')
            ->join('world_languages as wl','wl.id','=','global_language.language_id')  
            ->where('global_language.is_deleted',0)
            ->whereNotIn('global_language.id', $used_id)->get();                        
        }
        else
        {            
            $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN','is_default')
            ->where('status',1)
            ->where('is_deleted',0)
            ->get();              
        }   
        $defualt_languages_count = \App\Models\GlobalLanguage::where('is_deleted',0)->get();                              
        return view('admin.home-page-text.add',compact('languages', 'defualt_languages_count'));
    }

    public function addHomePageText(Request $request)
    {
        try {
            $HomePageText = \App\Models\HomePageText::where('is_deleted', 0)->get();
            if(!empty($HomePageText))
            {
                if(count($HomePageText) >= 1)
                {
                    $languages = \App\Models\GlobalLanguage::where('is_deleted',0)
                    ->get();
                    if(count($languages) == 1)
                    {
                        $notification = array(
                            'message' => "Sorry you can't add multiple record for default system language.", 
                            'alert-type' => 'error'
                        );            
                        return redirect()->back()->with($notification); 
                    }
                }
            }
            
            $HomePageText = new \App\Models\HomePageText;
            $HomePageText->language_id = $request->language;
            $HomePageText->content_1 = $request->content_1;
            $HomePageText->content_2 = $request->content_2;
            $HomePageText->save(); 
            $notification = array(
                'message' => config('message.HomePageText.HomePageTextAddSuccess'), 
                'alert-type' => 'success'
            );            
            return redirect('/admin/home-page-text')->with($notification);   
        } catch (\Exception $e) {
            return view('errors.500');
        }        
    }

    public function editHomePageText($id)
    {        
        $HomePageText = \App\Models\HomePageText::select('home_page_text.id',
        'home_page_text.content_1','home_page_text.content_2','world_languages.langEN as langName', 
        DB::raw("date_format(home_page_text.created_at,'%Y-%m-%d %h:%i:%s') as hpt_created_at"))
        ->join('global_language','global_language.id','=','home_page_text.language_id')
        ->join('world_languages','world_languages.id','=','global_language.language_id')            
        ->where('home_page_text.id', $id)
        ->first();        
        return view('admin.home-page-text.edit',compact('HomePageText'));   
    }

    public function updateHomePageText(Request $request)
    {
        try {
            $HomePageText = \App\Models\HomePageText::where('id', $request->hpt_id)->first();            
            if($HomePageText)
            {
                $HomePageText->content_1 = $request->content_1;
                $HomePageText->content_2 = $request->content_2;
                $HomePageText->save(); 
                $notification = array(
                    'message' => config('message.HomePageText.HomePageTextUpdateSuccess'), 
                    'alert-type' => 'success'
                );
                return redirect('/admin/home-page-text')->with($notification);   
            }
            else
            {
                $notification = array(
                    'message' => config('message.500.SomeThingWrong'), 
                    'alert-type' => 'error'
                );
                return redirect('/admin/home-page-text')->with($notification);
            }                        
        } catch (\Exception $e) {
            return view('errors.500');
        }  
    }

    public function deleteHomePageText(Request $request)
    {
        $HomePageText = \App\Models\HomePageText::where('id', $request->hpt_id)->first();            
        if($HomePageText)
        {
            $HomePageText->is_deleted = 1;
            $HomePageText->save();
            $result['status'] = 'true';
            $result['msg'] = config('message.HomePageText.HomePageTextDeleteSuccess');
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
