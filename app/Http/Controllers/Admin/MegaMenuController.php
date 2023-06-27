<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MegaMenu;
use App\Models\CmsPages;
use App\Models\Category;
use App\Models\GlobalLanguage;
use App\Models\Product;
use App\Models\Events;
use App\Models\Package;
use App\Models\Photographers;
use Validator;
use Carbon\Carbon;
use DataTables;
use DB;
use Session;
use Image;
use App\Traits\CommonTrait;
use Artisan;

class MegaMenuController extends Controller
{
    use CommonTrait;

    protected $megamenu;
    protected $category;

	public function __construct(MegaMenu $megamenu, Category $category) {
        $this->megamenu = $megamenu;
        $this->category = $category;
    }

    public function getMegamenu()
    {
        $baseUrl = $this->getBaseUrl();
        return view('admin.megamenu.index',compact('baseUrl'));
    }

    public function getMegamenuList(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData['id'];
        
        $megamenu0 = MegaMenu::select('megamenu.id', 'type','small_image','megamenu.sort_order','cd.title as name',
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                        ->join('cms_pages as cp','cp.id','=','megamenu.name')
                        ->join('cms_details as cd','cd.cms_id','=','cp.id')
                        ->whereNull('megamenu.deleted_at')
                        ->where('cd.language_id',$defaultLanguageId)
                        ->where('type',0)
                        ->get()->toArray();  
                          
        $megamenu1 = MegaMenu::select('megamenu.id', 'type','small_image','megamenu.sort_order','cd.title as name',
                        DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                        ->join('categories as c','c.id','=','megamenu.name')
                        ->join('category_details as cd','cd.category_id','=','c.id')
                        ->whereNull('megamenu.deleted_at')
                        ->where('cd.language_id',$defaultLanguageId)
                        ->where('type',1)
                        ->get()->toArray();  
                        
        $megamenu = array_merge($megamenu0,$megamenu1);
        
        return Datatables::of($megamenu)->make(true);  
    }

    public function menuAddView()
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];

        $cms_pages = CmsPages::select('cms_pages.id','title')
                                ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                ->where('language_id',$defaultLanguageId)
                                ->where('display_on',0)
                                ->whereNull('cms_pages.deleted_at')
                                ->get()->toArray();

        $categories = Category::select('categories.id','title')
                            ->join('category_details as cd','cd.category_id','=','categories.id')
                            ->where('language_id',$defaultLanguageId)
                            ->where('parent_id',0)
                            ->whereNull('categories.deleted_at')
                            ->get()->toArray();   

        return view('admin.megamenu.add',compact('cms_pages','categories'));
    }

    public function addMegamenu(Request $request) 
    {
        try
        {
            $messsages = array(
                'type.required' => 'Please select type',
                'name.required' => 'Please select dropdown option',
                'sort_order.required' => 'Please enter sort order'
            );
        
            $validator = Validator::make($request->all(), [
                'type'=>'required',
                'name'=>'required',
                'sort_order'=>'required'
            ],$messsages);

            if ($validator->fails()) 
            {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }    

            $megamenu = new MegaMenu;
            $megamenu->type = $request->type;
            $megamenu->name = $request->name;

            if($request->hasFile('small_image')) 
            {
                $max_height = config('app.megamenu_small_image.height');
                $max_width = config('app.megamenu_small_image.width');
                $small_image_height = $request->small_image_height;
                $small_image_width = $request->small_image_width;                

                if($small_image_width != $max_width || $small_image_height != $max_height)
                {    
                    $image = $request->file('small_image');
                    $filename = "small_".$image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width,$max_height);
                    $image_resize->save(public_path('assets/images/megamenu/small/' .$filename));    
                    $megamenu->small_image = $filename;                            
                }
                else
                {
                    $image = $request->file('small_image');
                    $filename = "small_".$image->getClientOriginalName();
                    $image->move(public_path('assets/images/megamenu/small/'), $filename);
                    $megamenu->small_image = $request->file('small_image')->getClientOriginalName();
                }
            }
            if($request->hasFile('big_image')) 
            {
                $max_height = config('app.megamenu_big_image.height');
                $max_width = config('app.megamenu_big_image.width');
                $big_image_height = $request->big_image_height;
                $big_image_width = $request->big_image_width;                

                if($big_image_width != $max_width || $big_image_height != $max_height)
                {    
                    $image = $request->file('big_image');
                    $filename = "big_".$image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width,$max_height);
                    $image_resize->save(public_path('assets/images/megamenu/big/' .$filename));    
                    $megamenu->big_image = $filename;                            
                }
                else
                {
                    $image = $request->file('big_image');
                    $filename = "big_".$image->getClientOriginalName();
                    $image->move(public_path('assets/images/megamenu/big/'), $filename);
                    $megamenu->big_image = $request->file('big_image')->getClientOriginalName();
                }
            }

            if($request->hasFile('icon_image')) 
            {
                $max_height = config('app.megamenu_icon_image.height');
                $max_width = config('app.megamenu_icon_image.width');
                $icon_image_height = $request->icon_image_height;
                $icon_image_width = $request->icon_image_width;                

                if($icon_image_width != $max_width || $icon_image_height != $max_height)
                {    
                    $image = $request->file('icon_image');
                    $filename = "icon_".$image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width,$max_height);
                    $image_resize->save(public_path('assets/images/megamenu/icon/' .$filename));    
                    $megamenu->icon_image = $filename;                            
                }
                else
                {
                    $image = $request->file('icon_image');
                    $filename = "icon_".$image->getClientOriginalName();
                    $image->move(public_path('assets/images/megamenu/icon/'), $filename);
                    $megamenu->icon_image = $request->file('icon_image')->getClientOriginalName();
                }
            }
            $megamenu->description = $request->description;
            $megamenu->sort_order = $request->sort_order;
            $megamenu->save();

            $notification = array(
                'message' => 'Megamenu added successfully!', 
                'alert-type' => 'success'
            );
            return redirect('admin/mega-menu')->with($notification);       
        }
        catch (\Exception $e) 
        {
                Session::flash('error', $e->getMessage());            
            return redirect('admin/mega-menu');
        }
    }

    public function menuEditView($id)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];

        $cms_pages = CmsPages::select('cms_pages.id','title')
                                ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                ->where('language_id',$defaultLanguageId)
                                ->where('display_on',0)
                                ->get()->toArray();

        $categories = Category::select('categories.id','title')
                            ->join('category_details as cd','cd.category_id','=','categories.id')
                            ->where('language_id',$defaultLanguageId)
                            ->where('parent_id',0)
                            ->get()->toArray();  

        $menu = MegaMenu::findOrFail($id);
        $baseUrl = $this->getBaseUrl();

        if(!empty($menu))
        {                                                
            return view('admin.megamenu.edit',compact('menu','categories','cms_pages','baseUrl'));
        }
    }

    public function updateMenu(Request $request)
    {
        $megamenu = MegaMenu::findOrFail($request->menu_id);
  
        if(!empty($megamenu)) 
        {
            $messsages = array(
                'type.required' => 'Please select type',
                'name.required' => 'Please select dropdown option',
                'sort_order.required' => 'Please enter sort order'
            );
        
            $validator = Validator::make($request->all(), [
                'type'=>'required',
                'name'=>'required',
                'sort_order'=>'required'
            ],$messsages);

            if ($validator->fails()) 
            {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            } 

            $megamenu->type = $request->type;
            $megamenu->name = $request->name;
            if($request->hasFile('small_image')) 
            {
                $max_height = config('app.megamenu_small_image.height');
                $max_width = config('app.megamenu_small_image.width');
                $small_image_height = $request->small_image_height;
                $small_image_width = $request->small_image_width;                

                if($small_image_width != $max_width || $small_image_height != $max_height)
                {    
                    $image = $request->file('small_image');
                    $filename = "small_".$image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width,$max_height);
                    $image_resize->save(public_path('assets/images/megamenu/small/' .$filename));    
                    $megamenu->small_image = $filename;                            
                }
                else
                {
                    $image = $request->file('small_image');
                    $filename = "small_".$image->getClientOriginalName();
                    $image->move(public_path('assets/images/megamenu/small/'), $filename);
                    $megamenu->small_image = $request->file('small_image')->getClientOriginalName();
                }
            }
            if($request->hasFile('big_image')) 
            {
                $max_height = config('app.megamenu_big_image.height');
                $max_width = config('app.megamenu_big_image.width');
                $big_image_height = $request->big_image_height;
                $big_image_width = $request->big_image_width;                

                if($big_image_width != $max_width || $big_image_height != $max_height)
                {    
                    $image = $request->file('big_image');
                    $filename = "big_".$image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width,$max_height);
                    $image_resize->save(public_path('assets/images/megamenu/big/' .$filename));    
                    $megamenu->big_image = $filename;                            
                }
                else
                {
                    $image = $request->file('big_image');
                    $filename = "big_".$image->getClientOriginalName();
                    $image->move(public_path('assets/images/megamenu/big/'), $filename);
                    $megamenu->big_image = $request->file('big_image')->getClientOriginalName();
                }
            }
            if($request->hasFile('icon_image')) 
            {
                $max_height = config('app.megamenu_icon_image.height');
                $max_width = config('app.megamenu_icon_image.width');
                $icon_image_height = $request->icon_image_height;
                $icon_image_width = $request->icon_image_width;                

                if($icon_image_width != $max_width || $icon_image_height != $max_height)
                {    
                    $image = $request->file('icon_image');
                    $filename = "icon_".$image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());              
                    $image_resize->resize($max_width,$max_height);
                    $image_resize->save(public_path('assets/images/megamenu/icon/' .$filename));    
                    $megamenu->icon_image = $filename;                            
                }
                else
                {
                    $image = $request->file('icon_image');
                    $filename = "icon_".$image->getClientOriginalName();
                    $image->move(public_path('assets/images/megamenu/icon/'), $filename);
                    $megamenu->icon_image = $request->file('icon_image')->getClientOriginalName();
                }
            }
            $megamenu->description = $request->description;
            $megamenu->sort_order = $request->sort_order;
            $megamenu->save();

            $notification = array(
                'message' => 'Megamenu updated successfully!', 
                'alert-type' => 'success'
            );

            return redirect('admin/mega-menu')->with($notification);      
        }    
    }

    public function deleteMenu(Request $request)
    {
        $menu = MegaMenu::find($request->menu_id);
        $menu->deleted_at = now();
        if ($menu->save()) 
        {
            $result['status'] = 'true';
            $result['msg'] = "Service Deleted Successfully!";
            return $result;
        }
    }

    // Megamenu generation functions
    public function generateMegamenuView()
    {
        $languages = GlobalLanguage::getAllLanguages();
        return view('admin/megamenu/generateMenu',compact('languages'));
    }

    public function generateMegamenu(Request $request)
    {
        $filename = "megamenu_".$request->code.".blade.php";
        $mobileFilename = "mobileMegamenu_".$request->code.".blade.php";
        $filecontent = $this->generateFileContents($request->languageId,$request->code);
        if (strlen($filename) > 0 && strlen($mobileFilename) > 0)
        {
            $folderPath = base_path().'/resources/views/admin/generatedMegamenu';
            if (!file_exists($folderPath)) 
            {
                // $permit = 0777;
                // mkdir($folderPath);
                // chmod($dir, $permit);
                // mkdir(dirname($path), 0755, true); // $path is a file
                mkdir($folderPath, 0755, true);          // $path is a directory
            }
            $file = @fopen($folderPath . DIRECTORY_SEPARATOR . $filename,"w");
            if ($file != false)
            {
                fwrite($file,$filecontent);
                fclose($file);
                $filecontent = $this->generateFileContentsForMobile($request->languageId,$request->code);
                $folderPath = base_path().'/resources/views/admin/generatedMegamenu';
                if (!file_exists($folderPath)) 
                {
                    mkdir($folderPath, 0755, true);
                }
                $file = @fopen($folderPath . DIRECTORY_SEPARATOR . $mobileFilename,"w");
                if ($file != false)
                {
                    fwrite($file,$filecontent);
                    fclose($file);
                }
                Artisan::call('cache:clear');

                return response()->json(['status' => 1,'msg' => 'Megamenu generated successfully !!']);
            }
        }
        return response()->json(['status' => -1,'msg' => 'Error Occurred']);
    }

    public function generateFileContents($langId,$langCode)
    {
        $defaultLang = GlobalLanguage::select('alpha2 as Code')
                                    ->join('world_languages as wl','wl.id','=','language_id')
                                    ->where('is_default',1)
                                    ->where('status',1)
                                    ->where('is_deleted',0)
                                    ->first();
        $defaultLangCode = $defaultLang['Code'];
        $arrMegaMenu = $this->megamenu->getAllMegaMenus();
        $baseUrl = $this->getBaseUrl();
        return view('frontend/include/navbar',compact('arrMegaMenu','langId','baseUrl','langCode','defaultLangCode'));
    }

    public function generateFileContentsForMobile($langId,$langCode)
    {
        $defaultLang = GlobalLanguage::select('alpha2 as Code')
                                    ->join('world_languages as wl','wl.id','=','language_id')
                                    ->where('is_default',1)
                                    ->where('status',1)
                                    ->where('is_deleted',0)
                                    ->first();
        $defaultLangCode = $defaultLang['Code'];
        $arrMegaMenu = $this->megamenu->getAllMegaMenus();
        $baseUrl = $this->getBaseUrl();
        return view('frontend/include/mobileNavbar',compact('arrMegaMenu','langId','baseUrl','langCode','defaultLangCode'));
    }
}