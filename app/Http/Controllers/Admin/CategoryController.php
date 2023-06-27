<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryDetails;
use App\Models\GlobalLanguage;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;
use Image;
use Config;

class CategoryController extends Controller
{
    use CommonTrait;

    public function getCategories()
    {
        $catId = isset($_GET['catId']) ? $_GET['catId'] : "";
        $languageId = isset($_GET['langId']) ? $_GET['langId'] : "";
        $catTitle = [];

        if(!empty($catId))
        {
            $category = Category::findOrFail($catId);
            if(!empty($category))
            {
                $catIds = explode(',',$category->category_path);

                foreach($catIds as $categoryId)
                {
                    $catDetails = CategoryDetails::select('title','category_id')
                                            ->where('category_id',$categoryId);
                                            if(!empty($languageId))
                                                $catDetails = $catDetails->where('language_id',$languageId);
                    $catDetails = $catDetails->first();
                    array_push($catTitle,$catDetails);
                }
            }
        }

        $languages = GlobalLanguage::getAllLanguages();
        $baseUrl = $this->getBaseUrl();
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        if($languageId != null)
            $defaultLanguageId = $languageId;
        else
            $defaultLanguageId = $defaultLanguageData->id;

        return view('admin.categories.index',compact('otherLanguages','languages','baseUrl','catId','defaultLanguageId','catTitle'));
    }

    public function getCategoriesList(Request $request)
    {
        $catId = isset($_GET['catId']) ? $_GET['catId'] : "";
        $id = Auth::guard('admin')->user()->id;
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        DB::statement(DB::raw('set @rownum=0'));
        $categories = Category::select('categories.id','title','category_image','slug','status','sort_order','flag_product',DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                            ->join('category_details as cd','cd.category_id','=','categories.id')
                            ->where('cd.language_id', $defaultLanguageId)
                            ->where('parent_id', 0)
                            ->whereNull('categories.deleted_at')
                            ->whereNull('cd.deleted_at')
                            ->orderBy('categories.updated_at','desc')
                            ->get();

        if($catId != null)
        {
            DB::statement(DB::raw('set @rownum=0'));
            $categories = Category::select('categories.id','title','category_image','slug','status','sort_order','flag_product',DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                            ->join('category_details as cd','cd.category_id','=','categories.id')
                            ->where('parent_id',$catId);
                            if($request['lang_id'] != null)
                                $categories = $categories->where('language_id',$request['lang_id']);
                            else
                                $categories = $categories->where('language_id',$defaultLanguageId);

                            $categories = $categories->whereNull('categories.deleted_at')
                            ->whereNull('cd.deleted_at')
                            ->orderBy('categories.updated_at','desc')
                            ->get();
        }

        if($request['lang_id'] != null)
        {
            DB::statement(DB::raw('set @rownum=0'));
            $categories = Category::select('categories.id','title','category_image','slug','status','sort_order','flag_product',DB::raw('@rownum  := @rownum  + 1 AS rownum'))
                            ->join('category_details as cd','cd.category_id','=','categories.id');
                            if($catId != null)
                                $categories = $categories->where('parent_id',$catId);
                            else
                                $categories = $categories->where('parent_id',0);

                            $categories = $categories->where('language_id',$request['lang_id'])
                                                    ->whereNull('categories.deleted_at')
                                                    ->whereNull('cd.deleted_at')
                                                    ->orderBy('categories.updated_at','desc')
                                                    ->get();
        }
        return Datatables::of($categories)->make(true);
    }

    public function categoryAddView($catId = null)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];
        $formTitle = "Add Category";

        $page = isset($_GET['page']) ? $_GET['page'] : "addPage";
        $catId = isset($_GET['catId']) ? $_GET['catId'] : "";
        $catTitle = [];
        if(!empty($catId))
        {
            $category = Category::findOrFail($catId);
            if(!empty($category))
            {
                $catIds = explode(',',$category->category_path);
                foreach($catIds as $categoryId)
                {
                    $catTitle[] = CategoryDetails::select('title','category_id')
                                            ->where('category_id',$categoryId)
                                            ->first();
                }
            }
        }

        if (!empty($catId))
        {
            $category = CategoryDetails::where(['category_id'=> $catId, 'language_id'=>$defaultLanguageId])
                                                ->whereNull('deleted_at')
                                                ->first();

            $catName = $category->title;
        }

        $language = "";
        if ($page == "anotherLanguage")
        {
            $existingLanguageIds = CategoryDetails::where('category_id', $catId)
                                                        ->whereNull('deleted_at')
                                                        ->get()
                                                        ->pluck('language_id')
                                                        ->toArray();

            $language = GlobalLanguage::select('global_language.id as globalLanguageId', 'world_languages.langEN as languageName')
                                    ->Join('world_languages', 'world_languages.id', '=', 'global_language.language_id')
                                    ->whereNotIn('global_language.id', $existingLanguageIds)
                                    ->where('global_language.status', 1)
                                    ->where('is_deleted',0)
                                    ->get()->toArray();

            $formTitle = "Add Category - Other Language ($catName)";
        }
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $baseUrl = $this->getBaseUrl();
        return view('admin.categories.add',compact('defaultLanguage','defaultLanguageId','catId','language','formTitle','otherLanguages','page','catTitle','baseUrl'));
    }

    public function addCategory(Request $request)
    {
        // add in other lang
        if(($request->page == 'anotherLanguage') && isset($request->catId))
        {
            $categoryDetails = new CategoryDetails;
            $categoryDetails->category_id = $request->catId;
            $categoryDetails->language_id = $request->defaultLanguage;
            $categoryDetails->title = $request->title;
            $categoryDetails->description = $request->description;
            $categoryDetails->meta_title = $request->meta_title;
            $categoryDetails->meta_keywords = $request->meta_keywords;
            $categoryDetails->meta_description = $request->meta_description;

            $reqdBannerWidth =Config::get('app.category_banner_image.width');
            $reqdBannerHeight =Config::get('app.category_banner_image.height');
            if($request->hasFile('banner_image'))
            {
                if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                {
                    $image       = $request->file('banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                    $image->move(public_path('assets/images/categories/banner/'), $filename);
                    $image_resize->save(public_path('assets/images/categories/banner/' .$filename));
                    $categoryDetails->banner_image = $filename;
                }
                else
                {
                    $image = $request->file('banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/categories/banner/'), $filename);
                    $categoryDetails->banner_image = $request->file('banner_image')->getClientOriginalName();
                }
            }

            $reqdMobBannerWidth =Config::get('app.category_mobile_banner_image.width');
            $reqdMobBannerHeight =Config::get('app.category_mobile_banner_image.height');
            if($request->hasFile('mobile_banner_image'))
            {
                if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                {
                    $image       = $request->file('mobile_banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                    $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                    $image_resize->save(public_path('assets/images/categories/mobile_banner/' .$filename));
                    $categoryDetails->mobile_banner = $filename;
                }
                else
                {
                    $image = $request->file('mobile_banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                    $categoryDetails->banner_image = $request->file('mobile_banner_image')->getClientOriginalName();
                }
            }

            $categoryDetails->save();

            $notification = array(
                'message' => 'Category added successfully!',
                'alert-type' => 'success'
            );

            return redirect($request->prevUrl)->with($notification);
        }
        else    // add new category
        {
            $category = new Category;

            $validator = Validator::make(['slug' => $request->slug], [
                'slug' => "required|unique:categories,slug,NULL,id,deleted_at,NULL",
            ]);

            if ($validator->fails())
            {
                $notification = array(
                    'message' => 'Slug already exists !!',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification)->withInput();
            }
            if(!empty($request->catId))
                $category->parent_id = $request->catId;
            else
                $category->parent_id = 0;

            $category->slug = $request->slug;

            $reqdImgWidth =Config::get('app.category_image.width');
            $reqdImgHeight =Config::get('app.category_image.height');
            if($request->hasFile('category_image'))
            {
                if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
                {
                    $image       = $request->file('category_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                    $image->move(public_path('assets/images/categories/'), $filename);
                    $image_resize->save(public_path('assets/images/categories/' .$filename));
                    $category->category_image = $filename;
                }
                else
                {
                    $image = $request->file('category_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/categories/'), $filename);
                    $category->category_image = $request->file('category_image')->getClientOriginalName();
                }
            }
            /* Code for banner and mobile banner :Nivedita(11-01-2021)*/
            $reqdBannerWidth =Config::get('app.category_banner_image.width');
            $reqdBannerHeight =Config::get('app.category_banner_image.height');
            if($request->hasFile('banner_image'))
            {
                if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                {
                    $image       = $request->file('banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                    $image->move(public_path('assets/images/categories/banner/'), $filename);
                    $image_resize->save(public_path('assets/images/categories/banner/' .$filename));
                    $category->banner_image = $filename;
                }
                else
                {
                    $image = $request->file('banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/categories/banner/'), $filename);
                    $category->banner_image = $request->file('banner_image')->getClientOriginalName();
                }
            }
            $reqdMobBannerWidth =Config::get('app.category_mobile_banner_image.width');
            $reqdMobBannerHeight =Config::get('app.category_mobile_banner_image.height');
            if($request->hasFile('mobile_banner_image'))
            {
                if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                {
                    $image       = $request->file('mobile_banner_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                    $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                    $image_resize->save(public_path('assets/images/categories/mobile_banner/' .$filename));
                    $category->mobile_banner_image = $filename;
                }
                else
                {
                    $image = $request->file('mobile_banner_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                    $category->banner_image = $request->file('mobile_banner_image')->getClientOriginalName();
                }
            }
            /* Code end for banner and mobile banner :Nivedita(11-01-2021)*/
            $request['lady_operator'] = isset($request->lady_operator) ? 1 : 0;
            $category->lady_operator = $request['lady_operator'];
            $request['upload_is_multiple'] = isset($request->upload_is_multiple) ? 1 : 0;
            $category->upload_is_multiple = $request->upload_is_multiple;
            $category->photo_upload = $request->photo_upload;
            $category->qty_matrix = $request->qty_matrix;
            $category->qty_range = $request->qty_range;
            $category->status = $request->status;
            $category->sort_order = $request->sort_order;
            $category->display_on = $request->display_on;

            if($category->save())
            {
                $cat = Category::findOrFail($category->id);
                if(isset($request->catId))
                {
                    $catPath = Category::findOrFail($request->catId);
                    $cat->category_path = $category->id.','.$catPath->category_path;
                    $catPath->flag_category = 1;
                    $catPath->save();
                }
                else
                    $cat->category_path = $category->id;

                $cat->save();
                $categoryDetails = new CategoryDetails;
                $categoryDetails->category_id = $category->id;
                $categoryDetails->language_id = $request->defaultLanguageId;
                $categoryDetails->title = $request->title;
                $categoryDetails->description = $request->description;
                $categoryDetails->meta_title = $request->meta_title;
                $categoryDetails->meta_keywords = $request->meta_keywords;
                $categoryDetails->meta_description = $request->meta_description;
                $categoryDetails->save();
            }
            $notification = array(
                'message' => 'Category added successfully!',
                'alert-type' => 'success'
            );
            // add under any category
            if(isset($request->catId))
                return redirect('admin/categories?catId='. $request->catId)->with($notification);
            // add new category
            else
                return redirect('admin/categories')->with($notification);
        }
    }

    public function categoryEditView($id)
    {
        $page = isset($_GET['page']) ? $_GET['page'] : "getDefaultLangData";
        $lang_id = isset($_GET['lang']) ? $_GET['lang'] : "defaultLang";
        $catId = isset($_GET['catId']) ? $_GET['catId'] : "";

        $catTitle = [];
        if(!empty($catId))
        {
            $category = Category::findOrFail($catId);
            if(!empty($category))
            {
                $catIds = explode(',',$category->category_path);
                foreach($catIds as $categoryId)
                {
                    $catTitle[] = CategoryDetails::select('title','category_id')
                                            ->where('category_id',$categoryId)
                                            ->first();
                }
            }
        }
        $nonDefaultLanguage = CategoryDetails::select('category_details.language_id','langEN')
                                            ->join('global_language as gl','gl.id','=','category_details.language_id')
                                            ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                                            ->whereNull('category_details.deleted_at')
                                            ->where('category_id',$id)
                                            ->get()->toArray();

        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;
        $categoryDetails = [];

        if($page != 'otherLang')
        {
            $categoryDetails = Category::select('categories.id as id', 'slug as slug', 'categories.status as status','lady_operator','photo_upload','qty_matrix',
                                    'qty_range','sort_order','category_image','display_on','cd.title as title', 'cd.language_id as languageId','cd.description'
                                    ,'cd.meta_title','cd.meta_keywords','cd.meta_description','categories.banner_image','categories.mobile_banner_image','categories.category_image','categories.upload_is_multiple')
                                    ->join('category_details as cd', 'cd.category_id', '=', 'categories.id')
                                    ->findOrFail($id);

            if(!empty($categoryDetails))
            {
                $categoryDetails = $categoryDetails->toArray();
            }
        }
        else
        {
            $categoryDetails = Category::select('categories.id as id','cd.title as title', 'cd.language_id as languageId','cd.description','cd.meta_title','cd.meta_keywords','cd.meta_description'
                                    ,'cd.banner_image','cd.mobile_banner','categories.category_image','categories.banner_image as cat_banner','categories.mobile_banner_image as cat_mob_banner')
                                    ->join('category_details as cd', 'cd.category_id', '=', 'categories.id')
                                    ->where('language_id',$lang_id)
                                    ->where('category_id',$id)
                                    ->whereNull('cd.deleted_at')
                                    ->first($id);
            if(!empty($categoryDetails))
            {
                $categoryDetails = $categoryDetails->toArray();
                return response()->json(['status' => true, 'categoryDetails' => $categoryDetails]);
            }
        }

        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $formTitle = "Edit Category";
        $baseUrl = $this->getBaseUrl();
        return view('admin.categories.edit',compact('categoryDetails','nonDefaultLanguage','defaultLanguageId','otherLanguages','formTitle','baseUrl','catTitle'));
    }

    public function updateCategory(Request $request)
    {
        $category = Category::findOrFail($request->catId);
        if (isset($category))
        {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $defaultLanguage = $defaultLanguageData['language']['langEN'];
            $defaultLanguageId = $defaultLanguageData['id'];

            $validator = Validator::make(['slug' => $request->slug], [
                'slug' => "required|unique:categories,slug,NULL,id,deleted_at,NULL".$request->catId
            ]);

            if ($validator->fails())
            {
                $notification = array(
                    'message' => 'Slug already exists !!',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification)->withInput();
            }

            $category->slug = $request->slug;

            $reqdImgWidth =Config::get('app.category_image.width');
            $reqdImgHeight =Config::get('app.category_image.height');
            if($request->hasFile('category_image'))
            {
                if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
                {
                    $image       = $request->file('category_image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                    $image->move(public_path('assets/images/categories/'), $filename);
                    $image_resize->save(public_path('assets/images/categories/' .$filename));
                    $category->category_image = $filename;
                }
                else
                {
                    $image = $request->file('category_image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/categories/'), $filename);
                    $category->category_image = $request->file('category_image')->getClientOriginalName();
                }
            }

            if($request->language_id == $defaultLanguageId)
            {
                /* Code for banner and mobile banner :Nivedita(11-01-2021)*/
                $reqdBannerWidth =Config::get('app.category_banner_image.width');
                $reqdBannerHeight =Config::get('app.category_banner_image.height');
                if($request->hasFile('banner_image'))
                {
                    if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                    {
                        $image       = $request->file('banner_image');
                        $filename    = $image->getClientOriginalName();

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                        $image->move(public_path('assets/images/categories/banner/'), $filename);
                        $image_resize->save(public_path('assets/images/categories/banner/' .$filename));
                        $category->banner_image = $filename;
                    }
                    else
                    {
                        $image = $request->file('banner_image');
                        $filename = $image->getClientOriginalName();
                        $image->move(public_path('assets/images/categories/banner/'), $filename);
                        $category->banner_image = $request->file('banner_image')->getClientOriginalName();
                    }
                }

                $reqdMobBannerWidth =Config::get('app.category_mobile_banner_image.width');
                $reqdMobBannerHeight =Config::get('app.category_mobile_banner_image.height');
                if($request->hasFile('mobile_banner_image'))
                {
                    if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                    {
                        $image       = $request->file('mobile_banner_image');
                        $filename    = $image->getClientOriginalName();

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                        $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                        $image_resize->save(public_path('assets/images/categories/mobile_banner/' .$filename));
                        $category->mobile_banner_image = $filename;
                    }
                    else
                    {
                        $image = $request->file('mobile_banner_image');
                        $filename = $image->getClientOriginalName();
                        $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                        $category->mobile_banner_image = $request->file('mobile_banner_image')->getClientOriginalName();
                    }
                }
                /* Code end for banner and mobile banner :Nivedita(11-01-2021)*/
            }
            $request['lady_operator'] = isset($request->lady_operator) ? 1 : 0;
            $category->lady_operator = $request['lady_operator'];
            $category->photo_upload = $request->photo_upload;
            $request['upload_is_multiple'] = isset($request->upload_is_multiple) ? 1 : 0;
            $category->upload_is_multiple = $request->upload_is_multiple;
            $category->qty_matrix = $request->qty_matrix;
            $category->qty_range = $request->qty_range;
            $category->status = $request->status;
            $category->sort_order = $request->sort_order;
            $category->display_on = $request->display_on;
            $category->save();

            $categoryDetails = CategoryDetails::where(['category_id'=>$request->catId, 'language_id'=>$request->language_id])->first();
            if (!empty($categoryDetails))
            {
                $categoryDetails->category_id = $category->id;
                $categoryDetails->language_id = $request->language_id;
                $categoryDetails->title = $request->title;
                $categoryDetails->description = $request->description;
                $categoryDetails->meta_title = $request->meta_title;
                $categoryDetails->meta_keywords = $request->meta_keywords;
                $categoryDetails->meta_description = $request->meta_description;$reqdBannerWidth =Config::get('app.category_banner_image.width');

                if($request->language_id != $defaultLanguageId)
                {
                    $reqdBannerHeight =Config::get('app.category_banner_image.height');
                    if($request->hasFile('banner_image'))
                    {
                        if($request->loaded_banner_width != $reqdBannerWidth || $request->loaded_banner_height != $reqdBannerHeight)
                        {
                            $image       = $request->file('banner_image');
                            $filename    = $image->getClientOriginalName();

                            $image_resize = Image::make($image->getRealPath());
                            $image_resize->resize($reqdBannerWidth, $reqdBannerHeight);
                            $image->move(public_path('assets/images/categories/banner/'), $filename);
                            $image_resize->save(public_path('assets/images/categories/banner/' .$filename));
                            $categoryDetails->banner_image = $filename;
                        }
                        else
                        {
                            $image = $request->file('banner_image');
                            $filename = $image->getClientOriginalName();
                            $image->move(public_path('assets/images/categories/banner/'), $filename);
                            $categoryDetails->banner_image = $request->file('banner_image')->getClientOriginalName();
                        }
                    }
                    $reqdMobBannerWidth =Config::get('app.category_mobile_banner_image.width');
                    $reqdMobBannerHeight =Config::get('app.category_mobile_banner_image.height');
                    if($request->hasFile('mobile_banner_image'))
                    {
                        if($request->loaded_mobile_banner_width != $reqdMobBannerWidth || $request->loaded_mobile_banner_height != $reqdMobBannerHeight)
                        {
                            $image       = $request->file('mobile_banner_image');
                            $filename    = $image->getClientOriginalName();

                            $image_resize = Image::make($image->getRealPath());
                            $image_resize->resize($reqdMobBannerWidth, $reqdMobBannerHeight);
                            $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                            $image_resize->save(public_path('assets/images/categories/mobile_banner/' .$filename));
                            $categoryDetails->mobile_banner = $filename;
                        }
                        else
                        {
                            $image = $request->file('mobile_banner_image');
                            $filename = $image->getClientOriginalName();
                            $image->move(public_path('assets/images/categories/mobile_banner/'), $filename);
                            $categoryDetails->mobile_banner = $request->file('mobile_banner_image')->getClientOriginalName();
                        }
                    }
                }
                $categoryDetails->save();
            }
            $notification = array(
                'message' => 'Category updated successfully!',
                'alert-type' => 'success'
            );
            return redirect($request->prevUrl)->with($notification);
        }
    }

    public function pageActiveInactive(Request $request)
    {
        try
        {
            $page = CmsPages::where('id',$request->pageId)->first();
            if($request->is_active == 1)
            {
                $page->status = $request->is_active;
                $msg = "Page Activated Successfully!";
            }
            else
            {
                $page->status = $request->is_active;
                $msg = "Page Deactivated Successfully!";
            }
            $page->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        catch(\Exception $ex)
        {
            return view('errors.500');
        }
    }

    public function categoryActiveInactive(Request $request)
    {
        try
        {
            $category = Category::where('id',$request->catId)->first();

            if($request->is_active == 1)
            {
                $category->status = $request->is_active;
                $msg = "Category Activated Successfully!";
            }
            else
            {
                $category->status = $request->is_active;
                $msg = "Category Deactivated Successfully!";
            }
            $category->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        catch(\Exception $ex)
        {
            return view('errors.500');
        }
    }

    public function getLanguageWiseCategory(Request $request)
    {
        $arrResult = [];
        $categoryDetails = CategoryDetails::with('globalLanguage')
                                            ->where('category_id',$request['catId'])
                                            ->whereNull('category_details.deleted_at')
                                            ->get();

        $index = 0;
        foreach ($categoryDetails as $key)
        {
            $arrResult[$index]['id'] = $key->id;
            $arrResult[$index]['catName'] = $key->title;
            $arrResult[$index]['languageName'] = $key['globalLanguage']['language']->langEN;
            $arrResult[$index++]['isDefault'] = $key['globalLanguage']->is_default;
        }

        return DataTables::of($arrResult)->make();
    }

    public function deleteCategory(Request $request)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $category = CategoryDetails::find($request->categoryDetailId);

        if ($category->language_id == $defaultLanguageData['id'])
        {
            $updateCategory = CategoryDetails::where('category_id', $category->category_id)->get();
            foreach($updateCategory as $category)
            {
                $category->deleted_at = Carbon::now();
                $category->save();
            }

            $categoryDelete = Category::find($category->category_id);
            $getCatWithParentId = Category::where('parent_id',$categoryDelete->parent_id)
                                            ->where('id','!=',$category->category_id)
                                            ->whereNull('deleted_at')
                                            ->get();

            if(($getCatWithParentId->isEmpty()))
            {
                // echo "in if"; die;
                $setFlag = Category::where('id',$categoryDelete->parent_id)->first();
                $setFlag->flag_category = 0;
                $setFlag->save();
            }
            $categoryDelete->deleted_at = now();
            if ($categoryDelete->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Category Deleted Successfully!!')
                );
            }
        }
        else
        {
            $category->deleted_at = now();
            if ($category->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Category Deleted Successfully!!')
                );
            }
        }
    }
    public function getCategory($id){
      $catId =$id;
      $category = Category::where('id',$catId)->first();
      return $category;
    }

    public function uploadCategoryImage(Request $request)
    {
        $folder_name = 'ckeditor-category-image';
        uploadCKeditorImage($request, $folder_name);
    }
}
