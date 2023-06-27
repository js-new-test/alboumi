<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\ProductDetails;
use App\Models\ProductVideo;
use App\Models\ProductPricing;
use App\Models\CustGroupPrice;
use App\Models\AttributeGroup;
use DB;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;
use Auth;
use Config;
use Image;
use Carbon\Carbon;

class Product extends Model
{
    use CommonTrait;
    public $table = 'products';

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function saveProduct($data) {
        // dd($data);
        if ($data['page'] == 'anotherLanguage') {

            $productId = $data['productId'];

            $productDetails = new ProductDetails;
            $productDetails->product_id = $data['productId'];
            $productDetails->language_id = $data['defaultLanguage'];
            $productDetails->title = $data['title'];
            $productDetails->description = $data['description'];
            $productDetails->meta_title = $data['metaTitle'];
            $productDetails->meta_description = $data['metaKeyword'];
            $productDetails->key_features = $data['keyFeatures'];
            $productDetails->meta_keyword = $data['metaDescription'];
            $productDetails->product_type = $data['product_type'];
            $productDetails->save();
            $productDetailsId=$productDetails->id;

        } else {

            $product_slug = Str::slug($data['title'], '-');

            $product = new Product;
            $product->category_id = $data['categoryId'];
            $product->manufacturer_id = $data['brandName'];
            $product->product_slug = $product_slug;
            $product->can_giftwrap = $data['canGiftWrap'];
            $product->width = $data['width'];
            $product->height = $data['height'];
            $product->weight = $data['weight'];
            $product->image_min_width = $data['image_min_width'];
            $product->image_min_height = $data['image_min_height'];
            $product->image_max_width = $data['image_max_width'];
            $product->image_max_height = $data['image_max_height'];
            $product->max_images = $data['max_images'];
            $product->status = $data['status'];
            $product->length = $data['length'];
            $product->out_of_stock = 'no';
            $product->tax_class_id = $data['tax_class_id'];
            $product->flexmedia_code = $data['flexmedia_code'];
            $product->is_customized = $data['isCustomized'];
            $product->flag_deliverydate = $data['dateDisplay'];
            $product->printing_product = $data['printing_product'];

            if(isset($data['lumise_product_id']) && !empty($data['lumise_product_id']))
            $product->design_tool_product_id = $data['lumise_product_id'];
            else
            $product->design_tool_product_id = NULL;
            $product->save();

            $productId = $product->id;

            $productDetails = new ProductDetails;
            $productDetails->product_id = $product->id;
            $productDetails->language_id = $data['defaultLanguage'];
            $productDetails->title = $data['title'];
            $productDetails->description = $data['description'];
            $productDetails->meta_title = $data['metaTitle'];
            $productDetails->meta_description = $data['metaKeyword'];
            $productDetails->key_features = $data['keyFeatures'];
            $productDetails->meta_keyword = $data['metaDescription'];
            $productDetails->save();
            $productDetailsId=$productDetails->id;
            $category = Category::find($data['categoryId']);
            if ($category->flag_product == 0) {
                $category->flag_product = 1;
                $category->save();
            }
        }


        return $productDetailsId;
    }

    // save product video details
    public function editProductVideoDetails($data) {
        $productVideo = ProductVideo::where('product_id', $data['productId'])->first();
        if (empty($productVideo)) {
            $productVideo = new ProductVideo;
        }
        $productVideo->product_id = $data['productId'];
        $productVideo->title = $data['videoTitle'];
        $productVideo->type = $data['videoType'];
        $productVideo->url = $data['videoURL'];
        $productVideo->status = $data['videoStatus'];
        $productVideo->save();

        return array(
                'success' => true,
                'message' => trans('Product Video added successfully')
            );

    }

    //save product inventory
    public function editProductInventoryDetails($data) {
        $product = Product::find($data['productId']);
        $product->low_stock_alert = $data['lowStockAlert'];
        $product->low_stock_alert_quantity = $data['lowStockAlertQuantity'];
        $product->max_order_quantity = $data['maximumQuantityPerOrder'];
        $product->save();

        return array(
                'success' => true,
                'message' => trans('Product Inventory added successfully')
            );
    }

    public function editProductGeneralDetails($data)
    {
        $product_slug = Str::slug($data['title'], '-');

        $product = Product::find($data['productId']);
        $product->category_id = $data['categoryId'];
        $product->manufacturer_id = $data['brandName'];
        $product->product_slug = $product_slug;
        $product->can_giftwrap = $data['canGiftWrap'];
        $product->width = $data['width'];
        $product->height = $data['height'];
        $product->length = $data['length'];
        $product->weight = $data['weight'];
        $product->image_min_width = $data['image_min_width'];
        $product->image_min_height = $data['image_min_height'];
        $product->image_max_width = $data['image_max_width'];
        $product->image_max_height = $data['image_max_height'];
        $product->max_images = $data['max_images'];
        $product->out_of_stock = 'no';
        $product->is_customized = $data['isCustomized'];
        $product->flag_deliverydate = $data['dateDisplay'];
        if(isset($data['lumise_product_id']) && !empty($data['lumise_product_id']))
        $product->design_tool_product_id = $data['lumise_product_id'];
        else
        $product->design_tool_product_id = NULL;
        $product->tax_class_id = $data['tax_class_id'];
        $product->flexmedia_code = $data['flexmedia_code'];
        $product->status = $data['status'];
        $product->printing_product = $data['printing_product'];
        $product->save();

        $productDetails = new ProductDetails;
        $productDetails = ProductDetails::where('product_id', $data['productId'])->where('language_id',$data['languageId'])->whereNull('deleted_at')->first();

        $productDetails->language_id = $data['languageId'];
        $productDetails->title = $data['title'];
        $productDetails->description = $data['description'];
        $productDetails->meta_title = $data['metaTitle'];
        $productDetails->meta_description = $data['metaKeyword'];
        $productDetails->key_features = $data['keyFeatures'];
        $productDetails->meta_keyword = $data['metaDescription'];
        $productDetails->product_type = $data['product_type'];
        $productDetails->save();
        $category = Category::find($data['categoryId']);
        if ($category->flag_product == 0) {
            $category->flag_product = 1;
            $category->save();
        }
        if($data['categoryId'] != $data['prevCategoryId'])
        {
            $productsFromOldCategory = Product::whereNull('deleted_at')->where('category_id',$data['prevCategoryId'])->get();
            if($productsFromOldCategory->isEmpty())
            {
                Category::where('id',$data['prevCategoryId'])->update(['flag_product'=>0]);
            }
        }

        $productDetailsId=$productDetails->id;
        return array(
                'success' => true,
                'productId'=>$productDetailsId,
                'message' => trans('Product details updated successfully')
            );
    }

    public function editProductSpecificationDetails($data)
    {
        $count = count($data['mrp']);
        $reqdImgWidth = Config::get('app.products.width');
        $reqdImgHeight = Config::get('app.products.height');
        $attributeGroups = AttributeGroup::select('attribute_groups.id', 'attribute_groups.attribute_type_id', 'attribute_groups.category_ids','attributeGroupDetails.name', 'attributeGroupDetails.display_name')
        ->join('attribute_group_details as attributeGroupDetails', 'attributeGroupDetails.attr_group_id', '=', 'attribute_groups.id')
        ->whereNull('attribute_groups.deleted_at')
        ->whereNotNull('attribute_groups.category_ids')->get();

        $a = array();
        $attributeGroupIds = array();
        foreach ($attributeGroups as $attributeGroup) {
            $temp = explode(",", $attributeGroup->category_ids);
            if (in_array($data['categoryId'], $temp)) {
              if(!in_array($attributeGroup->id, $attributeGroupIds, true)){
                array_push($a, $attributeGroup->name);
                array_push($attributeGroupIds, $attributeGroup->id);
              }
            }
        }
        $GroupIds=implode(',', array_reverse($attributeGroupIds));

        for ($i=0; $i <$count; $i++) {
            if($data['pricingId'][$i]!=0){
            $productPricing = ProductPricing::find($data['pricingId'][$i]);
            $productPricing->product_id = $data['productId'];
            $productPricing->category_id = $data['categoryId'];
            $productPricing->attribute_group_ids=$GroupIds;
            $attributeIds=array();
            foreach($attributeGroupIds as $v){
              if(isset($data[$v][$i]))
              array_push($attributeIds, $data[$v][$i]);
            }
            $productPricing->attribute_ids=implode(',', array_reverse($attributeIds));
            // if (isset($data['AttributeGroup'])) {
            //   $attributeGroups = AttributeGroup::select('attribute_groups.id','attributeGroupDetails.display_name')
            //   ->join('attribute_group_details as attributeGroupDetails', 'attributeGroupDetails.attr_group_id', '=', 'attribute_groups.id')
            //   ->where('attribute_groups.id',$data['AttributeGroup'][$i])
            //    ->first();
            //    $grpName=$attributeGroups->display_name;
            //     $j = 0;
            //     // foreach ($data['attributes'] as $attribute) {
            //     // return implode(',', $attribute);
            //         $productPricing->attribute_ids = implode(',', $data['AttributeGroup']);//json_encode($attribute);
            //
            //         $productPricing->attribute_group_ids = implode(',', $data[$grpName][$i]);//json_encode($data['groupIds'][$j++]);
            //     // }
            // }

            $productPricing->mrp = $data['mrp'][$i];
            $productPricing->sku = $data['sku'][$i];
            $productPricing->selling_price = $data['sellingPrice'][$i];
            $productPricing->offer_price = $data['offerPrice'][$i];
            $productPricing->quantity = $data['quantity'][$i];
            if(!empty($data['offerStartDate'][$i]) && $data['offerStartDate'][$i]!='')
            $productPricing->offer_start_date = date('Y-m-d',strtotime($data['offerStartDate'][$i]));
            if(!empty($data['offerEndDate'][$i]) && $data['offerEndDate'][$i]!='')
            $productPricing->offer_end_date = date('Y-m-d',strtotime($data['offerEndDate'][$i]));
            $productPricing->is_default = 0;
            if(!empty($data['image']) && isset($data['image'][$i]) && $data['image'][$i]!=''){
            $images = $data['image'][$i];

            $imagesName = $images->getClientOriginalName();
            $imagesName = str_replace(' ', '', $imagesName);
            $randonName = rand(1, 200);

            $image_resize = Image::make($images->getRealPath());
            $image_resize->resize($reqdImgWidth, $reqdImgHeight);
            $images->move(public_path('/images/product/'.$data['productId'].'/pricingoption/'), $imagesName);
            $image_resize->save(public_path('images/product/'.$data['productId'].'/pricingoption/' .$imagesName));
            $productPricing->image=$imagesName;
          }

            $productPricing->save();
            if(isset($data['isDefault'])){
              ProductPricing::where('product_id', '=', $data['productId'])->update(['is_default' => 0]);
              ProductPricing::where('id', '=', $data['isDefault'])->update(['is_default' => 1]);
            }
          }
          else{
            $productPricing = new ProductPricing;
            $productPricing->product_id = $data['productId'];
            $productPricing->category_id = $data['categoryId'];
            $productPricing->attribute_group_ids=$GroupIds;
            $attributeIds=array();
            foreach($attributeGroupIds as $v){
              if(isset($data[$v][$i]))
              array_push($attributeIds, $data[$v][$i]);
            }
            $productPricing->attribute_ids=implode(',', array_reverse($attributeIds));
            // if (isset($data['AttributeGroup'])) {
            //   $attributeGroups = AttributeGroup::select('attribute_groups.id','attributeGroupDetails.display_name')
            //   ->join('attribute_group_details as attributeGroupDetails', 'attributeGroupDetails.attr_group_id', '=', 'attribute_groups.id')
            //   ->where('attribute_groups.id',$data['AttributeGroup'][$i])
            //    ->first();
            //    $grpName=$attributeGroups->display_name;
            //     $j = 0;
            //     // foreach ($data['attributes'] as $attribute) {
            //     // return implode(',', $attribute);
            //         $productPricing->attribute_ids = implode(',', $data['AttributeGroup'][$i]);//json_encode($attribute);
            //
            //         $productPricing->attribute_group_ids = implode(',', $data[$grpName][$i]);//json_encode($data['groupIds'][$j++]);
            //     // }
            // }

            $productPricing->mrp = $data['mrp'][$i];
            $productPricing->sku = $data['sku'][$i];
            $productPricing->selling_price = $data['sellingPrice'][$i];
            $productPricing->offer_price = $data['offerPrice'][$i];
            $productPricing->quantity = $data['quantity'][$i];
            if(!empty($data['offerStartDate'][$i]) && $data['offerStartDate'][$i]!='')
            $productPricing->offer_start_date = date('Y-m-d',strtotime($data['offerStartDate'][$i]));
            if(!empty($data['offerEndDate'][$i]) && $data['offerEndDate'][$i]!='')
            $productPricing->offer_end_date = date('Y-m-d',strtotime($data['offerEndDate'][$i]));
            if(!empty($data['image']) && isset($data['image'][$i]) && $data['image'][$i]!=''){
            $images = $data['image'][$i];

            $imagesName = $images->getClientOriginalName();
            $imagesName = str_replace(' ', '', $imagesName);
            $randonName = rand(1, 200);

            $image_resize = Image::make($images->getRealPath());
            $image_resize->resize($reqdImgWidth, $reqdImgHeight);
            $images->move(public_path('/images/product/'.$data['productId'].'/pricingoption/'), $imagesName);
            $image_resize->save(public_path('images/product/'.$data['productId'].'/pricingoption/' .$imagesName));
            $productPricing->image=$imagesName;
          }
            $answer =$data['isDefault'];
            if ($answer == $i) {
                $productPricing->is_default = 1;
            }
            else {
              $productPricing->is_default = 0;
            }
            // if(isset($data['isDefault'][$i]))
            // $productPricing->is_default = 1;
            // else
            // $productPricing->is_default = 0;
            $productPricing->save();
          }
        }
        $productDetails = ProductDetails::where('product_id', $data['productId'])->whereNull('deleted_at')->first();
        return $productDetails->id;
    }

    public static function getProducts($categoryId,$langId)
    {
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData->id;

        $nonDefaultProductsList = Product::select('products.id','products.product_slug','pd.title', 'product_pricing.attribute_ids',
                                    DB::raw('(select name from images where imageable_id  = products.id
                                    and images.image_type = "product" order by sort_order asc limit 1) as image'))
                                    ->join('categories as c','c.id','=','category_id')
                                    ->leftJoin('product_details as pd', function($join) use($langId) {
                                        $join->on('pd.product_id', '=' , 'products.id');
                                        $join->where('pd.language_id','=',$langId);
                                        $join->whereNull('pd.deleted_at');
                                    })
                                    ->leftJoin('product_pricing', function($join) {
                                        $join->on('product_pricing.product_id', '=' , 'products.id');
                                        $join->where('product_pricing.is_default','=',1);
                                        $join->whereNull('product_pricing.deleted_at');
                                    })
                                    // ->where('products.category_id',$categoryId)
                                    ->where('products.status','Active')
                                    ->whereRaw("FIND_IN_SET('".$categoryId."', c.category_path)")
                                    ->whereNull('c.deleted_at')
                                    ->whereNull('products.deleted_at');

        $nonDefaultProductsList = $nonDefaultProductsList->get();

        $resultArr = [];
        $i = 0;
        foreach($nonDefaultProductsList as $nonDefaultProducts)
        {
            $prodTitle = $nonDefaultProducts['title'];
            if($prodTitle == null)
            {
                $prodDetails = ProductDetails::select('title')
                                            ->where('product_id',$nonDefaultProducts['id'])
                                            ->where('language_id',$defaultLanguageId)
                                            ->whereNull('deleted_at')
                                            ->first();
                $prodTitle =  $prodDetails['title'];
            }
            $resultArr[$i]['id'] = $nonDefaultProducts['id'];
            $resultArr[$i]['title'] = $prodTitle;
            $resultArr[$i]['product_slug'] = $nonDefaultProducts['product_slug'];
            $resultArr[$i]['attribute_ids'] = $nonDefaultProducts['attribute_ids'];
            $resultArr[$i++]['imgName'] = $nonDefaultProducts['image'];
        }
        return $resultArr;
    }

    public function editProductBulkPricing($data)
    {
        // return $data;
        $index = 0;
        $productId = $data['productId'];
        foreach ($data['bulkData'] as $bulkData) {
            // return $bulkData;
            // print_r($bulkData[$index]['value']);
            $optionId = $bulkData[$index]['value'] ?? 0;

            for ($i=1; $i < count($bulkData); $i++) {

                $temp = $bulkData[$i]['rangeData'];
                $fromQuantity = substr($temp, 0, strpos($temp, "_"));
                $toQuantity = substr($temp, strpos($temp, "_") + 1);

                $productBulkPrices = ProductBulkPrices::where(['product_id' => $productId, 'option_id'=>$optionId, 'from_quantity' => $fromQuantity, 'to_quantity' => $toQuantity])->first();
                if (empty($productBulkPrices)) {
                    $productBulkPrices = new ProductBulkPrices;
                }


                    $productBulkPrices->product_id = $productId;
                    $productBulkPrices->option_id = $optionId;
                    $productBulkPrices->from_quantity = $fromQuantity;

                    $productBulkPrices->to_quantity = $toQuantity;//substr($temp, 2, strpos($temp, "_"));
                    $productBulkPrices->price = $bulkData[$i]['value'] ?? 0;
                    $productBulkPrices->save();

            }
            // $index++;
        }

        return array(
                'success' => true,
                'message' => trans('Product details updated successfully')
            );
    }

    public function addProductAdvancePricing($data)
    {
        // return $data;
        $CustGroupPrice=new CustGroupPrice;
        $CustGroupPrice->product_id= $data['productId'];
        $CustGroupPrice->customer_group_id= $data['selectGroup'];
        $CustGroupPrice->price= $data['price'];
        if($CustGroupPrice->save()){
        return array(
                'success' => true,
                'message' => trans('Product advance price added successfully')
            );
        }
        else{
          return array(
                  'success' => false,
                  'message' => trans('Product advance price not added')
              );
        }
    }
    public function editProductAdvancePricing($data)
    {
        // return $data;
        $CustGroupPrice=CustGroupPrice::find($data['pricingId']);
        $CustGroupPrice->price= $data['price'];
        if($CustGroupPrice->update()){
        return array(
                'success' => true,
                'message' => trans('Product advance price added successfully')
            );
        }
        else{
          return array(
                  'success' => false,
                  'message' => trans('Product advance price not added')
              );
        }
    }

    public function createProductData($arrTopCategories,$arrProducts,$baseUrl,$cmsPages,$langId)
    {
        $categoryTabData = $level2 = [];
        $j = 0;
        $categoryTabData['title'] = $arrTopCategories[0]['title'];
        $categoryTabData['categoryId'] = "".$arrTopCategories[0]['id']."";
        $categoryTabData['backgroundImage'] = $baseUrl.'/public/assets/images/categories/'.$arrTopCategories[0]['image'];

        $categoryTabData['type'] = "1";

        foreach($arrProducts as $ck => $productData)
        {
            $subtitle[] = $productData['title'];
            $level2[$j]['id'] = "".$productData['id']."";
            $level2[$j]['title'] = $productData['title'];
            $level2[$j]['query'] = $baseUrl."/api/v1/getProductDetails?language_id=".$langId.'&product_id='.$productData['id'];
            $level2[$j]['type'] = "1";
            $level2[$j++]['navigationFlag'] = "1";
        }

        if(!empty($level2))
        {
            $categoryTabData['query'] = "";
            $categoryTabData['navigationFlag'] = "0";
            $categoryTabData['subTitle'] = implode(',',$subtitle);
        }
        else
        {
            $categoryTabData['query'] = $baseUrl."/api/v1/getProductList?language_id=".$langId.'&category_id='.$arrTopCategories[0]['id'];
            $categoryTabData['navigationFlag'] = "1";
        }
        $categoryTabData['level2'] = $level2;
        return $categoryTabData;
    }

    //used only for API
    public function getAllProductsFromCategory($catId,$langId,$sortBy,$custId,$pageSize,$pageNo,$skip,$request)
    {
        if(!empty($request['filterQuery']))
        {
            foreach($request['filterQuery'] as $filter)
            {
                if(!empty($filter))
                {
                    if($filter['attribute_id'] == "Category")
                    {
                        $categoryIds = explode(",",$filter['option_id']);
                        $categoryIds =(str_replace('"','', $categoryIds));
                        $categoryIds = str_replace('[','', $categoryIds);
                        $categoryIds = str_replace(']','', $categoryIds);
                    }
                    else if($filter['attribute_id'] == "Brands")
                    {
                        $brandIds = explode(",",$filter['option_id']);
                        $brandIds =(str_replace('"','', $brandIds));
                        $brandIds = str_replace('[','', $brandIds);
                        $brandIds = str_replace(']','', $brandIds);
                    }
                    else if($filter['attribute_id'] == "Price")
                    {
                        $priceArr = $filter['option_id'];
                    }
                    else
                    {
                        $attribute_ids[$filter['attribute_id']] = explode(",",$filter['option_id']);
                        $attribute_ids[$filter['attribute_id']] =(str_replace('"','', $attribute_ids[$filter['attribute_id']]));
                        $attribute_ids[$filter['attribute_id']] = str_replace('[','', $attribute_ids[$filter['attribute_id']]);
                        $attribute_ids[$filter['attribute_id']] = str_replace(']','', $attribute_ids[$filter['attribute_id']]);
                    }
                }
            }
        }

        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }
        $selling_price = "product_pricing.selling_price as price";
        if(!empty($attribute_ids))
        {
            $selling_price = "pp.selling_price as price";
        }

        $offer_price = "product_pricing.offer_price as discountedPrice";
        if(!empty($attribute_ids))
        {
            $offer_price = "pp.offer_price as discountedPrice";
        }
        $offer_start_date = "product_pricing.offer_start_date as offer_start_date";
        if(!empty($attribute_ids))
        {
            $offer_start_date = "pp.offer_start_date as offer_start_date";
        }

        $offer_end_date = "product_pricing.offer_end_date as offer_end_date";
        if(!empty($attribute_ids))
        {
            $offer_end_date = "pp.offer_end_date as offer_end_date";
        }

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        DB::enableQueryLog();
        $nonDefaultProductsList = Product::select('products.id','pd.title',DB::raw($selling_price),DB::raw($offer_price),'images.name as image','product_slug',DB::raw($offer_end_date),DB::raw($offer_start_date),DB::raw($group_price),
                                DB::raw('IF(cgp.price IS NOT NULL, cgp.price, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, product_pricing.offer_price, product_pricing.selling_price)) as productPrice,
                                IF(cgp.price IS NOT NULL, 1, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, 2, 3)) as productSale
                                '),'c.photo_upload','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
                                    ->leftJoin('images',function ($join) {
                                        $join->on('images.imageable_id', '=' , 'products.id');
                                        $join->where('images.image_type','=','product');
                                        $join->whereNull('images.deleted_at');
                                        $join->where('images.is_default','=','yes');
                                    })
                                    ->join('categories as c','c.id','=','category_id')
                                    ->leftJoin('product_details as pd', function($join) use($langId) {
                                        $join->on('pd.product_id', '=' , 'products.id');
                                        $join->where('pd.language_id','=',$langId);
                                        $join->whereNull('pd.deleted_at');
                                    })
                                    ->join('product_pricing', function($join) {
                                        $join->on('product_pricing.product_id', '=' , 'products.id');
                                        // $join->where('product_pricing.is_default','=',1);
                                        $join->whereNull('product_pricing.deleted_at');
                                    })
                                    ->where('products.status','Active')
                                    ->whereRaw("FIND_IN_SET('".$catId."', c.category_path)")
                                    ->whereNull('c.deleted_at')
                                    ->where('c.status','=',1)
                                    ->whereNull('products.deleted_at')
                                    ->groupBy('products.id');
                                    if(!empty($skip))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereNotIn('products.id',json_decode(stripslashes($skip)));
                                    }
                                    if(!empty($custId) && $custId->cust_group_id != 0)
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                    $join->on('cgp.product_id', '=' , 'products.id');
                                                                    $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                        });
                                    }
                                    else
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                            $join->on('cgp.product_id', '=' , 'products.id');
                                            $join->where('cgp.customer_group_id','=',0);
                                        });
                                    }
                                    if(!empty($request['sortBy']))
                                    {
                                        if($request['sortBy'] == '' || $request['sortBy'] == 1 || $request['sortBy'] == null)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('products.created_at','desc');
                                        }
                                        if($request['sortBy'] == 2)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productSale','asc');
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($request['sortBy'] == 3)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($request['sortBy'] == 4)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','desc');
                                        }
                                    }
                                    if(empty($request['sortBy']))
                                    {
                                        if($sortBy == '' || $sortBy == 1 || $sortBy == null)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('products.created_at','desc');
                                        }
                                        if($sortBy == 2)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productSale','asc');
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($sortBy == 3)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($sortBy == 4)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','desc');
                                        }
                                    }
                                    if(!empty($categoryIds) && $categoryIds[0] != "")
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereIn('products.category_id',$categoryIds);
                                    }
                                    if(!empty($priceArr))
                                    {
                                        foreach($priceArr as $price)
                                        {
                                            $min = round($price['min']);
                                            $max = round($price['max']);
                                            if(!empty($custId) && $custId->cust_group_id != 0)
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                            }
                                            else
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                            }

                                        }
                                    }
                                    if(!empty($attribute_ids))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('product_pricing as pp', function($join) {
                                            $join->on('pp.product_id', '=' , 'products.id');
                                        });
                                        foreach($attribute_ids as $key => $attributeIds)
                                        {
                                            if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("(FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                            }
                                            $numItems = count($attributeIds);
                                            foreach($attributeIds as $key => $attributeId)
                                            {
                                                if(!empty($attributeId) && $attributeId != '')
                                                {
                                                    if((count($attributeIds) == 1 && $key == 0))
                                                    {
                                                        $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                    }
                                                    else
                                                    {
                                                        if($key == 0)
                                                            $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                        if(++$key == $numItems)
                                                            $nonDefaultProductsList = $nonDefaultProductsList->orWhereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if(!empty($brandIds))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereIn('products.manufacturer_id',$brandIds);
                                    }


            $nonDefaultProductsList = $nonDefaultProductsList->get();

            // dd(DB::getQueryLog());
        // dd($nonDefaultProductsList);
        $resultArr = [];
        $i = 0;
        foreach($nonDefaultProductsList as $nonDefaultProducts)
        {
            $prodTitle = $nonDefaultProducts['title'];
            if($prodTitle == null)
            {
                $prodDetails = ProductDetails::select('title')
                                            ->where('product_id',$nonDefaultProducts['id'])
                                            ->where('language_id',$defaultLanguageId)
                                            ->whereNull('deleted_at')
                                            ->first();
                $prodTitle =  $prodDetails['title'];
            }
            $resultArr[$i]['id'] = "".$nonDefaultProducts['id']."";
            $resultArr[$i]['title'] = $prodTitle;

            if(empty($nonDefaultProducts['group_price']))
            {
                if (!empty($nonDefaultProducts['discountedPrice']) && (date("Y-m-d", strtotime($nonDefaultProducts['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d", strtotime($nonDefaultProducts['offer_end_date']))))
                {
                    $resultArr[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                    $resultArr[$i]['group_price'] = '';
                }
                else if (!empty($nonDefaultProducts['discountedPrice']))
                {
                    $resultArr[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                    $resultArr[$i]['group_price'] = '';
                }
                else
                {
                    $resultArr[$i]['group_price'] = '';
                    $resultArr[$i]['discountedPrice'] = '';
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                }
            }
            else
            {
                $resultArr[$i]['group_price'] = $nonDefaultProducts['group_price'];
                $resultArr[$i]['discountedPrice'] = '';
                $resultArr[$i]['price'] = $nonDefaultProducts['price'];
            }
            if($nonDefaultProducts['photo_upload'] != 0)
                $resultArr[$i]['flagInstock'] = 1;
            else
            {
                if(empty($nonDefaultProducts['attribute_group_ids']) && empty($nonDefaultProducts['attribute_ids']))
                {
                    if($nonDefaultProducts['quantity'] > 0)
                        $resultArr[$i]['flagInstock'] = 1;
                    else
                        $resultArr[$i]['flagInstock'] = 0;
                }
                else
                {
                    foreach(explode(",",$nonDefaultProducts['attribute_group_ids']) as $attrGroupId)
                    {
                        $attrGroup = AttributeGroup::find($attrGroupId);
                        if(!empty($attrGroup['status']))
                        {
                            if($attrGroup['status'] == 0 || !empty($attrGroup['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($nonDefaultProducts['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }
                    }
                    foreach(explode(",",$nonDefaultProducts['attribute_ids']) as $attrId)
                    {
                        $attribute = Attribute::find($attrId);
                        if(!empty($attribute['status']))
                        {
                            if($attribute['status'] == 0 || !empty($attribute['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($nonDefaultProducts['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }

                    }
                }
            }
            $resultArr[$i]['slug'] = $nonDefaultProducts['product_slug'];
            $resultArr[$i]['offer_start_date'] = $nonDefaultProducts['offer_start_date'];
            $resultArr[$i]['offer_end_date'] = $nonDefaultProducts['offer_end_date'];
            $resultArr[$i++]['image'] = $nonDefaultProducts['image'];
        }
        
        return $resultArr;
    }

    public function getProductsFromCategory($catId,$langId,$sortBy,$custId,$pageSize,$pageNo,$skip,$request)
    {
        if(!empty($request['filterQuery']))
        {
            foreach($request['filterQuery'] as $filter)
            {
                if(!empty($filter))
                {
                    if($filter['attribute_id'] == "Category")
                    {
                        $categoryIds = explode(",",$filter['option_id']);
                        $categoryIds =(str_replace('"','', $categoryIds));
                        $categoryIds = str_replace('[','', $categoryIds);
                        $categoryIds = str_replace(']','', $categoryIds);
                    }
                    else if($filter['attribute_id'] == "Brands")
                    {
                        $brandIds = explode(",",$filter['option_id']);
                        $brandIds =(str_replace('"','', $brandIds));
                        $brandIds = str_replace('[','', $brandIds);
                        $brandIds = str_replace(']','', $brandIds);
                    }
                    else if($filter['attribute_id'] == "Price")
                    {
                        $priceArr = $filter['option_id'];
                    }
                    else
                    {
                        $attribute_ids[$filter['attribute_id']] = explode(",",$filter['option_id']);
                        $attribute_ids[$filter['attribute_id']] =(str_replace('"','', $attribute_ids[$filter['attribute_id']]));
                        $attribute_ids[$filter['attribute_id']] = str_replace('[','', $attribute_ids[$filter['attribute_id']]);
                        $attribute_ids[$filter['attribute_id']] = str_replace(']','', $attribute_ids[$filter['attribute_id']]);
                    }
                }
            }
        }

        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }
        $selling_price = "product_pricing.selling_price as price";
        if(!empty($attribute_ids))
        {
            $selling_price = "pp.selling_price as price";
        }

        $offer_price = "product_pricing.offer_price as discountedPrice";
        if(!empty($attribute_ids))
        {
            $offer_price = "pp.offer_price as discountedPrice";
        }
        $offer_start_date = "product_pricing.offer_start_date as offer_start_date";
        if(!empty($attribute_ids))
        {
            $offer_start_date = "pp.offer_start_date as offer_start_date";
        }

        $offer_end_date = "product_pricing.offer_end_date as offer_end_date";
        if(!empty($attribute_ids))
        {
            $offer_end_date = "pp.offer_end_date as offer_end_date";
        }

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        DB::enableQueryLog();
        $nonDefaultProductsList = Product::select('products.id','pd.title',DB::raw($selling_price),DB::raw($offer_price),'images.name as image','product_slug',DB::raw($offer_end_date),DB::raw($offer_start_date),DB::raw($group_price),
                                DB::raw('IF(cgp.price IS NOT NULL, cgp.price, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, product_pricing.offer_price, product_pricing.selling_price)) as productPrice,
                                IF(cgp.price IS NOT NULL, 1, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, 2, 3)) as productSale
                                '),'c.photo_upload','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
                                    ->leftJoin('images',function ($join) {
                                        $join->on('images.imageable_id', '=' , 'products.id');
                                        $join->where('images.image_type','=','product');
                                        $join->whereNull('images.deleted_at');
                                        $join->where('images.is_default','=','yes');
                                    })
                                    ->join('categories as c','c.id','=','category_id')
                                    ->leftJoin('product_details as pd', function($join) use($langId) {
                                        $join->on('pd.product_id', '=' , 'products.id');
                                        $join->where('pd.language_id','=',$langId);
                                        $join->whereNull('pd.deleted_at');
                                    })
                                    ->join('product_pricing', function($join) {
                                        $join->on('product_pricing.product_id', '=' , 'products.id');
                                        // $join->where('product_pricing.is_default','=',1);
                                        $join->whereNull('product_pricing.deleted_at');
                                    })
                                    ->where('products.status','Active')
                                    ->whereRaw("FIND_IN_SET('".$catId."', c.category_path)")
                                    ->whereNull('c.deleted_at')
                                    ->where('c.status','=',1)
                                    ->whereNull('products.deleted_at')
                                    ->groupBy('products.id');
                                    if(!empty($skip))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereNotIn('products.id',json_decode(stripslashes($skip)));
                                    }
                                    if(!empty($custId) && $custId->cust_group_id != 0)
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                    $join->on('cgp.product_id', '=' , 'products.id');
                                                                    $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                        });
                                    }
                                    else
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                            $join->on('cgp.product_id', '=' , 'products.id');
                                            $join->where('cgp.customer_group_id','=',0);
                                        });
                                    }
                                    if(!empty($request['sortBy']))
                                    {
                                        if($request['sortBy'] == '' || $request['sortBy'] == 1 || $request['sortBy'] == null)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('products.created_at','desc');
                                        }
                                        if($request['sortBy'] == 2)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productSale','asc');
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($request['sortBy'] == 3)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($request['sortBy'] == 4)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','desc');
                                        }
                                    }
                                    if(empty($request['sortBy']))
                                    {
                                        if($sortBy == '' || $sortBy == 1 || $sortBy == null)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('products.created_at','desc');
                                        }
                                        if($sortBy == 2)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productSale','asc');
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($sortBy == 3)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($sortBy == 4)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','desc');
                                        }
                                    }
                                    if(!empty($categoryIds) && $categoryIds[0] != "")
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereIn('products.category_id',$categoryIds);
                                    }
                                    if(!empty($priceArr))
                                    {
                                        foreach($priceArr as $price)
                                        {
                                            $min = round($price['min']);
                                            $max = round($price['max']);
                                            if(!empty($custId) && $custId->cust_group_id != 0)
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                            }
                                            else
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                            }

                                        }
                                    }
                                    if(!empty($attribute_ids))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('product_pricing as pp', function($join) {
                                            $join->on('pp.product_id', '=' , 'products.id');
                                        });
                                        foreach($attribute_ids as $key => $attributeIds)
                                        {
                                            if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("(FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                            }
                                            $numItems = count($attributeIds);
                                            foreach($attributeIds as $key => $attributeId)
                                            {
                                                if(!empty($attributeId) && $attributeId != '')
                                                {
                                                    if((count($attributeIds) == 1 && $key == 0))
                                                    {
                                                        $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                    }
                                                    else
                                                    {
                                                        if($key == 0)
                                                            $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                        if(++$key == $numItems)
                                                            $nonDefaultProductsList = $nonDefaultProductsList->orWhereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if(!empty($brandIds))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereIn('products.manufacturer_id',$brandIds);
                                    }

        if($pageSize != 0 && $pageNo!=0)
            $nonDefaultProductsList = $nonDefaultProductsList->paginate($pageSize,$columns = ['*'],$pageName = 'page',$pageNo);
        else
        {
            $filteredProducts1 = $nonDefaultProductsList->get();
            $totalFilteredProducts = count($filteredProducts1);
            $nonDefaultProductsList = $nonDefaultProductsList->limit(12)->groupBy('products.id')->get()->toArray();
            // $nonDefaultProductsList = $nonDefaultProductsList->get();
        }
        // dd(DB::getQueryLog());
        // dd($nonDefaultProductsList);
        $resultArr = [];
        $i = 0;
        foreach($nonDefaultProductsList as $nonDefaultProducts)
        {
            $prodTitle = $nonDefaultProducts['title'];
            if($prodTitle == null)
            {
                $prodDetails = ProductDetails::select('title')
                                            ->where('product_id',$nonDefaultProducts['id'])
                                            ->where('language_id',$defaultLanguageId)
                                            ->whereNull('deleted_at')
                                            ->first();
                $prodTitle =  $prodDetails['title'];
            }
            $resultArr[$i]['id'] = "".$nonDefaultProducts['id']."";
            $resultArr[$i]['title'] = $prodTitle;

            if(empty($nonDefaultProducts['group_price']))
            {
                if (!empty($nonDefaultProducts['discountedPrice']) && (date("Y-m-d", strtotime($nonDefaultProducts['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d", strtotime($nonDefaultProducts['offer_end_date']))))
                {
                    $resultArr[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                    $resultArr[$i]['group_price'] = '';
                }
                else if (!empty($nonDefaultProducts['discountedPrice']))
                {
                    $resultArr[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                    $resultArr[$i]['group_price'] = '';
                }
                else
                {
                    $resultArr[$i]['group_price'] = '';
                    $resultArr[$i]['discountedPrice'] = '';
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                }
            }
            else
            {
                $resultArr[$i]['group_price'] = $nonDefaultProducts['group_price'];
                $resultArr[$i]['discountedPrice'] = '';
                $resultArr[$i]['price'] = $nonDefaultProducts['price'];
            }
            if($nonDefaultProducts['photo_upload'] != 0)
                $resultArr[$i]['flagInstock'] = 1;
            else
            {
                if(empty($nonDefaultProducts['attribute_group_ids']) && empty($nonDefaultProducts['attribute_ids']))
                {
                    if($nonDefaultProducts['quantity'] > 0)
                        $resultArr[$i]['flagInstock'] = 1;
                    else
                        $resultArr[$i]['flagInstock'] = 0;
                }
                else
                {
                    foreach(explode(",",$nonDefaultProducts['attribute_group_ids']) as $attrGroupId)
                    {
                        $attrGroup = AttributeGroup::find($attrGroupId);
                        if(!empty($attrGroup['status']))
                        {
                            if($attrGroup['status'] == 0 || !empty($attrGroup['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($nonDefaultProducts['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }
                    }
                    foreach(explode(",",$nonDefaultProducts['attribute_ids']) as $attrId)
                    {
                        $attribute = Attribute::find($attrId);
                        if(!empty($attribute['status']))
                        {
                            if($attribute['status'] == 0 || !empty($attribute['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($nonDefaultProducts['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }

                    }
                }
            }
            $resultArr[$i]['slug'] = $nonDefaultProducts['product_slug'];
            $resultArr[$i]['offer_start_date'] = $nonDefaultProducts['offer_start_date'];
            $resultArr[$i]['offer_end_date'] = $nonDefaultProducts['offer_end_date'];
            $resultArr[$i++]['image'] = $nonDefaultProducts['image'];
        }

        return $resultArr;
    }

    public function getProductsFromCategoryCount($catId,$langId,$sortBy,$custId,$pageSize,$pageNo,$skip,$request)
    {
        // dd($request);
        if(!empty($request['filterQuery']))
        {
            foreach($request['filterQuery'] as $filter)
            {
                if(!empty($filter))
                {
                    if($filter['attribute_id'] == "Category")
                    {
                        $categoryIds = explode(",",$filter['option_id']);
                        $categoryIds =(str_replace('"','', $categoryIds));
                        $categoryIds = str_replace('[','', $categoryIds);
                        $categoryIds = str_replace(']','', $categoryIds);
                    }
                    else if($filter['attribute_id'] == "Price")
                    {
                        $priceArr = $filter['option_id'];
                    }
                    else
                    {
                        $attribute_ids[$filter['attribute_id']] = explode(",",$filter['option_id']);
                        $attribute_ids[$filter['attribute_id']] =(str_replace('"','', $attribute_ids[$filter['attribute_id']]));
                        $attribute_ids[$filter['attribute_id']] = str_replace('[','', $attribute_ids[$filter['attribute_id']]);
                        $attribute_ids[$filter['attribute_id']] = str_replace(']','', $attribute_ids[$filter['attribute_id']]);
                    }
                }
            }
        }

        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }
        $selling_price = "product_pricing.selling_price as price";
        if(!empty($attribute_ids))
        {
            $selling_price = "pp.selling_price as price";
        }

        $offer_price = "product_pricing.offer_price as discountedPrice";
        if(!empty($attribute_ids))
        {
            $offer_price = "pp.offer_price as discountedPrice";
        }
        $offer_start_date = "product_pricing.offer_start_date as offer_start_date";
        if(!empty($attribute_ids))
        {
            $offer_start_date = "pp.offer_start_date as offer_start_date";
        }

        $offer_end_date = "product_pricing.offer_end_date as offer_end_date";
        if(!empty($attribute_ids))
        {
            $offer_end_date = "pp.offer_end_date as offer_end_date";
        }

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        DB::enableQueryLog();
        $nonDefaultProductsList = Product::select('products.id','pd.title',DB::raw($selling_price),DB::raw($offer_price),'images.name as image','product_slug',DB::raw($offer_end_date),DB::raw($offer_start_date),DB::raw($group_price),
                                DB::raw('IF(cgp.price IS NOT NULL, cgp.price, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, product_pricing.offer_price, product_pricing.selling_price)) as productPrice,
                                IF(cgp.price IS NOT NULL, 1, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, 2, 3)) as productSale
                                '),'c.photo_upload','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
                                    ->leftJoin('images',function ($join) {
                                        $join->on('images.imageable_id', '=' , 'products.id');
                                        $join->where('images.image_type','=','product');
                                        $join->whereNull('images.deleted_at');
                                        $join->where('images.is_default','=','yes');
                                    })
                                    ->join('categories as c','c.id','=','category_id')
                                    ->leftJoin('product_details as pd', function($join) use($langId) {
                                        $join->on('pd.product_id', '=' , 'products.id');
                                        $join->where('pd.language_id','=',$langId);
                                        $join->whereNull('pd.deleted_at');
                                    })
                                    ->join('product_pricing', function($join) {
                                        $join->on('product_pricing.product_id', '=' , 'products.id');
                                        // $join->where('product_pricing.is_default','=',1);
                                        $join->whereNull('product_pricing.deleted_at');
                                    })
                                    ->where('products.status','Active')
                                    ->whereRaw("FIND_IN_SET('".$catId."', c.category_path)")
                                    ->whereNull('c.deleted_at')
                                    ->where('c.status','=',1)
                                    ->whereNull('products.deleted_at')
                                    ->groupBy('products.id');
                                    if(!empty($skip))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereNotIn('products.id',json_decode(stripslashes($skip)));
                                    }
                                    if(!empty($custId) && $custId->cust_group_id != 0)
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                    $join->on('cgp.product_id', '=' , 'products.id');
                                                                    $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                        });
                                    }
                                    else
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                            $join->on('cgp.product_id', '=' , 'products.id');
                                            $join->where('cgp.customer_group_id','=',0);
                                        });
                                    }
                                    if(!empty($request['sortBy']))
                                    {
                                        if($request['sortBy'] == '' || $request['sortBy'] == 1 || $request['sortBy'] == null)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('products.created_at','desc');
                                        }
                                        if($request['sortBy'] == 2)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productSale','asc');
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($request['sortBy'] == 3)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($request['sortBy'] == 4)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','desc');
                                        }
                                    }
                                    if(empty($request['sortBy']))
                                    {
                                        if($sortBy == '' || $sortBy == 1 || $sortBy == null)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('products.created_at','desc');
                                        }
                                        if($sortBy == 2)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productSale','asc');
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($sortBy == 3)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','asc');
                                        }
                                        if($sortBy == 4)
                                        {
                                            $nonDefaultProductsList = $nonDefaultProductsList->orderBy('productPrice','desc');
                                        }
                                    }
                                    if(!empty($categoryIds) && $categoryIds[0] != "")
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereIn('products.category_id',$categoryIds);
                                    }
                                    if(!empty($priceArr))
                                    {
                                        foreach($priceArr as $price)
                                        {
                                            $min = round($price['min']);
                                            $max = round($price['max']);
                                            if(!empty($custId) && $custId->cust_group_id != 0)
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                            }
                                            else
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                            }

                                        }
                                    }
                                    if(!empty($attribute_ids))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->leftJoin('product_pricing as pp', function($join) {
                                            $join->on('pp.product_id', '=' , 'products.id');
                                        });
                                        foreach($attribute_ids as $key => $attributeIds)
                                        {
                                            if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                            {
                                                $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("(FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                            }
                                            $numItems = count($attributeIds);
                                            foreach($attributeIds as $key => $attributeId)
                                            {
                                                if(!empty($attributeId) && $attributeId != '')
                                                {
                                                    if((count($attributeIds) == 1 && $key == 0))
                                                    {
                                                        $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                    }
                                                    else
                                                    {
                                                        if($key == 0)
                                                            $nonDefaultProductsList = $nonDefaultProductsList->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                        if(++$key == $numItems)
                                                            $nonDefaultProductsList = $nonDefaultProductsList->orWhereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if(!empty($brandIds))
                                    {
                                        $nonDefaultProductsList = $nonDefaultProductsList->whereIn('products.manufacturer_id',$brandIds);
                                    }

        if($pageSize != 0 && $pageNo!=0)
            $nonDefaultProductsList = $nonDefaultProductsList->paginate($pageSize,$columns = ['*'],$pageName = 'page',$pageNo);
        else
        {
            $filteredProducts1 = $nonDefaultProductsList->get();
            // $totalCount = count($filteredProducts1);
        }
        // dd($totalCount);
        return $filteredProducts1;
    }

    public function getSearchedProducts($searchVal,$langId,$custId,$pageSize,$pageNo,$skip,$request)
    {
        $selling_price = "product_pricing.selling_price as price";
        if(!empty($attribute_ids))
        {
            $selling_price = "pp.selling_price as price";
        }

        $offer_price = "product_pricing.offer_price as discountedPrice";
        if(!empty($attribute_ids))
        {
            $offer_price = "pp.offer_price as discountedPrice";
        }
        $offer_start_date = "product_pricing.offer_start_date as offer_start_date";
        if(!empty($attribute_ids))
        {
            $offer_start_date = "pp.offer_start_date as offer_start_date";
        }

        $offer_end_date = "product_pricing.offer_end_date as offer_end_date";
        if(!empty($attribute_ids))
        {
            $offer_end_date = "pp.offer_end_date as offer_end_date";
        }
        $categoryIds = $brandIds = $priceArr = $attribute_ids = [];
        if(!empty($request['filterQuery']))
        {
            foreach($request['filterQuery'] as $filter)
            {
                if(!empty($filter))
                {
                    if($filter['attribute_id'] == "Category")
                    {
                        $categoryIds = explode(",",$filter['option_id']);
                        $categoryIds =(str_replace('"','', $categoryIds));
                        $categoryIds = str_replace('[','', $categoryIds);
                        $categoryIds = str_replace(']','', $categoryIds);
                    }
                    else if($filter['attribute_id'] == "Brands")
                    {
                        $brandIds = explode(",",$filter['option_id']);
                        $brandIds =(str_replace('"','', $brandIds));
                        $brandIds = str_replace('[','', $brandIds);
                        $brandIds = str_replace(']','', $brandIds);
                    }
                    else if($filter['attribute_id'] == "Price")
                    {
                        $priceArr = $filter['option_id'];
                    }
                    else
                    {
                        $attribute_ids[$filter['attribute_id']] = explode(",",$filter['option_id']);
                        $attribute_ids[$filter['attribute_id']] =(str_replace('"','', $attribute_ids[$filter['attribute_id']]));
                        $attribute_ids[$filter['attribute_id']] = str_replace('[','', $attribute_ids[$filter['attribute_id']]);
                        $attribute_ids[$filter['attribute_id']] = str_replace(']','', $attribute_ids[$filter['attribute_id']]);
                    }
                }
            }
        }
        // dd($brandIds);
        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }
        DB::enableQueryLog();
        $filteredProducts = Product::select('products.id','pd.title',DB::raw($selling_price),DB::raw($offer_price),'images.name as image','product_slug',DB::raw($offer_end_date),DB::raw($offer_start_date),DB::raw($group_price),
                            DB::raw('IF(cgp.price IS NOT NULL, cgp.price, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, product_pricing.offer_price, product_pricing.selling_price)) as productPrice,
                            IF(cgp.price IS NOT NULL, 1, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, 2, 3)) as productSale
                            '),'c.photo_upload','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')

                                ->leftJoin('images',function ($join) {
                                    $join->on('images.imageable_id', '=' , 'products.id');
                                    $join->where('images.image_type','=','product');
                                    $join->whereNull('images.deleted_at');
                                    $join->where('images.is_default','=','yes');
                                })
                                ->leftJoin('categories as c','c.id','=','category_id')
                                ->leftJoin('product_details as pd', function($join) use($langId) {
                                    $join->on('pd.product_id', '=' , 'products.id');
                                    $join->where('pd.language_id','=',$langId);
                                    $join->whereNull('pd.deleted_at');
                                })
                                ->leftJoin('category_details as cd', function($join) use($langId) {
                                    $join->on('cd.category_id', '=' , 'c.id');
                                    $join->where('cd.language_id','=',$langId);
                                    $join->whereNull('cd.deleted_at');
                                })
                                ->join('product_pricing', function($join) {
                                    $join->on('product_pricing.product_id', '=' , 'products.id');
                                    $join->where('product_pricing.is_default','=',1);
                                    $join->whereNull('product_pricing.deleted_at');
                                })
                                ->whereRaw( "(pd.title like ? OR cd.title like ? OR product_pricing.sku like ? )", array('%'.$searchVal.'%','%'.$searchVal.'%','%'.$searchVal.'%'))
                                ->whereNull('products.deleted_at')
                                ->where('products.status','Active')
                                ->whereNull('c.deleted_at')
                                ->where('c.status','=',1)
                                ->orderBy('products.created_at','desc');
                                if(!empty($skip))
                                {
                                    $filteredProducts = $filteredProducts->whereNotIn('products.id',json_decode(stripslashes($skip)));
                                }
                                if(!empty($custId) && $custId->cust_group_id != 0)
                                {
                                    $filteredProducts = $filteredProducts->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                $join->on('cgp.product_id', '=' , 'products.id');
                                                                $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                    });
                                }
                                else
                                {
                                    $filteredProducts = $filteredProducts->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                        $join->on('cgp.product_id', '=' , 'products.id');
                                        $join->where('cgp.customer_group_id','=',0);
                                    });
                                }
                                if(!empty($request['sortBy']))
                                {
                                    if($request['sortBy'] == '' || $request['sortBy'] == 1 || $request['sortBy'] == null)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('products.created_at','desc');
                                    }
                                    if($request['sortBy'] == 2)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productSale','asc');
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','asc');
                                    }
                                    if($request['sortBy'] == 3)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','asc');
                                    }
                                    if($request['sortBy'] == 4)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','desc');
                                    }
                                }

                                // if(!empty($categoryIds) && $categoryIds[0] != "")
                                // {
                                //     $filteredProducts = $filteredProducts->whereIn('products.category_id',$categoryIds);
                                // }
                                // if(!empty($priceArr))
                                // {
                                //     foreach($priceArr as $price)
                                //     {
                                //         $min = round($price['min']);
                                //         $max = round($price['max']);
                                //         if(!empty($custId) && $custId->cust_group_id != 0)
                                //         {
                                //             $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                //             OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                //         }
                                //         else
                                //         {
                                //             $filteredProducts = $filteredProducts->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                //             OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                //         }

                                //     }
                                // }
                                // if(!empty($attribute_ids))
                                // {
                                //     $filteredProducts = $filteredProducts->leftJoin('product_pricing as pp', function($join) {
                                //         $join->on('pp.product_id', '=' , 'products.id');
                                //     });
                                //     foreach($attribute_ids as $key => $attributeIds)
                                //     {
                                //         if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                //         {
                                //             $filteredProducts = $filteredProducts->whereRaw("(FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                //         }
                                //         $numItems = count($attributeIds);
                                //         foreach($attributeIds as $key => $attributeId)
                                //         {
                                //             if(!empty($attributeId) && $attributeId != '')
                                //             {
                                //                 if((count($attributeIds) == 1 && $key == 0))
                                //                 {
                                //                     $filteredProducts = $filteredProducts->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                //                 }
                                //                 else
                                //                 {
                                //                     if($key == 0)
                                //                         $filteredProducts = $filteredProducts->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                //                     if(++$key == $numItems)
                                //                         $filteredProducts = $filteredProducts->orWhereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                //                 }
                                //             }
                                //         }
                                //     }
                                // }
                                // if(!empty($brandIds))
                                // {
                                //     $filteredProducts = $filteredProducts->whereIn('products.manufacturer_id',$brandIds);
                                // }
        if($pageSize != 0 && $pageNo!=0)
            $filteredProducts = $filteredProducts->paginate($pageSize,$columns = ['*'],$pageName = 'page',$pageNo);
        else
        {
            $filteredProducts1 = $filteredProducts->get();
            $totalFilteredProducts = count($filteredProducts1);
            $filteredProducts = $filteredProducts->limit(12)->groupBy('products.id')->get()->toArray();
            // $filteredProducts = $filteredProducts->get();
        }

        // dd(DB::getQueryLog());
        // dd($filteredProducts);
        $resultArr = [];
        $i = 0;
        foreach($filteredProducts as $nonDefaultProducts)
        {
            // print_r($nonDefaultProducts);
            $prodTitle = $nonDefaultProducts['title'];
            if($prodTitle == null)
            {
                $prodDetails = ProductDetails::select('title')
                                            ->where('product_id',$nonDefaultProducts['id'])
                                            ->where('language_id',$defaultLanguageId)
                                            ->whereNull('deleted_at')
                                            ->first();
                $prodTitle =  $prodDetails['title'];
            }
            $resultArr[$i]['id'] = "".$nonDefaultProducts['id']."";
            $resultArr[$i]['title'] = $prodTitle;

            if(empty($nonDefaultProducts['group_price']))
            {
                if (!empty($nonDefaultProducts['discountedPrice']) && (date("Y-m-d", strtotime($nonDefaultProducts['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d", strtotime($nonDefaultProducts['offer_end_date']))))
                {
                    $resultArr[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                    $resultArr[$i]['group_price'] = '';
                }
                else if (!empty($nonDefaultProducts['discountedPrice']))
                {
                    $resultArr[$i]['discountedPrice'] = $nonDefaultProducts['discountedPrice'];
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                    $resultArr[$i]['group_price'] = '';
                }
                else
                {
                    $resultArr[$i]['group_price'] = '';
                    $resultArr[$i]['discountedPrice'] = '';
                    $resultArr[$i]['price'] = $nonDefaultProducts['price'];
                }
            }
            else
            {
                $resultArr[$i]['group_price'] = $nonDefaultProducts['group_price'];
                $resultArr[$i]['discountedPrice'] = '';
                $resultArr[$i]['price'] = $nonDefaultProducts['price'];
            }
            if($nonDefaultProducts['photo_upload'] != 0)
                $resultArr[$i]['flagInstock'] = 1;
            else
            {
                if(empty($nonDefaultProducts['attribute_group_ids']) && empty($nonDefaultProducts['attribute_ids']))
                {
                    if($nonDefaultProducts['quantity'] > 0)
                        $resultArr[$i]['flagInstock'] = 1;
                    else
                        $resultArr[$i]['flagInstock'] = 0;
                }
                else
                {
                    foreach(explode(",",$nonDefaultProducts['attribute_group_ids']) as $attrGroupId)
                    {
                        $attrGroup = AttributeGroup::find($attrGroupId);
                        if(!empty($attrGroup['status']))
                        {
                            if($attrGroup['status'] == 0 || !empty($attrGroup['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($nonDefaultProducts['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }
                    }
                    foreach(explode(",",$nonDefaultProducts['attribute_ids']) as $attrId)
                    {
                        $attribute = Attribute::find($attrId);
                        if(!empty($attribute['status']))
                        {
                            if($attribute['status'] == 0 || !empty($attribute['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($nonDefaultProducts['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }

                    }
                }
            }
            $resultArr[$i]['slug'] = $nonDefaultProducts['product_slug'];
            $resultArr[$i]['offer_start_date'] = $nonDefaultProducts['offer_start_date'];
            $resultArr[$i]['offer_end_date'] = $nonDefaultProducts['offer_end_date'];
            $resultArr[$i++]['image'] = $nonDefaultProducts['image'];
        }
        // die;
        // dd($resultArr);
        return $resultArr;
        // return $filteredProducts;
    }

    public function getSearchProductsCount($searchVal,$langId,$custId,$pageSize,$pageNo,$skip,$request)
    {
        $selling_price = "product_pricing.selling_price as price";
        if(!empty($attribute_ids))
        {
            $selling_price = "pp.selling_price as price";
        }

        $offer_price = "product_pricing.offer_price as discountedPrice";
        if(!empty($attribute_ids))
        {
            $offer_price = "pp.offer_price as discountedPrice";
        }
        $offer_start_date = "product_pricing.offer_start_date as offer_start_date";
        if(!empty($attribute_ids))
        {
            $offer_start_date = "pp.offer_start_date as offer_start_date";
        }

        $offer_end_date = "product_pricing.offer_end_date as offer_end_date";
        if(!empty($attribute_ids))
        {
            $offer_end_date = "pp.offer_end_date as offer_end_date";
        }
        if(!empty($request['filterQuery']))
        {
            foreach($request['filterQuery'] as $filter)
            {
                if(!empty($filter))
                {
                    if($filter['attribute_id'] == "Category")
                    {
                        $categoryIds = explode(",",$filter['option_id']);
                        $categoryIds =(str_replace('"','', $categoryIds));
                        $categoryIds = str_replace('[','', $categoryIds);
                        $categoryIds = str_replace(']','', $categoryIds);
                    }
                    else if($filter['attribute_id'] == "Brands")
                    {
                        $brandIds = explode(",",$filter['option_id']);
                        $brandIds =(str_replace('"','', $brandIds));
                        $brandIds = str_replace('[','', $brandIds);
                        $brandIds = str_replace(']','', $brandIds);
                    }
                    else if($filter['attribute_id'] == "Price")
                    {
                        $priceArr = $filter['option_id'];
                    }
                    else
                    {
                        $attribute_ids[$filter['attribute_id']] = explode(",",$filter['option_id']);
                        $attribute_ids[$filter['attribute_id']] =(str_replace('"','', $attribute_ids[$filter['attribute_id']]));
                        $attribute_ids[$filter['attribute_id']] = str_replace('[','', $attribute_ids[$filter['attribute_id']]);
                        $attribute_ids[$filter['attribute_id']] = str_replace(']','', $attribute_ids[$filter['attribute_id']]);
                    }
                }
            }
        }
        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }
        DB::enableQueryLog();
        $filteredProducts = Product::select('products.id','pd.title',DB::raw($selling_price),DB::raw($offer_price),'images.name as image','product_slug',DB::raw($offer_end_date),DB::raw($offer_start_date),DB::raw($group_price),
                            DB::raw('IF(cgp.price IS NOT NULL, cgp.price, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, product_pricing.offer_price, product_pricing.selling_price)) as productPrice,
                            IF(cgp.price IS NOT NULL, 1, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, 2, 3)) as productSale
                            '),'c.photo_upload','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')

                                ->leftJoin('images',function ($join) {
                                    $join->on('images.imageable_id', '=' , 'products.id');
                                    $join->where('images.image_type','=','product');
                                    $join->whereNull('images.deleted_at');
                                    $join->where('images.is_default','=','yes');
                                })
                                ->leftJoin('categories as c','c.id','=','category_id')
                                ->leftJoin('product_details as pd', function($join) use($langId) {
                                    $join->on('pd.product_id', '=' , 'products.id');
                                    $join->where('pd.language_id','=',$langId);
                                    $join->whereNull('pd.deleted_at');
                                })
                                ->leftJoin('category_details as cd', function($join) use($langId) {
                                    $join->on('cd.category_id', '=' , 'c.id');
                                    $join->where('cd.language_id','=',$langId);
                                    $join->whereNull('cd.deleted_at');
                                })
                                ->join('product_pricing', function($join) {
                                    $join->on('product_pricing.product_id', '=' , 'products.id');
                                    $join->where('product_pricing.is_default','=',1);
                                    $join->whereNull('product_pricing.deleted_at');
                                })
                                ->whereRaw( "(pd.title like ? OR cd.title like ? OR product_pricing.sku like ? )", array('%'.$searchVal.'%','%'.$searchVal.'%','%'.$searchVal.'%'))
                                ->whereNull('products.deleted_at')
                                ->where('products.status','Active')
                                ->whereNull('c.deleted_at')
                                ->where('c.status','=',1)
                                ->orderBy('products.created_at','desc');
                                if(!empty($skip))
                                {
                                    $filteredProducts = $filteredProducts->whereNotIn('products.id',json_decode(stripslashes($skip)));
                                }
                                if(!empty($custId) && $custId->cust_group_id != 0)
                                {
                                    $filteredProducts = $filteredProducts->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                $join->on('cgp.product_id', '=' , 'products.id');
                                                                $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                    });
                                }
                                else
                                {
                                    $filteredProducts = $filteredProducts->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                        $join->on('cgp.product_id', '=' , 'products.id');
                                        $join->where('cgp.customer_group_id','=',0);
                                    });
                                }
                                if(!empty($request['sortBy']))
                                {
                                    if($request['sortBy'] == '' || $request['sortBy'] == 1 || $request['sortBy'] == null)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('products.created_at','desc');
                                    }
                                    if($request['sortBy'] == 2)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productSale','asc');
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','asc');
                                    }
                                    if($request['sortBy'] == 3)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','asc');
                                    }
                                    if($request['sortBy'] == 4)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','desc');
                                    }
                                }

                                if(!empty($categoryIds) && $categoryIds[0] != "")
                                {
                                    $filteredProducts = $filteredProducts->whereIn('products.category_id',$categoryIds);
                                }
                                if(!empty($priceArr))
                                {
                                    foreach($priceArr as $price)
                                    {
                                        $min = round($price['min']);
                                        $max = round($price['max']);
                                        if(!empty($custId) && $custId->cust_group_id != 0)
                                        {
                                            $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                            OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                        }
                                        else
                                        {
                                            $filteredProducts = $filteredProducts->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                            OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                        }

                                    }
                                }
                                if(!empty($attribute_ids))
                                {
                                    $filteredProducts = $filteredProducts->leftJoin('product_pricing as pp', function($join) {
                                        $join->on('pp.product_id', '=' , 'products.id');
                                    });
                                    foreach($attribute_ids as $key => $attributeIds)
                                    {
                                        if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                        {
                                            $filteredProducts = $filteredProducts->whereRaw("(FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                        }
                                        $numItems = count($attributeIds);
                                        foreach($attributeIds as $key => $attributeId)
                                        {
                                            if(!empty($attributeId) && $attributeId != '')
                                            {
                                                if((count($attributeIds) == 1 && $key == 0))
                                                {
                                                    $filteredProducts = $filteredProducts->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                }
                                                else
                                                {
                                                    if($key == 0)
                                                        $filteredProducts = $filteredProducts->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                    if(++$key == $numItems)
                                                        $filteredProducts = $filteredProducts->orWhereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                }
                                            }
                                        }
                                    }
                                }
                                if(!empty($brandIds))
                                {
                                    $filteredProducts = $filteredProducts->whereIn('products.manufacturer_id',$brandIds);
                                }
        if($pageSize != 0 && $pageNo!=0)
            $filteredProducts = $filteredProducts->paginate($pageSize,$columns = ['*'],$pageName = 'page',$pageNo);
        else
        {
            $filteredProducts1 = $filteredProducts->get();
            // $totalFilteredProducts = count($filteredProducts1);
            // $filteredProducts = $filteredProducts->get();
        }

        // dd(DB::getQueryLog());
        // dd($filteredProducts);

        return $filteredProducts1;
    }

    // copy product functions (Pallavi : july 29, 2021)
    public function copyProductTableData($originalProd)
    {
        if(!empty($originalProd))
        {
            $newProd = new Product;
            $newProd->category_id = $originalProd->category_id;
            $newProd->manufacturer_id = $originalProd->manufacturer_id;
            $newProd->sku = $originalProd->sku;
            $newProd->printing_product = $originalProd->printing_product;
            $newProd->low_stock_alert = $originalProd->low_stock_alert;
            $newProd->low_stock_alert_quantity = $originalProd->low_stock_alert_quantity;
            $newProd->max_order_quantity = $originalProd->max_order_quantity;
            $newProd->can_giftwrap = $originalProd->can_giftwrap;
            $newProd->length = $originalProd->length;
            $newProd->width = $originalProd->width;
            $newProd->height = $originalProd->height;
            $newProd->weight = $originalProd->weight;
            $newProd->image_min_height = $originalProd->image_min_height;
            $newProd->image_min_width = $originalProd->image_min_width;
            $newProd->image_max_height = $originalProd->image_max_height;
            $newProd->image_max_width = $originalProd->image_max_width;
            $newProd->max_images = $originalProd->max_images;
            $newProd->status = $originalProd->status;
            $newProd->is_approved = $originalProd->is_approved;
            $newProd->out_of_stock = $originalProd->out_of_stock;
            $newProd->is_customized = $originalProd->is_customized;
            $newProd->flag_deliverydate = $originalProd->flag_deliverydate;
            $newProd->tax_class_id = $originalProd->tax_class_id;
            $newProd->flexmedia_code = $originalProd->flexmedia_code;
            $newProd->design_tool_product_id = $originalProd->design_tool_product_id;
            $newProd->created_at = Carbon::now();
            $newProd->save();

            $updateSlug = Product::findOrFail($newProd->id);
            $updateSlug->product_slug = $originalProd->product_slug.'-'.$newProd->id;
            $updateSlug->save();

            return $newProd;
        }
    }

    public function copyProdDetailsTable($originalProdDetails,$newProdId)
    {
        if(!empty($originalProdDetails))
        {
            $newProdDetails = new ProductDetails;
            $newProdDetails->product_id = $newProdId;
            $newProdDetails->language_id = $originalProdDetails->language_id;
            $newProdDetails->title = 'Copy of '.$originalProdDetails->title;
            $newProdDetails->subtitle = $originalProdDetails->subtitle;
            $newProdDetails->description = $originalProdDetails->description;
            $newProdDetails->key_features = $originalProdDetails->key_features;
            $newProdDetails->meta_title = $originalProdDetails->meta_title;
            $newProdDetails->meta_description = $originalProdDetails->meta_description;
            $newProdDetails->meta_keyword = $originalProdDetails->meta_keyword;
            $newProdDetails->in_the_box = $originalProdDetails->in_the_box;
            $newProdDetails->product_type = $originalProdDetails->product_type;
            $newProdDetails->created_at = Carbon::now();
            $newProdDetails->save();
            return $newProdDetails;
        }
    }

    public function copyProductPricingTable($originalProdPricingDetails,$newProdId)
    {
        if(!($originalProdPricingDetails->isEmpty()))
        {
            $i = 0;
            foreach($originalProdPricingDetails as $originalPricing)
            {
                $newProdPricing = new ProductPricing;
                $newProdPricing->product_id = $newProdId;
                $newProdPricing->category_id = $originalPricing->category_id;
                $newProdPricing->attribute_group_ids = $originalPricing->attribute_group_ids;
                $newProdPricing->attribute_ids = $originalPricing->attribute_ids;
                $newProdPricing->sku = $originalPricing->sku;
                $newProdPricing->mrp = $originalPricing->mrp;
                $newProdPricing->selling_price = $originalPricing->selling_price;
                $newProdPricing->offer_price = $originalPricing->offer_price;
                $newProdPricing->offer_start_date = $originalPricing->offer_start_date;
                $newProdPricing->offer_end_date = $originalPricing->offer_end_date;
                $newProdPricing->quantity = $originalPricing->quantity;

                $path = public_path('/images/product/'.$newProdId.'/pricingoption/');
                if(!\File::isDirectory($path))
                {
                    \File::makeDirectory($path, 0777, true, true);
                }

                $file = '';
                if($originalPricing->image != null)
                    $file = base_path('/public/images/product/'.$originalPricing->product_id.'/pricingoption/'.$originalPricing->image);

                if(file_exists($file))
                {
                    $success = \File::copy(base_path('/public/images/product/'.$originalPricing->product_id.'/pricingoption/'.$originalPricing->image),base_path('public/images/product/'.$newProdId.'/pricingoption/'.$originalPricing->image));
                }
                $newProdPricing->image = $originalPricing->image;

                $newProdPricing->is_default = $originalPricing->is_default;
                $newProdPricing->created_at = Carbon::now();
                $newProdPricing->save();

                $newPricingId[$i++] = $newProdPricing->id;
            }
            return $newPricingId;
        }
    }

    public function copyRecommendedProdTable($originalRecommProdDetails,$newProdId)
    {
        if(!($originalRecommProdDetails->isEmpty()))
        {
            foreach($originalRecommProdDetails as $origianlRecommProd)
            {
                $newRecommProd = new RecommendedProduct;
                $newRecommProd->product_id = $newProdId;
                $newRecommProd->recommended_id = $origianlRecommProd->recommended_id;
                $newRecommProd->created_at = Carbon::now();
                $newRecommProd->save();
            }
            return true;
        }
    }

    public function copyRelatedProdTable($originalRelatedProdDetails,$newProdId)
    {
        if(!($originalRelatedProdDetails->isEmpty()))
        {
            foreach($originalRelatedProdDetails as $originalRelatedProd)
            {
                $newRelatedProd = new RelatedProduct;
                $newRelatedProd->product_id = $newProdId;
                $newRelatedProd->related_id = $originalRelatedProd->related_id;
                $newRelatedProd->save();
            }
            return true;
        }

    }

    public function copyBulkPricingTable($originalBulkPricing,$newProdId,$optionIds)
    {
        if(!($originalBulkPricing->isEmpty()))
        {
            foreach($originalBulkPricing as $originalBulkPrice)
            {
                $originalOptionIds[] = $originalBulkPrice->option_id;
            }

            $originalUniqueOptionsIds = array_unique($originalOptionIds);
            $i = 0;

            foreach($originalUniqueOptionsIds as $originalOptionId)
            {
                $optionIdData = ProductBulkPrices::where('option_id',$originalOptionId)->whereNull('deleted_at')->get();

                if(!empty($optionIdData))
                {
                    foreach($optionIdData as $data)
                    {
                        if(!empty($optionIds[$i]))
                        {
                            $newBulkPrice = new ProductBulkPrices;
                            $newBulkPrice->product_id = $newProdId;
                            $newBulkPrice->option_id = $optionIds[$i];
                            $newBulkPrice->from_quantity = $data->from_quantity;
                            $newBulkPrice->to_quantity = $data->to_quantity;
                            $newBulkPrice->price = $data->price;
                            $newBulkPrice->created_at = Carbon::now();
                            $newBulkPrice->save();
                        }
                    }
                }
                $i++;
            }
            return true;
        }
    }

    public function copyProductImages($originalProdImages,$newProdId)
    {
        if(!($originalProdImages->isEmpty()))
        {
            foreach($originalProdImages as $originalImg)
            {
                $newImage = new \App\Models\Image;
                $newImage->name = $originalImg->name;
                $newImage->small_image = $originalImg->small_image;
                $newImage->thumb_image = $originalImg->thumb_image;
                $newImage->original_filename = $originalImg->original_filename;
                $newImage->label = $originalImg->label;
                $newImage->upload_path = '/images/product/'.$newProdId.'/';
                $newImage->is_visible = $originalImg->is_visible;
                $newImage->is_default = $originalImg->is_default;
                $newImage->is_primary_main = $originalImg->is_primary_main;
                $newImage->is_primary_small = $originalImg->is_primary_small;
                $newImage->is_primary_thumb = $originalImg->is_primary_thumb;
                $newImage->mime = $originalImg->mime;
                $newImage->sort_order = $originalImg->sort_order;
                $newImage->imageable_id = $newProdId;
                $newImage->imageable_type = $originalImg->imageable_type;
                $newImage->tags = $originalImg->tags;
                $newImage->description = $originalImg->description;
                $newImage->image_type = $originalImg->image_type;
                $newImage->created_at = Carbon::now();
                $newImage->save();

                $path = public_path('/images/product/'.$newProdId);
                if(!\File::isDirectory($path))
                {
                    \File::makeDirectory($path, 0777, true, true);
                }

                $file = base_path('public/images/product/'.$originalImg->imageable_id.'/'.$originalImg->original_filename);
                if(file_exists($file))
                {
                    copy($file,base_path('public/images/product/'.$newProdId.'/'.$originalImg->original_filename));
                }
            }
        }

    }
}
