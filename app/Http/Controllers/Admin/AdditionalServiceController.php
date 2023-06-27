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
use Config;

class AdditionalServiceController extends Controller
{
    /* ###########################################
    // Function: listServices
    // Description: Display list of services
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function listServices(Request $request)
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
                $services = \App\Models\AdditionalService::select('id','name','status','text as description', 
                DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as ser_created_at"), 'price', 'image')
                ->where('is_deleted', 0)
                ->whereIn('language_id', $global_languages_ids)
                ->get();
            }else{
                $global_language = \App\Models\GlobalLanguage::where('status',1)
                ->where('is_deleted', 0)
                ->where('is_default', 1)
                ->first();
                $services = \App\Models\AdditionalService::select('id','name','status','text as description', 
                DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as ser_created_at"), 'price', 'image')
                ->where('is_deleted', 0)
                ->where('language_id', $global_language->id)
                ->get();                
            }
            
            return Datatables::of($services)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true); 
        }

        $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        ->select('global_language.id','alpha2','langEN','is_default')
        ->where('status',1)
        ->where('is_deleted', 0)
        ->get();
        
        return view('admin.events.additional-service.list',compact('languages'));
    }

    /* ###########################################
    // Function: showAdditionalServiceForm
    // Description: Show additional service add form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function showAdditionalServiceForm()
    {    
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)->where('is_deleted', 0)
        ->count();
        $default_currency = \App\Models\GlobalCurrency::select('currency.currency_symbol')
        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
        ->where('global_currency.is_default', 1)->first();
        if($global_languages_count >= 2)
        {
            $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status',1)
            ->where('global_language.is_deleted', 0)
            ->get(); 
            return view('admin.events.additional-service.add',compact('global_languages','default_currency'));
        }
        else
        {
            $global_language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status',1)
            ->where('global_language.is_deleted', 0)
            ->first(); 
            return view('admin.events.additional-service.add',compact('global_language','default_currency'));
        }        
    }

    /* ###########################################
    // Function: addAdditionalService
    // Description: Show additional service add form
    // Parameter: service_image: File, addit_service_name: String, 
                loaded_image_height: Int, loaded_image_width: Int, service_description: String,
                act_deact_service_chk: Boolean 
    // ReturnType: view
    */ ###########################################
    public function addAdditionalService(Request $request)
    {                                     
        // try {
            // dd($request->all());
            $msg = [
                'service_image.required' => "The service image is required.",
                'addit_service_name.required' => "The service name is required.",                    
            ];        
            $validator = Validator::make($request->all(), [
                'service_image' => 'required',
                'addit_service_name' => 'required',            
            ],$msg);
                
            if($validator->fails()) {
                return redirect('/admin/additional-service/add')
                            ->withErrors($validator)
                            ->withInput();
            }

            if($request->hasFile('service_image'))
            {
                $valid_file_ext = ['jpeg','jpg','png'];
                $ext = $request->file('service_image')->extension();                
                if(!in_array($ext, $valid_file_ext))
                {
                    return redirect('/admin/additional-service/add')->with('msg', "Invalid extension. Please upload image in .jpeg, .jpg, .png format.")->with('alert-class', false);
                }
            }
            
            $invalid_ext = 'false';
            if($request->hasFile('samples'))
            {                    
                $valid_file_ext = ['jpeg','jpg','png'];
                foreach($request->file('samples') as $image)
                {                        
                    $ext = $image->extension();                    
                    if(!in_array($ext, $valid_file_ext))
                    {
                        $invalid_ext = 'true';                        
                    }
                }                              
            }
            
            if($invalid_ext == 'true')
            {
                return redirect('/admin/additional-service/add')->with('msg', "Invalid extension. Please upload samples file in .jpeg, .jpg, .png format.")->with('alert-class', false);
            }

            $max_height = config('app.service_image.height');
            $max_width = config('app.service_image.width');
            $loaded_image_height = $request->loaded_image_height;
            $loaded_image_width = $request->loaded_image_width;
            
            // if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
            // {                
            //     return redirect()->back()->with('msg', config('message.AdditionalService.ServiceImageShoulBe'))->with('alert-class', false);
            // }
            
            $additional_service = new \App\Models\AdditionalService;
            $additional_service->language_id = $request->language;
            $additional_service->name = $request->addit_service_name;
            if($request->hasFile('service_image')) 
            {
                if($request->loaded_image_width != $max_width || $request->loaded_image_height != $max_height)
                {
                    $image       = $request->file('service_image');
                    $ext = $request->file('service_image')->extension();
                    $filename    = "service_".rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width, $max_height);
                    $image->move(public_path('assets/images/additional-service/'), $filename);
                    $image_resize->save(public_path('assets/images/additional-service/' .$filename));
                    $additional_service->image = $filename;
                }
                else
                {
                    $image = $request->file('service_image');
                    $filename = "service_".rand().'_'.time().'.'.$ext;
                    $image->move(public_path('assets/images/additional-service/'), $filename);
                    $additional_service->image = $request->file('service_image')->getClientOriginalName();
                }
            }

            $additional_service->image = $filename;
            $additional_service->text = $request->service_description;
            $additional_service->price = $request->price;            
            $additional_service->created_at = date('Y-m-d H:i:s');

            $max_sample_height = config('app.service_sample_image.height');
            $max_sample_width = config('app.service_sample_image.width');

            if($additional_service->save())
            {
                $value1 = array();
                $value2 = array();
                foreach ($request->requirement_labels as $requirement_label) {
                    $value1[] = $requirement_label;
                }
                
                foreach($request->requirements as $requirement){                             
                    $value2[] = $requirement;
                }

                for ($i=0; $i < count($value1); $i++) { 
                    $AdditionalServiceRequirement = new \App\Models\AdditionalServiceRequirement;
                    $AdditionalServiceRequirement->addi_serv_id = $additional_service->id;
                    $AdditionalServiceRequirement->requirements = $value1[$i];
                    $AdditionalServiceRequirement->value = $value2[$i];                        
                    $AdditionalServiceRequirement->save();
                }

                if($request->hasFile('samples'))
                {                
                    foreach($request->file('samples') as $image)
                    {                   
                        $ext = $image->extension();
                        $filename = "service_samples_".rand().'_'.time().'.'.$ext;   

                        $image_resize = Image::make($image->getRealPath());    
                        $image_resize->resize($max_sample_width, $max_sample_height);
                        $image->move(public_path('assets/images/additional-service/samples/'), $filename);
                        $image_resize->save(public_path('assets/images/additional-service/samples/' .$filename));
                      
                        $AdditionalServiceSamples = new \App\Models\AdditionalServiceSamples;
                        $AdditionalServiceSamples->addi_serv_id = $additional_service->id;
                        $AdditionalServiceSamples->image = $filename;
                        $AdditionalServiceSamples->save();;
                    }                    
                }
            }            
            $notification = array(
                'message' => config('message.AdditionalService.ServiceAddSuccess'), 
                'alert-type' => 'success'
            );                                
            return redirect('/admin/additional-service')->with($notification);                    
        // } catch (\Exception $th) {
        //     return view('errors.500');
        // }  
    }

    /* ###########################################
    // Function: editAdditionalService
    // Description: Edit service information
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editAdditionalService($id)
    {
        $services = \App\Models\AdditionalService::where('id', $id)->first(); 
        $services_requirement = \App\Models\AdditionalServiceRequirement::where('addi_serv_id', $services->id)->get();
        $services_samples = \App\Models\AdditionalServiceSamples::where('addi_serv_id', $services->id)->get();
        $default_currency = \App\Models\GlobalCurrency::select('currency.currency_symbol')
        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
        ->where('global_currency.is_default', 1)->first();
        $global_languages_count = \App\Models\GlobalLanguage::where('status',1)
        ->where('is_deleted', 0)
        ->count();
        $global_languages = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
        ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
        ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id');
        if($global_languages_count >= 2){
            
            $global_languages->where(function($query){
                return $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });        
            $global_languages = $global_languages->get();      
            return view('admin.events.additional-service.edit',compact('services','global_languages','services_requirement','services_samples','default_currency'));
        }
        else{
            $global_languages->where(function($query){
                return $query->where('global_language.status',1)->where('global_language.is_deleted', 0);
            });        
            $global_language = $global_languages->get();      
            return view('admin.events.additional-service.edit',compact('services','global_language','services_requirement','services_samples','default_currency'));
        }          
    }

    /* ###########################################
    // Function: updateService
    // Description: Update service information
    // Parameter: service_image: String, addit_service_name: Int, service_description: String, act_deact_service_chk: Int, service_image: File
    // ReturnType: view
    */ ###########################################
    public function updateService(Request $request)
    {                                   
        try {                         
            if($request->hasFile('service_image_update'))
            {
                $valid_file_ext = ['jpeg','jpg','png'];
                $ext = $request->file('service_image_update')->extension();                
                if(!in_array($ext, $valid_file_ext))
                {
                    return redirect()->back()->with('msg', "Invalid extension. Please upload image in .jpeg, .jpg, .png format.")->with('alert-class', false);
                }
            }
            
            $invalid_ext = 'false';
            if($request->hasFile('samples_update'))
            {                    
                $valid_file_ext = ['jpeg','jpg','png'];
                foreach($request->file('samples_update') as $image)
                {                        
                    $ext = $image->extension();                    
                    if(!in_array($ext, $valid_file_ext))
                    {
                        $invalid_ext = 'true';                        
                    }
                }                              
            }
            
            if($invalid_ext == 'true')
            {
                return redirect()->back()->with('msg', "Invalid extension. Please upload samples file in .jpeg, .jpg, .png format.")->with('alert-class', false);
            }

            $service = \App\Models\AdditionalService::where('id', $request->service_id)->first();
            $service->name = $request->addit_service_name;
            $service->language_id = $request->language;
            $service->price = $request->price;

            $max_height = config('app.service_image.height');
            $max_width = config('app.service_image.width');
            $loaded_image_height = $request->loaded_image_height;
            $loaded_image_width = $request->loaded_image_width;

            if($request->hasFile('service_image_update')) 
            {
                $path = public_path('/assets/images/additional-service').'/'.$service->image;                
                if(file_exists($path))
                {
                    unlink($path);
                }                
                if($request->loaded_image_width != $max_width || $request->loaded_image_height != $max_height)
                {
                    // echo "in if"; die;
                    $image       = $request->file('service_image_update');
                    $ext = $request->file('service_image_update')->extension();
                    $filename    = "service_".rand().'_'.time().'.'.$ext;

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width, $max_height);
                    $image->move(public_path('assets/images/additional-service/'), $filename);
                    $image_resize->save(public_path('assets/images/additional-service/' .$filename));
                    // dd($save);
                    $service->image = $filename;
                }
                else
                {
                    // echo "in else"; die;
                    $image = $request->file('service_image_update');
                    $filename = "service_".rand().'_'.time().'.'.$ext;
                    $image->move(public_path('assets/images/additional-service/'), $filename);
                    $service->image = $request->file('service_image_update')->getClientOriginalName();
                    $service->image = $filename;
                }
            }

            $service->text = $request->service_description;            
            // $service->status = $request->service_act_deact;
            $service->save();

            foreach($request->update_requirement_labels as $key => $requirement_label)
            {
                $AdditionalServiceRequirement = \App\Models\AdditionalServiceRequirement::where('id', $key)->first();
                $AdditionalServiceRequirement->addi_serv_id = $request->service_id;
                $AdditionalServiceRequirement->requirements = $requirement_label;
                $AdditionalServiceRequirement->save();                               
            }            

            foreach($request->update_requirements as $key => $requirement)
            {
                $AdditionalServiceRequirement = \App\Models\AdditionalServiceRequirement::where('id', $key)->first();
                $AdditionalServiceRequirement->addi_serv_id = $request->service_id;
                $AdditionalServiceRequirement->value = $requirement;
                $AdditionalServiceRequirement->save();                               
            }

            $value1 = array();
            $value2 = array();
            if(isset($request->requirement_labels))
            {
                foreach ($request->requirement_labels as $requirement_label) {
                    $value1[] = $requirement_label;
                }
            }
            
            if(isset($request->requirements))
            {
                foreach($request->requirements as $requirement){                             
                    $value2[] = $requirement;
                }
            }
                        
            if(!empty($value1) && !empty($value2))
            {
                for ($i=0; $i < count($value1); $i++) { 
                    $AdditionalServiceRequirement = new \App\Models\AdditionalServiceRequirement;
                    $AdditionalServiceRequirement->addi_serv_id = $service->id;
                    $AdditionalServiceRequirement->requirements = $value1[$i];
                    $AdditionalServiceRequirement->value = $value2[$i];                        
                    $AdditionalServiceRequirement->save();
                }
            }

            if($request->hasFile('samples_update'))
            {                    
                foreach($request->file('samples_update') as $image)
                {                        
                    $ext = $image->extension();
                    $filename = "service_samples_".rand().'_'.time().'.'.$ext;   
                    $image->move(public_path().'/assets/images/additional-service/samples/', $filename);
                
                    $AdditionalServiceSamples = new \App\Models\AdditionalServiceSamples;
                    $AdditionalServiceSamples->addi_serv_id = $service->id;
                    $AdditionalServiceSamples->image = $filename;
                    $AdditionalServiceSamples->save();;
                }                    
            }

            $notification = array(
                'message' => config('message.AdditionalService.ServiceUpdateSuccess'), 
                'alert-type' => 'success'
            );
            return redirect('/admin/additional-service')->with($notification);                    
        } catch (\Exception $th) {
            return view('errors.500');
        } 
    }

    /* ###########################################
    // Function: deleteService
    // Description: Delete existing service
    // Parameter: service_id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteService(Request $request)
    {
        if($request->ajax())
        {
            $service = \App\Models\AdditionalService::where('id', $request->service_id)->first();
            if($service)
            {
                $service->is_deleted = 1;
                $service->save();
                $result['status'] = 'true';
                $result['msg'] = config('message.AdditionalService.ServiceDeleteSuccess');
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
    // Function: serviceActDeact
    // Description: Activate Deactivate service
    // Parameter: service_id: Int
    // ReturnType: array
    */ ###########################################
    public function serviceActDeact(Request $request)
    {
        if($request->ajax())
        {
            $service = \App\Models\AdditionalService::where('id', $request->service_id)->first();
            if($service)
            {
                $service->status = ($request->status == 0) ? 1 : 0;
                $service->save();
                $result['status'] = 'true';
                if($request->status == 0)
                {
                    $result['msg'] = config('message.AdditionalService.ServiceActSuccess');    
                }
                else
                {
                    $result['msg'] = config('message.AdditionalService.ServiceDeactSuccess');
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

    /* ###########################################
    // Function: filterAdditionalService
    // Description: Filter additional service
    // Parameter: id: Int
    // ReturnType: array
    */ ###########################################
    public function filterAdditionalService(Request $request)
    {        
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $services = \App\Models\AdditionalService::select('id','name','status','text as description', 
        DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as ser_created_at"), 'price', 'image');        
        if($request->filter_addi_lang_lang == 'all')
        {
            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)->get();                    
            $global_languages_ids = $global_languages_count->pluck('id');
            $services->where(function($query) use($global_languages_ids){            
                return $query->where('is_deleted', 0)->whereIn('language_id', $global_languages_ids);
            });
        }
        else
        {
            $services->where(function($query) use($request){               
                return $query->where('language_id', $request->filter_addi_lang_lang)->where('is_deleted', 0);
            });
        }
        $services = $services->get();
        return Datatables::of($services)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    /* ###########################################
    // Function: deleteRequirement
    // Description: Delete service requirement 
    // Parameter: serv_req_id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteRequirement(Request $request)
    {        
        $services_requirement = \App\Models\AdditionalServiceRequirement::where('id', $request->serv_req_id)
        ->where('addi_serv_id', $request->service_id)
        ->first();
        if($services_requirement)
        {
            $services_requirement->delete();
            $result['status'] = 'true';
            $result['msg'] = config('message.AdditionalService.ServiceReqDeleteSuccess');
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
    // Function: deleteSamples
    // Description: Delete service samples 
    // Parameter: service_sample_id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteSamples(Request $request)
    {
        $services_samples = \App\Models\AdditionalServiceSamples::where('id', $request->service_sample_id)    
        ->first();
        if($services_samples)
        {
            $path = public_path('/assets/images/additional-service/samples').'/'.$services_samples->image;                
            if(file_exists($path))
            {
                unlink($path);
            }
            $services_samples->delete();
            $result['status'] = 'true';
            $result['msg'] = config('message.AdditionalService.ServiceSampDeleteSuccess');
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
