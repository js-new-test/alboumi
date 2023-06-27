<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Models\Image as ImageModel;
use App\Traits\ExportTrait;
use App\Traits\CommonTrait;
use DataTables;
use Storage;
use App\Models\BrandDetails;
use App\Models\GlobalLanguage;
use DB;
use Auth;
use Validator;
use Image;
use Config;

class ManufacturerController extends Controller
{
    /**
     *
     * Creating an Object
     */
    protected $Manufacturer;

    use ExportTrait, CommonTrait;

    public function __construct(Manufacturer $Manufacturer)
    {
        $this->Manufacturer = $Manufacturer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $baseUrl = $this->getBaseUrl();
        $languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN','is_default')
                                    ->where('status',1)
                                    ->where('global_language.is_deleted',0)
                                    ->get();
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        return view('admin.manufacturer.index', compact('baseUrl','otherLanguages','languages'));

    }

    public function getManufactuerList(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        $brands = Manufacturer::select('manufacturers.id', 'manufacturers.status', 'slug','brand_details.name as brandName', DB::raw("date_format(manufacturers.created_at,'%Y-%m-%d %h:%i:%s') as mfg_created_at"), 'images.name as imageName')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'manufacturers.id');
            $join->where('images.image_type','=','brand');
        })
        // ->leftJoin('images', 'images.imageable_id', '=', 'manufacturers.id')
        ->where('brand_details.language_id', $defaultLanguageId)
        // ->where('images.image_type', "=", "brand")
        ->whereNull('brand_details.deleted_at')
        ->whereNull('manufacturers.deleted_at')
        ->get();

        if($request['lang_id'] != null)
    		{
                $brands = Manufacturer::select('manufacturers.id', 'manufacturers.status','slug', 'brand_details.name as brandName', DB::raw("date_format(manufacturers.created_at,'%Y-%m-%d %h:%i:%s') as mfg_created_at"), 'images.name as imageName')
    		        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
                ->leftJoin('images',function ($join) {
                    $join->on('images.imageable_id', '=' , 'manufacturers.id');
                    $join->where('images.image_type','=','brand');
                })
    		        ->where('brand_details.language_id',$request['lang_id'])
    		        //->where('images.image_type', "=", "brand")
                ->whereNull('brand_details.deleted_at')
    		        ->whereNull('manufacturers.deleted_at')
    		        ->get();
        }

        return DataTables::of($brands)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make();


        // $brands = Manufacturer::select('manufacturers.id', 'manufacturers.status', 'brand_details.name as brandName')
        // ->get();
        // return DataTables::of($brands)->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getAddManufactuer()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : "addBrand";
        $brandId = isset($_GET['brandId']) ? $_GET['brandId'] : "";
        $formTitle = "Add Brand";

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];

        if (!empty($brandId)) {
            $brand = BrandDetails::where(['brand_id'=>$brandId, 'language_id'=>$defaultLanguageId])->whereNull('deleted_at')->first();
            $brandName = $brand->name;
        }

        $language = "";
        if ($page == "anotherLanguage") {
            //get existing language ids
            $existingLanguageIds = BrandDetails::where('brand_id', $brandId)->whereNull('deleted_at')->get()->pluck('language_id')->toArray();

            $language = GlobalLanguage::select('global_language.id as globalLanguageId', 'world_languages.langEN as languageName')
            ->Join('world_languages', 'world_languages.id', '=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->whereNotIn('global_language.id', $existingLanguageIds)
            ->get()->toArray();

            $formTitle = "Add Brand - Other Language ($brandName)";
        }

        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();

        return view('admin.manufacturer.addManufactuer',compact('defaultLanguage', 'page', 'formTitle', 'brandId', 'language', 'defaultLanguageId','otherLanguages'));
    }

    public function getBrands()
    {
        $brands = Manufacturer::all();
        return $brands;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postAddManufactuer(Request $request)
    {
        if (isset($request->brandId)) {
            $brandDetails = BrandDetails::where(['brand_id'=>$request->brandId, 'language_id'=>$request->defaultLanguage])->first();

                $brandDetails = new BrandDetails;
                $brandDetails->brand_id = $request->brandId;
                $brandDetails->name = $request->brandName;
                $brandDetails->language_id = $request->defaultLanguage;
                $brandDetails->description = $request->description;
                $brandDetails->save();

        } else {
            $manufacturer = new Manufacturer();
            $manufacturer->status = $request->status;

            $validator = Validator::make(['slug' => $request->slug], [
                'slug' => "required|unique:manufacturers,slug",
            ]);

            if ($validator->fails())
            {
                $notification = array(
                    'message' => 'Slug already exists !!',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification)->withInput();
            }

            $manufacturer->slug = $request->slug;
            if($manufacturer->save()){

                $brandDetails = new BrandDetails;
                $brandDetails->brand_id = $manufacturer->id;
                $brandDetails->name = $request->brandName;
                $brandDetails->language_id = $request->defaultLanguage;
                $brandDetails->description = $request->description;
                $brandDetails->save();

                $node = Manufacturer::findOrFail($manufacturer->id);
                $reqdImgWidth =Config::get('app.brand_image.width');
                $reqdImgHeight =Config::get('app.brand_image.height');
                if($request->hasFile('brandLogo'))
                {
                    if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
                    {
                        $image       = $request->file('brandLogo');
                        $filename    = $image->getClientOriginalName();

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                        $image->move(public_path('assets/images/brands/'), $filename);
                        $image_resize->save(public_path('assets/images/brands/' .$filename));

                        $imageInfo = pathinfo($request->file('brandLogo')->getClientOriginalName());
                        $smallImageName = $imageInfo['filename'] . '_' . $manufacturer->id . '_small' . '.' . $imageInfo['extension'];
                        $thumbImageName = $imageInfo['filename'] . '_' . $manufacturer->id . '_thumb' . '.' . $imageInfo['extension'];

                        $imageObj = new ImageModel;
                        $imageObj->name = $filename;
                        $imageObj->small_image = $manufacturer->id.$smallImageName;
                        $imageObj->thumb_image = $manufacturer->id.$thumbImageName;
                        $imageObj->original_filename = $imageInfo['basename'];
                        $imageObj->created_at = Carbon::now();
                        $imageObj->updated_at = Carbon::now();
                        $imageObj->upload_path = public_path('assets/images/brands/');
                        $imageObj->image_type = 'brand';
                        $imageObj->label = 'brand';
                        $imageObj->mime = 'png';
                        $imageObj->sort_order = 0;
                        $imageObj->imageable_id = 0;
                        $imageObj->imageable_type = " ";
                        $imageObj->tags = " ";
                        $imageObj->description = " ";
                        $imageObj->save();
                        $node->images()->save($imageObj);
                    }
                    else
                    {
                        $image = $request->file('brandLogo');
                        $filename = $image->getClientOriginalName();
                        $image->move(public_path('assets/images/brands/'), $filename);
                        $imageInfo = pathinfo($request->file('brandLogo')->getClientOriginalName());
                        $smallImageName = $imageInfo['filename'] . '_' . $manufacturer->id . '_small' . '.' . $imageInfo['extension'];
                        $thumbImageName = $imageInfo['filename'] . '_' . $manufacturer->id . '_thumb' . '.' . $imageInfo['extension'];

                        $imageObj = new ImageModel;
                        $imageObj->name = $filename;
                        $imageObj->small_image = $manufacturer->id.$smallImageName;
                        $imageObj->thumb_image = $manufacturer->id.$thumbImageName;
                        $imageObj->original_filename = $imageInfo['basename'];
                        $imageObj->created_at = Carbon::now();
                        $imageObj->updated_at = Carbon::now();
                        $imageObj->upload_path = public_path('assets/images/brands/');
                        $imageObj->image_type = 'brand';
                        $imageObj->label = 'brand';
                        $imageObj->mime = 'png';
                        $imageObj->sort_order = 0;
                        $imageObj->imageable_id = 0;
                        $imageObj->imageable_type = " ";
                        $imageObj->tags = " ";
                        $imageObj->description = " ";
                        $imageObj->save();
                        $node->images()->save($imageObj);
                    }
                }
                // if ($request->brandLogo != "") {

                //     $imageInfo = pathinfo($request->file('brandLogo')->getClientOriginalName());
                //     //append image name with category id
                //     $imageName = $imageInfo['filename'] . '_' . $manufacturer->id . '.' . $imageInfo['extension'];
                //     $smallImageName = $imageInfo['filename'] . '_' . $manufacturer->id . '_small' . '.' . $imageInfo['extension'];
                //     $thumbImageName = $imageInfo['filename'] . '_' . $manufacturer->id . '_thumb' . '.' . $imageInfo['extension'];
                //     if (Image::where('imageable_id', '=', $manufacturer->id)->where('image_type' ,'brand')) {


                //         $storeImage = "admin/images/thumb/".$node->getBrandImageByType('brand')->name;
                //         $url_arr = explode('/', $storeImage);
                //         // return $url_arr;

                //         $ct = count($url_arr);

                //         $name = $url_arr[$ct - 1];

                //         if ($name != 'default_main_image') {

                //             $name_div = explode('.', $name);
                //             $ct_dot = count($name_div);
                //             $img_type = $name_div[$ct_dot - 1];
                //             $img_base_name = $name_div[$ct_dot - 2];


                //             $main_image = $img_base_name . "." . $img_type;
                //             $small_image = $img_base_name . "_small." . $img_type;
                //             $thumb_image = $img_base_name . "_thumb." . $img_type;

                //             $uploadPath = base_path('storage' . DIRECTORY_SEPARATOR . 'app'. DIRECTORY_SEPARATOR . "brands") . DIRECTORY_SEPARATOR . config('app.image_paths.BRAND') .$manufacturer->id . DIRECTORY_SEPARATOR;


                //             File::Delete($uploadPath . $main_image);
                //             File::Delete($uploadPath . $small_image);
                //             File::Delete($uploadPath . $thumb_image);
                //             Image::where('imageable_id', '=', $manufacturer->id)->where('image_type' ,'brand')->delete();
                //         }


                //     }
                //     if (Image::where('name', '=', $imageName)->where('image_type' ,'brand')->count()) { //if already have this image, delete it
                //         Image::where('name', '=', $imageName)->where('image_type' ,'brand')->delete();
                //     }

                //     $prefix = Image::getPathPrefix();
                //     $uploadPath = config('app.image_paths.BRAND') . $manufacturer->id . DIRECTORY_SEPARATOR;
                //     if (!Storage::exists($uploadPath)) {
                //         Storage::makeDirectory($uploadPath);
                //     }

                //     move_uploaded_file($request->file('brandLogo'), $prefix . $uploadPath . $imageName);
                //     // $image = Image::make($request->brandLogo)->move($prefix . $uploadPath . $manufacturer->id.$imageName);
                //     // $image->fit(config('app.image_size.SMALL.height'),
                //     //     config('app.image_size.SMALL.width'))->save($prefix . $uploadPath . $manufacturer->id.$smallImageName);
                //     // $image->fit(config('app.image_size.THUMB.height'),
                //     //     config('app.image_size.THUMB.width'))->save($prefix . $uploadPath . $manufacturer->id.$thumbImageName);


                //     $imageObj = new Image;
                //     $imageObj->name = $imageName;
                //     $imageObj->small_image = $manufacturer->id.$smallImageName;
                //     $imageObj->thumb_image = $manufacturer->id.$thumbImageName;
                //     $imageObj->original_filename = $imageInfo['basename'];
                //     $imageObj->created_at = Carbon::now();
                //     $imageObj->updated_at = Carbon::now();
                //     $imageObj->upload_path = $uploadPath;
                //     $imageObj->image_type = 'brand';
                //     $imageObj->label = 'brand';
                //     $imageObj->mime = 'png';
                //     $imageObj->sort_order = 0;
                //     $imageObj->imageable_id = 0;
                //     $imageObj->imageable_type = " ";
                //     $imageObj->tags = " ";
                //     $imageObj->description = " ";
                //     $imageObj->save();
                //     $node->images()->save($imageObj);
                // }

            }
        }

        $notification = array(
            'message' => 'Brand updated successfully!',
            'alert-type' => 'success'
        );

        return redirect('admin/manufacturers')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function getShowBrand($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        return view('admin.manufacturer.showBrand', compact('manufacturer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function getEditManufactuer($id)
    {
        $baseUrl = $this->getBaseUrl();

        $brandDetails = BrandDetails::where('brand_id', $id)->whereNull('deleted_at')->get()->pluck('language_id')->toArray();

        $nonDefaultLanguage = GlobalLanguage::with('language')->where('status', 1)->whereIn('id',$brandDetails)->get();

        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;
        $manufacturer = Manufacturer::select('manufacturers.id as id', 'brand_details.name as name', 'slug','manufacturers.status as status', 'brand_details.description as description', 'brand_details.language_id as languageId')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')->findOrFail($id);

        if(!empty($manufacturer)){
            $manufacturer = $manufacturer->toArray();
            $manufacturer['image'] = '';
            $image = ImageModel::where('imageable_id', $manufacturer['id'])->where('image_type', 'brand')->first();
            if(!empty($image)){
                $manufacturer['image'] = 'public/assets/images/brands/' . $image->name;// config('app.image_paths.BRAND') . $id . DIRECTORY_SEPARATOR . $image->name;
            }
        }
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();

        return view('admin.manufacturer.editManufacturer', compact('manufacturer', 'nonDefaultLanguage', 'defaultLanguageId', 'baseUrl','otherLanguages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function postEditManufactuer(Request $request)
    {
        $id = $request['brandId'];
        $data = $request->all();
        $Manufacturer = Manufacturer::findOrFail($id);

        $brandDetails = BrandDetails::where('brand_id', $id)->where('language_id', $data['defaultLanguage'])->whereNull('deleted_at')->first();
        if (!empty($brandDetails)) {
            $brandDetails->name = $data['brandName'];
            $brandDetails->description = $data['description'];
            $brandDetails->save();

        }else{
            $brandDetails = new BrandDetails;
            $brandDetails->brand_id = $id;
            $brandDetails->language_id = $data['defaultLanguage'];
            $brandDetails->name = $data['brandName'];
            $brandDetails->description = $data['description'];
            $brandDetails->save();
        }
        $Manufacturer->status = $data['status'];

        $validator = Validator::make(['slug' => $request->slug], [
            'slug' => "required|unique:manufacturers,slug,".$id
        ]);

        if ($validator->fails())
        {
            $notification = array(
                'message' => 'Slug already exists !!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification)->withInput();
        }

        $Manufacturer->slug = $data['slug'];
        $Manufacturer->save();

        $reqdImgWidth =Config::get('app.brand_image.width');
        $reqdImgHeight =Config::get('app.brand_image.height');
        if($request->hasFile('brandLogo'))
        {
            if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
            {
                $image       = $request->file('brandLogo');
                $filename    = $image->getClientOriginalName();

                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                $image->move(public_path('assets/images/brands/'), $filename);
                $image_resize->save(public_path('assets/images/brands/' .$filename));

                $imageInfo = pathinfo($request->file('brandLogo')->getClientOriginalName());
                $smallImageName = $imageInfo['filename'] . '_' . $Manufacturer->id . '_small' . '.' . $imageInfo['extension'];
                $thumbImageName = $imageInfo['filename'] . '_' . $Manufacturer->id . '_thumb' . '.' . $imageInfo['extension'];

                $imageObj = ImageModel::where('imageable_id', '=', $id)->where('image_type' ,'brand')->first();
                $imageObj->name = $filename;
                $imageObj->small_image = $Manufacturer->id.$smallImageName;
                $imageObj->thumb_image = $Manufacturer->id.$thumbImageName;
                $imageObj->original_filename = $imageInfo['basename'];
                $imageObj->created_at = Carbon::now();
                $imageObj->updated_at = Carbon::now();
                $imageObj->upload_path = public_path('assets/images/brands/');
                $imageObj->image_type = 'brand';
                $imageObj->label = 'brand';
                $imageObj->mime = 'png';
                $imageObj->sort_order = 0;
                $imageObj->imageable_id = 0;
                $imageObj->imageable_type = " ";
                $imageObj->tags = " ";
                $imageObj->description = " ";
                $imageObj->save();
                $Manufacturer->images()->save($imageObj);
            }
            else
            {
                $image = $request->file('brandLogo');
                $filename = $image->getClientOriginalName();
                $image->move(public_path('assets/images/brands/'), $filename);
                $imageInfo = pathinfo($request->file('brandLogo')->getClientOriginalName());
                $smallImageName = $imageInfo['filename'] . '_' . $Manufacturer->id . '_small' . '.' . $imageInfo['extension'];
                $thumbImageName = $imageInfo['filename'] . '_' . $Manufacturer->id . '_thumb' . '.' . $imageInfo['extension'];

                $imageObj = ImageModel::where('imageable_id', '=', $id)->where('image_type' ,'brand')->first();
                $imageObj->name = $filename;
                $imageObj->small_image = $Manufacturer->id.$smallImageName;
                $imageObj->thumb_image = $Manufacturer->id.$thumbImageName;
                $imageObj->original_filename = $imageInfo['basename'];
                $imageObj->created_at = Carbon::now();
                $imageObj->updated_at = Carbon::now();
                $imageObj->upload_path = public_path('assets/images/brands/');
                $imageObj->image_type = 'brand';
                $imageObj->label = 'brand';
                $imageObj->mime = 'png';
                $imageObj->sort_order = 0;
                $imageObj->imageable_id = 0;
                $imageObj->imageable_type = " ";
                $imageObj->tags = " ";
                $imageObj->description = " ";
                $imageObj->save();
                $Manufacturer->images()->save($imageObj);
            }
        }
        // if ($request->brandLogo != "") {
        //     $imageInfo = pathinfo($request->file('brandLogo')->getClientOriginalName());

        //     $imageName = $imageInfo['filename'] . '_' . $id . '.' . $imageInfo['extension'];
        //     $smallImageName = $imageInfo['filename'] . '_' . $id . '_small' . '.' . $imageInfo['extension'];
        //     $thumbImageName = $imageInfo['filename'] . '_' . $id . '_thumb' . '.' . $imageInfo['extension'];
        //     if (Image::where('imageable_id', '=', $id)->where('image_type' ,'brand')) {

        //         $storeImage = "admin/images/thumb/".$Manufacturer->getBrandImageByType('brand')->name;
        //         $url_arr = explode('/', $storeImage);


        //         $ct = count($url_arr);

        //         $name = $url_arr[$ct - 1];

        //         if ($name != 'default_main_image') {

        //             $name_div = explode('.', $name);
        //             $ct_dot = count($name_div);
        //             $img_type = $name_div[$ct_dot - 1];
        //             $img_base_name = $name_div[$ct_dot - 2];

        //             $main_image = $img_base_name . "." . $img_type;
        //             $small_image = $img_base_name . "_small." . $img_type;
        //             $thumb_image = $img_base_name . "_thumb." . $img_type;

        //             $uploadPath = base_path('storage' . DIRECTORY_SEPARATOR . 'app') . DIRECTORY_SEPARATOR . config('app.image_paths.BRAND') .$id . DIRECTORY_SEPARATOR;

        //             File::Delete($uploadPath . $main_image);
        //             File::Delete($uploadPath . $small_image);
        //             File::Delete($uploadPath . $thumb_image);
        //             Image::where('imageable_id', '=', $id)->where('image_type' ,'brand')->delete();
        //         }


        //     }
        //     if (Image::where('name', '=', $imageName)->where('image_type' ,'brand')->count()) { //if already have this image, delete it
        //         Image::where('name', '=', $imageName)->where('image_type' ,'brand')->delete();
        //     }

        //     $prefix = Image::getPathPrefix();
        //     $uploadPath = config('app.image_paths.BRAND') . $id . DIRECTORY_SEPARATOR;

        //     if (!Storage::exists($uploadPath)) {
        //         Storage::makeDirectory($uploadPath);
        //     }

        //     move_uploaded_file($request->file('brandLogo'), $prefix . $uploadPath . $imageName);

        //     // $image = \Image::make(Input::file('brand_image'))->save($prefix . $uploadPath . $id.$imageName);
        //     // $image->fit(config('app.image_size.SMALL.height'),
        //     //     config('app.image_size.SMALL.width'))->save($prefix . $uploadPath . $id.$smallImageName);
        //     // $image->fit(config('app.image_size.THUMB.height'),
        //         // config('app.image_size.THUMB.width'))->save($prefix . $uploadPath . $id.$thumbImageName);

        //     $imageObj = new Image;
        //     $imageObj->name = $imageName;
        //     $imageObj->small_image = $Manufacturer->id.$smallImageName;
        //     $imageObj->thumb_image = $Manufacturer->id.$thumbImageName;
        //     $imageObj->original_filename = $imageInfo['basename'];
        //     $imageObj->created_at = Carbon::now();
        //     $imageObj->updated_at = Carbon::now();
        //     $imageObj->upload_path = $uploadPath;
        //     $imageObj->image_type = 'brand';
        //     $imageObj->label = 'brand';
        //     $imageObj->mime = 'png';
        //     $imageObj->sort_order = 0;
        //     $imageObj->imageable_id = 0;
        //     $imageObj->imageable_type = " ";
        //     $imageObj->tags = " ";
        //     $imageObj->description = " ";
        //     $imageObj->save();

        //     $Manufacturer->images()->save($imageObj);
        // }

        $notification = array(
            'message' => 'Brand updated successfully!',
            'alert-type' => 'success'
        );

        return redirect('admin/manufacturers')->with($notification);

        // return redirect('admin/manufacturers');
        // \Session::flash('success', trans('messages.success.manufacturer.update'));
        // return route($request->segment(1));
        // return redirect(route($request->segment(1) . '.manufacturer.edit', $id));
        //        if($request->previous_page == '') {
        //            return redirect(route($request->segment(1) . '.manufacturer.index'));
        //        }
        //        else{
        //
        //            return redirect($request->segment(1) . '/manufacturer?'.$request->previous_page);
        //        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */

    /*
        comment by Ajay
        no more need for this method so right now the code is commented once confirm from my own testing then will remove the code
    */
    // public function destroy($id, Request $request)
    // {
    //     $previous_url =  URL::previous();
    //     $values = parse_url($previous_url);

    //     $can_delete =  $this->product->where('manufacturer_id', '=', $id)->where('status', '!=', 'Deleted')->first();
    //     if (!empty($can_delete)) {
    //         \Session::flash('error', trans('messages.success.manufacturer.not_delete'));
    //         //return redirect(route('admin.seller.index'));
    //         if (isset($values['query'])) {
    //             if ($request->records == 1) {
    //                 $page_count = explode('=', $values['query']);
    //                 $page = $page_count[1] - 1;
    //                 return redirect($request->segment(1).'/manufacturer?'.$page_count[0].'='.$page);
    //             } else {
    //                 return redirect($request->segment(1).'/manufacturer?'.$values['query']);
    //             }
    //         } else {
    //             return redirect(route($request->segment(1).'.manufacturer.index'));
    //         }
    //     }
    //     $this->Manufacturer->deleteManufacturer($id);
    //     Session::flash('success', trans('messages.success.manufacturer.delete'));
    //     if(isset($values['query'])) {
    //         if($request->records == 1)
    //         {
    //             $page_count = explode('=', $values['query']);
    //             $page = $page_count[1] - 1;
    //             return redirect($request->segment(1).'/manufacturer?'.$page_count[0].'='.$page);
    //         }
    //         else{
    //             return redirect($request->segment(1).'/manufacturer?'.$values['query']);
    //         }
    //     }
    //     else
    //     {
    //         return redirect(route($request->segment(1).'.manufacturer.index'));
    //     }
    // }

    /**
     * Exporting Brands to xls file
     *
     */

    public function getExportBrand(Request $request)
    {
        try{
            $manufacturers = $this->Manufacturer->select('manufacturers.id as Id', 'manufacturers.name as Name', 'manufacturers.status as Status')
                ->orderBy('manufacturers.id','desc')
                ->get()
                ->toArray();
            $sheetTitle = 'brands';
            return $this->exportBrands($manufacturers, $sheetTitle);
        } catch(\Exception $ex) {
            return $ex;
            // Session::flash('error', $ex->getMessage());
            return redirect($request->segment(1).'/manufacturers');
        }
    }

    public function getDeleteBrand(Request $request)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);

        $brand = BrandDetails::find($request->brandDetailId);
        if ($brand->language_id == $defaultLanguageData['id']) {

            $temp = ['deleted_at'=>now()];
            $updatebrands = BrandDetails::where('brand_id', $brand->brand_id)->update($temp);
            $brandDelete = Manufacturer::find($brand->brand_id);
            $brandDelete->deleted_at = now();
            if ($brandDelete->save()) {
                return array(
                    'success' => true,
                    'message' => trans('Brand Deleted Successfully')
                );
            }
        }else{
            $brand->deleted_at = now();
            if ($brand->save()) {
                return array(
                    'success' => true,
                    'message' => trans('Brand Deleted Successfully')
                );
            }
        }
    }

    public function getLanguageWiseBrand(Request $request)
    {
        $arrResult = [];
        $manufacturerDetails = BrandDetails::with('brands')->with('globalLanguage')->where('brand_id',$request['brandId'])->whereNull('deleted_at')->get();

        $index = 0;
        foreach ($manufacturerDetails as $key) {
            $arrResult[$index]['id'] = $key->id;
            $arrResult[$index]['brandName'] = $key->name;
            $arrResult[$index]['languageName'] = $key['globalLanguage']['language']->langEN;
            $arrResult[$index++]['isDefault'] = $key['globalLanguage']->is_default;
        }

        return DataTables::of($arrResult)->make();
    }

    public function getLanguageBrandData(Request $request)
    {
        $languageBrandData = Manufacturer::
                            join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
                            ->where('manufacturers.id', $request['brandId'])
                            ->where('brand_details.language_id', $request['languageId'])
                            ->whereNull('brand_details.deleted_at')
                            ->first();

        return $languageBrandData;
    }

    public function getBrandUpdateStatus(Request $request)
    {
        $data = $request->all();
        $manufacturer = Manufacturer::find($data['brandId']);

        $status = 'Active';
        if ($request['brandStatus'] == 0) {
            $status = 'Inactive';
        }
        $manufacturer->status = $status;
        $manufacturer->save();
        $message = "Status Changed Successfully";

        return array(
                'success' => true,
                'message' => $message
            );
    }

    public function uploadCKeditorBrandImage(Request $request)
    {
        $folder_name = 'ckeditor-brand-image';
        uploadCKeditorImage($request, $folder_name);
    }
}
