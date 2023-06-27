<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\GlobalLanguage;
use App\Models\Collection;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;
use Session;
use Image;
use App\Traits\ReuseFunctionTrait;

class CollectionController extends Controller
{
    use ReuseFunctionTrait;

    public function getCollection()
    {
        $page_name = 'index';
        $languages = GlobalLanguage::getAllLanguages();
        $baseUrl = $this->getBaseUrl();
        return view('admin.collection.index',compact('languages','page_name','baseUrl'));
    }

    public function getCollectionList(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $collections = Collection::select('collections.id', 'collection_title', 'collection_image', 'collections.status',
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                        ->leftjoin('global_language as gl','gl.id','=','collections.language_id')
                        ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                        ->where('gl.is_default',1)
                        ->where('gl.status',1)
                        ->whereNull('collections.deleted_at')
                        ->orderBy('collections.updated_at','desc')
                        ->get();  

        if($request['lang_id'] != null)
        {
            DB::statement(DB::raw('set @rownum=0'));

            $collections = Collection::select('id', 'collection_title', 'collection_image', 'status',
                                DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                                ->where('language_id',$request['lang_id'])
                                ->whereNull('deleted_at')
                                ->orderBy('updated_at','desc')
                                ->get(); 
        }
        return Datatables::of($collections)->make(true);  
    }

    public function collectionAddView()
    {
        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang); 
        $page_name = 'add';
        $languages = GlobalLanguage::getAllLanguages();
        return view('admin.collection.add',compact('languages','page_name','categories'));
    }

    public function addCollection(Request $request)
    {
        try
        {
            $messsages = array(
                'language_id.required' => 'Please select language',
                'collection_title.required' => 'Please enter collection title',
                'collection_image.required' => 'Please select image',
                'collection_link.required' => 'Please enter link',
                'sort_order.required' => 'Please enter sort order'
            );
        
            $validator = Validator::make($request->all(), [
                'language_id'=>'required',
                'collection_title'=>'required',
                'collection_image'=>'required',
                'collection_link'=>'required',
                'sort_order'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }    

            $collection = new Collection;
            $collection->language_id = $request->language_id;
            $collection->collection_title = $request->collection_title;

            if($request->hasFile('collection_image')) 
            {
                $max_height = config('app.collection_image.height');
                $max_width = config('app.collection_image.width');
                $loaded_image_height = $request->loaded_image_height;
                $loaded_image_width = $request->loaded_image_width;                

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('collection_image');
                    $filename = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.collection_image.width'), config('app.collection_image.height'));
                    $image->move(public_path('assets/images/collections/'), $filename);
                    $image_resize->save(public_path('assets/images/collections/' .$filename));    
                    $collection->collection_image = $filename;                            
                }
                else
                {
                    $image = $request->file('collection_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/collections/'), $filename);
                    $collection->collection_image = $request->file('collection_image')->getClientOriginalName();
                }
            }
            $collection->category_id = $request->collection_link;
            $collection->sort_order = $request->sort_order;
            $collection->status = $request->status;
            $collection->save();

            $notification = array(
                'message' => 'Collection added successfully!', 
                'alert-type' => 'success'
            );
            return redirect('admin/collection')->with($notification);       
        }
        catch (\Exception $e) 
        {
                Session::flash('error', $e->getMessage());            
            return redirect('admin/collection');
        }
    }

    public function collectionEditView($id)
    {
        $collection = Collection::findOrFail($id);
        $defaultLang = $this->getDefaultLanguage();
        $categories = getParentCategories($defaultLang); 
        $page_name = 'edit';
        $baseUrl = $this->getBaseUrl();
        if(!empty($collection))
        {                                                
            $languages = GlobalLanguage::getAllLanguages();                        
            return view('admin.collection.edit',compact('collection','languages','page_name','baseUrl',
            'categories'));
        }
    }

    public function updateCollection(Request $request)
    {
        $collection = Collection::findOrFail($request->collection_id);
  
        if(!empty($collection)) 
        {
            $messsages = array(
                'language_id.required' => 'Please select language',
                'collection_title.required' => 'Please enter collection title',
                'collection_link.required' => 'Please enter link',
                'sort_order.required' => 'Please enter sort order'
            );
        
            $validator = Validator::make($request->all(), [
                'language_id'=>'required',
                'collection_title'=>'required',
                'collection_link'=>'required',
                'sort_order'=>'required'
            ],$messsages);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }    
   
            $collection->language_id = $request->language_id;
            $collection->collection_title = $request->collection_title;

            if($request->hasFile('collection_image')) 
            {
                $max_height = config('app.collection_image.height');
                $max_width = config('app.collection_image.width');
                $loaded_image_height = $request->loaded_image_height;
                $loaded_image_width = $request->loaded_image_width;                
                
                $path = public_path('assets/images/collections/').'/'.$collection->collection_image;                
                if(file_exists($path))
                {
                    unlink($path);
                }

                if($loaded_image_width != $max_width || $loaded_image_height != $max_height)
                {    
                    $image = $request->file('collection_image');
                    $filename = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize(config('app.collection_image.width'), config('app.collection_image.height'));
                    $image->move(public_path('assets/images/collections/'), $filename);
                    $image_resize->save(public_path('assets/images/collections/' .$filename));    
                    $collection->collection_image = $filename;                            
                }
                else
                {
                    $image = $request->file('collection_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/collections/'), $filename);
                    $collection->collection_image = $request->file('collection_image')->getClientOriginalName();
                }
            }
            $collection->category_id = $request->collection_link;
            $collection->sort_order = $request->sort_order;
            $collection->status = $request->status;
            $collection->save();

            $notification = array(
                'message' => 'Collection updated successfully!', 
                'alert-type' => 'success'
            );

            return redirect('admin/collection')->with($notification);      
        }    
    }

    public function collectionActiveInactive(Request $request)
    {
        try 
        {
            $collection = Collection::where('id',$request->collection_id)->first();
            if($request->is_active == 1) 
            {
                $collection->status = $request->is_active;
                $msg = "Collection Activated Successfully!";
            }
            else
            {
                $collection->status = $request->is_active;
                $msg = "Collection Deactivated Successfully!";
            }            
            $collection->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        } 
        catch(\Exception $ex) 
        {
            return view('errors.500');            
        }        
    }

    public function deleteCollection(Request $request)
    {
        $collection = Collection::select('id')
                        ->where('id', $request->collection_id)
                        ->first();
                        
        if(!empty($collection))
        {
            $collection->deleted_at = Carbon::now();
            $collection->save();
            $result['status'] = 'true';
            $result['msg'] = "Collection Deleted Successfully!";
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
