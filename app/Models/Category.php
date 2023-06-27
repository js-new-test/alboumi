<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Traits\CommonTrait;
use App\Models\CustGroupPrice;
use Config;

class Category extends Model
{
	use CommonTrait;
    protected $table = 'categories';

    protected $fillable = ['parent_id','category_image','category_slug','lady_operator','photo_upload',
                            'qty_matrix','qty_range','status','sort_order','deleted_at'];


    public function categoryDropDown($languageId)
    {
        $resultArr = [];
    	$languageId = $languageId ?? "";
        if (empty($languageId) || $languageId == 'null') {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $languageId = $defaultLanguageData['id'];
        }

        $categories = Category::select('categories.id', 'categories.parent_id', 'categories.category_path')
        ->where('flag_category', 0)->whereOr('flag_product',1)->whereNull('categories.deleted_at')->get();

        $index = 0;
        foreach ($categories as $category) {
            $ids = explode (",", $category->category_path);

            $temp = "";

            $categoryDetails = CategoryDetails::where('language_id',$languageId)->whereIn('category_id',$ids)->orderBy('id', 'DESC')->get();
            for ($i=0; $i < count($categoryDetails); $i++)
            {
                if ($i == 0)
                {
            		$temp = $categoryDetails[$i]->title;
                }
                else
                {
                    if ($i == 1)
                    {
            				$temp = $temp ." - ". $categoryDetails[$i]->title;
                    }
                    else
                    {
            			$temp = $temp ." > ". $categoryDetails[$i]->title;
            		}
            	}
            }
            $resultArr[$index]['id'] = $category->id;
            $resultArr[$index++]['category'] = $temp;
        }
        return $resultArr;
    }

    public static function getTopCategories($cmsId,$langId)
    {
        $topCategory = Category::select('categories.id','categories.slug','cd.title','flag_product','flag_category','category_image as image')
                                        ->join('category_details as cd','cd.category_id','=','categories.id')
                                        ->where('language_id',$langId)
                                        ->where('categories.id',$cmsId)
                                        ->whereNull('categories.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('categories.status',1)
                                        ->get();

        if(count($topCategory) == 0 )
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];

            $topCategory = Category::select('categories.id','categories.slug','cd.title','flag_product','flag_category','category_image as image')
                                        ->join('category_details as cd','cd.category_id','=','categories.id')
                                        ->where('language_id',$defaultLanguageId)
                                        ->where('categories.id',$cmsId)
                                        ->whereNull('categories.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('categories.status',1)
                                        ->get();
        }
        return $topCategory;
    }

    public static function getChildCategories($catid,$langId)
    {
        // dd($langId);
        $subCategory = Category::select('categories.id','categories.slug','cd.title','flag_product','flag_category','category_image')
                                        ->join('category_details as cd','cd.category_id','=','categories.id')
                                        ->where('categories.parent_id',$catid)
                                        ->whereNull('categories.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('categories.status',1)
                                        ->get();
        // dd($subCategory);
        $allSubCatIds = array_column($subCategory->toArray(),'id');
        $subCatArr = $resultArr = [];
        $defaultLangSubCategory = [];
        if(count($subCategory) != 0)
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];

            $subCats = Category::select('categories.id','categories.slug','cd.title','flag_product','flag_category','category_image')
                                        ->join('category_details as cd','cd.category_id','=','categories.id')
                                        ->where('language_id',$langId)
                                        ->where('categories.parent_id',$catid)
                                        ->whereNull('categories.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('categories.status',1)
                                        ->get()->toArray();
            $subCatArr[] = $subCats;

            $selectedLangSubCatIds = array_column($subCats,'id');

            $notInSelectLangSubCatIds = array_diff($allSubCatIds,$selectedLangSubCatIds);


            if(!empty($notInSelectLangSubCatIds))
            {
                foreach($notInSelectLangSubCatIds as $defaultCatId)
                {
                    $defaultLangSubCategory[] = Category::select('categories.id','categories.slug','cd.title','flag_product','flag_category','category_image')
                                            ->join('category_details as cd','cd.category_id','=','categories.id')
                                            ->where('language_id',$defaultLanguageId)
                                            ->where('cd.category_id',$defaultCatId)
                                            ->whereNull('categories.deleted_at')
                                            ->whereNull('cd.deleted_at')
                                            ->whereNull('categories.deleted_at')
                                            ->where('categories.status',1)
                                            ->get()->toArray();
                }
            }
        }

        $allSubCategories = array_merge($subCatArr,$defaultLangSubCategory);
        if(!empty($allSubCategories))
        {
            foreach($allSubCategories as $subCategory)
            {
                // dd($subCategory);
                foreach($subCategory as $childCat)
                {
                    if(!empty($childCat))
                        $resultArr[] = $childCat;
                }
            }
        }
        // dd($resultArr);
        return $resultArr;
    }

    public function createCategoryData($arrTopCategories,$arrSubCategories,$baseUrl,$cmsPages,$langId)
    {
        $codes = ['WILLMAKEYOURBOOK','MORE'];
        $photoBookLabel = getCodesMsg($langId, $codes);

        $categoryTabData = $level2 = [];
        $j = 0;

        $categoryTabData['title'] = $arrTopCategories[0]['title'];
        $categoryTabData['categoryId'] = "".$arrTopCategories[0]['id']."";
        $categoryTabData['backgroundImage'] = $baseUrl.'/public/assets/images/categories/'.$arrTopCategories[0]['image'];

        $categoryTabData['type'] = "1";
        $subtitle = [];
        
        if(!empty($arrSubCategories) || $arrSubCategories != "")
        {
            // echo "<pre>";

            // print_r($arrTopCategories[0]['id']);
            // echo "<pre>";
            // if($arrTopCategories[0]['id'] == Config::get('app.photoBookCatId'))
            // {

            // }
            // else
            // {
                
                foreach($arrSubCategories as $ek => $subCat)
                {
                    $subtitle[] = $subCat['title'];
                    $level2[$j]['id'] = "".$subCat['id']."";
                    $level2[$j]['title'] = $subCat['title'];
                    $level2[$j]['query'] = $baseUrl."/api/v1/getProductList?language_id=".$langId.'&category_id='.$subCat['id'];
                    $level2[$j]['type'] = "1";
                    $level2[$j++]['navigationFlag'] = "1";
                }
                if($arrTopCategories[0]['id'] == Config::get('app.photoBookCatId'))
                {
                    // echo "in if";
                    $level2[$j]['id'] = "";
                    $level2[$j]['title'] = $photoBookLabel['MORE'];
                    $level2[$j]['query'] = $baseUrl."/api/v1/getPhotoBookList?language_id=".$langId;
                    $level2[$j]['type'] = "5";
                    $level2[$j++]['navigationFlag'] = "1";
                }
            // }    
            
            
        }
        if(!empty($level2))
        {
            // if($arrTopCategories[0]['id'] == Config::get('app.photoBookCatId'))
            // {
            //     $catURL =  $baseUrl.'/api/v1/getPhotoBookList?language_id='.$langId;
            //     $level2[$j]['title'] = $photoBookLabel['WILLMAKEYOURBOOK'];
            //     $level2[$j]['query'] = $catURL;
            //     $level2[$j]['type'] = "5";
            //     $level2[$j++]['navigationFlag'] = "1";
            // }  
            // else
            // {
                $categoryTabData['query'] = "";
                $categoryTabData['navigationFlag'] = "0";
                $categoryTabData['subTitle'] = implode(',',$subtitle);
            // }
       
        }
        else
        {
            if($arrTopCategories[0]['id'] == Config::get('app.photoBookCatId'))
            {
                $categoryTabData['type'] = "5";
                $categoryTabData['query'] = $baseUrl.'/api/v1/getPhotoBookList?language_id='.$langId;
                $categoryTabData['navigationFlag'] = "1";
            }  
            else
            {
                $categoryTabData['query'] = $baseUrl."/api/v1/getProductList?language_id=".$langId.'&category_id='.$arrTopCategories[0]['id'];
                $categoryTabData['navigationFlag'] = "1";
            }
        }   
        $categoryTabData['level2'] = $level2;
        return $categoryTabData;
    }

    // filter - frontend
    public function getFilterOptions($productsList,$langId,$defaultLanguageData,$custId)
    {
        // dd($custId);
        $attributeId = $categoryId = $prices = $attributeIds = $brands = [];
        foreach($productsList as $products)
        {
            // print_r($products);
            foreach($products as $product)
            {
                $categoryId[] = Product::select('category_id')
                                        ->leftJoin('categories as c','c.id','=','products.category_id')
                                        ->where('products.id', $product['id'])
                                        ->whereNull('products.deleted_at')
                                        ->where('c.status',1)
                                        ->first();

                $attributeId[] = ProductPricing::select('attribute_ids')
                                                ->where('product_id', $product['id'])
                                                ->whereNull('deleted_at')
                                                ->get()->toArray();

                $prices[] = ProductPricing::select('selling_price','offer_price','offer_start_date','offer_end_date','product_id')
                                                ->where('product_id', $product['id'])
                                                ->where('is_default',1)
                                                ->whereNull('deleted_at')
                                                ->get()->toArray();

                $brands[] = Product::select('manufacturer_id')
                                    ->leftJoin('manufacturers as m','m.id','=','products.manufacturer_id')
                                    ->where('products.id', $product['id'])
                                    ->whereNull('products.deleted_at')
                                    ->whereNull('m.deleted_at')
                                    ->where('m.status','Active')
                                    ->first();
            }
        }
        // dd($categoryId);
        foreach($attributeId as $attrId)
        {
            foreach($attrId as $id)
            {
                $attributeIds[] = implode(',',$id);
            }
        }

        $categoryIds = array_unique($categoryId);
        $brandIds = array_unique($brands);

        $attrIdList = array_unique(explode(',',implode(',',$attributeIds)));
        // dd($attrIdList);
        $resultFilterArr = $attriDetails = $attrGroupDetail = $catNames = $brandNames = [];
        $i = $j = 0;

        foreach($categoryIds as $categoryId)
        {
            if(!empty($categoryId))
            {
                $categoryDetail = CategoryDetails::select('title','category_details.id','category_id')
                                ->leftJoin('categories as c','c.id','=','category_details.category_id')
                                ->where('category_id', $categoryId->category_id)
                                ->where('language_id',$langId)
                                ->whereNull('category_details.deleted_at')
                                ->whereNull('c.deleted_at')
                                ->where('c.status',1)
                                ->first();

                if($categoryDetail == null)
                {
                    $categoryDetail = CategoryDetails::select('title','category_details.id','category_id')
                                ->leftJoin('categories as c','c.id','=','category_details.category_id')
                                ->where('category_id', $categoryId->category_id)
                                ->where('language_id',$defaultLanguageData['id'])
                                ->whereNull('category_details.deleted_at')
                                ->whereNull('c.deleted_at')
                                ->where('c.status',1)
                                ->first();
                }

                if(!empty($categoryDetail))
                {
                    $catNames[$i]['id'] = "".$categoryDetail->category_id."";
                    $catNames[$i]['title'] = $categoryDetail->title;
                    $catNames[$i++]['type'] = "1";
                }
            }
        }
        $resultFilterArr['category'] = $catNames;
        
        foreach($attrIdList as $attributeId)
        {
            $attributeGroupDetail = Attribute::select('agd.id','attribute_group_id','agd.display_name','at.name as typeName')
                                            ->leftJoin('attribute_details as ad','ad.attribute_id','=','attribute.id')
                                            ->leftJoin('attribute_group_details as agd','agd.attr_group_id','=','ad.attribute_group_id')
                                            ->leftJoin('attribute_groups as ag','ag.id','=','agd.attr_group_id')
                                            ->leftJoin('attribute_types as at','at.id','=','ag.attribute_type_id')
                                            ->where('ad.attribute_id', $attributeId)
                                            ->where('attribute.status',1)
                                            ->where('ag.status',1)
                                            ->whereNull('attribute.deleted_at')
                                            ->whereNull('ad.deleted_at')
                                            ->whereNull('ag.deleted_at')
                                            ->whereNull('agd.deleted_at')
                                            ->first();
            if(!empty($attributeGroupDetail))
            {
                $attributeDetail = AttributeDetails::select('attribute_details.id','display_name','attribute_group_id','a.color','attribute_id')
                                            ->leftJoin('attribute as a','a.id','=','attribute_details.attribute_id')
                                            ->where('attribute_id', $attributeId)
                                            ->where('attribute_group_id', $attributeGroupDetail['id'])
                                            ->whereNull('attribute_details.deleted_at')
                                            ->whereNull('a.deleted_at')
                                            ->where('a.status',1)
                                            ->where('language_id',$langId)
                                            ->first();

                if($attributeDetail == null)
                {
                    $attributeDetail = AttributeDetails::select('attribute_details.id','display_name','attribute_group_id','a.color','attribute_id')
                                                        ->leftJoin('attribute as a','a.id','=','attribute_details.attribute_id')
                                                        ->where('attribute_id', $attributeId)
                                                        ->where('attribute_group_id', $attributeGroupDetail['id'])
                                                        ->whereNull('attribute_details.deleted_at')
                                                        ->where('a.status',1)
                                                        ->whereNull('a.deleted_at')
                                                        ->where('language_id',$defaultLanguageData['id'])
                                                        ->first();
                }
                
                if(!empty($attributeDetail))
                {
                    $attriDetails['id'] = "".$attributeDetail->attribute_id."";
                    $attriDetails['title'] = $attributeDetail->display_name;
                    $attriDetails['type'] = "1";
                    $attriDetails['color'] = isset($attributeDetail['color']) ? $attributeDetail['color'] : "";

                    $attrGroupName = AttributeGroupDetails::select('display_name')
                                                            ->where('language_id',$langId)
                                                            ->where('attr_group_id',$attributeGroupDetail['id'])
                                                            ->first();

                    $attrGroupDetail[$j]['id'] = "".$attributeGroupDetail['id']."";
                    $attrGroupDetail[$j]['name'] = $attrGroupName->display_name;
                    $attrGroupDetail[$j]['groupType'] = $attributeGroupDetail->typeName;
                    $attrGroupDetail[$j++]['attributes'] = $attriDetails;
                }
            }
        }
        
        $attrGroupName = array_unique(array_column($attrGroupDetail,'name'));
        $arrAttributeGroup = $priceArr = $p = $list = [];
        foreach($attrGroupName as $attrGroup)
        {
            $k = 0;
            $arrAttributeGroup[$attrGroup] = [];
            foreach($attrGroupDetail as $groupDetail)
            {
                if($groupDetail['name'] == $attrGroup)
                {
                    $arrAttributeGroup[$attrGroup]['filterId'] = "".$groupDetail['id']."";
                    $arrAttributeGroup[$attrGroup]['filterTypeName'] = $groupDetail['name'];
                    $arrAttributeGroup[$attrGroup]['groupType'] = $groupDetail['groupType'];
                    $arrAttributeGroup[$attrGroup]['filtertype'] = 'MS';
                    $arrAttributeGroup[$attrGroup]['data'][$k++] = $groupDetail['attributes'];
                }
            }
            $list[] = $arrAttributeGroup[$attrGroup];
        }

        $resultFilterArr['attributeGroups'] = $list;
        // dd($custId);
        if(!empty($prices))
        {
            foreach($prices as $price)
            {
                foreach($price as $prodPrice)
                {
                    if(!empty($prodPrice))
                    {
                        if(!empty($custId) && $custId->cust_group_id != 0)
                        {
                            $groupPrice = CustGroupPrice::where('product_id',$prodPrice['product_id'])->where('customer_group_id', $custId->cust_group_id)->first();
                        }
                        if(!empty($prodPrice['offer_price']) && (date("Y-m-d",strtotime($prodPrice['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($prodPrice['offer_end_date']))))
                        {
                            if(!empty($groupPrice['price']))
                            {
                                $priceArr[]['price'] = $groupPrice['price'];
                            }
                            else
                                $priceArr[]['price'] = $prodPrice['offer_price'];
                        }
                        else if(!empty($groupPrice['price']))
                        {
                            $priceArr[]['price'] = $groupPrice['price'];
                        }
                        else
                            $priceArr[]['price'] = $prodPrice['selling_price'];
                    }
                }
            }
        }

        foreach($priceArr as $pArray)
        {
            $p[] = implode(',', $pArray);
        }
    
        $resultFilterArr['price'] = $p;

        foreach($brandIds as $brand)
        {
            if(!empty($brand))
            {
                $brandDetails = BrandDetails::select('name','brand_details.id','brand_id')
                                ->leftJoin('manufacturers as m','m.id','=','brand_details.brand_id')
                                ->where('brand_id', $brand['manufacturer_id'])
                                ->where('language_id',$langId)
                                ->whereNull('brand_details.deleted_at')
                                ->whereNull('m.deleted_at')
                                ->where('m.status','Active')
                                ->first();

                if($brandDetails == null)
                {
                    $brandDetails = BrandDetails::select('name','brand_details.id','brand_id')
                                ->leftJoin('manufacturers as m','m.id','=','brand_details.brand_id')
                                ->where('brand_id', $brand->manufacturer_id)
                                ->where('language_id',$defaultLanguageData['id'])
                                ->whereNull('brand_details.deleted_at')
                                ->whereNull('m.deleted_at')
                                ->first();
                }

                if(!empty($brandDetails))
                {
                    $brandNames[$i]['id'] = "".$brandDetails->brand_id."";
                    $brandNames[$i]['name'] = $brandDetails->name;
                    $brandNames[$i++]['type'] = "1";
                }
            }
        }
        $resultFilterArr['brands'] = $brandNames;

        return $resultFilterArr;
    }

    public function getFilterOptionsForFilteredProducts($productsList,$langId,$defaultLanguageData,$selectedCategoryIds,$selectedBrandIds,$selectedAttributeIds,$custId)
    {
        // dd($productsList);
        $attributeId = $categoryId = $prices = $attributeIds = $brands = [];

        foreach($productsList as $products)
        {
            foreach($products as $product)
            {
                // print_r($product);
                $categoryId[] = Product::select('category_id')
                                        ->leftJoin('categories as c','c.id','=','products.category_id')
                                        ->where('products.id', $product['id'])
                                        ->whereNull('products.deleted_at')
                                        ->where('c.status',1)
                                        ->first();
                // print_r($categoryId);
                $attributeId[] = ProductPricing::select('attribute_ids')
                                                ->where('product_id', $product['id'])
                                                ->whereNull('deleted_at')
                                                ->get()->toArray();

                $prices[] = ProductPricing::select('selling_price','offer_price','offer_start_date','offer_end_date','product_id')
                                            ->where('product_id', $product['id'])
                                            ->where('is_default',1)
                                            ->whereNull('deleted_at')
                                            ->get()->toArray();

                $brands[] = Product::select('manufacturer_id')
                                    ->leftJoin('manufacturers as m','m.id','=','products.manufacturer_id')
                                    ->where('products.id', $product['id'])
                                    ->whereNull('products.deleted_at')
                                    ->whereNull('m.deleted_at')
                                    ->where('m.status','Active')
                                    ->first();
            
            }
        }
        // die;
        foreach($attributeId as $attrId)
        {
            foreach($attrId as $id)
            {
                $attributeIds[] = implode(',',$id);
            }
        }
        // dd($categoryId);
        $categoryIds = array_unique($categoryId);
        // dd($categoryIds);
        $brandIds = array_unique($brands);

        $attrIdList = array_unique(explode(',',implode(',',$attributeIds)));
       
        $resultFilterArr = $attriDetails = $attrGroupDetail = $catNames = $brandNames = [];
        $i = $j = 0;
        // dd($categoryIds);
        foreach($categoryIds as $categoryId)
        {
            if(!empty($categoryId))
            {
                $categoryDetail = CategoryDetails::select('title','category_details.id','category_id')
                                                    ->leftJoin('categories as c','c.id','=','category_details.category_id')
                                                    ->where('category_id', $categoryId->category_id)
                                                    ->where('language_id',$langId)
                                                    ->whereNull('category_details.deleted_at')
                                                    ->whereNull('c.deleted_at')
                                                    ->where('c.status',1)
                                                    ->first();

                if($categoryDetail == null)
                {
                    $categoryDetail =  CategoryDetails::select('title','category_details.id','category_id')
                                                        ->leftJoin('categories as c','c.id','=','category_details.category_id')
                                                        ->where('category_id', $categoryId->category_id)
                                                        ->where('language_id',$defaultLanguageData['id'])
                                                        ->whereNull('category_details.deleted_at')
                                                        ->whereNull('c.deleted_at')
                                                        ->where('c.status',1)
                                                        ->first();
                }

                if(!empty($categoryDetail))
                {
                    if(!empty($selectedCategoryIds))
                    {
                        if(in_array($categoryDetail->category_id,$selectedCategoryIds))
                        {
                            $catNames[$i]['flagSelected'] = "1";
                            $catNames[$i]['id'] = "".$categoryDetail->category_id."";
                            $catNames[$i]['title'] = $categoryDetail->title;
                            $catNames[$i]['color'] = "";
                            $catNames[$i++]['type'] = "1";
                        }
                        else
                        {
                            $catNames[$i]['flagSelected'] = "0";
                            $catNames[$i]['id'] = "".$categoryDetail->category_id."";
                            $catNames[$i]['title'] = $categoryDetail->title;
                            $catNames[$i]['color'] = "";
                            $catNames[$i++]['type'] = "1";
                        }
                    }
                    else
                    {
                        $catNames[$i]['flagSelected'] = "0";
                        $catNames[$i]['id'] = "".$categoryDetail->category_id."";
                        $catNames[$i]['title'] = $categoryDetail->title;
                        $catNames[$i]['color'] = "";
                        $catNames[$i++]['type'] = "1";
                    }
                }
            }
        }
        // dd($catNames);
        $resultFilterArr['category'] = $catNames;

        foreach($attrIdList as $attributeId)
        {
            $attributeGroupDetail = Attribute::select('agd.id','attribute_group_id','agd.display_name','at.name as typeName','ag.sort_order')
                                            ->leftJoin('attribute_details as ad','ad.attribute_id','=','attribute.id')
                                            ->leftJoin('attribute_group_details as agd','agd.attr_group_id','=','ad.attribute_group_id')
                                            ->leftJoin('attribute_groups as ag',function ($join) {
                                                $join->on('ag.id','=','agd.attr_group_id');
                                                $join->whereNull('ag.deleted_at');
                                                $join->where('ag.status','=',1);
                                            })
                                            ->leftJoin('attribute_types as at','at.id','=','ag.attribute_type_id')
                                            ->where('ad.attribute_id', $attributeId)
                                            ->whereNull('attribute.deleted_at')
                                            ->whereNull('ad.deleted_at')
                                            ->whereNull('agd.deleted_at')
                                            ->whereNull('ag.deleted_at')
                                            ->where('attribute.status','=',1)
                                            ->where('ag.status','=',1)
                                            ->first();
                                            
            if(!empty($attributeGroupDetail))
            {
                $attributeDetail = AttributeDetails::select('attribute_details.id','display_name','attribute_group_id','a.color','attribute_id')
                                            ->leftJoin('attribute as a','a.id','=','attribute_details.attribute_id')
                                            ->where('attribute_id', $attributeId)
                                            ->where('attribute_group_id', $attributeGroupDetail['id'])
                                            ->whereNull('attribute_details.deleted_at')
                                            ->whereNull('a.deleted_at')
                                            ->where('a.status','=',1)
                                            ->where('language_id',$langId)
                                            ->first();
                if($attributeDetail == null)
                {
                    $attributeDetail = AttributeDetails::select('attribute_details.id','display_name','attribute_group_id','a.color','attribute_id')
                                                        ->leftJoin('attribute as a','a.id','=','attribute_details.attribute_id')
                                                        ->where('attribute_id', $attributeId)
                                                        ->where('attribute_group_id', $attributeGroupDetail['id'])
                                                        ->whereNull('attribute_details.deleted_at')
                                                        ->whereNull('a.deleted_at')
                                                        ->where('a.status','=',1)
                                                        ->where('language_id',$defaultLanguageData['id'])
                                                        ->first();
                }
                
                if(!empty($attributeDetail))
                {
                    if(array_key_exists($attributeGroupDetail['id'],$selectedAttributeIds))
                    {
                        if(in_array($attributeDetail->attribute_id,explode(',',$selectedAttributeIds[$attributeGroupDetail['id']])))
                        {
                            $attriDetails['flagSelected'] = "1";
                            $attriDetails['id'] = "".$attributeDetail->attribute_id."";
                            $attriDetails['title'] = $attributeDetail->display_name;
                            $attriDetails['color'] = isset($attributeDetail['color']) ? $attributeDetail['color'] : "";
                        }
                        else
                        {
                            $attriDetails['flagSelected'] = "0";
                            $attriDetails['id'] = "".$attributeDetail->attribute_id."";
                            $attriDetails['title'] = $attributeDetail->display_name;
                            $attriDetails['color'] = isset($attributeDetail['color']) ? $attributeDetail['color'] : "";
                        }
                    }
                    else
                    {
                        $attriDetails['flagSelected'] = "0";
                        $attriDetails['id'] = "".$attributeDetail->attribute_id."";
                        $attriDetails['title'] = $attributeDetail->display_name;
                        $attriDetails['color'] = isset($attributeDetail['color']) ? $attributeDetail['color'] : "";
                    }

                    $attrGroupDetail[$j]['id'] = "".$attributeGroupDetail['id']."";
                    $attrGroupDetail[$j]['name'] = $attributeGroupDetail->display_name;
                    $attrGroupDetail[$j]['groupType'] = $attributeGroupDetail->typeName;
                    $attrGroupDetail[$j++]['attributes'] = $attriDetails;
                }
            }
        }
        // dd($attributeDetail);
        // die;
        $attrGroupName = array_unique(array_column($attrGroupDetail,'name'));
        $arrAttributeGroup = $priceArr = $p = $list = [];
        foreach($attrGroupName as $attrGroup)
        {
            $k = 0;
            $arrAttributeGroup[$attrGroup] = [];
            foreach($attrGroupDetail as $groupDetail)
            {
                if($groupDetail['name'] == $attrGroup)
                {
                    $arrAttributeGroup[$attrGroup]['filterId'] = "".$groupDetail['id']."";
                    $arrAttributeGroup[$attrGroup]['filterTypeName'] = $groupDetail['name'];
                    $arrAttributeGroup[$attrGroup]['groupType'] = $groupDetail['groupType'];
                    $arrAttributeGroup[$attrGroup]['filtertype'] = 'MS';
                    $arrAttributeGroup[$attrGroup]['data'][$k++] = $groupDetail['attributes'];
                }
            }
            $list[] = $arrAttributeGroup[$attrGroup];
        }

        $resultFilterArr['attributeGroups'] = $list;

        if(!empty($prices))
        {
            foreach($prices as $price)
            {
                foreach($price as $prodPrice)
                {
                    if(!empty($prodPrice))
                    {
                        if(!empty($custId) && $custId->cust_group_id != 0)
                        {
                            $groupPrice = CustGroupPrice::where('product_id',$prodPrice['product_id'])->where('customer_group_id', $custId->cust_group_id)->first();
                        }
                        if(!empty($prodPrice['offer_price']) && (date("Y-m-d",strtotime($prodPrice['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($prodPrice['offer_end_date']))))
                        {
                            if(!empty($groupPrice['price']))
                            {
                                $priceArr[]['price'] = $groupPrice['price'];
                            }
                            else
                                $priceArr[]['price'] = $prodPrice['offer_price'];
                        }
                        else if(!empty($groupPrice['price']))
                        {
                            $priceArr[]['price'] = $groupPrice['price'];
                        }
                        else
                            $priceArr[]['price'] = $prodPrice['selling_price'];
                    }
                }
            }
        }

        foreach($priceArr as $pArray)
        {
            $p[] = implode(',', $pArray);
        }

        $resultFilterArr['price'] = $p;
        $m = 0;
        // dd($brandIds);
        foreach($brandIds as $brand)
        {
            if(!empty($brand))
            {
                $brandDetails = BrandDetails::select('name','brand_details.id','brand_id')
                                ->leftJoin('manufacturers as m','m.id','=','brand_details.brand_id')
                                ->where('brand_id', $brand['manufacturer_id'])
                                ->where('language_id',$langId)
                                ->whereNull('brand_details.deleted_at')
                                ->whereNull('m.deleted_at')
                                ->where('m.status','Active')
                                ->first();

                if($brandDetails == null)
                {
                    $brandDetails = BrandDetails::select('name','brand_details.id','brand_id')
                                ->leftJoin('manufacturers as m','m.id','=','brand_details.brand_id')
                                ->where('brand_id', $brand->manufacturer_id)
                                ->where('language_id',$defaultLanguageData['id'])
                                ->whereNull('brand_details.deleted_at')
                                ->whereNull('m.deleted_at')
                                ->first();
                }
                
                if(!empty($brandDetails))
                {
                    if(!empty($selectedBrandIds))
                    {
                        if(in_array($brandDetails->brand_id,$selectedBrandIds))
                        {
                            $brandNames[$m]['flagSelected'] = "1";
                            $brandNames[$m]['id'] = "".$brandDetails->brand_id."";
                            $brandNames[$m]['title'] = $brandDetails->name;
                            $brandNames[$m]['color'] = "";
                            $brandNames[$m++]['type'] = "1";
                        }
                        else
                        {
                            $brandNames[$m]['flagSelected'] = "0";
                            $brandNames[$m]['id'] = "".$brandDetails->brand_id."";
                            $brandNames[$m]['title'] = $brandDetails->name;
                            $brandNames[$m]['color'] = "";
                            $brandNames[$m++]['type'] = "1";
                        }
                    }
                    else
                    {
                        $brandNames[$m]['flagSelected'] = "0";
                        $brandNames[$m]['id'] = "".$brandDetails->brand_id."";
                        $brandNames[$m]['title'] = $brandDetails->name;
                        $brandNames[$m]['color'] = "";
                        $brandNames[$m++]['type'] = "1";
                    }
                }
            }
        }

        $resultFilterArr['brands'] = $brandNames;
        // dd($resultFilterArr);
        return $resultFilterArr;
    }
}
