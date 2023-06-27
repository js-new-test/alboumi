<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Attribute;
use App\Models\AttributeGroupDetails;
use App\Models\AttributeDetails;
use App\Models\GlobalLanguage;
use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\CategoryDetails;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\ExportTrait;
use App\Traits\CommonTrait;
use Image;
use Config;

class AttributeController extends Controller
{
    protected $category;
    use ExportTrait,CommonTrait;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getListOfAttribute()
    {
        $languages = GlobalLanguage::getAllLanguages();
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $baseUrl = $this->getBaseUrl();
        return view('admin.attributes.index',compact('otherLanguages','languages','baseUrl'));
    }

    public function getAttributeData(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        DB::statement(DB::raw('set @rownum=0'));

        $attributes = Attribute::select('attribute.id','attribute.sort_order',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(ad.created_at,'%Y-%m-%d %h:%i:%s') as a_created_at"),
                            'ad.display_name','ad.name','agd.name as group_name','attribute.status')
                            ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
                            ->join('attribute_groups as ag','ag.id','=','ad.attribute_group_id')
                            ->join('attribute_group_details as agd','agd.attr_group_id','=','ag.id')
                            ->where('ad.language_id', $defaultLanguageId)
                            // ->where('agd.language_id', $defaultLanguageId)
                            ->whereNull('ad.deleted_at')
                            ->orderBy('ad.updated_at','desc')
                            ->get();
        
        if($request['lang_id'] != null)
        {
            // dd('in if');
            DB::statement(DB::raw('set @rownum=0'));

            $attributes = Attribute::select('attribute.id','attribute.sort_order',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            DB::raw("date_format(ad.created_at,'%Y-%m-%d %h:%i:%s') as a_created_at"),
                            'ad.display_name','ad.name','agd.name as group_name','attribute.status')
                            ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
                            ->join('attribute_groups as ag','ag.id','=','ad.attribute_group_id')
                            ->join('attribute_group_details as agd','agd.attr_group_id','=','ag.id')
                            ->where('ad.language_id',$request['lang_id'])
                            // ->where('agd.language_id',$request['lang_id'])
                            ->whereNull('ad.deleted_at')
                            ->orderBy('ad.updated_at','desc')
                            ->get();
        }
        // dd($attributes);
        return Datatables::of($attributes)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    public function attributeAddView()
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];
        $formTitle = "Add Attribute";

        $attribute_groups = AttributeGroupDetails::getAttributeGroups($defaultLanguageId);

        $page = isset($_GET['page']) ? $_GET['page'] : "addAttribute";
        $attributeId = isset($_GET['attributeId']) ? $_GET['attributeId'] : "";

        if (!empty($attributeId))
        {
            $attribute = AttributeDetails::where(['attribute_id'=> $attributeId, 'language_id'=>$defaultLanguageId])
                                                ->whereNull('deleted_at')
                                                ->first();
            $attributeName = $attribute->name;
        }

        $language = $attributeGroupId = "";
        if ($page == "anotherLanguage")
        {
            $existingLanguageIds = AttributeDetails::where('attribute_id', $attributeId)
                                                        ->whereNull('deleted_at')
                                                        ->get()
                                                        ->pluck('language_id')
                                                        ->toArray();

            $language = GlobalLanguage::select('global_language.id as globalLanguageId', 'world_languages.langEN as languageName')
                                    ->Join('world_languages', 'world_languages.id', '=', 'global_language.language_id')
                                    ->whereNotIn('global_language.id', $existingLanguageIds)
                                    ->where('status', 1)
                                    ->where('is_deleted',0)
                                    ->get()->toArray();

            $attributeGroupId = AttributeDetails::where('attribute_id', $attributeId)
                                                ->whereNull('deleted_at')
                                                ->first();

            $formTitle = "Add Attribute - Other Language ($attributeName)";
        }
        $page_name = 'add';
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        return view('admin.attributes.add',compact('attribute_groups','defaultLanguage','defaultLanguageId','page','attributeId','language','attributeGroupId','page_name','formTitle','otherLanguages'));
    }

    public function addAttribute(Request $request)
    {
        if (isset($request->attributeId))
        {
            $attr_details = new AttributeDetails;
            $attr_details->name = $request->name;
            $attr_details->display_name = $request->display_name;
            $attr_details->language_id = $request->defaultLanguage;
            $attr_details->attribute_id = $request->attributeId;
            $attr_details->attribute_group_id = $request->attributeGroupId;
            $attr_details->save();
        }
        else
        {
            $attr = new Attribute;
            $attr->sort_order = $request->sort_order;
            $attr->status = $request->status;

            $request['is_filterable'] = isset($request->is_filterable) ? 1 : 0;
            $attr->is_filterable = $request['is_filterable'];
            $attr->color = $request->color;

            $reqdImgWidth =Config::get('app.attribute_image.width');
            $reqdImgHeight =Config::get('app.attribute_image.height');
            if($request->hasFile('image'))
            {
                if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
                {
                    $image       = $request->file('image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                    $image->move(public_path('assets/images/attributes/'), $filename);
                    $image_resize->save(public_path('assets/images/attributes/' .$filename));
                    $attr->image = $filename;
                }
                else
                {
                    $image = $request->file('image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/attributes/'), $filename);
                    $attr->image = $request->file('image')->getClientOriginalName();
                }
            }
            if($attr->save())
            {
                $attr_details = new AttributeDetails;
                $attr_details->name = $request->name;
                $attr_details->display_name = $request->display_name;
                $attr_details->language_id = $request->defaultLanguageId;
                $attr_details->attribute_id = $attr->id;
                $attr_details->attribute_group_id = $request->attribute_group_id;
                $attr_details->save();
            }
        }

        $notification = array(
            'message' => 'Attribute added successfully!',
            'alert-type' => 'success'
        );

        return redirect('admin/attribute')->with($notification);
    }

    public function attributeEditView($id)
    {
        $page = isset($_GET['page']) ? $_GET['page'] : "getDefaultLangData";
        $lang_id = isset($_GET['lang']) ? $_GET['lang'] : "defaultLang";

        $nonDefaultLanguage = AttributeDetails::select('attribute_details.language_id','langEN')
                                            ->join('global_language as gl','gl.id','=','attribute_details.language_id')
                                            ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                                            ->whereNull('attribute_details.deleted_at')
                                            ->where('attribute_id',$id)
                                            ->where('status',1)
                                            ->get()->toArray();

        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;
        $attribute = [];
        if($page != 'otherLang')
        {
            $attribute = Attribute::select('attribute.id as id', 'ad.name as name', 'attribute.status as status', 'is_filterable','color','image',
                                    'sort_order','ad.display_name as display_name', 'ad.language_id as languageId','ad.attribute_group_id')
                                    ->join('attribute_details as ad', 'ad.attribute_id', '=', 'attribute.id')
                                    ->findOrFail($id);

            if(!empty($attribute))
            {
                $attribute = $attribute->toArray();
            }
        }
        else
        {
            $attribute = Attribute::select('attribute.id as id', 'ad.name as name', 'attribute.status as status', 'is_filterable','color','image',
                                    'sort_order','ad.display_name as display_name', 'ad.language_id as languageId','ad.attribute_group_id')
                                    ->join('attribute_details as ad', 'ad.attribute_id', '=', 'attribute.id')
                                    ->where('language_id',$lang_id)
                                    ->where('attribute_id',$id)
                                    ->whereNull('ad.deleted_at')
                                    ->first($id);
            if(!empty($attribute))
            {
                $attribute = $attribute->toArray();
                return response()->json(['status' => true, 'attribute' => $attribute]);
            }

        }

        $attribute_groups = AttributeGroupDetails::getAttributeGroups($defaultLanguageId);

        $attr_type = AttributeGroup::select('at.code')
                                    ->join('attribute_types as at','at.id','=','attribute_type_id')
                                    ->where('attribute_groups.id',$attribute['attribute_group_id'])
                                    ->whereNull('deleted_at')
                                    ->first();

        $page_name = 'edit';
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();

        $baseUrl = $this->getBaseUrl();
        return view('admin.attributes.edit',compact('attribute','attribute_groups','nonDefaultLanguage','defaultLanguageId','page_name','otherLanguages','attr_type','baseUrl'));
    }

    public function updateAttribute(Request $request)
    {
        $attribute = Attribute::findOrFail($request->attribute_id);

        if (isset($attribute))
        {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $defaultLanguage = $defaultLanguageData['language']['langEN'];
            $defaultLanguageId = $defaultLanguageData['id'];

            $attribute->sort_order = $request->sort_order;
            $request['is_filterable'] = isset($request->is_filterable) ? 1 : 0;
            $attribute->is_filterable = $request['is_filterable'];
            $attribute->status = $request->status;
            $attribute->color = $request->color;
            $reqdImgWidth =Config::get('app.attribute_image.width');
            $reqdImgHeight =Config::get('app.attribute_image.height');
            if($request->hasFile('image'))
            {
                if($request->loaded_image_width != $reqdImgWidth || $request->loaded_image_height != $reqdImgHeight)
                {
                    $image       = $request->file('image');
                    $filename    = $image->getClientOriginalName();

                    $image_resize = Image::make($image->getRealPath());
                    $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                    $image->move(public_path('assets/images/attributes/'), $filename);
                    $image_resize->save(public_path('assets/images/attributes/' .$filename));
                    $attribute->image = $filename;
                }
                else
                {
                    $image = $request->file('image');
                    $filename = $image->getClientOriginalName();
                    $image->move(public_path('assets/images/attributes/'), $filename);
                    $attribute->image = $request->file('image')->getClientOriginalName();
                }
            }
            $attribute->save();

            $attributeDetails = AttributeDetails::where(['attribute_id'=>$request->attribute_id, 'language_id'=>$request->language_id])->first();
            if (!empty($attributeDetails))
            {
                $attributeDetails->attribute_id = $request->attribute_id;
                $attributeDetails->language_id = $request->language_id;
                $attributeDetails->attribute_group_id = $request->attribute_group_id;
                $attributeDetails->name = $request->name;
                $attributeDetails->display_name = $request->display_name;

                if($attributeDetails->save())
                {
                    $notification = array(
                        'message' => 'Attribute updated successfully!',
                        'alert-type' => 'success'
                    );
                    return redirect('admin/attribute')->with($notification);
                }
            }
        }
    }

    public function getLanguageWiseAttr(Request $request)
    {
        $arrResult = [];

        $attrDetails = AttributeDetails::with('globalLanguage')
                                            ->where('attribute_id',$request['attrId'])
                                            ->whereNull('attribute_details.deleted_at')
                                            ->get();
        $index = 0;
        foreach ($attrDetails as $key)
        {
            $arrResult[$index]['id'] = $key->id;
            $arrResult[$index]['attrName'] = $key->name;
            $arrResult[$index]['displayName'] = $key->display_name;
            $arrResult[$index]['languageName'] = $key['globalLanguage']['language']->langEN;
            $arrResult[$index++]['isDefault'] = $key['globalLanguage']->is_default;
        }

        return DataTables::of($arrResult)->make();
    }

    public function deleteAttribute(Request $request)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);

        $attr = AttributeDetails::find($request->attrDetailId);
        if ($attr->language_id == $defaultLanguageData['id'])
        {
            $updateAttr = AttributeDetails::where('attribute_id', $attr->attribute_id)->get();
            foreach($updateAttr as $attribute)
            {
                $attribute->deleted_at = Carbon::now();
                $attribute->save();
            }
            $attrDelete = Attribute::find($attr->attribute_id);
            $attrDelete->deleted_at = now();
            if ($attrDelete->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Attribute Deleted Successfully!!')
                );
            }
        }
        else
        {
            $attr->deleted_at = now();
            if ($attr->save())
            {
                return array(
                    'success' => true,
                    'message' => trans('Attribute Deleted Successfully!!')
                );
            }
        }
    }

    public function getAttributeType($attrGroupId)
    {
        $attr_type = AttributeGroup::select('at.code')
                                    ->join('attribute_types as at','at.id','=','attribute_type_id')
                                    ->where('attribute_groups.id',$attrGroupId)
                                    ->whereNull('deleted_at')
                                    ->first();
        if(!empty($attr_type))
        {
            return response()->json(['status' => true, 'attr_type' => $attr_type]);
        }
        else
        {
            return response()->json(['status' => false]);
        }
    }

    public function attributeActiveInactive(Request $request)
    {
        try
        {
            $attribute = Attribute::where('id',$request->attributeId)->first();

            if($request->is_active == 1)
            {
                $attribute->status = $request->is_active;
                $msg = "Attribute Activated Successfully!";
            }
            else
            {
                $attribute->status = $request->is_active;
                $msg = "Attribute Deactivated Successfully!";
            }
            $attribute->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        }
        catch(\Exception $ex)
        {
            return view('errors.500');
        }
    }

}
