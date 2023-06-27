<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ReuseFunctionTrait;
use Validator;
use Image;
use DB;
use DataTables;
use Auth;

class HowItWrksBnrsController extends Controller
{
    use ReuseFunctionTrait;

    public function listHowItWrksBnrs(Request $request)
    {
        if($request->ajax())
        {
            $id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
            
            $howItWrksBnr = \App\Models\HowItWrksBnnr::select('id','image',
            DB::raw("date_format(how_it_works_banners.created_at,'%Y-%m-%d %h:%i:%s') as hiwb_created_at"))
            ->whereNull('deleted_at')
            ->get();
            return Datatables::of($howItWrksBnr)->editColumn('user_zone', function () use($timezone){
                return $timezone;
            })->make(true);        
        }

        return view('admin.howitworksbanners.list');
    }

    public function showHowItWrksBnrsForm()
    {
        $howItWrksBnr = \App\Models\HowItWrksBnnr::get();        
        if(count($howItWrksBnr) > 0)
        {                                    
            $used_id = array();            
            foreach ($howItWrksBnr as $hptext) {
                if(empty($hptext->deleted_at))
                {                    
                    array_push($used_id, $hptext->language_id);
                }                              
            }            
            $all_languages = \App\Models\GlobalLanguage::select('global_language.id',
            'wl.alpha2','wl.langEN as text','global_language.is_default')
            ->join('world_languages as wl','wl.id','=','global_language.language_id')  
            ->where('global_language.is_deleted',0)
            ->whereNotIn('global_language.id', $used_id)->get();                        
        }
        else
        {            
            $all_languages = \App\Models\GlobalLanguage::getAllLanguages();             
        }                   
        $default_language = $this->getDefaultLanguage();    
        return view('admin.howitworksbanners.add', compact('all_languages','default_language'));
    }

    public function addHowItWrksBnrs(Request $request)
    {
        $msg = [
            'banner_image.required' => "Banner image is required.",            
            'language.required' => "Language is required.",        
        ];
        $validator = Validator::make($request->all(), [
            'banner_image' => 'required',            
            'language' => 'required',            
        ],$msg);

        if($validator->fails()) {
            return redirect('/admin/how-it-works-banner/add')
                        ->withErrors($validator)
                        ->withInput();
        }

        $howItWrksBnrchekc = \App\Models\HowItWrksBnnr::whereNull('deleted_at')->get();
        if(!empty($howItWrksBnrchekc))
        {
            if(count($howItWrksBnrchekc) >= 1)
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

        $howItWrksBnr = new \App\Models\HowItWrksBnnr;
        $howItWrksBnr->language_id = $request->language;
        if($request->hasFile('banner_image'))
        {
            $image = $request->file('banner_image');
            $ext = $request->file('banner_image')->extension();
            $filename = rand().'_'.time().'.'.$ext;

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(config('app.how_it_works_banner.width'), config('app.how_it_works_banner.height'));
            $image_resize->save(public_path('/assets/images/how-it-works-banner/' .$filename));
            $howItWrksBnr->image = $filename;
        }
        $howItWrksBnr->save();
        $notification = array(
            'message' => "Banner added successfully",
            'alert-type' => 'success'
        );
        return redirect('/admin/how-it-works-banner')->with($notification);
    }

    public function editHowItWrksBnrs($id)
    {                
        $howItWrksBnr = \App\Models\HowItWrksBnnr::select('how_it_works_banners.id','how_it_works_banners.image','world_languages.langEN as langName', 
        DB::raw("date_format(how_it_works_banners.created_at,'%Y-%m-%d %h:%i:%s') as hiwb_created_at"))
        ->join('global_language','global_language.id','=','how_it_works_banners.language_id')
        ->join('world_languages','world_languages.id','=','global_language.language_id')            
        ->where('how_it_works_banners.id', $id)
        ->whereNull('how_it_works_banners.deleted_at')
        ->first();
        return view('admin.howitworksbanners.edit',compact('howItWrksBnr'));  
    }

    public function updateHowItworksBnrs(Request $request)
    {        
        $howItWrksBnr = \App\Models\HowItWrksBnnr::where('id', $request->hitwb_id)->first();
        if($request->hasFile('edit_banner_image'))
        {
            $path = public_path('/assets/images/how-it-works-banner').'/'.$howItWrksBnr->image;
            if(file_exists($path))
            {
                unlink($path);
            }        

            $image = $request->file('edit_banner_image');
            $ext = $request->file('edit_banner_image')->extension();
            $filename = rand().'_'.time().'.'.$ext;

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(config('app.how_it_works_banner.width'), config('app.how_it_works_banner.height'));
            $image_resize->save(public_path('/assets/images/how-it-works-banner/' .$filename));
            $howItWrksBnr->image = $filename;
        }
        $howItWrksBnr->save();
        $notification = array(
            'message' => "Banner updated successfully",
            'alert-type' => 'success'
        );
        return redirect('/admin/how-it-works-banner')->with($notification);
    }

    public function deleteHowItWrksBnrs(Request $request)
    {
        $howItWrksBnr = \App\Models\HowItWrksBnnr::where('id', $request->hitwb_id)->first();
        if($howItWrksBnr)
        {
            $howItWrksBnr->deleted_at = date('Y-m-d H:i:s');
            $howItWrksBnr->save();
            $result['status'] = 'true';
            $result['msg'] = "Banner deleted successfully";
            return $result;
        }
        else
        {            
            $result['status'] = 'false';
            $result['msg'] = "Something went wrong";
            return $result;
        }
    }
}
