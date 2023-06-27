<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Events;
use App\Models\Package;
use App\Models\GlobalCurrency;
use Auth;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;
use Session;
use App\Traits\ExportTrait;

class PackageController extends Controller
{
    use ExportTrait;

    public function getListOfPackages(Request $request)
    {
        if($request->ajax()) 
        {     
            try 
            {
                $id = Auth::guard('admin')->user()->id;
                $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

                DB::statement(DB::raw('set @rownum=0'));
                $packages = Package::select('packages.id','package_name','price','packages.is_active','e.event_name','discounted_price',
                            DB::raw('@rownum  := @rownum  + 1 AS rownum'), DB::raw("date_format(packages.created_at,'%Y-%m-%d %h:%i:%s') as p_created_at"))
                            ->join('events as e','e.id','=','packages.event_id');
                            // ->whereNull('packages.deleted_at')
                            // ->orderBy('packages.updated_at','desc')
                            // ->get();

                $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)
                ->where('is_default', 1)
                ->get();        
                if(count($global_languages_count) >= 2)
                {
                    $global_languages_ids = $global_languages_count->pluck('id');   
                    $packages->where(function($query) use($global_languages_ids){
                        return $query->whereNull('packages.deleted_at')->whereIn('packages.language_id', $global_languages_ids);
                    });
                    $packages = $packages->get();
                }
                else
                {
                    $global_language = \App\Models\GlobalLanguage::where('status',1)
                    ->where('is_deleted', 0)
                    ->where('is_default', 1)
                    ->first();
                    $packages->where(function($query) use($global_language){
                        return $query->whereNull('packages.deleted_at')->where('packages.language_id', $global_language->id);
                    });
                    $packages = $packages->get();
                }
                
                // dd($packages);
                return Datatables::of($packages)->rawColumns(['other_details'])->editColumn('user_zone', function () use($timezone){
                    return $timezone;
                })->make(true);

                // $packages = Package::whereNull('deleted_at')->get();
                // $rownum = 1;
              
                // $resultArray = [];
                // $index = 0;
                // foreach($packages as $package)
                // {
              
                //     $event_id = explode(",",$package->event_id);
                //     $event = Events::whereIn('id',$event_id)        
                //                     ->get();
        
                //     foreach ($event as $e)
                //     {
              
                //         $resultArray[$index]['id'] = $package->id;
                //         $resultArray[$index]['event_id'] = $e->id;
                //         $resultArray[$index]['event_name'] = $e->event_name;
                //         $resultArray[$index]['package_name'] = $package->package_name;
                //         $resultArray[$index]['price'] = $package->price;
                //         $resultArray[$index]['rownum'] = $rownum;
                //         $resultArray[$index++]['is_active'] = $package->is_active;
                //         $rownum = $rownum + 1;
                //     }
                // }
              
            } 
            catch (\Exception $e) 
            {
                return view('errors.500');
            }
        }     
        $page_name = 'index';
        $default_currency = GlobalCurrency::select('currency.currency_code')
                                        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                                        ->where('global_currency.is_default', 1)->first();
        
        $languages = \App\Models\GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
        ->select('global_language.id','alpha2','langEN','is_default')
        ->where('status',1)
        ->where('is_deleted',0)
        ->get();

        return view('admin.events.packages.index',compact('page_name','default_currency','languages'));
    }

    public function packageAddView()
    {
        $events = Events::select('event_name','id')
                        ->where('is_active',1)
                        ->whereNull('deleted_at')
                        ->get();
        $default_currency =  GlobalCurrency::select('currency.currency_code')
                            ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                            ->where('global_currency.is_default', 1)->first();
        $page_name = 'add';        

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
            return view('admin.events.packages.add',compact('events','page_name','default_currency','global_languages'));
        }
        else
        {
            $global_language = \App\Models\GlobalLanguage::select('world_languages.langEN as lang_name'
            ,'world_languages.alpha2 as sortcode','global_language.id as gl_id')
            ->leftJoin('world_languages', 'world_languages.id','=', 'global_language.language_id')
            ->where('global_language.status',1)
            ->where('global_language.is_deleted',0)
            ->first();
            return view('admin.events.packages.add',compact('events','page_name','default_currency','global_language'));
        }        
    }

    public function addPackage(Request $request)
    {        
        try
        {
            $package = new Package;
            $package->language_id = $request->language;
            $package->event_id = $request->event_id;
            $package->package_name = $request->package_name;
            $package->discounted_price = $request->discounted_price;
            $package->price = $request->price;
            $package->is_active = $request->is_active;
            $package->other_details = $request->other_details;
            $package->sort_order = $request->sort_order;
            $package->save();

            if(!empty($request->feature_value))
            {
                foreach ($request->feature_value as $key => $value) {
                    $pckg_feature = new \App\Models\PackageFeatures;
                    $pckg_feature->package_id = $package->id;
                    $pckg_feature->feature_id = $key;
                    $pckg_feature->feature_id = $key;
                    $pckg_feature->package_value = $value;
                    $pckg_feature->created_at = date('Y-m-d H:i:s');
                    $pckg_feature->save();
                }
            }

            $notification = array(
                'message' => 'Package added successfully!', 
                'alert-type' => 'success'
            );
            return redirect('admin/package/list')->with($notification);       
        }
        catch (\Exception $e) 
        {
                Session::flash('error', $e->getMessage());            
            return redirect('admin/package/list');
        }
    }

    public function deletePackage(Request $request)
    {
        $pkg = Package::select('id')
                        ->where('id', $request->package_id)
                        ->first();
                        
        if(!empty($pkg))
        {
            $pkg->deleted_at = Carbon::now();
            $pkg->save();
            $result['status'] = 'true';
            $result['msg'] = "Package Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong!!";
            return $result;
        }
    }

    public function packageActiveInactive(Request $request)
    {
        try 
        {
            $package = Package::where('id',$request->package_id)->first();
            if($request->is_active == 1) 
            {
                $package->is_active = $request->is_active;
                $msg = "Package Activated Successfully!";
            }
            else
            {
                $package->is_active = $request->is_active;
                $msg = "Package Deactivated Successfully!";
            }            
            $package->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        } 
        catch(\Exception $ex) 
        {
            return view('errors.500');            
        }        
    }

    public function packageEditView($id)
    {
        $package = Package::findOrFail($id);
        if(!empty($package))
        {                                                
            $events = Events::select('event_name','id')
                        ->where('is_active',1)
                        ->whereNull('deleted_at')
                        ->get();


            $event_features = \App\Models\EventFeatures::where('event_id', $package->event_id)->get();
            
            $package_features_arr = [];
            $i = 0;
            foreach ($event_features as $event_feature) {
                $package_features = \App\Models\PackageFeatures::where('package_id', $package->id)
                ->where('feature_id', $event_feature->id)
                ->first();
                if($package_features)
                {
                    if($package_features->feature_id == $event_feature->id)
                    {
                        $package_features_arr[$i]['feature_name'] = $event_feature->feature_name;
                        $package_features_arr[$i]['package_value'] = $package_features->package_value;
                        $package_features_arr[$i]['package_id'] = $package_features->id;
                    }
                }                
                else
                {
                    $package_features_arr[$i]['feature_name'] = $event_feature->feature_name;
                    $package_features_arr[$i]['package_value'] = '';
                    $package_features_arr[$i]['package_id'] = $event_feature->id;
                }
                $i++;
            }
            
            // $package_features = \App\Models\PackageFeatures::select('event_features.feature_name'
            //     ,'package_features.package_value','package_features.id as package_id')
            //     ->leftJoin('event_features', 'event_features.id', '=', 'package_features.feature_id')
            //     ->where('package_id', $package->id)
            //     ->get();            

            $package_features_count = count($package_features_arr);

            $default_currency = GlobalCurrency::select('currency.currency_code')
                                            ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                                            ->where('global_currency.is_default', 1)->first();

            $page_name = 'edit';

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
                return view('admin.events.packages.edit',compact('package','events','page_name','default_currency','package_features_arr','global_languages','package_features_count'));
            }
            else
            {
                $global_languages->where(function($query){
                    return $query->where('global_language.status', 1)->where('global_language.is_deleted', 0);
                });
                $global_language = $global_languages->first();
                return view('admin.events.packages.edit',compact('package','events','page_name','default_currency','package_features_arr','global_language','package_features_count'));
            }             
        }
    }

    public function updatePackage(Request $request)
    {
        $package = Package::findOrFail($request->get('package_id'));
  
        if(!empty($package)) 
        {
            $package->event_id = $request->event_id;
            $package->language_id = $request->language;
            $package->package_name = $request->package_name;
            $package->discounted_price = $request->discounted_price;
            $package->price = $request->price;
            // $package->is_active = $request->is_active;
            $package->other_details = $request->other_details;
            $package->sort_order = $request->sort_order;
            $package->save();

            if(!empty($request->feature_value))
            {
                foreach ($request->feature_value as $key => $value) {
                    $pckg_feature = \App\Models\PackageFeatures::where('id',$key)
                    ->where('package_id', $package->id)
                    ->first();   
                    if($pckg_feature)
                    {
                        $pckg_feature->package_value = $value;                    
                        $pckg_feature->save();
                    }                                                          
                    else
                    {
                        $pckg_feature = new \App\Models\PackageFeatures;
                        $pckg_feature->package_id = $package->id;
                        $pckg_feature->feature_id = $key;                        
                        $pckg_feature->package_value = $value;
                        $pckg_feature->created_at = date('Y-m-d H:i:s');
                        $pckg_feature->save();
                    }
                }
            }

            $notification = array(
                'message' => 'Package updated successfully!', 
                'alert-type' => 'success'
            );

            return redirect('admin/package/list')->with($notification);      
        }    
    }
    public function getExportPackages(Request $request)
    {
        try
        {
            $global_languages_ids = \App\Models\GlobalLanguage::where('status',1)
            ->where('is_deleted', 0)
            ->pluck('id');            
            $packages = Package::join('events as e','e.id','=','packages.event_id')
                ->select('packages.id as Id','e.event_name as Event Name' ,'package_name as Package Name', 'price as Price','discounted_price as Discounted Price','other_details as Other Details')
                ->whereIn('packages.language_id', $global_languages_ids)
                ->orderBy('packages.id','desc')
                ->get()
                ->toArray();            
            $sheetTitle = 'Packages';
            return $this->exportPackages($packages, $sheetTitle);
        } 
        catch(\Exception $ex) 
        {
            // return $ex;
            // Session::flash('error', $ex->getMessage());
            return redirect($request->segment(1).'/package/list');
        }
    }


    public function filterPackage(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        
        DB::statement(DB::raw('set @rownum=0'));
        $packages = Package::select('packages.id','packages.language_id','package_name','price','packages.is_active','e.event_name','discounted_price',
        DB::raw('@rownum  := @rownum  + 1 AS rownum'), DB::raw("date_format(packages.created_at,'%Y-%m-%d %h:%i:%s') as p_created_at"))
        ->join('events as e','e.id','=','packages.event_id');                    
        if($request->filter_package_lang == 'all')
        {
            $global_languages_count = \App\Models\GlobalLanguage::where('is_deleted', 0)->get();                    
            $global_languages_ids = $global_languages_count->pluck('id'); 
            $packages->where(function($query) use($global_languages_ids){            
                return $query->whereNull('packages.deleted_at')->whereIn('packages.language_id', $global_languages_ids)->orderBy('packages.updated_at','desc');
            });
        }
        else
        {
            $packages->where(function($query) use($request){               
                return $query->where('packages.language_id', $request->filter_package_lang)->whereNull('packages.deleted_at')->orderBy('packages.updated_at','desc');
            });
        }
        $packages = $packages->get();
        return Datatables::of($packages)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }
}
?>
