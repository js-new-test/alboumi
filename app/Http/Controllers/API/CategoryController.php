<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\MegaMenu;
use App\Models\CmsPages;
use App\Models\Category;
use App\Models\Customer;
use App\Models\GlobalLanguage;
use App\Models\GlobalCurrency;
use App\Models\Product;
use App\Models\Events;
use App\Models\Package;
use App\Models\Photographers;
use App\Models\ProductPricing;
use App\Models\CategoryDetails;
use App\Models\AttributeDetails;
use App\Models\Attribute;
use DB;
use Auth;

class CategoryController extends Controller
{
    protected $megamenu;
    protected $category;
    protected $events;
    protected $photographer;
    protected $product;

	public function __construct(MegaMenu $megamenu, Category $category,Events $events,Photographers $photographer,Product $product) {
        $this->megamenu = $megamenu;
        $this->category = $category;
        $this->photographer = $photographer;
        $this->events = $events;
        $this->product = $product;
    }

    public function getCategoryList(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'statusCode' => "300",
            'message' => $validator->errors(),
            ],300);
        }

        $langId = $request->language_id;
        $arrMegaMenu = $this->megamenu->getAllMegaMenus();

        $cnt = 0;
        $baseUrl = $this->getBaseUrl();

        $categoryDetails = $productDetails = [];
        $eventDetails = $photoDetails = $moreTabData = $topCategoryDetails =[];

        $codes = ['MORE','EVENTOCCASIONS','BAHRAINPHOTOGRAPHER'];
        $labels = getCodesMsg($request->language_id, $codes);
        if(!empty($arrMegaMenu))
        {
            $m = $p = 0;
            foreach($arrMegaMenu as $k => $menuData)
            {
                $type = $menuData->type;
                $related_id = $menuData->name;

                $cmsPages = CmsPages::getCmsPagesForMobile($related_id,$langId);
                // print_r($cmsPages);
                $arrPhotographers = Photographers::getPhotographers($langId);
                $arrEvents = Events::getEvents($langId);

                $cnt += 1;
                // First 8 tabs
                if($cnt <= 9)
                {
                    // CMS - event & photographers
                    if($type == 0)
                    {
                        if(!empty($cmsPages))
                        {
                            if($cmsPages[0]['slug'] != 'events-occasions' && $cmsPages[0]['slug'] != 'made-in-bahrain')
                            {
                                continue;
                            }
                            if($cmsPages[0]['slug'] == 'events-occasions')
                            {
                                $eventDetails['componentId'] = 'categoryTab';
                                $eventDetails['sequenceId'] = '1';
                                $eventDetails['isActive'] = '1';

                                $categoryTabData = $this->events->createEventsData($arrEvents,$menuData,$baseUrl,$cmsPages,$langId);
                                $eventDetails['categoryTabData'] = $categoryTabData;
                            }
                            if($cmsPages[0]['slug'] == 'made-in-bahrain')
                            {
                                $photoDetails['componentId'] = 'categoryTab';
                                $photoDetails['sequenceId'] = '1';
                                $photoDetails['isActive'] = '1';

                                $categoryTabData = $this->photographer->createPhotographerData($arrPhotographers,$menuData,$baseUrl,$cmsPages,$langId);
                                $photoDetails['categoryTabData'] = $categoryTabData;
                            }
                        }
                    }
                    //categories
                    if($type == 1)
                    {
                        $arrTopCategories = Category::getTopCategories($related_id,$langId);

                        if(count($arrTopCategories) > 0)
                        {
                            // Category page but having sub categories
                            if(($arrTopCategories[0]['flag_product'] == '0'))
                            {
                                $arrSubCategories = Category::getChildCategories($arrTopCategories[0]['id'],$langId);
                                // print_r($arrSubCategories);
                                $subCatcounts = count($arrSubCategories);

                                if($subCatcounts > 0)
                                {
                                    $categoryDetails[$p]['componentId'] = 'categoryTab';
                                    $categoryDetails[$p]['sequenceId'] = '1';
                                    $categoryDetails[$p]['isActive'] = '1';

                                    $categoryTabData = $this->category->createCategoryData($arrTopCategories,$arrSubCategories,$baseUrl,$cmsPages,$langId);
                                    $categoryDetails[$p++]['categoryTabData'] = $categoryTabData;
                                }
                                else
                                {
                                    $topCategoryDetails[$m]['componentId'] = 'categoryTab';
                                    $topCategoryDetails[$m]['sequenceId'] = '1';
                                    $topCategoryDetails[$m]['isActive'] = '1';

                                    $topCategoryTabData = $this->category->createCategoryData($arrTopCategories,'',$baseUrl,$cmsPages,$langId);
                                    $topCategoryDetails[$m++]['categoryTabData'] = $topCategoryTabData;
                                }
                            }

                             // Category page but having products
                            if(($arrTopCategories[0]['flag_product'] == '1'))
                            {
                                $topCategoryDetails[$m]['componentId'] = 'categoryTab';
                                $topCategoryDetails[$m]['sequenceId'] = '1';
                                $topCategoryDetails[$m]['isActive'] = '1';

                                $topCategoryTabData = $this->category->createCategoryData($arrTopCategories,'',$baseUrl,$cmsPages,$langId);
                                $topCategoryDetails[$m++]['categoryTabData'] = $topCategoryTabData;

                                // $arrProducts = $this->product->getProductsFromCategory($arrTopCategories[0]['id'],$langId,'');
                                // // Total
                                // $productscount = count($arrProducts);

                                // // Sub Menu
                                // if($productscount > 0)
                                // {
                                //     if(!empty($arrProducts))
                                //     {
                                //         $productDetails['componentId'] = 'categoryTab';
                                //         $productDetails['sequenceId'] = '1';
                                //         $productDetails['isActive'] = '1';

                                //         $categoryTabData = $this->product->createProductData($arrTopCategories,$arrProducts,$baseUrl,$cmsPages,$langId);
                                //         $productDetails['categoryTabData'] = $categoryTabData;
                                //     }
                                // }

                            }
                        }
                    }
                }

                else
                {
                    if($cnt == 10)
                    {
                        $moreTabData['componentId'] = 'categoryTab';
                        $moreTabData['sequenceId'] = '1';
                        $moreTabData['isActive'] = '1';

                        $level1Data['title'] = $labels['MORE'];
                        $level1Data['categoryId'] = '';
                        if($menuData['icon_image'] != null)
                            $level1Data['backgroundImage'] = $baseUrl.'/public/assets/images/megamenu/icon/'.$menuData['icon_image'];
                        else
                            $level1Data['backgroundImage'] = $baseUrl.'/public/assets/frontend/img/more/8Camera.jpg';

                        $level1Data['type'] = '';
                    }

                    if(!empty($cmsPages))
                    {
                        if($type == 0)
                        {
                            if($cmsPages[0]['slug'] != 'events-occasions' && $cmsPages[0]['slug'] != 'made-in-bahrain')
                            {
                                continue;
                            }

                            if($cmsPages[0]['slug'] == 'events-occasions')
                            {
                                $level2Data = [];
                                $level2Data['title'] = $labels['EVENTOCCASIONS'];;
                                $level2Data['type'] = "";

                                $level3Data = $this->events->createEventsData($arrEvents,$menuData,$baseUrl,$cmsPages,$langId);
                                if(!empty($level3Data))
                                {
                                    $level2Data['navigationFlag'] = "0";
                                    $level2Data['query'] = "";
                                }
                                else
                                {
                                    $level2Data['navigationFlag'] = "1";
                                    $level2Data['query'] = "";
                                }

                                $level1Data['subTitle'] = $level3Data['subTitle'];
                                $level2Data['level3'] = $level3Data['level2'];

                                if(!empty($level2Data))
                                {
                                    $level1Data['navigationFlag'] = "0";
                                    $level1Data['query'] = '';
                                }
                                else
                                {
                                    $level1Data['navigationFlag'] = "1";
                                    $level1Data['query'] = "";
                                }

                                $level1Data['level2'][] = $level2Data;
                                $moreTabData['categoryTabData'] = $level1Data;
                            }

                            if($cmsPages[0]['slug'] == 'made-in-bahrain')
                            {
                                $level2Data = [];
                                $level2Data['title'] =$labels['BAHRAINPHOTOGRAPHER'];
                                $level2Data['type'] = "";

                                $level3Data = $this->photographer->createPhotographerData($arrPhotographers,$menuData,$baseUrl,$cmsPages,$langId);
                                if(!empty($level3Data))
                                {
                                    $level2Data['navigationFlag'] = "0";
                                    $level2Data['query'] = "";
                                }
                                else
                                {
                                    $level2Data['navigationFlag'] = "1";
                                    $level2Data['query'] = "";
                                }

                                $level1Data['subTitle'] = $level3Data['subTitle'];
                                $level2Data['level3'] = $level3Data['level2'];

                                if(!empty($level2Data))
                                {
                                    $level1Data['navigationFlag'] = "0";
                                    $level1Data['query'] = '';
                                }
                                else
                                {
                                    $level1Data['navigationFlag'] = "1";
                                    $level1Data['query'] = "";
                                }
                                $level1Data['level2'][] = $level2Data;
                                $moreTabData['categoryTabData'] = $level1Data;

                            }
                        }
                    }
                    //categories
                    if($type == 1)
                    {
                        $arrTopCategories = Category::getTopCategories($related_id,$langId);
                        if(count($arrTopCategories) > 0)
                        {
                            // Category page but having sub categories
                            if(($arrTopCategories[0]['flag_product'] == '0'))
                            {
                                $arrSubCategories = Category::getChildCategories($arrTopCategories[0]['id'],$langId);
                                $subCatcounts = count($arrSubCategories);

                                if($subCatcounts > 0)
                                {
                                    $level2Data = [];

                                    $level2Data['id'] = "".$arrTopCategories[0]['id']."";
                                    $level2Data['title'] = $arrTopCategories[0]['title'];
                                    $level2Data['type'] = '1';

                                    $level3Data = $this->category->createCategoryData($arrTopCategories,$arrSubCategories,$baseUrl,$cmsPages,$langId,$langId);

                                    if(!empty($level3Data['level2']))
                                    {
                                        $level2Data['navigationFlag'] = "0";
                                        $level2Data['query'] = "";
                                    }
                                    else
                                    {
                                        $level2Data['navigationFlag'] = "1";
                                        $level2Data['query'] = $baseUrl."/api/v1/getProductList?language_id=".$langId.'&category_id='.$arrTopCategories[0]['id'];
                                    }

                                    $level1Data['subTitle'] = $level3Data['subTitle'];
                                    $level2Data['level3'] = $level3Data['level2'];

                                    if(!empty($level2Data))
                                    {
                                        $level1Data['navigationFlag'] = "0";
                                        $level1Data['query'] = '';
                                    }
                                    else
                                    {
                                        $level1Data['navigationFlag'] = "1";
                                        $level1Data['query'] = "";
                                    }

                                    $level1Data['level2'][] = $level2Data;
                                    $moreTabData['categoryTabData'] = $level1Data;
                                }
                                else
                                {
                                    $level2Data = [];

                                    $level2Data['id'] = "".$arrTopCategories[0]['id']."";
                                    $level2Data['title'] = $arrTopCategories[0]['title'];
                                    $level2Data['type'] = '1';
                                    $level2Data['navigationFlag'] = "1";

                                    $level3Data = $this->category->createCategoryData($arrTopCategories,'',$baseUrl,$cmsPages,$langId,$langId);

                                    if(!empty($level3Data['level2']))
                                    {
                                        $level2Data['navigationFlag'] = "0";
                                        $level2Data['query'] = "";
                                    }
                                    else
                                    {
                                        $level2Data['navigationFlag'] = "1";
                                        $level2Data['query'] = $baseUrl."/api/v1/getProductList?language_id=".$langId.'&category_id='.$arrTopCategories[0]['id'];
                                    }

                                    $level1Data['subTitle'] = isset($level3Data['subTitle']) ? $level3Data['subTitle'] : '';
                                    $level2Data['level3'] = isset($level3Data['level2']) ? $level3Data['level2'] : '';

                                    if(!empty($level2Data))
                                    {
                                        $level1Data['navigationFlag'] = "0";
                                        $level1Data['query'] = '';
                                    }
                                    else
                                    {
                                        $level1Data['navigationFlag'] = "1";
                                        $level1Data['query'] = "";
                                    }

                                    $level1Data['level2'][] = $level2Data;
                                    $moreTabData['categoryTabData'] = $level1Data;
                                }
                            }

                            // Category page but having products
                            if(($arrTopCategories[0]['flag_product'] == '1'))
                            {

                                $level2Data = $level3Data = [];
                                $level2Data['id'] = "".$arrTopCategories[0]['id']."";
                                $level2Data['title'] = $arrTopCategories[0]['title'];
                                $level2Data['type'] = '1';

                                $level2Data['navigationFlag'] = "1";
                                $level2Data['query'] = $baseUrl."/api/v1/getProductList?language_id=".$langId.'&category_id='.$arrTopCategories[0]['id'];
                                $level2Data['level3'] = $level3Data;
                                $level1Data['level2'][] = $level2Data;
                                $moreTabData['categoryTabData'] = $level1Data;


                                // $arrProducts = $this->product->getProductsFromCategory($arrTopCategories[0]['id'],$langId,'');
                                // // Total
                                // $productscount = count($arrProducts);

                                // Sub Menu
                                // if($productscount > 0)
                                // {
                                //     if(!empty($arrProducts))
                                //     {
                                //         $level2Data = [];

                                //         $level2Data['id'] = "".$arrTopCategories[0]['id']."";
                                //         $level2Data['title'] = $arrTopCategories[0]['title'];
                                //         $level2Data['type'] = '1';

                                //         $level3Data = $this->product->createProductData($arrTopCategories,$arrProducts,$baseUrl,$cmsPages,$langId);

                                //         if(!empty($level3Data))
                                //         {
                                //             $level2Data['navigationFlag'] = "0";
                                //             $level2Data['query'] = "";
                                //         }
                                //         else
                                //         {
                                //             $level2Data['navigationFlag'] = "1";
                                //             $level2Data['query'] = $baseUrl."/api/v1/getProductList?language_id=".$langId.'&category_id='.$arrTopCategories[0]['id'];
                                //         }

                                //         $level1Data['subTitle'] = isset($level3Data['subTitle']) ? $level3Data['subTitle'] : '';
                                //         $level2Data['level3'] = isset($level3Data['level2']) ? $level3Data['level2'] : '';

                                //         if(!empty($level2Data))
                                //         {
                                //             $level1Data['navigationFlag'] = "0";
                                //             $level1Data['query'] = '';
                                //         }
                                //         else
                                //         {
                                //             $level1Data['navigationFlag'] = "1";
                                //             $level1Data['query'] = "";
                                //         }
                                //         $level1Data['subTitle'] = $level3Data['subTitle'];
                                //         $level2Data['level3'] = $level3Data['level2'];
                                //         $level1Data['level2'][] = $level2Data;
                                //         $moreTabData['categoryTabData'] = $level1Data;
                                //     }
                                // }
                            }
                        }
                    }
                }
            }
        }
        // die;
        $result['status'] = "OK";
        $result['statusCode'] = 200;
        $result['message'] = "Success";
        $result['component'] = [];

        $topCat = [];
        if(!empty($categoryDetails))
            $result['component'] = $categoryDetails;

        foreach($topCategoryDetails as $topCategory)
        {
            $topCat = $topCategory;
            if(!empty($topCat))
                $result['component'][] = $topCat;
        }
        // dd($eventDetails);
        // die;
        if(!empty($eventDetails))
            $result['component'][] = $eventDetails;
        if(!empty($photoDetails))
            $result['component'][] = $photoDetails;
        if(!empty($productDetails))
            $result['component'][] = $productDetails;
        if(!empty($moreTabData))
            $result['component'][] = $moreTabData;
        if(!empty($photographerTabdata))
            $result['component'][] = $photographerTabdata;
        if(!empty($subCatTabdata))
            $result['component'][] = $subCatTabdata;
        if(!empty($productTabdata))
            $result['component'][] = $productTabdata;
        if(!empty($topCategoryData))
            $result['component'][] = $topCategoryData;
        return response()->json($result);

    }

    public function getFilterList(Request $request)
    {
        $custId = "";
        $pageSize = $pageNo = $skip = 0;

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
        if(Auth::guard('api')->user() != null)
        {
            $loggedInId = Auth::guard('api')->user()->token()->user_id;
            $custId = Customer::select('cust_group_id')->where('id',$loggedInId)->first();
        }

        $list = $categoryIds = $prices = $attribute_ids = $brands = $brandIds = $attrIds = [];
        $sortBy = null;

        $codes = ['CATEGORY','PRICE','BRAND'];
        $catLabels = getCodesMsg($request->language_id, $codes);

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
                                                    ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                                                    ->where('global_currency.is_deleted', 0)
                                                    ->where('global_currency.is_default', 1)
                                                    ->first();

        $currencyCode = getCurrSymBasedOnLangId($request->language_id);

        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$request->language_id)->first();
        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;

        if(!empty($request->category_id))
        {
            $childCatgories = Category::getChildCategories($request->category_id, $request->language_id);

            $productsList = [];
            $product = new Product;
            $productsList[] = $this->product->getProductsFromCategory($request->category_id, $request->language_id,$sortBy,$custId,$pageSize,$pageNo,$skip,'');
            $allProdOfSelectedCategory[] = $this->product->getAllProductsFromCategory($request->category_id, $request->language_id,$sortBy,$custId,$pageSize,$pageNo,$skip,'');
        }

        if(!empty($request->search_value))
        {
            $productsList[] = $this->product->getSearchedProducts($request->search_value, $request->language_id,$sortBy,$pageSize,$pageNo,'',$request);
        }
        // get prod ids
        foreach($productsList as $products)
        {
            foreach($products as $product)
            {
                $prodIds[] = $product['id'];
            }
        }

        if(!empty($request->filterQuery))
        {
            $productsList = $prices = $priceArr = [];
            $langId = $request->language_id;
            foreach($request->filterQuery as $filter)
            {
                if(!empty($filter))
                {
                    if($filter['attribute_id'] == "Category")
                    {
                        $categoryIds = explode("$",$filter['option_id']);
                    }
                    else if($filter['attribute_id'] == "Brands")
                    {
                        $brandIds = explode("$",$filter['option_id']);
                    }
                    else if($filter['attribute_id'] == "Price")
                    {
                        $priceArr = explode("$",$filter['option_id']);
                        foreach($priceArr as $price)
                        {
                            $prices[]= explode("-",$price);
                        }
                    }
                    else
                    {
                        $attribute_ids[$filter['attribute_id']] = implode(',',explode('$',$filter['option_id']));
                    }
                }
            }
            $result = Array();
            $i = 0;
            foreach($attribute_ids as $key => $attributeIds)
            {
                $value = explode(',',$attributeIds);
                $result[$key] = $value;
            }

            if($currencyCode != $defaultCurrency->currency_code)
            {
                $i = 0;
                foreach($prices as $price)
                {
                    foreach($price as $value)
                    {
                        $value = str_replace(',', '', $value);
                        // print_r($value);
                        $finalPrice[$i][] = number_format($value/$conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    }
                    $i++;
                }
            }

            DB::enableQueryLog();
            $products = Product::select('products.id','pd.title',DB::raw($selling_price),DB::raw($offer_price),'images.name as image','product_slug',DB::raw($offer_end_date),DB::raw($offer_start_date),DB::raw($group_price),
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
                                        $join->where('product_pricing.is_default','=',1);
                                        $join->whereNull('product_pricing.deleted_at');
                                    })
                                    // ->whereRaw("FIND_IN_SET('".$mainCatId."', c.category_path)")
                                    ->whereNull('products.deleted_at')
                                    // ->where('products.category_id','!=',null)
                                    ->whereNull('c.deleted_at')
                                    ->where('products.status',"Active")
                                    ->groupBy('products.id')
                                    ->whereIn('products.id',$prodIds);
                                    if(!empty($custId) && $custId->cust_group_id != 0)
                                    {
                                        $products = $products->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                                                    $join->on('cgp.product_id', '=' , 'products.id');
                                                                    $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
                                        });
                                    }
                                    else
                                    {
                                        $products = $products->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                            $join->on('cgp.product_id', '=' , 'products.id');
                                            $join->where('cgp.customer_group_id','=',0);
                                        });
                                    }
                                    // if($sortBy == '' || $sortBy == 1 || $sortBy == null)
                                    // {
                                    //     $products = $products->orderBy('products.created_at','desc');
                                    // }
                                    // if($sortBy == 2)
                                    // {
                                    //     $products = $products->orderBy('productSale','asc');
                                    //     $products = $products->orderBy('productPrice','asc');
                                    // }
                                    // if($sortBy == 3)
                                    // {
                                    //     $products = $products->orderBy('productPrice','asc');
                                    // }
                                    // if($sortBy == 4)
                                    // {
                                    //     $products = $products->orderBy('productPrice','desc');
                                    // }
                                    // if(!empty($categoryIds) && $categoryIds[0] != "")
                                    // {
                                    //     $products = $products->whereIn('products.category_id',$categoryIds);
                                    // }
                                    // if(!empty($finalPrice))
                                    // {
                                    //     foreach($finalPrice as $key => $price)
                                    //     {
                                    //         $price = str_replace(',', '', $price);
                                    //         if($key == 0)
                                    //         {
                                    //             if(!empty($custId) && $custId->cust_group_id != 0)
                                    //             {
                                    //                 $products = $products->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                    //             }
                                    //             else
                                    //             {
                                    //                 $products = $products->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                    //             }

                                    //             // $products = $products->whereBetween('product_pricing.offer_price', [$price, $price])
                                    //             //                                         ->orWhereBetween('product_pricing.selling_price', [$price, $price]);
                                    //         }
                                    //         else
                                    //         {
                                    //             if(!empty($custId) && $custId->cust_group_id != 0)
                                    //             {
                                    //                 $products = $products->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                    //             }
                                    //             else
                                    //             {
                                    //                 $products = $products->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                    //             }

                                    //             // $products = $products->orWhereBetween('product_pricing.offer_price', [$price, $price])
                                    //             //                                 ->orWhereBetween('product_pricing.selling_price', [$price, $price]);
                                    //         }
                                    //     }
                                    // }
                                    // if(!empty($result))
                                    // {
                                    //     $products = $products->join('product_pricing as pp', function($join){
                                    //         $join->on('pp.product_id', '=' , 'products.id');
                                    //     });
                                    //     foreach($result as $key => $attributeIds)
                                    //     {
                                    //         if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                    //         {
                                    //             $products = $products->whereRaw("(FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                    //         }
                                    //         $numItems = count($attributeIds);
                                    //         foreach($attributeIds as $key => $attributeId)
                                    //         {
                                    //             if(!empty($attributeId) && $attributeId != '')
                                    //             {
                                    //                 if((count($attributeIds) == 1 && $key == 0))
                                    //                 {
                                    //                     $products = $products->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                    //                 }
                                    //                 else
                                    //                 {
                                    //                     if($key == 0)
                                    //                     {
                                    //                         $products = $products->whereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                    //                     }

                                    //                     if(++$key == $numItems)
                                    //                     {
                                    //                         $products = $products->orWhereRaw("FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                    //                     }
                                    //                 }
                                    //             }
                                    //         }
                                    //     }
                                    // }
                                    // if(!empty($brandIds))
                                    // {
                                    //     $products = $products->whereIn('products.manufacturer_id',$brandIds);
                                    // }
            $products = $products->get()->toArray();
            // dd(DB::getQueryLog());
            $productsList[] = $products;
        }

        $resultFilterArr = $this->category->getFilterOptionsForFilteredProducts($allProdOfSelectedCategory,$request->language_id,$defaultLanguageData,$categoryIds,$brandIds,$attribute_ids,$custId);

        // for category
        if(!empty($resultFilterArr['category']))
        {
            $category['filterId'] = 'Category';
            $category['filterTypeName'] = $catLabels['CATEGORY'];
            $category['filtertype'] = 'MS';
            $category['data'] = $resultFilterArr['category'];
            $list[] = $category;
        }
        if(!empty($resultFilterArr['brands']))
        {
            $brands['filterId'] = 'Brands';
            $brands['filterTypeName'] = $catLabels['BRAND'];
            $brands['filtertype'] = 'MS';
            $brands['data'] = $resultFilterArr['brands'];
            $list[] = $brands;
        }

        // for attributes
        if(!empty($resultFilterArr['attributeGroups']))
        {
            foreach($resultFilterArr['attributeGroups'] as $attribute)
            {
                $list[] = $attribute;
            }
        }

        // for price
        if(!empty($resultFilterArr['price']))
        {
            sort($resultFilterArr['price']);
            $countDigitedNumbers = preg_grep('/\d{5}/',$resultFilterArr['price']);
            if(count($countDigitedNumbers) > 3)
            {
                $rangeLimits = array(0,1000,2500,5000,10000,15000,20000,25000,35000,50000,75000,100000);
            }
            else
            {
                $rangeLimits = array(0,50,250,500,1000,1500,2000,2500,5000,7500,10000,15000,25000);
            }

            $ranges['filterId'] = "Price";
            $ranges['filterTypeName'] = $catLabels['PRICE']."(".$currencyCode.")";
            $ranges['filtertype'] = "SS";

            $k = 0;
            for($i = 0; $i < count($rangeLimits); $i++)
            {
                if($i == count($rangeLimits)-1)
                {
                    break;
                }
                $lowLimit = $rangeLimits[$i];
                $highLimit = $rangeLimits[$i+1];

                $flagExits = false;
                foreach($resultFilterArr['price'] as $perPrice)
                {
                    if($perPrice >= $lowLimit && $perPrice < $highLimit)
                    {
                        $flagExits = true;
                    }
                }
                if($flagExits)
                {
                    $ranges['data'][$k]['id'] = number_format($lowLimit  * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator)."-".number_format($highLimit  * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator);
                    $ranges['data'][$k]['title'] = number_format($lowLimit  * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator)."-".number_format($highLimit  * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator);
                    foreach($ranges['data'] as $range)
                    {
                        if(!empty($priceArr))
                        {
                            if(in_array($range['id'],$priceArr))
                            {
                                $ranges['data'][$k]['flagSelected'] = "1";
                            }
                            else
                                $ranges['data'][$k]['flagSelected'] = "0";
                        }
                        else
                            $ranges['data'][$k]['flagSelected'] = "0";
                    }
                    $ranges['data'][$k++]['type'] = "1";
                }
            }
            if(!empty($ranges['data']))
            {
                $list[] = $ranges;
            }
        }

        $result['status'] = "OK";
        $result['statusCode'] = 200;
        $result['message'] = "Success";
        $result['titleNavigationBar'] = "FILTERS";
        $result['activeFilterAttributeId'] = isset($request->activeFilterAttributeId) ? $request->activeFilterAttributeId : '';

        $result['component'] = [];
        $componentDetails['componentId'] = "filter";
        $componentDetails['sequenceId'] = "1";
        $componentDetails['isActive'] = "1";

        $filterData['list'] = $list;

        $componentDetails['filterData'] = $filterData;
        $result['component'][] = $componentDetails;

        return response()->json($result);
    }
}
?>
