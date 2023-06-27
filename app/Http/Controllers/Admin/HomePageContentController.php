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

class HomePageContentController extends Controller
{
    use ReuseFunctionTrait;

    /* ###########################################
    // Function: listHomePageContent
    // Description: Get list of home page content
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function listHomePageContent(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
            
            $global_languages_ids = \App\Models\GlobalLanguage::where('is_deleted', 0)->pluck('id');             
            $HomePageContent = \App\Models\HomePageContent::select('home_page_content.id',
            'home_page_content.title','home_page_content.description','home_page_content.link', 
            'home_page_content.image_text_1','home_page_content.image_text_2','home_page_content.image_1', 
            'home_page_content.image_2','world_languages.langEN as langName', 
            'home_page_content.mobile_image_1','home_page_content.mobile_image_2',
            DB::raw("date_format(home_page_content.created_at,'%Y-%m-%d %h:%i:%s') as hpc_created_at"))
            ->join('global_language','global_language.id','=','home_page_content.language_id')
            ->join('world_languages','world_languages.id','=','global_language.language_id')    
            ->where('home_page_content.is_deleted', 0)  
            ->whereIn('home_page_content.language_id', $global_languages_ids)      
            ->get();            

            return Datatables::of($HomePageContent)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true);
        }   
        $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        ->select('global_language.id','alpha2','langEN','is_default')
        ->where('status',1)
        ->where('is_deleted', 0)
        ->get();

        return view('admin.home-page-content.list',compact('languages'));
    }

    /* ###########################################
    // Function: homePageContentForm
    // Description: Shoe home page content form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function homePageContentForm()
    {
        $HomePageContent = \App\Models\HomePageContent::get();
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang);  
        $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
        ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
        ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id');
        if($global_languages_count >= 2)
        {
            $used_id = array();            
            foreach ($HomePageContent as $hpcontent) {
                if($hpcontent->is_deleted == 0)
                {                    
                    array_push($used_id, $hpcontent->language_id);
                }                              
            }            
            $global_languages->where(function($query) use($used_id){
                $query->whereNotIn('global_language.id', $used_id)->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });
            $languages = $global_languages->get();            
            return view('admin.home-page-content.add', compact('languages','categories'));            
        }
        else
        {
            $global_languages->where(function($query){
                $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });
            $language = $global_languages->first();            
            return view('admin.home-page-content.add', compact('language','categories'));            
        }
        
    }

    /* ###########################################
    // Function: addHomePageContent
    // Description: Add home page content to database
    // Parameter: language: Int, title: String, description: String, link: String, 
                            image_text_1: String, image_1: File, image_text_2: String, image_2: File 
    // ReturnType: view
    */ ###########################################
    // public function addHomePageContent(Request $request)
    // {   
    //     try {            
    //         $HomePageContent = \App\Models\HomePageContent::get();
    //         if(!empty($HomePageContent))
    //         {
    //             if(count($HomePageContent) >= 1)
    //             {
    //                 $languages = \App\Models\GlobalLanguage::where('is_deleted',0)
    //                 ->get();
    //                 if(count($languages) == 1)
    //                 {
    //                     $notification = array(
    //                         'message' => "Sorry you can't add multiple record for default system language.", 
    //                         'alert-type' => 'error'
    //                     );            
    //                     return redirect()->back()->with($notification); 
    //                 }
    //             }
    //         }
            
    //         $HomePageContent = new \App\Models\HomePageContent;
    //         $HomePageContent->language_id = $request->language;
    //         $HomePageContent->title = $request->title;
    //         $HomePageContent->description = $request->description;
    //         $HomePageContent->link = $request->link;
    //         $HomePageContent->image_text_1 = $request->image_text_1;
    //         if($request->hasFile('image_1'))
    //         {
    //             $max_height = config('app.home_page_content.height_1');
    //             $max_width = config('app.home_page_content.width_1');
    //             $loaded_image_height = $request->loaded_image_height_1;
    //             $loaded_image_width = $request->loaded_image_width_1;                

    //             if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
    //             {    
    //                 $image = $request->file('image_1');
    //                 $ext = $request->file('image_1')->extension();
    //                 $filename = rand().'_'.time().'.'.$ext;

    //                 $image_resize = Image::make($image->getRealPath());              
    //                 $image_resize->resize(config('app.home_page_content.width'), config('app.home_page_content.height'));
    //                 $image->move(public_path().'/assets/images/home-page-content/', $filename); 
    //                 $image_resize->save(public_path('/assets/images/home-page-content/'.$filename));                                                 
    //             }
    //             else
    //             {
    //                 $photo = $request->file('image_1');
    //                 $ext = $request->file('image_1')->extension();
    //                 $filename = rand().'_'.time().'.'.$ext;   
    //                 $photo->move(public_path().'/assets/images/home-page-content/', $filename); 
    //             }

    //             // $photo = $request->file('image_1');
    //             // $ext = $request->file('image_1')->extension();
    //             // $filename = rand().'_'.time().'.'.$ext;   
    //             // $photo->move(public_path().'/assets/images/home-page-content', $filename);     
    //             $HomePageContent->image_1 = $filename;    
    //         }            
    //         $HomePageContent->image_text_2 = $request->image_text_2;
    //         if($request->hasFile('image_2'))
    //         {
    //             $max_height = config('app.home_page_content.height_2');
    //             $max_width = config('app.home_page_content.width_2');
    //             $loaded_image_height = $request->loaded_image_height_2;
    //             $loaded_image_width = $request->loaded_image_width_2;                

    //             if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
    //             {    
    //                 $image = $request->file('image_2');                    
    //                 $ext = $request->file('image_2')->extension();
    //                 $filename = rand().'_'.time().'.'.$ext;

    //                 $image_resize = Image::make($image->getRealPath());              
    //                 $image_resize->resize(config('app.home_page_content.width'), config('app.home_page_content.height'));
    //                 $image->move(public_path().'/assets/images/home-page-content', $filename);
    //                 $image_resize->save(public_path('/assets/images/home-page-content/' .$filename));                                                 
    //             }
    //             else
    //             {
    //                 $photo = $request->file('image_2');
    //                 $ext = $request->file('image_2')->extension();
    //                 $filename = rand().'_'.time().'.'.$ext;   
    //                 $photo->move(public_path().'/assets/images/home-page-content', $filename); 
    //             }

    //             // $photo = $request->file('image_2');
    //             // $ext = $request->file('image_2')->extension();
    //             // $filename = rand().'_'.time().'.'.$ext;   
    //             // $photo->move(public_path().'/assets/images/home-page-content', $filename);    
    //             $HomePageContent->image_2 = $filename;     
    //         }  
                        
    //         $HomePageContent->save(); 
    //         $notification = array(
    //             'message' => config('message.HomePageContent.HomePageContentAddSuccess'), 
    //             'alert-type' => 'success'
    //         );            
    //         return redirect('/admin/home-page-content')->with($notification);
    //     } catch (\Exception $e) {
    //         return view('errors.500');
    //     }     
        
    // }
    
    public function addHomePageContent(Request $request)
    {           
        try {            
            $HomePageContent = \App\Models\HomePageContent::get();
            if(!empty($HomePageContent))
            {
                if(count($HomePageContent) >= 1)
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
            
            $HomePageContent = new \App\Models\HomePageContent;
            $HomePageContent->language_id = $request->language;
            $HomePageContent->title = $request->title;
            $HomePageContent->description = $request->description;
            $HomePageContent->category_id_1 = $request->link;
            $HomePageContent->category_id_2 = $request->link_2;
            $HomePageContent->image_text_1 = $request->image_text_1;
            if($request->hasFile('image_1'))
            {
                $max_height = config('app.home_page_content.height');
                $max_width = config('app.home_page_content.width');
                $loaded_image_height = $request->loaded_image_height_1;
                $loaded_image_width = $request->loaded_image_width_1;                

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('image_1');
                    $ext = $request->file('image_1')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.home_page_content.width'), config('app.home_page_content.height'));
                    $image_resize->save(public_path('/assets/images/home-page-content/desktop/'.$filename));                                                 
                }
                else
                {
                    $photo = $request->file('image_1');
                    $ext = $request->file('image_1')->extension();
                    $filename = rand().'_'.time().'.'.$ext;   
                    $photo->move(public_path().'/assets/images/home-page-content/desktop/', $filename); 
                }
                   
                $HomePageContent->image_1 = $filename;    
            }            
            $HomePageContent->image_text_2 = $request->image_text_2;
            if($request->hasFile('image_2'))
            {
                $max_height = config('app.home_page_content.height');
                $max_width = config('app.home_page_content.width');
                $loaded_image_height = $request->loaded_image_height_2;
                $loaded_image_width = $request->loaded_image_width_2;                

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('image_2');                    
                    $ext = $request->file('image_2')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.home_page_content.width'), config('app.home_page_content.height'));
                    $image_resize->save(public_path('/assets/images/home-page-content/desktop/' .$filename));                                                 
                }
                else
                {
                    $photo = $request->file('image_2');
                    $ext = $request->file('image_2')->extension();
                    $filename = rand().'_'.time().'.'.$ext;   
                    $photo->move(public_path().'/assets/images/home-page-content/desktop/', $filename); 
                }
               
                $HomePageContent->image_2 = $filename;     
            }  
            
            if($request->hasFile('mobile_image_1'))
            {
                $max_height = config('app.home_page_content_mobile.height');
                $max_width = config('app.home_page_content_mobile.width');
                $loaded_image_height = $request->loaded_image_height_1;
                $loaded_image_width = $request->loaded_image_width_1;                

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('mobile_image_1');
                    $ext = $request->file('mobile_image_1')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.home_page_content_mobile.width'), config('app.home_page_content_mobile.height'));
                    $image_resize->save(public_path('/assets/images/home-page-content/mobile/'.$filename));                                                 
                }
                else
                {
                    $photo = $request->file('mobile_image_1');
                    $ext = $request->file('mobile_image_1')->extension();
                    $filename = rand().'_'.time().'.'.$ext;   
                    $photo->move(public_path().'/assets/images/home-page-content/mobile/', $filename); 
                }
                    
                $HomePageContent->mobile_image_1 = $filename;    
            }
            
            if($request->hasFile('mobile_image_2'))
            {
                $max_height = config('app.home_page_content_mobile.height');
                $max_width = config('app.home_page_content_mobile.width');
                $loaded_image_height = $request->loaded_image_height_1;
                $loaded_image_width = $request->loaded_image_width_1;                

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('mobile_image_2');
                    $ext = $request->file('mobile_image_2')->extension();
                    $filename = rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.home_page_content_mobile.width'), config('app.home_page_content_mobile.height'));
                    $image_resize->save(public_path('/assets/images/home-page-content/mobile/'.$filename));                                                 
                }
                else
                {
                    $photo = $request->file('mobile_image_2');
                    $ext = $request->file('mobile_image_2')->extension();
                    $filename = rand().'_'.time().'.'.$ext;   
                    $photo->move(public_path().'/assets/images/home-page-content/mobile/', $filename); 
                }
                    
                $HomePageContent->mobile_image_2 = $filename;    
            }

            $HomePageContent->save(); 
            $notification = array(
                'message' => config('message.HomePageContent.HomePageContentAddSuccess'), 
                'alert-type' => 'success'
            );            
            return redirect('/admin/home-page-content')->with($notification);
        } catch (\Exception $e) {
            return view('errors.500');
        }     
        
    }

    /* ###########################################
    // Function: editHomePageContent
    // Description: Get home page content data for updating
    // Parameter: id: Int, 
    // ReturnType: view
    */ ###########################################
    public function editHomePageContent($id)
    {        
        $HomePageContent = \App\Models\HomePageContent::select('home_page_content.id',
        'home_page_content.title','home_page_content.description','home_page_content.link', 
        'home_page_content.image_text_1','home_page_content.image_text_2','home_page_content.image_1', 
        'home_page_content.image_2','home_page_content.mobile_image_1','home_page_content.mobile_image_2',
        'world_languages.langEN as langName','home_page_content.category_id_1','home_page_content.category_id_2')
        ->join('global_language','global_language.id','=','home_page_content.language_id')
        ->join('world_languages','world_languages.id','=','global_language.language_id')            
        ->where('home_page_content.id', $id)
        ->first();   
        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang);     
        return view('admin.home-page-content.edit',compact('HomePageContent','categories'));   
    }

    /* ###########################################
    // Function: updateHomePageContent
    // Description: Get home page content data for updating
    // Parameter: language: Int, title: String, description: String, link: String, 
                image_text_1: String, image_1: File, image_text_2: String, image_2: File 
    // ReturnType: view
    */ ###########################################
    public function updateHomePageContent(Request $request)
    {  
        try {
            $HomePageContent = \App\Models\HomePageContent::where('id', $request->hpc_id)->first();        
            if($HomePageContent)
            {            
                $HomePageContent->title = $request->title;
                $HomePageContent->description = $request->description;
                $HomePageContent->category_id_1 = $request->link;
                $HomePageContent->category_id_2 = $request->link_2;
                $HomePageContent->image_text_1 = $request->image_text_1;
                if($request->hasFile('image_1_update'))
                {                         
                    $path = public_path('/assets/images/home-page-content/desktop').'/'.$HomePageContent->image_1;                
                    if(file_exists($path))
                    {
                        unlink($path);
                    }

                    $max_height = config('app.home_page_content.height');
                    $max_width = config('app.home_page_content.width');
                    $loaded_image_height = $request->loaded_image_height_1;
                    $loaded_image_width = $request->loaded_image_width_1;                

                    if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                    {    
                        $image = $request->file('image_1_update');
                        $ext = $request->file('image_1_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;

                        $image_resize = Image::make($image->getRealPath());              
                        $image_resize->resize(config('app.home_page_content.width'), config('app.home_page_content.height'));
                        $image_resize->save(public_path('/assets/images/home-page-content/desktop/'.$filename));                                                 
                    }
                    else
                    {
                        $photo = $request->file('image_1_update');
                        $ext = $request->file('image_1_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;   
                        $photo->move(public_path().'/assets/images/home-page-content/desktop/', $filename); 
                    }                            
                    $HomePageContent->image_1 = $filename;                    
                }          
                $HomePageContent->image_text_2 = $request->image_text_2;
                if($request->hasFile('image_2_update'))
                {                                     
                    $path = public_path('/assets/images/home-page-content/desktop').'/'.$HomePageContent->image_2;                
                    if(file_exists($path))
                    {
                        unlink($path);
                    }

                    $max_height = config('app.home_page_content.height');
                    $max_width = config('app.home_page_content.width');
                    $loaded_image_height = $request->loaded_image_height_2;
                    $loaded_image_width = $request->loaded_image_width_2;                

                    if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                    {    
                        $image = $request->file('image_2_update');                    
                        $ext = $request->file('image_2_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;

                        $image_resize = Image::make($image->getRealPath());              
                        $image_resize->resize(config('app.home_page_content.width'), config('app.home_page_content.height'));
                        $image_resize->save(public_path('/assets/images/home-page-content/desktop/' .$filename));                                                 
                    }
                    else
                    {
                        $photo = $request->file('image_2_update');
                        $ext = $request->file('image_2_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;   
                        $photo->move(public_path().'/assets/images/home-page-content/desktop/', $filename);
                    }                    
                    $HomePageContent->image_2 = $filename;                    
                } 

                if($request->hasFile('mobile_image_1_update'))
                {                                     
                    $path = public_path('/assets/images/home-page-content/mobile').'/'.$HomePageContent->mobile_image_1;                
                    if(file_exists($path))
                    {
                        unlink($path);
                    }

                    $max_height = config('app.home_page_content_mobile.height');
                    $max_width = config('app.home_page_content_mobile.width');
                    $loaded_image_height = $request->loaded_mobile_image_height_1;
                    $loaded_image_width = $request->loaded_mobile_image_height_1;                

                    if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                    {    
                        $image = $request->file('mobile_image_1_update');                    
                        $ext = $request->file('mobile_image_1_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;

                        $image_resize = Image::make($image->getRealPath());              
                        $image_resize->resize(config('app.home_page_content_mobile.width'), config('app.home_page_content_mobile.height'));
                        $image_resize->save(public_path('/assets/images/home-page-content/mobile/' .$filename));                                                 
                    }
                    else
                    {
                        $photo = $request->file('mobile_image_1_update');
                        $ext = $request->file('mobile_image_1_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;   
                        $photo->move(public_path().'/assets/images/home-page-content/mobile/', $filename);
                    }                    
                    $HomePageContent->mobile_image_1 = $filename;                    
                }

                if($request->hasFile('mobile_image_2_update'))
                {                                     
                    $path = public_path('/assets/images/home-page-content/mobile').'/'.$HomePageContent->mobile_image_2;                
                    if(file_exists($path))
                    {
                        unlink($path);
                    }

                    $max_height = config('app.home_page_content_mobile.height');
                    $max_width = config('app.home_page_content_mobile.width');
                    $loaded_image_height = $request->loaded_mobile_image_height_2;
                    $loaded_image_width = $request->loaded_mobile_image_height_2;                

                    if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                    {    
                        $image = $request->file('mobile_image_2_update');                    
                        $ext = $request->file('mobile_image_2_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;

                        $image_resize = Image::make($image->getRealPath());              
                        $image_resize->resize(config('app.home_page_content_mobile.width'), config('app.home_page_content_mobile.height'));
                        $image_resize->save(public_path('/assets/images/home-page-content/mobile/' .$filename));                                                 
                    }
                    else
                    {
                        $photo = $request->file('mobile_image_2_update');
                        $ext = $request->file('mobile_image_2_update')->extension();
                        $filename = rand().'_'.time().'.'.$ext;   
                        $photo->move(public_path().'/assets/images/home-page-content/mobile/', $filename);
                    }                    
                    $HomePageContent->mobile_image_2 = $filename;                    
                } 
                                
                $HomePageContent->save(); 
                $notification = array(
                    'message' => config('message.HomePageContent.HomePageContentUpdateSuccess'), 
                    'alert-type' => 'success'
                );            
                return redirect('/admin/home-page-content')->with($notification);
            }
        } catch (\Exception $th) {
            return view('errors.500');
        }                              
    }

    /* ###########################################
    // Function: deleteHomePageContent
    // Description: Delete home page content data from database
    // Parameter: id: Int, 
    // ReturnType: array
    */ ###########################################
    public function deleteHomePageContent(Request $request)
    {
        $HomePageContent = \App\Models\HomePageContent::where('id', $request->hpc_id)->first();
        if($HomePageContent)
        {
            $HomePageContent->is_deleted = 1;
            $HomePageContent->save();
            $result['status'] = 'true';
            $result['msg'] = config('message.HomePageContent.HomePageContentDeleteSuccess');
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
    // Function: filterHomePageContent
    // Description: Apply filer on home page content
    // Parameter: filter_HPC_lang: Int
    // ReturnType: array
    */ ###########################################
    public function filterHomePageContent(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $HomePageContent = \App\Models\HomePageContent::select('home_page_content.id',
        'home_page_content.title','home_page_content.description','home_page_content.link', 
        'home_page_content.image_text_1','home_page_content.image_text_2','home_page_content.image_1', 
        'home_page_content.image_2','world_languages.langEN as langName','home_page_content.mobile_image_1','home_page_content.mobile_image_2',
        DB::raw("date_format(home_page_content.created_at,'%Y-%m-%d %h:%i:%s') as hpc_created_at"))
        ->join('global_language','global_language.id','=','home_page_content.language_id')
        ->join('world_languages','world_languages.id','=','global_language.language_id');         
        if($request->filter_HPC_lang == 'all')
        {
            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)->get();                    
            $global_languages_ids = $global_languages_count->pluck('id'); 
            $HomePageContent->where(function($query) use($global_languages_ids){            
                return $query->where('home_page_content.is_deleted', 0)->whereIn('home_page_content.language_id', $global_languages_ids);
            });
        }
        else
        {
            $HomePageContent->where(function($query) use($request){               
                return $query->where('home_page_content.language_id', $request->filter_HPC_lang)->where('home_page_content.is_deleted', 0);
            });
        }
        $HomePageContent = $HomePageContent->get();
        return Datatables::of($HomePageContent)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }
}
