<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\GlobalLanguage;
use App\Models\Services;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;
use Session;
use Image;
use App\Traits\ReuseFunctionTrait;

class ServicesController extends Controller
{
    use ReuseFunctionTrait;

    public function getServices()
    {
        $page_name = 'index';
        $languages = GlobalLanguage::getAllLanguages();
        $baseUrl = $this->getBaseUrl();
        return view('admin.services.index',compact('languages','page_name','baseUrl'));
    }

    public function getServicesList(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $services = Services::select('services.id', 'service_name', 'service_image', 'services.status',
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                        ->leftjoin('global_language as gl','gl.id','=','services.language_id')
                        ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                        ->where('gl.is_default',1)
                        ->where('gl.status',1)
                        ->whereNull('services.deleted_at')
                        ->orderBy('services.updated_at','desc')
                        ->get();  

        if($request['lang_id'] != null)
        {
            DB::statement(DB::raw('set @rownum=0'));

            $services = Services::select('id', 'service_name', 'service_image', 'status',
                                DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                                ->where('language_id',$request['lang_id'])
                                ->whereNull('deleted_at')
                                ->orderBy('updated_at','desc')
                                ->get(); 
        }
        return Datatables::of($services)->make(true);  
    }

    public function serviceAddView()
    {
        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang); 

        $page_name = 'add';
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        if($global_languages_count >= 2)
        {
            $languages = GlobalLanguage::getAllLanguages();                        
            return view('admin.services.add',compact('languages','page_name','categories'));
        }
        else
        {
            $language = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
            ->select('global_language.id','alpha2','langEN')
            ->where('status',1)
            ->where('is_deleted',0)
            ->first();
            return view('admin.services.add',compact('language','page_name','categories'));
        }
        
    }

    public function addService(Request $request)
    {
        try
        {
            $messsages = array(
                'language_id.required' => 'Please select language',
                'service_name.required' => 'Please enter service name',
                'service_image.required' => 'Please select image',
                'short_desc.required' => 'Please enter short description',
                "link.required" => 'Please provide link',
                'sort_order.required' => 'Please enter sort order'
            );
        
            $validator = Validator::make($request->all(), [
                'language_id'=>'required',
                'service_name'=>'required',
                'service_image'=>'required',
                'short_desc'=>'required',
                'link'=>'required',
                'sort_order'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }  
            
            $max_height = config('app.service_image.height');
            $max_width = config('app.service_image.width');
            $loaded_image_height = $request->loaded_image_height;
            $loaded_image_width = $request->loaded_image_width;

            $service = new Services;
            $service->language_id = $request->language_id;
            $service->service_name = $request->service_name;

            if($request->hasFile('service_image')) 
            {
                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('service_image');                    
                    $filename = 'service_'.time().'_'.str_replace(' ', '', $image->getClientOriginalName());

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.service_image.width'), config('app.service_image.height'));
                    $image->move(public_path('assets/images/services/'), $filename);
                    $image_resize->save(public_path('assets/images/services/' .$filename));    
                    $service->service_image = $filename;                            
                }
                else
                {
                    $image = $request->file('service_image');
                    $filename = 'service_'.time().'_'.str_replace(' ', '', $image->getClientOriginalName());
                    $image->move(public_path('assets/images/services/'), $filename);
                    $service->service_image = $request->file('service_image')->getClientOriginalName(); 
                }             
            }
            $service->short_desc = $request->short_desc;
            $service->category_id = $request->link;
            $service->sort_order = $request->sort_order;
            $service->status = $request->status;
            $service->save();

            $notification = array(
                'message' => 'Service added successfully!', 
                'alert-type' => 'success'
            );
            return redirect('admin/services')->with($notification);       
        }
        catch (\Exception $e) 
        {
                Session::flash('error', $e->getMessage());            
            return redirect('admin/services');
        }
    }

    public function serviceEditView($id)
    {
        $service = Services::findOrFail($id);
        $page_name = 'edit';

        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang);

        if(!empty($service))
        {                                                            
            $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
            ->where('is_deleted', 0)
            ->count();
            if($global_languages_count >= 2)
            {
                $languages = GlobalLanguage::getAllLanguages();       
                $baseUrl = $this->getBaseUrl();                 
                return view('admin.services.edit',compact('service','languages','page_name','baseUrl',
                'categories'));                
            }   
            else
            {
                $language = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                ->select('global_language.id','alpha2','langEN')
                ->where('status',1)
                ->where('is_deleted',0)
                ->first();
                $baseUrl = $this->getBaseUrl();
                return view('admin.services.edit',compact('service','language','page_name','baseUrl',
                'categories'));
            }           
        }
    }

    public function updateService(Request $request)
    {
        $service = Services::findOrFail($request->service_id);
  
        if(!empty($service)) 
        {
            $messsages = array(
                'language_id.required' => 'Please select language',
                'service_name.required' => 'Please enter service name',
                'short_desc.required' => 'Please enter short description',
                "link.required" => 'Please provide link',
                'sort_order.required' => 'Please enter sort order'
            );
        
            $validator = Validator::make($request->all(), [
                'language_id'=>'required',
                'service_name'=>'required',
                'short_desc'=>'required',
                'link'=>'required',
                'sort_order'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }    
   
            $service->language_id = $request->language_id;
            $service->service_name = $request->service_name;

            if($request->hasFile('service_image')) 
            {
                $max_height = config('app.service_image.height');
                $max_width = config('app.service_image.width');
                $loaded_image_height = $request->loaded_image_height;
                $loaded_image_width = $request->loaded_image_width;

                $path = public_path('/assets/images/services').'/'.$service->service_image;                
                if(file_exists($path))
                {
                    unlink($path);
                }

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('service_image');
                    $filename = 'service_'.time().'_'.str_replace(' ', '', $image->getClientOriginalName());

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.service_image.width'), config('app.service_image.height'));
                    $image->move(public_path('assets/images/services/'), $filename);
                    $image_resize->save(public_path('assets/images/services/' .$filename));                                
                }
                else
                {
                    $image = $request->file('service_image');
                    $filename = 'service_'.time().'_'.str_replace(' ', '', $image->getClientOriginalName());
                    $image->move(public_path('assets/images/services/'), $filename);
                    $service->service_image = $request->file('service_image')->getClientOriginalName(); 
                }
                $service->service_image = $filename;
            }
            $service->short_desc = $request->short_desc;
            $service->category_id = $request->link;
            $service->sort_order = $request->sort_order;
            $service->status = $request->status;
            $service->save();

            $notification = array(
                'message' => 'Service updated successfully!', 
                'alert-type' => 'success'
            );

            return redirect('admin/services')->with($notification);      
        }    
    }

    public function serviceActiveInactive(Request $request)
    {
        try 
        {
            $service = Services::where('id',$request->service_id)->first();
            if($request->is_active == 1) 
            {
                $service->status = $request->is_active;
                $msg = "Service Activated Successfully!";
            }
            else
            {
                $service->status = $request->is_active;
                $msg = "Service Deactivated Successfully!";
            }            
            $service->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        } 
        catch(\Exception $ex) 
        {
            return view('errors.500');            
        }        
    }

    public function deleteService(Request $request)
    {
        $service = Services::select('id')
                        ->where('id', $request->service_id)
                        ->first();
                        
        if(!empty($service))
        {
            $service->deleted_at = Carbon::now();
            $service->save();
            $result['status'] = 'true';
            $result['msg'] = "Service Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong!!";
            return $result;
        }
    }

}
?>
