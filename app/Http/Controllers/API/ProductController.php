<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Product;
use App\Models\GlobalCurrency;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\ProductDetails;
use App\Models\ProductPricing;
use App\Models\ProductBulkPrices;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\RelatedProduct;
use App\Models\RecommendedProduct;
use App\Models\Image;
use App\Models\CustGroupPrice;
use App\Models\Photographers;
use App\Models\Settings;
use App\Models\PhotoBooks;
use DB;
use App\Traits\CommonTrait;
use Auth;

use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    use CommonTrait;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProductList()
    {
        $custId = "";
        if(!empty($_GET['pageSize']))
            $pageSize = $_GET['pageSize'];
        else
            $pageSize = 0;

        if(!empty($_GET['pageNo']))
            $pageNo = $_GET['pageNo'];
        else
            $pageNo = 0;

        $skip = 0;

        if(Auth::guard('api')->user() != null)
        {
            $loggedInId = Auth::guard('api')->user()->token()->user_id;
            $custId = Customer::select('cust_group_id')->where('id',$loggedInId)->first();
        }
        $productsList = [];
        if(!empty($_GET['category_id']))
        {
            $catName = Category::select('cd.title')
                                ->join('category_details as cd','cd.category_id','=','categories.id')
                                ->where('categories.id',$_GET['category_id'])
                                ->whereNull('categories.deleted_at')
                                ->whereNull('cd.deleted_at')
                                ->where('status',1)
                                ->where('language_id',$_GET['language_id'])
                                ->first();

            if(empty($catName))
            {
                $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
                $defaultLanguageId = $defaultLanguageData['id'];

                $catName = Category::select('cd.title')
                            ->join('category_details as cd','cd.category_id','=','categories.id')
                            ->where('categories.id',$_GET['category_id'])
                            ->whereNull('categories.deleted_at')
                            ->whereNull('cd.deleted_at')
                            ->where('status',1)
                            ->where('language_id',$defaultLanguageId)
                            ->first();
            }
            $productsList = $this->product->getProductsFromCategory($_GET['category_id'],$_GET['language_id'],'',$custId,$pageSize,$pageNo,$skip,'');
        }
        if(!empty($_GET['search_value']))
        {
            $langId = $_GET['language_id'];

            $productsList = $this->product->getSearchedProducts($_GET['search_value'],$langId,$custId,$pageSize,$pageNo,'','');
        }

        $currenyCode = getCurrSymBasedOnLangId($_GET['language_id']);
        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id', $_GET['language_id'])->first();
        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;

        // for products
        $productList = [];
        $i = 0;
        // dd($productsList);
        foreach($productsList as $product)
        {
            // dd($product);
            $productList[$i]['id'] = "".$product['id']."";
            if($product['image'] != null)
                $productList[$i]['image'] = $this->getBaseUrl().'/public/images/product/'.$product['id'].'/'.$product['image'];
            else
                $productList[$i]['image'] = $this->getBaseUrl().'/public/assets/images/no_image.png';

            $productList[$i]['type'] = "2";
            $productList[$i]['navigationFlag'] = "1";
            $productList[$i]['query'] = $this->getBaseUrl()."/api/v1/getProductDetails?language_id=".$_GET['language_id'].'&product_id='.$product['id'];

            if(empty($product['group_price']))
            {
                if(!empty($product['discountedPrice']) && (date("Y-m-d",strtotime($product['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($product['offer_end_date']))))
                {
                    $productList[$i]['price'] = isset($product['price']) ? $currenyCode." ".number_format($product['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                    $productList[$i]['discountedPrice'] = isset($product['discountedPrice']) ? $currenyCode." ".number_format($product['discountedPrice'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                }
                else if(!empty($product['discountedPrice']))
                {
                    $productList[$i]['price'] = isset($product['price']) ? $currenyCode." ".number_format($product['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                    $productList[$i]['price'] = isset($product['price']) ? $currenyCode." ".number_format($product['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                }
                else
                {
                    $productList[$i]['discountedPrice'] = "";
                    $productList[$i]['price'] = isset($product['price']) ? $currenyCode." ".number_format($product['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                }
            }
            else
            {
                $productList[$i]['discountedPrice'] = isset($product['group_price']) ? $currenyCode." ".number_format($product['group_price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";

                $productList[$i]['price'] = isset($product['price']) ? $currenyCode." ".number_format($product['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
            }
            $productList[$i]['flagInstock'] = "".$product['flagInstock']."";
            $productList[$i++]['title'] = $product['title'];
        }
        $prod['componentId'] = 'componentProductList';
        $prod['sequenceId'] = '2';
        $prod['isActive'] = '1';
        $prod['categoryId'] = isset($_GET['category_id']) ? $_GET['category_id'] : '';
        $prod['componentProductListData'] = $productList;

        // for sort filter data
        $codes = ['MOSTRECENT', 'ONSALE', 'PRICELTOH'];
        $productSortLabels = getCodesMsg($_GET['language_id'], $codes);

        $codeHTL = ['PRICEHTOL'];
        $HTLLabel = getCodesMsg($_GET['language_id'], $codeHTL);
        // dd($HTLLabel);
        $prodNotAvailable = ['NOPRODAVAILABLE'];
        $noProdAvail = getCodesMsg($_GET['language_id'], $prodNotAvailable);

        $k = 0;
        $j = 1;
        foreach($productSortLabels as $key => $sortLabels)
        {
            $sortDetails[$k]['id'] = "".$j++."";
            $sortDetails[$k++]['sort'] = $sortLabels;
        }
        $sortDetails[$k]['id'] = "".$j++."";
        $sortDetails[$k++]['sort'] = $HTLLabel['PRICEHTOL'];

        if(!empty($prod['componentProductListData']))
        {
            $sortData['componentId'] = 'sortFilterBar';
            $sortData['sequenceId'] = '1';
            $sortData['isActive'] = '1';
            $sortData['sortTitleData'] = $sortDetails;

            $result['status'] = "OK";
            $result['statusCode'] = 200;
            $result['message'] = "Success";
            $result['titleNavigationBar'] = isset($catName) ? $catName->title : "";
            $result['component'][] = $sortData;
            $result['component'][] = $prod;
        }
        else
        {
            $result['status'] = "OK";
            $result['statusCode'] = 300;
            $result['message'] = $noProdAvail['NOPRODAVAILABLE'];
            $result['component'] = [];
        }
        return response()->json($result);
    }

    // ajax call for getting data when different options of product is selectedCurr
    public function getAttributeGroupData(Request $request)
    {

      $atribute1=$request->atrribute_id1;
      $atribute2=$request->atrribute_id2;
      $atribute3=$request->atrribute_id3;
      $ProductID=$request->product_id;
      $lang_id=$request->language_id;
      // for sort filter data
      $codes = ['PRODUCTNOTAVAILABLE'];
      $productLabels = getCodesMsg($lang_id, $codes);
      // Toget currency code and decimal data
      $currenyCode =getCurrSymBasedOnLangId($lang_id);
      $get_curr = \App\Models\GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id', $lang_id)->first();
      $conversionRate = getCurrencyRates($get_curr->currency_id);
      $decimalNumber=$get_curr->decimal_number;
      $decimalSeparator=$get_curr->decimal_separator;
      $thousandSeparator=$get_curr->thousand_separator;
      $baseUrl = $this->getBaseUrl();
      // To get group price
      $customer_id = $request->customer_id;
      if(!$customer_id)
      $customer_id=0;
      $GroupPrice=0;
      if($customer_id!=0){
          $customeData=Customer::where("id",$customer_id)->first();
          if($customeData->cust_group_id!=0){
              $custGrpPrice=CustGroupPrice::where("product_id",$product->id)->where('customer_group_id', $customeData->cust_group_id)->first();
              if(!empty($custGrpPrice))
              $GroupPrice=$custGrpPrice->price;
          }
      }
      // To get category data
      $categoryData=Product::select('categories.photo_upload')->leftjoin('categories','categories.id','=','products.category_id')->where("products.id",$ProductID)->where("products.status",'Active')->whereNull("products.deleted_at")->first();
      //$categoryData=Category::select('photo_upload')->where('id',$product->category_id)->first();
      // $attributeID = \App\Models\AttributeDetails::select('attribute_id')
      //                                       ->where('display_name', $atribute3)
      //                                       ->whereNull('deleted_at')
      //                                       ->first();
      $whereQury='1=1 ';
      if(!empty($atribute1)){
        foreach($atribute1 as $attr){
          if($attr!='')
          $whereQury.=" and FIND_IN_SET($attr,attribute_ids)";
        }
       }
      if(!empty($atribute2)){
         foreach($atribute2 as $attr2){
           if($attr2!='')
           $whereQury.="and FIND_IN_SET($attr2,attribute_ids)";
         }
      }
      if($atribute3)
      $whereQury.=" and FIND_IN_SET($atribute3,attribute_ids)";
      $result = \App\Models\ProductPricing::select('id','product_id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku','offer_start_date','offer_end_date','image')
                                                            ->where('product_id', $ProductID)
                                                            ->whereRaw($whereQury)
                                                            ->whereNull('deleted_at')
                                                            ->first();
      $image=\App\Models\Image::select('images.name')
                                    ->where('imageable_id',$ProductID)
                                    ->where('image_type','product')
                                    ->whereNull('deleted_at')
                                    ->orderByRaw('is_default',1)
                                    ->first();
      $res=[];
      if($result){
      $res['id']=$result->id;
      $res['orignalprice']=number_format($result->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
      if($GroupPrice==0){
            if(!empty($result->offer_price) && date("Y-m-d",strtotime($result->offer_start_date)) <= date('Y-m-d') && date('Y-m-d') <= date("Y-m-d",strtotime($result->offer_end_date))){
                $res['offeraplicable']=1;
                $res['price']= number_format($result->offer_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
            }
            else{
                $res['offeraplicable']=0;
                $res['price']= number_format($result->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
              }
      }
      else{
              $res['offeraplicable']=1;
              $res['price']= number_format($GroupPrice * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
      }
      if($categoryData->photo_upload==1)
      $res['quantity']='999999';
      else
      $res['quantity']=$result->quantity;
      $res['attribute_ids']=$result->attribute_ids;
      $res['attribute_group_ids']=$result->attribute_group_ids;
      $res['sku']=$result->sku;
      if($result->image!=null)
      $img=$baseUrl.'/public/images/product/'.$result->product_id.'/pricingoption/'.$result->image;
      else
      $img=$baseUrl.'/public/images/product/'.$result->product_id.'/'.$image->name;
      $res['image']=$img;
      $res['product_id']=$result->product_id;
    }

    // For checking prduct is oos //
    $attributesActive=1;
    $product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku')
                                                  ->where('product_id', $ProductID)
                                                  ->whereNull('deleted_at')
                                                  ->orderBy('is_default', 'DESC')
                                                  ->orderBy('id', 'ASC')
                                                  ->first();
    if(!empty($product_details['attribute_group_ids'])){
    $attrGrp=explode(',',$product_details['attribute_group_ids']);
          foreach($attrGrp as $grpIds){
                $grp_details = \App\Models\AttributeGroup::select('status','deleted_at')
                                                              ->where('id', $grpIds)
                                                            //  ->whereNull('deleted_at')
                                                              ->first();
                if($grp_details['status']==0 || !empty($grp_details['deleted_at']))
                $attributesActive=0;

          }
    }
    if(!empty($result->attribute_ids)){
          $arrDefaultSelected=explode(',',$result->attribute_ids);
              foreach($arrDefaultSelected as $attrIds){
                $attr_details = Attribute::select('attribute.status','attribute.deleted_at')
                                          ->where('attribute.id', $attrIds)
                                          ->first();
                if(!empty($attr_details) && ($attr_details['status']==0 || !empty($attr_details['deleted_at'])))
                $attributesActive=0;
              }
    }
    //End For checking prduct is oos//
      if($result && $res['quantity']>0 && $attributesActive==1)
      return response()->json(['status' => 'OK',"statusCode" => 200,'Data' => $res]);
      else{
        $result['statusCode'] = '300';
        $result['message'] = 'Your selected option is not available. Please select another option';
        return response()->json($result);
      }
    }

    public function getFilteredProducts(Request $request)
    {
        $filteredProducts = [];
        $custId = "";
        if(!empty($request->pageSize))
            $pageSize = $request->pageSize;
        else
            $pageSize = 0;

        if(!empty($request->pageNo))
            $pageNo = $request->pageNo;
        else
            $pageNo = 0;

        if(Auth::guard('api')->user() != null)
        {
            $loggedInId = Auth::guard('api')->user()->token()->user_id;
            $custId = Customer::select('cust_group_id')->where('id',$loggedInId)->first();
        }

        $langId = $request->language_id;
        $sortBy = $request->sort_by;
        if(!empty($request->category_id))
          $mainCatId = $request->category_id;

        if(!empty($request->search_value))
            $searchVal = $request->search_value;

        $codes = ['MOSTRECENT', 'ONSALE', 'PRICELTOH', 'PRICEHTOL','NOPRODAVAILABLE'];
        $productSortLabels = getCodesMsg($langId, $codes);

        $categoryIds = $prices = $attribute_ids = $finalPrice = $brandIds = [];

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        if(!empty($request->filterQuery))
        {
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
                        $attribute_ids[$filter['attribute_id']] = explode('$',$filter['option_id']);
                    }
                }
            }
        }
        $finalPrice[] = $prices;
        // dd($finalPrice);
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
                                                        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                                                        ->where('global_currency.is_deleted', 0)
                                                        ->where('global_currency.is_default', 1)
                                                        ->first();

        $currenyCode = getCurrSymBasedOnLangId($langId);
        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$langId)->first();
        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;

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

        if($currenyCode != $defaultCurrency->currency_code)
        {
            $i = 0;
            foreach($prices as $price)
            {
                foreach($price as $value)
                {
                    $value = str_replace(',', '', $value);
                    $finalPrice[$i][] = number_format($value/$conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                $i++;
            }
        }
        // dd($finalPrice);
        DB::enableQueryLog();
        if(!empty($mainCatId))
        {
            // $productIds = $this->product->getProductsFromCategory($mainCatId,$langId,$sortBy,$custId,$pageSize,$pageNo,0,$request);
            // dd($productIds);
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
                                    ->whereRaw("FIND_IN_SET('".$mainCatId."', c.category_path)")
                                    ->whereNull('products.deleted_at')
                                    ->where('products.category_id','!=',null)
                                    ->whereNull('c.deleted_at')
                                    ->where('products.status','Active')
                                    ->groupBy('products.id');
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
                                    if($sortBy == '' || $sortBy == 1 || $sortBy == null)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('products.created_at','desc');
                                    }
                                    if($sortBy == 2)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productSale','asc');
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','asc');
                                    }
                                    if($sortBy == 3)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','asc');
                                    }
                                    if($sortBy == 4)
                                    {
                                        $filteredProducts = $filteredProducts->orderBy('productPrice','desc');
                                    }
                                    if(!empty($categoryIds) && $categoryIds[0] != "")
                                    {
                                        $filteredProducts = $filteredProducts->whereIn('products.category_id',$categoryIds);
                                    }
                                    if(!empty($finalPrice))
                                    {
                                        foreach($finalPrice as $prices)
                                        {
                                            foreach($prices as $price)
                                            {
                                                $price = str_replace(',', '', $price);

                                                $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".($price[0])."' and cgp.price <= '".($price[1])."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($price[0])."' and product_pricing.offer_price <='".($price[1])."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($price[0])."' and product_pricing.selling_price <='".($price[1])."')))");
                                            }

                                        }
                                    }

                                    if(!empty($attribute_ids))
                                    {
                                        $filteredProducts = $filteredProducts->leftJoin('product_pricing as pp', function($join) {
                                            $join->on('pp.product_id', '=' , 'products.id');
                                        });
                                        $len = count($attribute_ids);
                                        foreach($attribute_ids as $key => $attributeIds)
                                        {
                                            if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                            {
                                                $filteredProducts = $filteredProducts->whereRaw("FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                            }

                                            $mul_where = [];
                                            if(!empty($attributeIds) && count($attributeIds) >= 2)
                                            {
                                                foreach($attributeIds as $key => $attributeId)
                                                {
                                                    if(!empty($attributeId) && $attributeId != '')
                                                    {
                                                        if($key === array_key_first($attributeIds))
                                                        {
                                                            array_push($mul_where, "(FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                        }
                                                        else if($key === array_key_last($attributeIds))
                                                        {
                                                            array_push($mul_where, "OR FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                        }
                                                        else
                                                        {
                                                            array_push($mul_where, "OR FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                        }
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                array_push($mul_where, "(FIND_IN_SET('".$attributeIds[0]."', pp.attribute_ids))");
                                            }
                                            $attrRes = implode(" ",$mul_where);
                                            $filteredProducts = $filteredProducts->whereRaw($attrRes);

                                        }
                                    }
                                    // if(!empty($finalPrice))
                                    // {
                                    //     foreach($finalPrice as $price)
                                    //     {
                                    //         $price = str_replace(',', '', $price);

                                    //         if(!empty($custId) && $custId->cust_group_id != 0)
                                    //         {
                                    //             $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".($min)."' and cgp.price <= '".($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($min)."' and product_pricing.offer_price <='".($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //             OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($min)."' and product_pricing.selling_price <='".($max)."')))");
                                    //         }
                                    //         else
                                    //         {
                                    //             $filteredProducts = $filteredProducts->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($min)."' and product_pricing.offer_price <='".($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //             OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($min)."' and product_pricing.selling_price <='".($max)."'))");
                                    //         }
                                    //     }
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
                                    //                 $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                    //             }
                                    //             else
                                    //             {
                                    //                 $filteredProducts = $filteredProducts->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                    //             }

                                    //             // $filteredProducts = $filteredProducts->whereBetween('product_pricing.offer_price', [$price, $price])
                                    //             //                                         ->orWhereBetween('product_pricing.selling_price', [$price, $price]);
                                    //         }
                                    //         else
                                    //         {
                                    //             if(!empty($custId) && $custId->cust_group_id != 0)
                                    //             {
                                    //                 $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".round($min)."' and cgp.price <= '".round($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."')))");
                                    //             }
                                    //             else
                                    //             {
                                    //                 $filteredProducts = $filteredProducts->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".round($min)."' and product_pricing.offer_price <='".round($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                    //                 OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".round($min)."' and product_pricing.selling_price <='".round($max)."'))");
                                    //             }

                                    //             // $filteredProducts = $filteredProducts->orWhereBetween('product_pricing.offer_price', [$price, $price])
                                    //             //                                 ->orWhereBetween('product_pricing.selling_price', [$price, $price]);
                                    //         }
                                    //     }
                                    // }
                                    // if(!empty($attribute_ids))
                                    // {
                                    //     $filteredProducts = $filteredProducts->join('product_pricing as pp', function($join) {
                                    //         $join->on('pp.product_id', '=' , 'products.id');
                                    //         $join->whereNull('pp.deleted_at');
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
                                    if(!empty($brandIds))
                                    {
                                        $filteredProducts = $filteredProducts->whereIn('products.manufacturer_id',$brandIds);
                                    }
            if($pageSize != 0 && $pageNo!=0)
                $filteredProducts = $filteredProducts->paginate($pageSize,$columns = ['*'],$pageName = 'page',$pageNo);
            else
                $filteredProducts = $filteredProducts->get();

                // dd(DB::getQueryLog());
        }
        // dd($filteredProducts);
        if(!empty($searchVal))
        {
            $productList = [];
            $i = 0;
            $products = $this->product->getSearchedProducts($searchVal,$langId,$custId,$pageSize,$pageNo,'',$request);
            foreach($products as $product)
            {
                $productList[$i++] = "".$product['id']."";
            }
            $prodIds = explode(',',implode(',',$productList));

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
                                ->join('categories as c','c.id','=','category_id')
                                ->leftJoin('category_details as cd', function($join) use($langId) {
                                    $join->on('cd.category_id', '=' , 'c.id');
                                    $join->where('cd.language_id','=',$langId);
                                    $join->whereNull('cd.deleted_at');
                                })
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
                                ->whereRaw( "(pd.title like ? OR cd.title like ? OR product_pricing.sku like ? )", array('%'.$searchVal.'%','%'.$searchVal.'%','%'.$searchVal.'%'))
                                ->whereNull('products.deleted_at')
                                ->whereNull('c.deleted_at')
                                ->where('products.status','Active')
                                ->groupBy('products.id');
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
                                if($sortBy == '' || $sortBy == 1 || $sortBy == null)
                                {
                                    $filteredProducts = $filteredProducts->whereIn('products.id',$prodIds)
                                                                        ->orderBy('products.created_at','desc');
                                }
                                if($sortBy == 2)
                                {
                                    $filteredProducts = $filteredProducts->whereIn('products.id',$prodIds)
                                                                        ->orderBy('productSale','asc');
                                    $filteredProducts = $filteredProducts->whereIn('products.id',$prodIds)
                                                                        ->orderBy('productPrice','asc');
                                }
                                if($sortBy == 3)
                                {
                                    $filteredProducts = $filteredProducts->whereIn('products.id',$prodIds)
                                                                        ->orderBy('productPrice','asc');
                                }
                                if($sortBy == 4)
                                {
                                    $filteredProducts = $filteredProducts->whereIn('products.id',$prodIds)
                                                                        ->orderBy('productPrice','desc');
                                }
                                if(!empty($categoryIds) && $categoryIds[0] != "")
                                {
                                    $filteredProducts = $filteredProducts->whereIn('products.category_id',$categoryIds);
                                }
                                if(!empty($finalPrice))
                                {
                                    foreach($finalPrice as $prices)
                                    {
                                        foreach($prices as $price)
                                        {
                                            $price = str_replace(',', '', $price);

                                            $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".($price[0])."' and cgp.price <= '".($price[1])."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($price[0])."' and product_pricing.offer_price <='".($price[1])."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                            OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($price[0])."' and product_pricing.selling_price <='".($price[1])."')))");
                                        }

                                    }
                                }

                                if(!empty($attribute_ids))
                                {
                                    $filteredProducts = $filteredProducts->leftJoin('product_pricing as pp', function($join) {
                                        $join->on('pp.product_id', '=' , 'products.id');
                                    });
                                    $len = count($attribute_ids);
                                    foreach($attribute_ids as $key => $attributeIds)
                                    {
                                        if(!empty($attributeIds[0]) && $attributeIds[0] != '')
                                        {
                                            $filteredProducts = $filteredProducts->whereRaw("FIND_IN_SET('".$key."', pp.attribute_group_ids)");
                                        }

                                        $mul_where = [];
                                        if(!empty($attributeIds) && count($attributeIds) >= 2)
                                        {
                                            foreach($attributeIds as $key => $attributeId)
                                            {
                                                if(!empty($attributeId) && $attributeId != '')
                                                {
                                                    if($key === array_key_first($attributeIds))
                                                    {
                                                        array_push($mul_where, "(FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                    }
                                                    else if($key === array_key_last($attributeIds))
                                                    {
                                                        array_push($mul_where, "OR FIND_IN_SET('".$attributeId."', pp.attribute_ids))");
                                                    }
                                                    else
                                                    {
                                                        array_push($mul_where, "OR FIND_IN_SET('".$attributeId."', pp.attribute_ids)");
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            array_push($mul_where, "(FIND_IN_SET('".$attributeIds[0]."', pp.attribute_ids))");
                                        }
                                        $attrRes = implode(" ",$mul_where);
                                        $filteredProducts = $filteredProducts->whereRaw($attrRes);

                                    }
                                }
            if($pageSize != 0 && $pageNo!=0)
                $filteredProducts = $filteredProducts->paginate($pageSize,$columns = ['*'],$pageName = 'page',$pageNo);
            else
                $filteredProducts = $filteredProducts->get();
        }

        // dd(DB::getQueryLog());
        $resultArr = [];
        $i = 0;
        foreach($filteredProducts as $filteredProduct)
        {
            $prodTitle = $filteredProduct['title'];
            if($prodTitle == null)
            {
                $prodDetails = ProductDetails::select('title')
                                            ->where('product_id',$filteredProduct['id'])
                                            ->where('language_id',$defaultLanguageId)
                                            ->where('deleted_at')
                                            ->first();
                $prodTitle =  $prodDetails['title'];
            }
            $resultArr[$i]['id'] = "".$filteredProduct['id']."";
            $resultArr[$i]['title'] = $prodTitle;

            if(empty($filteredProduct['group_price']))
            {
                if(!empty($filteredProduct['discountedPrice']) && (date("Y-m-d",strtotime($filteredProduct['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($filteredProduct['offer_end_date']))))
                {
                    $resultArr[$i]['price'] = isset($filteredProduct['price']) ? $currenyCode." ".number_format($filteredProduct['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                    $resultArr[$i]['discountedPrice'] = isset($filteredProduct['discountedPrice']) ? $currenyCode." ".number_format($filteredProduct['discountedPrice'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                }
                else if(!empty($filteredProduct['discountedPrice']))
                {
                    $resultArr[$i]['price'] = isset($filteredProduct['price']) ? $currenyCode." ".number_format($filteredProduct['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                    $resultArr[$i]['discountedPrice'] = isset($filteredProduct['discountedPrice']) ? $currenyCode." ".number_format($filteredProduct['discountedPrice'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                }
                else
                {
                    $resultArr[$i]['discountedPrice'] = "";
                    $resultArr[$i]['price'] = isset($filteredProduct['price']) ? $currenyCode." ".number_format($filteredProduct['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                }
            }
            else
            {
                $resultArr[$i]['discountedPrice'] = isset($filteredProduct['group_price']) ? $currenyCode." ".number_format($filteredProduct['group_price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
                $resultArr[$i]['price'] = isset($filteredProduct['price']) ? $currenyCode." ".number_format($filteredProduct['group_price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
            }

            if($filteredProduct['image'] != null)
                $resultArr[$i]['image'] = $this->getBaseUrl().'/public/images/product/'.$filteredProduct['id'].'/'.$filteredProduct['image'];
            else
                $resultArr[$i]['image'] = $this->getBaseUrl().'/public/assets/images/no_image.png';
            $resultArr[$i]['type'] = "2";
            $resultArr[$i]['navigationFlag'] = "1";

            if($filteredProduct['photo_upload'] != 0)
                $resultArr[$i]['flagInstock'] = 1;
            else
            {
                if(empty($filteredProduct['attribute_group_ids']) && empty($filteredProduct['attribute_ids']))
                {
                    if($filteredProduct['quantity'] > 0)
                        $resultArr[$i]['flagInstock'] = 1;
                    else
                        $resultArr[$i]['flagInstock'] = 0;
                }
                else
                {
                    foreach(explode(",",$filteredProduct['attribute_group_ids']) as $attrGroupId)
                    {
                        $attrGroup = AttributeGroup::find($attrGroupId);
                        if(!empty($attrGroup['status']))
                        {
                            if($attrGroup['status'] == 0 || !empty($attrGroup['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($filteredProduct['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }
                    }
                    foreach(explode(",",$filteredProduct['attribute_ids']) as $attrId)
                    {
                        $attribute = Attribute::find($attrId);
                        if(!empty($attribute['status']))
                        {
                            if($attribute['status'] == 0 || !empty($attribute['deleted_at']))
                                $resultArr[$i]['flagInstock'] = 0;
                            else
                            {
                                if($filteredProduct['quantity'] > 0)
                                    $resultArr[$i]['flagInstock'] = 1;
                                else
                                    $resultArr[$i]['flagInstock'] = 0;
                            }
                        }

                    }
                }
            }
            $resultArr[$i++]['query'] = $this->getBaseUrl()."/api/v1/getProductDetails?language_id=".$langId.'&product_id='.$filteredProduct['id'];
        }

        if(!empty($resultArr))
        {
            $prod['componentId'] = 'componentProductList';
            $prod['sequenceId'] = '2';
            $prod['isActive'] = '1';
            $prod['categoryId'] = $request->category_id;
            $prod['componentProductListData'] = $resultArr;
            $result['status'] = "OK";
            $result['statusCode'] = 200;
            $result['message'] = "Success";
            $result['component'][] = $prod;
        }
        else
        {
            $prod['componentProductListData'] = $resultArr;
            $result['status'] = "OK";
            $result['statusCode'] = 300;
            $result['message'] = $productSortLabels['NOPRODAVAILABLE'];
            $result['component'][] = $prod;
        }


        return response()->json($result);

    }

    // To get the option id for chossen conmbinations//
    public function applyVariant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id'    => 'required|numeric',
            'product_id'     => 'required|numeric',
            'attribute_ids'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
              'statusCode' => 300,
              'message' => $validator->errors(),
            ]);
        }

        $langId = $request->language_id;
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $attributeIDs = $request->attribute_ids;
        $productId = $request->product_id;

        $codes = ['PRODUCTNOTAVAILABLE','VARIATION','DEFAULT','QTY','EACH','PRICING','DESCRIPTIONLABEL'];
        $productLabels = getCodesMsg($langId, $codes);
        $currenyCode =getCurrSymBasedOnLangId($langId);
        $get_curr = \App\Models\GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id', $langId)->first();
        $conversionRate = getCurrencyRates($get_curr->currency_id);
        $decimalNumber=$get_curr->decimal_number;
        $decimalSeparator=$get_curr->decimal_separator;
        $thousandSeparator=$get_curr->thousand_separator;

        $whereQury='1=1 and ';
        $attributeArr=explode(",",$attributeIDs);
        $k=1;
        foreach($attributeArr as $attrId){
          if($k<count($attributeArr))
            $whereQury.="FIND_IN_SET($attrId,attribute_ids) and ";
          else
            $whereQury.="FIND_IN_SET($attrId,attribute_ids) ";
            $k++;
        }

        $variants=ProductPricing::where('product_id',$productId)
                                  ->whereNull('deleted_at')
                                  ->whereRaw($whereQury)
                                  ->first();
        $product=Product::where("id",$productId)->where("status",'Active')->whereNull("deleted_at")->first();
        $categoryData=Category::select('photo_upload')->where('id',$product->category_id)->first();
        // Componant bulk pricing
        if(!empty($variants)){
        $pricingData=ProductBulkPrices::where('option_id',$variants->id)->where('product_id',$productId)->whereNull('deleted_at')->orderBy('from_quantity', 'ASC')->get();
        if(!empty($pricingData) && count($pricingData)>0){
          $variationArr=[];
          $r=0;
          foreach($pricingData as $range){
            $from=$range['from_quantity'];
            if($range['to_quantity']==0)
              $to=' + '.$productLabels['QTY'].'('.$productLabels['EACH'].')';
              else
              $to=' - '.$range['to_quantity'].' '.$productLabels['QTY'].'('.$productLabels['EACH'].')';
              $variationArr[$r++]['leftText']=$from.$to;
            }
            if($variants){
              $dataPricingVariation=[];
              $datanameVariation=[];
              // Size attribute group id
              $sizeAttribute = config('app.sizeAttribute.size');
              $product_bulk_price = ProductBulkPrices::select('id','from_quantity','to_quantity','price')
                                                      ->where('option_id', $variants->id)
                                                      ->where('product_id', $productId)
                                                      ->orderBy('from_quantity', 'ASC')
                                                      ->get();
              if(empty($variants->attribute_ids)){
                $datanameVariation=$productLabels["DEFAULT"];
                $i=0;
                foreach($product_bulk_price as $bp){
                  $variationArr[$i++]['rightText']=$currenyCode." ".number_format($bp->price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                }
                else{
                  $pAttribites = explode(',',$variants->attribute_ids);
                  $pGroups = explode(',',$variants->attribute_group_ids);
                  // finding size attribute
                  $flagSize = false;
                  $sizeKey = "";
                  foreach ($pGroups as $kg => $groupId) {
                    if($sizeAttribute == $groupId)
                    {
                        $flagSize = true;
                        $sizeKey = $kg;
                    }
                  }
                  $displayname='';
                  $k=1;
                  foreach($pAttribites as $ka => $attr){
                    if($flagSize && $ka != $sizeKey) continue;
                    $optionvariation=ProductPricing::getOptionVariations($attr,$langId);
                    if(empty($optionvariation))
                    $optionvariation=ProductPricing::getOptionVariations($attr,$defaultLanguageId);
                    if($k < count($pAttribites))
                    $displayname.=$optionvariation.', ';
                    else
                    $displayname.=$optionvariation;
                    $k++;
                  }
                  // rtrim
                  $displayname = rtrim($displayname, ", ");
                  $datanameVariation=$displayname;
                  $i=0;
                  foreach($product_bulk_price as $bp){
                    $variationArr[$i++]['rightText']=$currenyCode." ".number_format($bp->price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                  }
                }
              }
              $componantPricing=[];
              $componantPricing['componentId']='pricing';
              $componantPricing['sequenceId']='10';
              $componantPricing['isActive']='1';
              $componantPricing['pricingData']['title']=$productLabels['PRICING'];
              $componantPricing['pricingData']['showrows']='2';
              $componantPricing['pricingData']['leftTextTitle']=$productLabels['VARIATION'];
              $componantPricing['pricingData']['rightTextTitle']=$datanameVariation;
              $componantPricing['pricingData']['list']=$variationArr;
            }
            else{
              $componantPricing=[];
              $componantPricing['componentId']='pricing';
              $componantPricing['sequenceId']='10';
              $componantPricing['isActive']='0';
              $componantPricing['pricingData']['title']=$productLabels['PRICING'];
              $componantPricing['pricingData']['leftTextTitle']='';
              $componantPricing['pricingData']['rightTextTitle']='';
              $componantPricing['pricingData']['list']=[];
            }
          }
            else{
              $componantPricing=[];
              $componantPricing['componentId']='pricing';
              $componantPricing['sequenceId']='10';
              $componantPricing['isActive']='0';
              $componantPricing['pricingData']['title']=$productLabels['PRICING'];
              $componantPricing['pricingData']['leftTextTitle']='';
              $componantPricing['pricingData']['rightTextTitle']='';
              $componantPricing['pricingData']['list']=[];
            }
        if(!empty($variants)){
          //To get default price
          $token = $request->header('Authorization');
          if(!empty($token))
          $customer_id = Auth::guard('api')->user()->token()->user_id;
          else
          $customer_id=0;
          $GroupPrice=0;
          if($customer_id!=0){
              $customeData=Customer::where("id",$customer_id)->first();
              if($customeData->cust_group_id!=0){
                  $custGrpPrice=CustGroupPrice::where("product_id",$productId)->where('customer_group_id', $customeData->cust_group_id)->first();
                  if(!empty($custGrpPrice))
                  $GroupPrice=$custGrpPrice->price;
              }
          }
          if($GroupPrice==0){
          if(!empty($variants->offer_price) && (date("Y-m-d",strtotime($variants->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($variants->offer_end_date)))){
            $price=$variants->selling_price;
            $discountedPrice=$currenyCode." ".number_format($variants->offer_price * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator);
          }
          else{
            $price=$variants->selling_price;
            $discountedPrice="";
          }
          }
          else{
            $price=$variants->selling_price;
            $discountedPrice=$GroupPrice;
          }
          $attributesIsActive=1;
          $pricingdata=ProductPricing::where('product_id',$productId)
                                      ->whereNull('deleted_at')
                                      ->orderBy('is_default', 'DESC')
                                      ->orderBy('id', 'ASC')
                                      ->first();
          if(!empty($pricingdata['attribute_group_ids'])){
            $attrGrp=explode(',',$pricingdata['attribute_group_ids']);
            foreach($attrGrp as $grpIds){
              $grp_details = \App\Models\AttributeGroup::select('status','deleted_at')
                                                         ->where('id', $grpIds)
                                                          ->first();
            if($grp_details['status']==0 || !empty($grp_details['deleted_at']))
            $attributesIsActive=0;
          }
        }
        if(!empty($pricingdata['attribute_ids'])){
        $arrDefaultSelected=explode(',',$pricingdata['attribute_ids']);
        foreach($arrDefaultSelected as $attrIds){
          $attr_details = Attribute::select('attribute.status','attribute.deleted_at')
                                    ->where('attribute.id', $attrIds)
                                    ->first();
          if(!empty($attr_details) && ($attr_details['status']==0 || !empty($attr_details['deleted_at'])))
          $attributesIsActive=0;
        }
      }
          $result['status'] = "OK";
          $result['statusCode'] = 200;
          $result['option_id'] = "".$variants->id."";
          $result['price'] = isset($price) ? $currenyCode." ".number_format($price * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) : $currenyCode. " 0.00";
          $result['discountedPrice'] =$discountedPrice ;
          if($attributesIsActive==1){
            if($categoryData->photo_upload==1)
            $result['qty'] = "999999";
            else
            $result['qty'] = "".$variants->quantity."";
          }
          else{
            $result['qty'] = "0";
          }
          if(isset($variants->image) && $variants->image!='')
          $result['image']=$this->getBaseUrl().'/public/images/product/'.$productId.'/pricingoption/'.$variants->image;
          else
          $result['image']='';
          $result['message'] = "Success";
          $result['component'][] = $componantPricing;
       }
       else
       {
          $result['status'] = "OK";
          $result['statusCode'] = 300;
          $result['option_id'] = "0";
          $result['price'] = "0";
          $result['qty'] = "0";
          $result['component'][] = $componantPricing;
          $result['message'] = $productLabels['PRODUCTNOTAVAILABLE'];
       }
      return response()->json($result);
    }
    //To get product details//
    public function getProductDetails(Request $request)
    {

      $langId = $_GET['language_id'];
      $productId = $_GET['product_id'];
      if(isset($_GET['photographer_id']))
      $photographerId=$_GET['photographer_id'];
      if(isset($_GET['recentViewIds']))
      $recentViewIds=$_GET['recentViewIds'];
      $cartCount=0;
      if(isset($_GET['cart_master_id']) && !empty($_GET['cart_master_id'])){
      $cart_master_id=$_GET['cart_master_id'];
      $totalCartItems = \App\Models\Cart::where('cart_master_id',$cart_master_id)->count();
      $cartCount = $totalCartItems;
      }
      $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
      $defaultLanguageId = $defaultLanguageData['id'];
      $currenyCode =getCurrSymBasedOnLangId($langId);
      $get_curr = \App\Models\GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id', $langId)->first();
      $conversionRate = getCurrencyRates($get_curr->currency_id);
      $decimalNumber=$get_curr->decimal_number;
      $decimalSeparator=$get_curr->decimal_separator;
      $thousandSeparator=$get_curr->thousand_separator;

      //To get localization data
      $codes = ['OK','SUCCESS','LADYOPERATORTEXT','GIFTWRAPTHISITEM','WRITEMESSAGEFORGIFT','VARIATION','QTY','EACH','YOUMAYLIKE','RECENTLYVIEWED','DEFAULT','FREQUENTLYBOUGHTTOGETHER','MSGPRINTSTAFF','WRITEHERE','MULTIPLEIMAGEUPLOAD','BOOKNOTE','NOTE','WRITECAPTIONBOOK','PHOTOBOOKPLACEHOLDER','CUSTOMIZE','INCLOFVAT','UPLOADIMAGE','PRICING','DESCRIPTIONLABEL'];
      $productLabels = getCodesMsg($langId, $codes);

      $product=Product::select('products.id','products.product_slug','products.design_tool_product_id','products.is_customized','products.category_id','products.can_giftwrap','PD.title','PD.description','PD.key_features','PP.selling_price','PP.offer_price','PP.attribute_group_ids','PP.quantity','PP.id as option_id','PP.offer_start_date','PP.offer_end_date','products.image_min_height','products.image_min_width','products.image_max_height','products.image_max_width','products.flag_deliverydate','PP.image','products.max_images')
                        ->join('product_details as PD','PD.product_id','=','products.id')
                        ->join('product_pricing as PP','PP.product_id','=','products.id')
                        ->where("products.id",$productId)
                        ->where("PD.language_id",$langId)
                        ->where("products.status",'Active')
                        ->where("PP.is_default",'1')
                        ->whereNull("products.deleted_at")
                        ->first();
      if(empty($product)){
        $product=Product::select('products.id','products.product_slug','products.design_tool_product_id','products.is_customized','products.category_id','products.can_giftwrap','PD.title','PD.description','PD.key_features','PP.selling_price','PP.offer_price','PP.quantity','PP.id as option_id','PP.offer_start_date','PP.offer_end_date','products.image_min_height','products.image_min_width','products.image_max_height','products.image_max_width','products.flag_deliverydate','PP.image','products.max_images')
                          ->join('product_details as PD','PD.product_id','=','products.id')
                          ->join('product_pricing as PP','PP.product_id','=','products.id')
                          ->where("products.id",$productId)
                          ->where("PD.language_id",$defaultLanguageId)
                          ->where("products.status",'Active')
                          ->where("PP.is_default",'1')
                          ->whereNull("products.deleted_at")
                          ->first();
      }
      if(!empty($product)){
      // To get category data of default products
      $categoryData=Category::select('photo_upload','upload_is_multiple','lady_operator')->where('id',$product['category_id'])->first();

      //To get default price
      $token = $request->header('Authorization');
      if(!empty($token))
      $customer_id = Auth::guard('api')->user()->token()->user_id;
      else
      $customer_id=0;
      $GroupPrice=0;
      if($customer_id!=0){
          $customeData=Customer::where("id",$customer_id)->first();
          if($customeData->cust_group_id!=0){
              $custGrpPrice=CustGroupPrice::where("product_id",$product->id)->where('customer_group_id', $customeData->cust_group_id)->first();
              if(!empty($custGrpPrice))
              $GroupPrice=$custGrpPrice->price;
          }
      }

      // To get all images of product as ImageComponant
      $images=Image::select('images.id','images.name as product_image','is_default')
                                  ->where('imageable_id',$productId)
                                  ->where('image_type','product')
                                  ->whereNull('deleted_at')
                                  ->orderByRaw('is_default','DESC')
                                  ->orderBy('sort_order','ASC')
                                  ->get();
      $imageArr=[];
      $i = 0;
      foreach($images as $img){
        if($img->is_default=='yes')
        $imageArr[0]['image']=$this->getBaseUrl().'/public/images/product/'.$productId.'/'.$img->product_image;
        else
        $imageArr[$i]['image']=$this->getBaseUrl().'/public/images/product/'.$productId.'/'.$img->product_image;
        $i++;
      }

      $productImageDataArr=[];
      $productImageDataArr['id']="".$productId."";
      $productImageDataArr['imageWidth']="500";
      $productImageDataArr['imageHeight']="500";
      $productImageDataArr['list']=$imageArr;

      //componant one
      $componantOne=[];
      $componantOne['componentId']='productImage';
      $componantOne['sequenceId']='1';
      if(count($images)>0)
        $componantOne['isActive']='1';
      else
        $componantOne['isActive']='0';
      $componantOne['productImageData']=$productImageDataArr;

      //To get product details componant
      $productdetailsArr=[];
      $productdetailsArr['productName']=$product->title;
      $productdetailsArr['productId']="".$product->id."";
      if($GroupPrice==0){
        if(!empty($product->offer_price) && (date("Y-m-d",strtotime($product->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($product->offer_end_date)))){
        $productdetailsArr['price']= $currenyCode." ".number_format($product->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
        $productdetailsArr['discountedPrice']= $currenyCode." ".number_format($product->offer_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;;
        }
        else{
        $productdetailsArr['price']= $currenyCode." ".number_format($product->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
        $productdetailsArr['discountedPrice']= "";
        }
      }
      else{
        $productdetailsArr['price']= $currenyCode." ".number_format($product->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
        $productdetailsArr['discountedPrice']= $currenyCode." ".number_format($GroupPrice * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator) ;
      }
      $productdetailsArr['description']=$product->description;
      $productdetailsArr['textForVAT']="(".$productLabels['INCLOFVAT'].")";
      $productdetailsArr['option_id']="".$product->option_id."";
      $productdetailsArr['productUrl']=$this->getBaseUrl().'/product/'.$product->product_slug;
      // if photographer_id is passes in param
      if(!empty($photographerId)){
          $photographers=Photographers::select('photographers.profile_pic','photographer_details.name')->join('photographer_details','photographer_details.photographer_id','=','photographers.id')->where("photographers.id",$photographerId)->whereNull('photographers.deleted_at')->first();
          $productdetailsArr['isFor']="2";
          $productdetailsArr['created_by']=$photographers->name;
          $productdetailsArr['image']=$this->getBaseUrl()."/public/assets/images/photographers/".$photographers->profile_pic;
      }
      else{
        $productdetailsArr['isFor']="1";
        $productdetailsArr['created_by']="";
        $productdetailsArr['image']="";
      }
      //componant Two
      $componantTwo=[];
      $componantTwo['componentId']='productDetailsComponent';
      $componantTwo['sequenceId']='2';
      $componantTwo['isActive']='1';
      $componantTwo['productDetailsComponentData']=$productdetailsArr;

      //Prefrance check box Data
      $prefranceArr=[];
      if($categoryData->lady_operator==1)
        $prefranceArr['isSelected']='1';
      else
        $prefranceArr['isSelected']='0';
      $prefranceArr['prefrenceText']=$productLabels['LADYOPERATORTEXT'];

      //componant Three
      $componantThree=[];
      $componantThree['componentId']='prefranceCheckbox';
      $componantThree['sequenceId']='3';
      if($categoryData->lady_operator==1)
      $componantThree['isActive']='1';
      else
      $componantThree['isActive']='0';
      $componantThree['prefranceCheckboxData']=$prefranceArr;

      //Default pricing option
      $DefaultData = ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku')
                                                            ->where('is_default', 1)
                                                            ->where('product_id', $productId)
                                                            ->whereNull('deleted_at')
                                                            ->first();
      $arrDefaultSelected=array();
      if(!empty($DefaultData->attribute_ids))
      $arrDefaultSelected = explode(',', $DefaultData->attribute_ids);
      //attributes Data

      $productPricing=ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku')
                                                    ->where('product_id', $productId)
                                                    ->whereNull('deleted_at')
                                                    ->orderBy('is_default', 'DESC')
                                                    ->orderBy('id', 'ASC')
                                                    ->get();
    // attriburte componant
    $componantFour1=[];
    $arrProductAttributes=[];
    foreach($productPricing as $dataattr){
      $arrAttrIds = explode(',', $dataattr->attribute_ids);
      foreach($arrAttrIds as $AttIds){
        $arrProductAttributes[$AttIds]=$AttIds;
      }
    }
    foreach($productPricing as $data){
      $attributeArrGrp=[];
      $arrAttrIds = explode(',', $data->attribute_ids);
      $arrAttrGrpIds = array_reverse(explode(',', $data->attribute_group_ids));
      $listAttr=[];
      $l=0;
      if(!empty($arrAttrGrpIds)){
      foreach($arrAttrGrpIds as $GrpID){
        $atrrGrpDetails=\App\Models\AttributeGroup::select('attribute_groups.id','attribute_groups.sort_order','attribute_groups.attribute_type_id','GD.name','GD.display_name')
                                                    ->join('attribute_group_details as GD','GD.attr_group_id','=','attribute_groups.id')
                                                    ->where('attribute_groups.id', $GrpID)
                                                    ->where('GD.language_id',$langId)
                                                    ->whereNull('attribute_groups.deleted_at')
                                                    ->orderBy('attribute_groups.sort_order', 'ASC')
                                                    ->first();
       if(empty($atrrGrpDetails)){
         $atrrGrpDetails=\App\Models\AttributeGroup::select('attribute_groups.id','attribute_groups.sort_order','attribute_groups.attribute_type_id','GD.name','GD.display_name')
                                                    ->join('attribute_group_details as GD','GD.attr_group_id','=','attribute_groups.id')
                                                    ->where('attribute_groups.id', $GrpID)
                                                    ->where('GD.language_id',$defaultLanguageId)
                                                    ->whereNull('attribute_groups.deleted_at')
                                                    ->orderBy('attribute_groups.sort_order', 'ASC')
                                                    ->first();
                                                  }
          $attribute_details = Attribute::select('attribute.id','attribute.sort_order','attribute.color','attribute.image','AD.name','AD.display_name')
                                          ->join('attribute_details as AD','AD.attribute_id','=','attribute.id')
                                          ->where('AD.attribute_group_id', $GrpID)
                                          ->whereIn('attribute.id',$arrProductAttributes)
                                          ->where('AD.language_id',$langId)
                                          ->whereNull('attribute.deleted_at')
                                          ->orderBy('sort_order', 'ASC')
                                          ->get();
          if(empty($attribute_details)){
          $attribute_details = Attribute::select('attribute.id','attribute.sort_order','attribute.color','attribute.image','AD.name','AD.display_name')
                                          ->join('attribute_details as AD','AD.attribute_id','=','attribute.id')
                                          ->where('AD.attribute_group_id', $GrpID)
                                          ->whereIn('attribute.id',$arrProductAttributes)
                                          ->where('AD.language_id',$defaultLanguageId)
                                          ->whereNull('attribute.deleted_at')
                                          ->orderBy('sort_order', 'ASC')
                                          ->get();
          }
          if(!empty($atrrGrpDetails)){
          $attrdata=[];
            $a=0;
            foreach($attribute_details as $val){
              $attrdata[$a]['attributeId']="".$val->id."";
              $attrdata[$a]['value']="".$val->display_name."";
              if(in_array($val->id, $arrDefaultSelected))
              $attrdata[$a]['defaultSelected']='1';
              else
              $attrdata[$a]['defaultSelected']='0';
              $attrdata[$a]['colorCode']="".$val->color."";
              if(!empty($val->image))
              $attrdata[$a++]['image']=$this->getBaseUrl().'/public/assets/images/attributes/'.$val->image;
              else
              $attrdata[$a++]['image']='';
            }
            $attributeArr['attributeGroupId']="".$GrpID."";
            $attributeArr['title']="".$atrrGrpDetails->display_name."";
            $attributeArr['type']="SS";
            $attributeArr['options']=$attrdata;
            $listAttr['list'][0]=$attributeArr;
            $componantFour1[$l]['componentId']='attributes';
            $componantFour1[$l]['attributeType']="".$atrrGrpDetails->attribute_type_id."";
            $componantFour1[$l]['sequenceId']='4';
            $componantFour1[$l]['isActive']='1';
            $componantFour1[$l++]['attributesData']=$listAttr;
          }
          }
        }
        }
      if(empty($componantFour1)){
        $componantFour1[0]['componentId']='attributes';
        $componantFour1[0]['attributeType']="";
        $componantFour1[0]['sequenceId']='4';
        $componantFour1[0]['isActive']='0';
        $componantFour1[0]['attributesData']['list'] =[];
      }
      //Quantity componant
      $attributesIsActive=1;
      $pricingdata=ProductPricing::where('product_id',$productId)
                                  ->whereNull('deleted_at')
                                  ->orderBy('is_default', 'DESC')
                                  ->orderBy('id', 'ASC')
                                  ->first();
      if(!empty($pricingdata['attribute_group_ids'])){
        $attrGrp=explode(',',$pricingdata['attribute_group_ids']);
        foreach($attrGrp as $grpIds){
          $grp_details = \App\Models\AttributeGroup::select('status','deleted_at')
                                                     ->where('id', $grpIds)
                                                      ->first();
        if($grp_details['status']==0 || !empty($grp_details['deleted_at']))
        $attributesIsActive=0;
      }}
      if(!empty($pricingdata['attribute_ids'])){
      $arrDefaultSelected=explode(',',$pricingdata['attribute_ids']);
      foreach($arrDefaultSelected as $attrIds){
        $attr_details = Attribute::select('attribute.status','attribute.deleted_at')
                                  ->where('attribute.id', $attrIds)
                                  ->first();
        if(!empty($attr_details) && ($attr_details['status']==0 || !empty($attr_details['deleted_at'])))
        $attributesIsActive=0;
      }
    }
      $componantQuantity['componentId']='quantity';
      $componantQuantity['sequenceId']='6';
      $componantQuantity['isActive']='1';
      if($attributesIsActive==1){
        if($categoryData->photo_upload==1)
        $componantQuantity['quantityData']="999999";
        else
        $componantQuantity['quantityData']="".$product->quantity."";
      }
      else{
        $componantQuantity['quantityData']="0";
      }

      //gift wrap componant
      $giftArr=[];
      $giftArr['checboxTitle']="".$productLabels['GIFTWRAPTHISITEM']."";
      $giftArr['isSelected']='0';
      $giftArr['messageTitle']="".$productLabels['WRITEMESSAGEFORGIFT']."";
      //componant Gift wrap
      $componantGift=[];
      $componantGift['componentId']='giftWrap';
      $componantGift['sequenceId']='8';
      if($product->can_giftwrap=='Yes')
      $componantGift['isActive']='1';
      else
      $componantGift['isActive']='0';
      $componantGift['giftWrapData']=$giftArr;


      // Componant pricing
      $pricingData=ProductBulkPrices::where('option_id',$product->option_id)->where('product_id',$productId)->whereNull('deleted_at')->orderBy('from_quantity', 'ASC')->get();
      if(!empty($pricingData) && count($pricingData)>0){
        $variationArr=[];
        $r=0;
        foreach($pricingData as $range){
          $from=$range['from_quantity'];
          if($range['to_quantity']==0)
          $to=' + '.$productLabels['QTY'].'('.$productLabels['EACH'].')';
          else
          $to=' - '.$range['to_quantity'].' '.$productLabels['QTY'].'('.$productLabels['EACH'].')';
          $variationArr[$r++]['leftText']=$from.$to;
        }
        if(!empty($DefaultData)){
          $dataPricingVariation=[];
          $datanameVariation=[];
          // Size attribute group id
          $sizeAttribute = config('app.sizeAttribute.size');
          $product_bulk_price = ProductBulkPrices::select('id','from_quantity','to_quantity','price')
                                                                ->where('option_id', $DefaultData['id'])
                                                                ->where('product_id', $productId)
                                                                ->orderBy('from_quantity', 'ASC')
                                                                ->get();
            if(empty($DefaultData['attribute_ids'])){
              $datanameVariation=$DefaultData["DEFAULT"];
              $i=0;
              foreach($product_bulk_price as $bp){
              $variationArr[$i++]['rightText']=$currenyCode." ".number_format($bp->price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
              }
            }
            else{
                $pAttribites = explode(',',$DefaultData['attribute_ids']);
                $pGroups = explode(',',$DefaultData['attribute_group_ids']);

                // finding size attribute
                $flagSize = false;
                $sizeKey = "";
                foreach ($pGroups as $kg => $groupId) {
                    if($sizeAttribute == $groupId)
                    {
                        $flagSize = true;
                        $sizeKey = $kg;
                    }
                }

                $displayname='';
                $k=1;
                foreach($pAttribites as $ka => $attr){
                    if($flagSize && $ka != $sizeKey) continue;
                    $optionvariation=ProductPricing::getOptionVariations($attr,$langId);
                    if(empty($optionvariation))
                    $optionvariation=ProductPricing::getOptionVariations($attr,$defaultLanguageId);
                    if($k < count($pAttribites))
                    $displayname.=$optionvariation.', ';
                    else
                    $displayname.=$optionvariation;
                    $k++;
                }

                // rtrim
                $displayname = rtrim($displayname, ", ");
                $datanameVariation=$displayname;
                $i=0;
                foreach($product_bulk_price as $bp){
                $variationArr[$i++]['rightText']=$currenyCode." ".number_format($bp->price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }

            }
        }
        $componantPricing=[];
        $componantPricing['componentId']='pricing';
        $componantPricing['sequenceId']='10';
        $componantPricing['isActive']='1';
        $componantPricing['pricingData']['title']=$productLabels['PRICING'];
        $componantPricing['pricingData']['showrows']='2';
        $componantPricing['pricingData']['leftTextTitle']=$productLabels['VARIATION'];
        $componantPricing['pricingData']['rightTextTitle']=$datanameVariation;
        $componantPricing['pricingData']['list']=$variationArr;
      }
      else{
        $componantPricing=[];
        $componantPricing['componentId']='pricing';
        $componantPricing['sequenceId']='10';
        $componantPricing['isActive']='0';
        $componantPricing['pricingData']['title']=$productLabels['PRICING'];
        $componantPricing['pricingData']['leftTextTitle']='';
        $componantPricing['pricingData']['rightTextTitle']='';
        $componantPricing['pricingData']['list']=[];
      }

      // Componant pricing
      $componantDescription=[];
      $componantDescription['componentId']='productDescriptionComponent';
      $componantDescription['sequenceId']='11';
      if(!empty($product->key_features))
      $componantDescription['isActive']='1';
      else
      $componantDescription['isActive']='0';
      $componantDescription['title']=$productLabels['DESCRIPTIONLABEL'];
      $componantDescription['productDescriptionComponentData']['desc']="".$product->key_features."";


      //Related product componant

      $related_products = RelatedProduct::select('products.id', 'products.category_id','products.can_giftwrap',
      'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
      'product_pricing.selling_price', 'product_pricing.offer_price','products.product_slug','product_pricing.offer_start_date','product_pricing.offer_end_date',
      'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
      ->join('products', 'products.id', '=', 'related_products.related_id')
      ->Join('product_details', function($join) use($langId) {
          $join->on('product_details.product_id', '=' , 'products.id');
          $join->where('product_details.language_id','=',$langId);
          $join->whereNull('product_details.deleted_at');
      })
      ->Join('product_pricing', function($join) {
          $join->on('product_pricing.product_id', '=' , 'products.id');
          $join->where('product_pricing.is_default','=',1);
          $join->whereNull('product_pricing.deleted_at');
      })
      ->Join('images',function ($join) {
          $join->on('images.imageable_id', '=' , 'products.id');
          $join->where('images.image_type','=','product');
          $join->whereNull('images.deleted_at');
          $join->where('images.is_default','=','yes');
      })
      ->where('related_products.product_id', $productId)
      ->where('product_details.language_id', $langId)
      ->whereNull('products.deleted_at')
      ->get();
      if(empty($related_products)){
        $related_products = RelatedProduct::select('products.id', 'products.category_id','products.can_giftwrap',
        'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
        'product_pricing.selling_price', 'product_pricing.offer_price','products.product_slug','product_pricing.offer_start_date','product_pricing.offer_end_date',
        'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
        ->join('products', 'products.id', '=', 'related_products.related_id')
        ->Join('product_details', function($join) use($defaultLanguageId) {
            $join->on('product_details.product_id', '=' , 'products.id');
            $join->where('product_details.language_id','=',$defaultLanguageId);
            $join->whereNull('product_details.deleted_at');
        })
        ->Join('product_pricing', function($join) {
            $join->on('product_pricing.product_id', '=' , 'products.id');
            $join->where('product_pricing.is_default','=',1);
            $join->whereNull('product_pricing.deleted_at');
        })
        ->Join('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->whereNull('images.deleted_at');
            $join->where('images.is_default','=','yes');
        })
        ->where('related_products.product_id', $productId)
        ->whereNull('products.deleted_at')
        ->get();
      }
      $relateProarr=[];
      $r=0;
      $flagInstock=1;
      foreach($related_products as $pro){
        $GroupPriceRelated=0;
        if($customer_id!=0){
            $customeData=Customer::where("id",$customer_id)->first();
            if($customeData->cust_group_id!=0){
                $custGrpPrice=CustGroupPrice::where("product_id",$pro->id)->where('customer_group_id', $customeData->cust_group_id)->first();
                if(!empty($custGrpPrice))
                $GroupPriceRelated=$custGrpPrice->price;
            }
        }
        $relateProarr[$r]['id']="".$pro->id."";
        if($pro->product_image != null)
            $relateProarr[$r]['image'] = $this->getBaseUrl().'/public/images/product/'.$pro->id.'/'.$pro->product_image;
        else
            $relateProarr[$r]['image'] = $this->getBaseUrl().'/public/assets/images/no_image.png';
        $relateProarr[$r]['navigationFlag']='1';
        $relateProarr[$r]['type']='2';
        $relateProarr[$r]['query']=$this->getBaseUrl()."/api/v1/getProductDetails?language_id=".$langId.'&product_id='.$pro->id;
        $relateProarr[$r]['title']=$pro->title;
        if($GroupPriceRelated==0){
            if(!empty($pro->offer_price) && (date("Y-m-d",strtotime($pro->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($pro->offer_end_date)))){
            $relateProarr[$r]['price']= $currenyCode." ".number_format($pro->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
            $relateProarr[$r]['discountedPrice']= $currenyCode." ".number_format($pro->offer_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
          }
          else{
            $relateProarr[$r]['price']= $currenyCode." ".number_format($pro->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
            $relateProarr[$r]['discountedPrice']= "";
          }
      }
      else{
        $relateProarr[$r]['price']= $currenyCode." ".number_format($pro->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
        $relateProarr[$r]['discountedPrice']=$currenyCode." ".number_format($GroupPriceRelated * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);;
      }
      //To get category By Nivedita 19 may 2021 //
      $category = Category::where('id', $pro->category_id)->whereNull('deleted_at')->first();
      if($category['photo_upload']!=0)
        $flagInstock=1;
      else{
        if(empty($pro->attribute_group_ids) && empty($pro->attribute_ids))
        {
            if($pro->quantity >0)
              $flagInstock=1;
            else
              $flagInstock=0;

        }
        else{
          $aatGrpIds=explode(',',$pro->attribute_group_ids);
          $attrIds=explode(',',$pro->attribute_ids);
          foreach($aatGrpIds as $grpId)
          {
            $grp_details = AttributeGroup::select('status','deleted_at')
                                            ->where('id', $grpId)
                                            ->first();
            if($grp_details['status']==0 || !empty($grp_details['deleted_at']))
            $flagInstock=0;
            else
            {
              if($pro->quantity <= 0)
                $flagInstock = 0;
            }
          }
          foreach($attrIds as $attrId)
          {
            $attr_details = Attribute::select('attribute.status','attribute.deleted_at')
                                      ->where('attribute.id', $attrId)
                                      ->first();
            if(!empty($attr_details) && ($attr_details['status']==0 || !empty($attr_details['deleted_at'])))
            $flagInstock=0;
            else
            {
              if($pro->quantity <= 0)
                $flagInstock = 1;
            }
          }
        }
      }
        $relateProarr[$r++]['flagInstock']="".$flagInstock."";
      //End To get category //
      }
      $arr['title']=$productLabels['YOUMAYLIKE'];
      $arr['list']=$relateProarr;
      $componantRelated=[];
      $componantRelated['componentId']='componentSlider';
      $componantRelated['sequenceId']='12';
      if(count($related_products)>0)
      $componantRelated['isActive']='1';
      else
      $componantRelated['isActive']='0';
      $componantRelated['componentSliderData']=$arr;

      // Recently viewed componant
      $recentIds=[];
      if(isset($recentViewIds))
      $recentIds=explode(",",$recentViewIds);
      if(!empty($recentIds)){
        $recentProarr=[];
        $p=0;
        $flagInstock=1;
        foreach($recentIds as $id){
          $recent_products = RelatedProduct::select('products.id','products.category_id', 'products.can_giftwrap',
          'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
          'product_pricing.selling_price', 'product_pricing.offer_price','products.product_slug','product_pricing.offer_start_date','product_pricing.offer_end_date',
          'products.product_slug','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
          ->join('products', 'products.id', '=', 'related_products.related_id')
          ->Join('product_details', function($join) use($langId) {
              $join->on('product_details.product_id', '=' , 'products.id');
              $join->where('product_details.language_id','=',$langId);
              $join->whereNull('product_details.deleted_at');
          })
          ->Join('product_pricing', function($join) {
              $join->on('product_pricing.product_id', '=' , 'products.id');
              $join->where('product_pricing.is_default','=',1);
              $join->whereNull('product_pricing.deleted_at');
          })
          ->Join('images',function ($join) {
              $join->on('images.imageable_id', '=' , 'products.id');
              $join->where('images.image_type','=','product');
              $join->whereNull('images.deleted_at');
              $join->where('images.is_default','=','yes');
          })
          ->where('related_products.product_id', $id)
          ->whereNull('products.deleted_at')
          ->first();
          if(empty($recent_products)){
            $recent_products = RelatedProduct::select('products.id', 'products.category_id','products.can_giftwrap',
            'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
            'product_pricing.selling_price', 'product_pricing.offer_price','products.product_slug','product_pricing.offer_start_date','product_pricing.offer_end_date',
            'products.product_slug','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
            ->join('products', 'products.id', '=', 'related_products.related_id')
            ->Join('product_details', function($join) use($defaultLanguageId) {
                $join->on('product_details.product_id', '=' , 'products.id');
                $join->where('product_details.language_id','=',$defaultLanguageId);
                $join->whereNull('product_details.deleted_at');
            })
            ->Join('product_pricing', function($join) {
                $join->on('product_pricing.product_id', '=' , 'products.id');
                $join->where('product_pricing.is_default','=',1);
                $join->whereNull('product_pricing.deleted_at');
            })
            ->Join('images',function ($join) {
                $join->on('images.imageable_id', '=' , 'products.id');
                $join->where('images.image_type','=','product');
                $join->whereNull('images.deleted_at');
                $join->where('images.is_default','=','yes');
            })
            ->where('related_products.product_id', $id)
            ->whereNull('products.deleted_at')
            ->first();
        }
        if(!empty($recent_products)){
          $GroupPriceRec=0;
          if($customer_id!=0){
              $customeData=Customer::where("id",$customer_id)->first();
              if($customeData->cust_group_id!=0){
                  $custGrpPrice=CustGroupPrice::where("product_id",$recent_products->id)->where('customer_group_id', $customeData->cust_group_id)->first();
                  if(!empty($custGrpPrice))
                  $GroupPriceRec=$custGrpPrice->price;
              }
          }
        $recentProarr[$p]['id']="".$recent_products['id']."";
        if($recent_products['product_image'] != null)
            $recentProarr[$p]['image'] = $this->getBaseUrl().'/public/images/product/'.$recent_products['id'].'/'.$recent_products['product_image'];
        else
            $recentProarr[$p]['image'] = $this->getBaseUrl().'/public/assets/images/no_image.png';
        $recentProarr[$p]['navigationFlag']='1';
        $recentProarr[$p]['type']='2';
        $recentProarr[$p]['query']=$this->getBaseUrl()."/api/v1/getProductDetails?language_id=".$langId.'&product_id='.$recent_products['id'];
        $recentProarr[$p]['title']=$recent_products['title'];
        if($GroupPriceRec==0){
          if(!empty($pro->offer_price) && (date("Y-m-d",strtotime($recent_products->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($recent_products->offer_end_date)))){
          $recentProarr[$p]['price']=$currenyCode." ".number_format($recent_products['selling_price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
          $recentProarr[$p]['discountedPrice']=$currenyCode." ".number_format($recent_products['offer_price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
        }
        else{
          $recentProarr[$p]['price']=$currenyCode." ".number_format($recent_products['selling_price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
          $recentProarr[$p]['discountedPrice']="";
        }
      }
      else{
        $recentProarr[$p]['price']=$currenyCode." ".number_format($recent_products['selling_price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
        $recentProarr[$p]['discountedPrice']=$currenyCode." ".number_format($GroupPriceRec * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
      }
      //To get category By Nivedita 19 may 2021 //
      $category = Category::where('id', $recent_products['category_id'])->whereNull('deleted_at')->first();
      if($category['photo_upload']!=0)
        $flagInstock=1;
      else{
        if(empty($recent_products['attribute_group_ids']) && empty($recent_products['attribute_ids']))
        {
            if($recent_products['quantity'] >0)
              $flagInstock=1;
            else
              $flagInstock=0;

        }
        else{
          $aatGrpIds=explode(',',$recent_products['attribute_group_ids']);
          $attrIds=explode(',',$recent_products['attribute_ids']);
          foreach($aatGrpIds as $grpId)
          {
            $grp_details = AttributeGroup::select('status','deleted_at')
                                            ->where('id', $grpId)
                                            ->first();
            if($grp_details['status']==0 || !empty($grp_details['deleted_at']))
            $flagInstock=0;
            else
            {
              if($recent_products['quantity'] <= 0)
                $flagInstock = 0;
            }
          }
          foreach($attrIds as $attrId)
          {
            $attr_details = Attribute::select('attribute.status','attribute.deleted_at')
                                      ->where('attribute.id', $attrId)
                                      ->first();
            if(!empty($attr_details) && ($attr_details['status']==0 || !empty($attr_details['deleted_at'])))
            $flagInstock=0;
            else
            {
              if($recent_products['quantity'] <= 0)
                $flagInstock = 1;
            }
          }
        }
      }
      $recentProarr[$p++]['flagInstock']="".$flagInstock."";
      //End To get category //
      }
      $recProArr['title']=$productLabels['RECENTLYVIEWED'];
      $recProArr['list']=$recentProarr;
      $componantRecent=[];
      $componantRecent['componentId']='componentSlider';
      $componantRecent['sequenceId']='12';
      $componantRecent['isActive']='1';
      $componantRecent['componentSliderData']=$recProArr;
    }
    }
    else{
      $componantRecent=[];
      $componantRecent['componentId']='componentSlider';
      $componantRecent['sequenceId']='12';
      $componantRecent['isActive']='0';
      $componantRecent['componentSliderData']['title']=$productLabels['RECENTLYVIEWED'];
      $componantRecent['componentSliderData']['list']=[];
    }

    //Recommended product componant

    $recommended_products = RecommendedProduct::select('products.id','products.category_id','products.can_giftwrap',
    'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
    'product_pricing.selling_price', 'product_pricing.offer_price','products.product_slug','product_pricing.offer_start_date','product_pricing.offer_end_date',
    'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
    ->join('products', 'products.id', '=', 'recommended_products.recommended_id')
    ->Join('product_details', function($join) use($langId) {
        $join->on('product_details.product_id', '=' , 'products.id');
        $join->where('product_details.language_id','=',$langId);
        $join->whereNull('product_details.deleted_at');
    })
    ->Join('product_pricing', function($join) {
        $join->on('product_pricing.product_id', '=' , 'products.id');
        $join->where('product_pricing.is_default','=',1);
        $join->whereNull('product_pricing.deleted_at');
    })
    ->Join('images',function ($join) {
        $join->on('images.imageable_id', '=' , 'products.id');
        $join->where('images.image_type','=','product');
        $join->whereNull('images.deleted_at');
        $join->where('images.is_default','=','yes');
    })
    ->where('recommended_products.product_id', $productId)
    ->whereNull('products.deleted_at')
    ->get();
    if(empty($recommended_products)){
      $recommended_products = RecommendedProduct::select('products.id','products.category_id', 'products.can_giftwrap',
      'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
      'product_pricing.selling_price', 'product_pricing.offer_price','products.product_slug','product_pricing.offer_start_date','product_pricing.offer_end_date',
      'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
      ->join('products', 'products.id', '=', 'recommended_products.recommended_id')
      ->Join('product_details', function($join) use($defaultLanguageId) {
          $join->on('product_details.product_id', '=' , 'products.id');
          $join->where('product_details.language_id','=',$defaultLanguageId);
          $join->whereNull('product_details.deleted_at');
      })
      ->Join('product_pricing', function($join) {
          $join->on('product_pricing.product_id', '=' , 'products.id');
          $join->where('product_pricing.is_default','=',1);
          $join->whereNull('product_pricing.deleted_at');
      })
      ->Join('images',function ($join) {
          $join->on('images.imageable_id', '=' , 'products.id');
          $join->where('images.image_type','=','product');
          $join->whereNull('images.deleted_at');
          $join->where('images.is_default','=','yes');
      })
      ->where('recommended_products.product_id', $productId)
      ->whereNull('products.deleted_at')
      ->get();
    }
    $recommendedProarr=[];
    $r=0;
    $totalPrice=0;
    $flagInstock=1;
    foreach($recommended_products as $pro){
      $GroupPriceRecommended=0;
      if($customer_id){
          $customeData=Customer::where("id",$customer_id)->first();
          if($customeData->cust_group_id!=0){
              $custGrpPrice=CustGroupPrice::where("product_id",$pro->id)->where('customer_group_id', $customeData->cust_group_id)->first();
              if(!empty($custGrpPrice))
              $GroupPriceRecommended=$custGrpPrice->price;
          }
      }
      $recommendedProarr[$r]['id']="".$pro->id."";
      if($pro->product_image != null)
          $recommendedProarr[$r]['image'] = $this->getBaseUrl().'/public/images/product/'.$pro->id.'/'.$pro->product_image;
      else
          $recommendedProarr[$r]['image'] = $this->getBaseUrl().'/public/assets/images/no_image.png';
      $recommendedProarr[$r]['navigationFlag']='0';
    //  $recommendedProarr[$r]['query']=$this->getBaseUrl()."/api/v1/getProductDetails?language_id=".$langId.'&product_id='.$pro->id;
    $recommendedProarr[$r]['query']="";
    $recommendedProarr[$r]['type']="2";
      $recommendedProarr[$r]['title']=$pro->title;
      if($GroupPriceRecommended==0){
          if(!empty($pro->offer_price) && (date("Y-m-d",strtotime($pro->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($pro->offer_end_date)))){
          $recommendedProarr[$r]['price']= $currenyCode." ".number_format($pro->offer_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
          $totalPrice=$totalPrice+$pro->offer_price;
        }
          else{
          $recommendedProarr[$r]['price']= $currenyCode." ".number_format($pro->selling_price * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
          $totalPrice=$totalPrice+$pro->selling_price;
        }
    }
    else{
      $recommendedProarr[$r]['price']= $currenyCode." ".number_format($GroupPriceRecommended * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
      $totalPrice=$totalPrice+$GroupPriceRecommended;
    }
    //To get category By Nivedita 19 may 2021 //
    $category = Category::where('id', $pro->category_id)->whereNull('deleted_at')->first();
    if($category['photo_upload']!=0)
      $flagInstock=1;
    else{
      if(empty($pro->attribute_group_ids) && empty($pro->attribute_ids))
      {
          if($pro->quantity >0)
            $flagInstock=1;
          else
            $flagInstock=0;
      }
      else{
        $aatGrpIds=explode(',',$pro->attribute_group_ids);
        $attrIds=explode(',',$pro->attribute_ids);
        foreach($aatGrpIds as $grpId)
        {
          $grp_details = AttributeGroup::select('status','deleted_at')
                                          ->where('id', $grpId)
                                          ->first();
          if($grp_details['status']==0 || !empty($grp_details['deleted_at']))
          $flagInstock=0;
          else
          {
            if($pro->quantity <= 0)
              $flagInstock = 0;
          }
        }
        foreach($attrIds as $attrId)
        {
          $attr_details = Attribute::select('attribute.status','attribute.deleted_at')
                                    ->where('attribute.id', $attrId)
                                    ->first();
          if(!empty($attr_details) && ($attr_details['status']==0 || !empty($attr_details['deleted_at'])))
          $flagInstock=0;
          else
          {
            if($pro->quantity <= 0)
              $flagInstock = 0;
          }
        }
      }
    }
    $recommendedProarr[$r++]['flagInstock']="".$flagInstock."";
    //End To get category //
    }
    $isAtiveFlag=array();
    foreach($recommendedProarr as $rpro){
      array_push($isAtiveFlag,$rpro['flagInstock']);
    }
    $arrRecommended['title']=$productLabels['FREQUENTLYBOUGHTTOGETHER'];
    $arrRecommended['totalprice']=$currenyCode." ".number_format($totalPrice * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);;
    $arrRecommended['list']=$recommendedProarr;
    $componantRecommended=[];
    $componantRecommended['componentId']='frequentlyBroughtTogether';
    $componantRecommended['sequenceId']='9';
    if(in_array(0, $isAtiveFlag))
    {
        $componantRecommended['isActive']='0';
    }
    else
    {
      if(count($recommended_products)>0)
      $componantRecommended['isActive']='1';
      else
      $componantRecommended['isActive']='0';
    }
    $componantRecommended['frequentlyBroughtTogetherData']=$arrRecommended;

    // image componant
    $componantImage=[];
    $imageList[]=array("image"=>"");
    //$categoryData=Category::select('photo_upload')->where('id',$product->category_id)->first();
    $componantImage['componentId']='uploadImage';
    $componantImage['sequenceId']='5';
    if($categoryData->photo_upload==1)
    $componantImage['isActive']='1';
    else
    $componantImage['isActive']='0';
    $componantImage['uploadImageData']['multiple_image_checkbox']="".$categoryData->upload_is_multiple."";
    $componantImage['uploadImageData']['uploadImageTitle']=$productLabels['UPLOADIMAGE'];
    $componantImage['uploadImageData']['checkBoxTitle']=$productLabels['MULTIPLEIMAGEUPLOAD'];
    $componantImage['uploadImageData']['messageLabel']=$productLabels['WRITEHERE'];
    $componantImage['uploadImageData']['placeHolderMessage']=$productLabels['MSGPRINTSTAFF'];
    $componantImage['uploadImageData']['min_width']=$product->image_min_width;
    $componantImage['uploadImageData']['min_height']=$product->image_min_height;
    $componantImage['uploadImageData']['max_width']=$product->image_max_width;
    $componantImage['uploadImageData']['max_height']=$product->image_max_height;
    $componantImage['uploadImageData']['max_images']="".$product->max_images."";

    // Delivery by Componant
    $componantDeliveryBy=[];
    $deliveryDateList=array("deliveryby"=>"");
    //Get Delivery Date
    $current_date = date("Y-m-d");
    $qty = 1;
    $settingsData=Settings::where('id', 1)->first();
    $minQty = $settingsData->min_qty;
    $minDays = $settingsData->delivery_days;
    $afterDays =$settingsData->delivery_days_exceed_min_qty;

    // Calculate total require days
    $reqDays = $minDays;
    $remQty = $qty - $minQty;
    $additionalQty = 0;
    if($remQty  > 0)
    {
        $additionalQty = ceil($remQty / $minQty);
    }

    // Final require days
    $reqDays = $reqDays + ($additionalQty * $afterDays);
    $delevery_date = $this->getNextDeliveryDate($reqDays, $current_date,1);
    if($product->flag_deliverydate==1)
    $deliveryDateList=array("deliveryby"=>$delevery_date);
    $componantDeliveryBy['componentId']='deliveryBy';
    $componantDeliveryBy['sequenceId']='7';
    if($product->flag_deliverydate==1)
    $componantDeliveryBy['isActive']='1';
    else
    $componantDeliveryBy['isActive']='0';
    $componantDeliveryBy['deliveryByData']=$deliveryDateList;

    // Photo book componant
    $componantPhotoBook=[];
    $PhotoBooks=PhotoBooks::where('link',$product->id)->whereNull('deleted_at')->get();
    $componantPhotoBook['componentId']='photoBookCaption';
    $componantPhotoBook['sequenceId']='8';
    if(!empty($PhotoBooks) && count($PhotoBooks)>0)
    $componantPhotoBook['isActive']='1';
    else
    $componantPhotoBook['isActive']='0';
    $componantPhotoBook['photoBookCaptionData']=array(
                                                      "boxTitle"=>$productLabels['WRITECAPTIONBOOK'],
                                                      "messageTitle"=>$productLabels['PHOTOBOOKPLACEHOLDER']);
    // Photo book note componant
    $componantPhotoBookNote=[];
    $componantPhotoBookNote['componentId']='photoBookNote';
    $componantPhotoBookNote['sequenceId']='11';
    if(!empty($PhotoBooks) && count($PhotoBooks)>0)
    $componantPhotoBookNote['isActive']='1';
    else
    $componantPhotoBookNote['isActive']='0';
    $componantPhotoBookNote['photoBookNoteData']=array(
                                                      "title"=>$productLabels['NOTE'],
                                                      "messageTitle"=>$productLabels['BOOKNOTE']);

   // customize button componant
   $componantCustomizeButton=[];
   $componantCustomizeButton['componentId']='customizeButton';
   $componantCustomizeButton['sequenceId']='13';
   if($product->is_customized==1)
   $componantCustomizeButton['isActive']='1';
   else
   $componantCustomizeButton['isActive']='0';
   $componantCustomizeButton['customizeButtonData']=array(
     "buttonTitle"=>$productLabels['CUSTOMIZE'],
     "buttonURL"=>$this->getBaseUrl()."/design-tool/editor.php?product_base=".$product->design_tool_product_id."&isMobile=1");

      //Product detail final result
      $result['status'] = $productLabels['OK'];
      $result['statusCode'] = "200";
      $result['message'] = $productLabels['SUCCESS'];
      $result['cartCount'] = "".$cartCount."";
      $result['component'] = $componantFour1;
      $result['component'][] = $componantOne;
      $result['component'][] = $componantTwo;
      $result['component'][] = $componantThree;
      $result['component'][] = $componantQuantity;
      $result['component'][] = $componantGift;
      $result['component'][] = $componantPricing;
      $result['component'][] = $componantDescription;
      $result['component'][] = $componantRelated;
      $result['component'][] = $componantRecent;
      $result['component'][] = $componantImage;
      $result['component'][] = $componantRecommended;
      $result['component'][] = $componantDeliveryBy;
      $result['component'][] = $componantPhotoBook;
      $result['component'][] = $componantPhotoBookNote;
      $result['component'][] = $componantCustomizeButton;
    }
  else{
    return response()->json(['status' => "OK",'statusCode' => 500, 'message' => 'Product not available']);
  }

      return response()->json($result);
    }
    // to get delivery date using settings By Nivedita (April 1 2021)
    public function getNextDeliveryDate($reqDays, $date, $currDay = 1)
    {
        $next_date = date('Y-m-d', strtotime($date .' +1 day'));

        // Get day of date
        $day = date("N", strtotime($next_date));

        // If Friday (as per client)
        if($day == 5/* OR $day == 7*/)
        {
            return $this->getNextDeliveryDate($reqDays, $next_date, $currDay);
        }
        else
        {
            // Check if date is in Holiday..Function should return 0 or 1 based on date exists into holiday or not..If exists then 1 else 0
            if($this->checkDateHoliday($next_date))
            {
                return $this->getNextDeliveryDate($reqDays, $next_date, $currDay);
            }
            else
            {
                // check if currDay matches with reqDays
                if($currDay == $reqDays)
                {
                    // Format delivery date
                    $next_delivery = date('l, jS M, Y', strtotime($next_date));

                    return $next_delivery;
                }
                else
                {
                    return $this->getNextDeliveryDate($reqDays, $next_date, $currDay+1);
                }
            }
        }
    }
    public function checkDateHoliday($date)
    {
        // query to check date exists into holiday table or not (non-deleted record)
        $holidays = \App\Models\Holiday::whereDate('date','=', $date)->where('is_deleted', 0)->count();
        if($holidays > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    // To get the next delivery date//
    public function nextDeliveryDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity'  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
              'statusCode' => 300,
              'message' => $validator->errors(),
            ]);
        }

        //Get Delivery Date
        $current_date = date("Y-m-d");
        $qty = $request->quantity;
        $settingsData=Settings::where('id', 1)->first();
        $minQty = $settingsData->min_qty;
        $minDays = $settingsData->delivery_days;
        $afterDays =$settingsData->delivery_days_exceed_min_qty;

        // Calculate total require days
        $reqDays = $minDays;
        $remQty = $qty - $minQty;
        $additionalQty = 0;
        if($remQty  > 0)
        {
            $additionalQty = ceil($remQty / $minQty);
        }

        // Final require days
        $reqDays = $reqDays + ($additionalQty * $afterDays);
        $delevery_date = $this->getNextDeliveryDate($reqDays, $current_date,1);
        if(!empty($delevery_date))  {
          $result['status'] = "OK";
          $result['statusCode'] = 200;
          $result['deliveryby'] = "".$delevery_date."";
          $result['message'] = "Success";
          return response()->json($result);
        }
        else{
          return response()->json(['status' => "OK",'statusCode' => 500, 'message' => $cartLabels['PRODUCTNOTADDEDTOCART']]);
        }
    }
    // public function getAttribute(){
    //   $productId=$_GET['product_id'];
    //   $langId=$_GET['language_id'];
    //   $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
    //   $defaultLanguageId = $defaultLanguageData['id'];
    //   //Default pricing option
    //   $DefaultData = ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku')
    //                                                         ->where('is_default', 1)
    //                                                         ->where('product_id', $productId)
    //                                                         ->whereNull('deleted_at')
    //                                                         ->first();
    //   $arrDefaultSelected = explode(',', $DefaultData->attribute_ids);
    //   $productPricing=ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku')
    //                                                 ->where('product_id', $productId)
    //                                                 ->whereNull('deleted_at')
    //                                                 ->where('is_default', 1)
    //                                                 ->orderBy('id', 'ASC')
    //                                                 ->get();
    //   $j=0;
    //   foreach($productPricing as $data){
    //     $attributeArrGrp=[];
    //     $arrAttrIds = explode(',', $data->attribute_ids);
    //     $arrAttrGrpIds = array_reverse(explode(',', $data->attribute_group_ids));
    //     $listAttr=[];
    //     $l=0;
    //     foreach($arrAttrGrpIds as $GrpID){
    //       $atrrGrpDetails=\App\Models\AttributeGroup::select('attribute_groups.id','attribute_groups.sort_order','attribute_groups.attribute_type_id','GD.name','GD.display_name')
    //                                                   ->join('attribute_group_details as GD','GD.attr_group_id','=','attribute_groups.id')
    //                                                   ->where('attribute_groups.id', $GrpID)
    //                                                   ->where('GD.language_id',$langId)
    //                                                   ->whereNull('attribute_groups.deleted_at')
    //                                                   ->orderBy('attribute_groups.sort_order', 'ASC')
    //                                                   ->first();
    //       if(empty($atrrGrpDetails)){
    //       $atrrGrpDetails=\App\Models\AttributeGroup::select('attribute_groups.id','attribute_groups.sort_order','attribute_groups.attribute_type_id','GD.name','GD.display_name')
    //                                                   ->join('attribute_group_details as GD','GD.attr_group_id','=','attribute_groups.id')
    //                                                   ->where('attribute_groups.id', $GrpID)
    //                                                   ->where('GD.language_id',$defaultLanguageId)
    //                                                   ->whereNull('attribute_groups.deleted_at')
    //                                                   ->orderBy('attribute_groups.sort_order', 'ASC')
    //                                                   ->first();
    //                                                 }
    //       $attribute_details = Attribute::select('attribute.id','attribute.sort_order','attribute.color','attribute.image','AD.name','AD.display_name')
    //                                       ->join('attribute_details as AD','AD.attribute_id','=','attribute.id')
    //                                       ->where('AD.attribute_group_id', $GrpID)
    //                                       ->where('AD.language_id',$langId)
    //                                       ->whereNull('attribute.deleted_at')
    //                                       ->orderBy('sort_order', 'ASC')
    //                                       ->get();
    //       $attribute_details = Attribute::select('attribute.id','attribute.sort_order','attribute.color','attribute.image','AD.name','AD.display_name')
    //                                       ->join('attribute_details as AD','AD.attribute_id','=','attribute.id')
    //                                       ->where('AD.attribute_group_id', $GrpID)
    //                                       ->where('AD.language_id',$defaultLanguageId)
    //                                       ->whereNull('attribute.deleted_at')
    //                                       ->orderBy('sort_order', 'ASC')
    //                                       ->get();
    //
    //       $attrdata=[];
    //       $a=0;
    //       foreach($attribute_details as $val){
    //         $attrdata[$a]['attributeId']="".$val->id."";
    //         $attrdata[$a]['value']="".$val->display_name."";
    //         if(in_array($val->id, $arrDefaultSelected))
    //         $attrdata[$a]['defaultSelected']='1';
    //         else
    //         $attrdata[$a]['defaultSelected']='0';
    //         $attrdata[$a++]['colorCode']="".$val->color."";
    //       }
    //       $attributeArr['attributeGroupId']="".$GrpID."";
    //       $attributeArr['title']="".$atrrGrpDetails->display_name."";
    //       $attributeArr['type']="SS";
    //       $attributeArr['options']=$attrdata;
    //       $listAttr['list'][0]=$attributeArr;
    //     $componantFour1[$l]['componentId']='attributes';
    //     $componantFour1[$l]['attributeType']="".$atrrGrpDetails->attribute_type_id."";
    //     $componantFour1[$l]['sequenceId']='3';
    //     $componantFour1[$l]['isActive']='1';
    //     $componantFour1[$l++]['attributesData']=$listAttr;
    //     }
    //   }
    //
    //   $result['statusCode'] = "200";
    //   $result['component'] = $componantFour1;
    //   return response()->json($result);
    // }

}
?>
