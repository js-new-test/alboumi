<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AttributeGroup;
use App\Models\AttributeGroupDetails;
use App\Models\AttributeDetails;
use App\Models\Attribute;
use App\Models\GlobalLanguage;
use App\Models\AttributeTypes;
use App\Models\Category;
use App\Models\CategoryDetails;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\ExportTrait;
use App\Traits\CommonTrait;

class AttributeGroupController extends Controller
{
    use ExportTrait,CommonTrait;

    public function getListOfAttributeGroups()
    {
        $languages = GlobalLanguage::getAllLanguages();
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $baseUrl = $this->getBaseUrl();
        return view('admin.attribute_groups.index',compact('otherLanguages','languages','baseUrl'));
    }

    public function getAttributeGroupData(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;
        
        DB::statement(DB::raw('set @rownum=0'));

        $attr_groups = AttributeGroup::select('attribute_groups.id','display_name','name','sort_order',DB::raw('@rownum  := @rownum  + 1 AS rownum'),DB::raw("date_format(attribute_groups.created_at,'%Y-%m-%d %h:%i:%s') as ag_created_at"),'status')
                            ->join('attribute_group_details as agd', 'agd.attr_group_id', '=', 'attribute_groups.id')
                            ->where('agd.language_id', $defaultLanguageId)
                            ->whereNull('agd.deleted_at')
                            ->orderBy('agd.updated_at','desc')
                            ->get();
        
        if($request['lang_id'] != null)
        {
            DB::statement(DB::raw('set @rownum=0'));

            $attr_groups = AttributeGroup::select('attribute_groups.id','display_name','name','sort_order',DB::raw('@rownum  := @rownum  + 1 AS rownum'),DB::raw("date_format(attribute_groups.created_at,'%Y-%m-%d %h:%i:%s') as ag_created_at"),'status')
                            ->join('attribute_group_details as agd', 'agd.attr_group_id', '=', 'attribute_groups.id')
                            ->where('language_id',$request['lang_id'])
                            ->whereNull('agd.deleted_at')
                            ->orderBy('agd.updated_at','desc')
                            ->get();
        }
        return Datatables::of($attr_groups)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);      
    }

    public function attributeGroupAddView()
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];
        $formTitle = "Add Attribute Group";

        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
 
        $page = isset($_GET['page']) ? $_GET['page'] : "addAttributeGroup";
        $groupId = isset($_GET['groupId']) ? $_GET['groupId'] : "";

        $attributeTypes = AttributeTypes::get();

        if (!empty($groupId)) 
        {
            $attrGroup = AttributeGroupDetails::where(['attr_group_id'=>$groupId, 'language_id'=>$defaultLanguageId])
                                                ->whereNull('deleted_at')
                                                ->first();
            $attrGroupName = $attrGroup->name;
        }

        $language = "";
        if ($page == "anotherLanguage") 
        {
            $existingLanguageIds = AttributeGroupDetails::where('attr_group_id', $groupId)
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

            $formTitle = "Add Attribute Group - Other Language ($attrGroupName)";                                   

        }
        return view('admin.attribute_groups.add',compact('defaultLanguage','defaultLanguageId','page','groupId','language','formTitle','otherLanguages','attributeTypes'));
    }

    public function addAttributeGroup(Request $request)
    {
        if (!empty($request->groupId))  // add in other lang
        {
            $attrGroupDetails = new AttributeGroupDetails;
            $attrGroupDetails->attr_group_id = $request->groupId;
            $attrGroupDetails->name = $request->name;
            $attrGroupDetails->display_name = $request->display_name;
            $attrGroupDetails->language_id = $request->defaultLanguage;
            $attrGroupDetails->save();
        }
        else        // add in default lang
        {
            $attr_group = new AttributeGroup;
            $attr_group->attribute_type_id = $request->attribute_type_id;
            $attr_group->sort_order = $request->sort_order;
            $attr_group->status = $request->status;
            if(!empty($request->category_ids))
                $attr_group->category_ids = implode(',', $request->category_ids);
            if($attr_group->save())
            {
                $attr_group_details = new AttributeGroupDetails;
                $attr_group_details->name = $request->name;
                $attr_group_details->display_name = $request->display_name;
                $attr_group_details->language_id = $request->defaultLanguageId;
                $attr_group_details->attr_group_id = $attr_group->id;
                $attr_group_details->save();
            }
        }
        $notification = array(
            'message' => 'Attribute group added successfully!', 
            'alert-type' => 'success'
        );

        return redirect('admin/attributeGroup')->with($notification);
    }

    public function attributeGroupEditView($id)
    {
        $page = isset($_GET['page']) ? $_GET['page'] : "getDefaultLangData";
        $lang_id = isset($_GET['lang']) ? $_GET['lang'] : "defaultLang";

        $nonDefaultLanguage = AttributeGroupDetails::select('attribute_group_details.language_id','langEN')
                                            ->join('global_language as gl','gl.id','=','attribute_group_details.language_id')
                                            ->leftjoin('world_languages as wl','wl.id','=','gl.language_id')
                                            ->whereNull('attribute_group_details.deleted_at')
                                            ->where('attr_group_id',$id)
                                            ->where('status', 1)
                                            ->get()->toArray();
                                            
        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;

        $attr_group = [];
        if($page != 'otherLang')
        {
            $attr_group = AttributeGroup::select('attribute_groups.id as id', 'agd.name as name', 'attribute_groups.status as status', 
                                    'category_ids','sort_order','agd.display_name as display_name', 'agd.language_id as languageId','attribute_type_id')
                                     ->join('attribute_group_details as agd', 'agd.attr_group_id', '=', 'attribute_groups.id')
                                     ->findOrFail($id);
        
            if(!empty($attr_group))
            {
                $attr_group = $attr_group->toArray();
            }
        }
        else
        {
            $attr_group = AttributeGroup::select('attribute_groups.id as id', 'agd.name as name', 'attribute_groups.status as status', 
                                    'category_ids','sort_order','agd.display_name as display_name', 'agd.language_id as languageId','attribute_type_id')
                                     ->join('attribute_group_details as agd', 'agd.attr_group_id', '=', 'attribute_groups.id')
                                     ->where('language_id',$lang_id)
                                     ->where('attr_group_id',$id)
                                     ->whereNull('agd.deleted_at')
                                     ->first($id);

            if(!empty($attr_group))
            {
                $attr_group = $attr_group->toArray();
                return response()->json(['status' => true, 'attr_group' => $attr_group]);
            }
        }
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $attributeTypes = AttributeTypes::get();
        $allCategories = $this->getCategoriesForAttribute($defaultLanguageId);
        return view('admin.attribute_groups.edit',compact('attr_group','nonDefaultLanguage','defaultLanguageId','otherLanguages','attributeTypes','allCategories'));
    }

    public function updateAttributeGroup(Request $request)
    {
        $attr_group = AttributeGroup::findOrFail($request->id);
        if(!empty($attr_group))
        {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $defaultLanguage = $defaultLanguageData['language']['langEN'];
            $defaultLanguageId = $defaultLanguageData['id'];

            $attr_group->attribute_type_id = $request->attribute_type_id;
            $attr_group->sort_order = $request->sort_order;
            $attr_group->status = $request->status;
            if(!empty($request->category_ids))
                $attr_group->category_ids = implode(',', $request->category_ids);
            $attr_group->save();

            $attributeGroupDetails = AttributeGroupDetails::where(['attr_group_id'=>$request->id, 'language_id'=>$request->language_id])->first();
            
            if (!empty($attributeGroupDetails)) 
            {
                $attributeGroupDetails->attr_group_id = $request->id;
                $attributeGroupDetails->language_id = $request->language_id;
                $attributeGroupDetails->name = $request->name;
                $attributeGroupDetails->display_name = $request->display_name;
                $attributeGroupDetails->save();
                
            }
            $notification = array(
                'message' => 'Attribute group updated successfully!', 
                'alert-type' => 'success'
            );
    
            return redirect('admin/attributeGroup')->with($notification);
        }
    }

    public function getLanguageWiseAttrGroup(Request $request)
    {
        $arrResult = [];

        $attrGroupDetails = AttributeGroupDetails::with('attributes')->with('globalLanguage')
                                            ->where('attr_group_id',$request['attrGroupId'])
                                            ->whereNull('attribute_group_details.deleted_at')
                                            ->get();
        $index = 0;
        foreach ($attrGroupDetails as $key) 
        {
            $arrResult[$index]['id'] = $key->id;
            $arrResult[$index]['attrGroupName'] = $key->name;
            $arrResult[$index]['displayName'] = $key->display_name;
            $arrResult[$index]['languageName'] = $key['globalLanguage']['language']->langEN;
            $arrResult[$index++]['isDefault'] = $key['globalLanguage']->is_default;
        }

        return DataTables::of($arrResult)->make();
    }

    public function deleteAttributeGroup(Request $request)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        
        $attrGroup = AttributeGroupDetails::find($request->attrGroupDetailId);
        if ($attrGroup->language_id == $defaultLanguageData['id']) 
        {
            $updateAttrGroup = AttributeGroupDetails::where('attr_group_id', $attrGroup->attr_group_id)->get();
            foreach($updateAttrGroup as $attribute)
            {
                $attribute->deleted_at = Carbon::now();
                $attribute->save();
            }
            $updateAttributes = AttributeDetails::where('attribute_group_id', $attrGroup->attr_group_id)->get();
            foreach($updateAttributes as $attribute)
            {
                $attribute->deleted_at = Carbon::now();
                $attribute->save();
            }

            $attributeId = AttributeDetails::where('attribute_group_id', $attrGroup->attr_group_id)->first();
            if(!empty($attributeId)) 
            {
                $attrDelete = Attribute::find($attributeId->attribute_id);
                $attrDelete->status = 0;
                $attrDelete->save();
            }
        
            $attrGroupDelete = AttributeGroup::find($attrGroup->attr_group_id);
            $attrGroupDelete->deleted_at = now();
            if ($attrGroupDelete->save()) 
            {
                return array(
                    'success' => true,
                    'message' => trans('Attribute Group Deleted Successfully!!')
                );
            }
        }
        else
        {
            $attrGroup->deleted_at = now();
            if ($attrGroup->save()) 
            {
                return array(
                    'success' => true,
                    'message' => trans('Attribute Group Deleted Successfully!!')
                );
            }
        }
    }

    public function getCategoriesForAttribute($languageId)
    {
        $category = new Category;
        return $category->categoryDropDown($languageId);
    }

    public function attributeGroupActiveInactive(Request $request)
    {
        try
        {
            $attrGroup = AttributeGroup::where('id',$request->attrGroupId)->first();

            if($request->is_active == 1)
            {
                $attrGroup->status = $request->is_active;
                $msg = "Attribute Group Activated Successfully!";
            }
            else
            {
                $attrGroup->status = $request->is_active;
                $msg = "Attribute Group Deactivated Successfully!";
            }
            $attrGroup->save();
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
