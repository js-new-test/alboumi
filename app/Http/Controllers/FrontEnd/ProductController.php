<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\GlobalCurrency;
use App\Models\Currency;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\ProductDetails;
use App\Models\Category;
use App\Models\Customer;
use App\Models\AttributeGroup;
use App\Models\Attribute;
use App\Models\CustGroupPrice;
use App\Models\Settings;
use App\Models\PhotoBooks;
use DB;
use Auth;

class ProductController extends Controller
{
    use ReuseFunctionTrait;
    protected $category;
    protected $product;

	public function __construct(Category $category,Product $product) {
        $this->category = $category;
        $this->product = $product;
    }

    public function showProductPage($slug,$option_id='')
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);
        $decimalNumber=Session::get('decimal_number');
        $decimalSeparator=Session::get('decimal_separator');
        $thousandSeparator=Session::get('thousand_separator');

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $defaultLanguageCode = \App\Models\WorldLanguage::select('alpha2 as Code')->where('id',$defaultLanguageData['language_id'])->first();
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        $lang_id = Session::get('language_id');

        $Currency_symbol = getCurrSymBasedOnLangId(Session::get('language_id'));
        $get_curr = \App\Models\GlobalLanguage::select('currency_id')->where('id', $lang_id)->first();
        $rate = getCurrencyRates($get_curr->currency_id);
      //  $rate = getCurrencyRates(Session::get('currency_id'));


        //Localization
        $codes = ['CONTACTUSPAGELABEL3', 'BHD', 'PREVIEW','QUANTITY','DELIVERYBY','GIFTWRAPTHISITEM'
        ,'ADDTOCART', 'BUYNOW','FREQUENTLYBOUGHTTOGETHER','APPNAME', 'TOTALPRICE', 'ADDBOTHTOCART',
        'PRICING','DESCRIPTION','EACH','YOUMAYLIKE','EXPLORE','FROM','RECENTLYVIEWED','WRITEMESSAGEFORGIFT',
        'AMAZINGGIFTFORAMAZINGFAMILY','PRODUCT','PRODUCTDETAILSSEODESC','PRODUCTDETAILSSEOKEYWORD','PRODUCTDETAILSDESC','VARIATION','QTY','LADYOPERATORTEXT','GIFTWRAPTHISITEM','WRITEMESSAGEFORGIFT','DEFAULT','INCLOFVAT','CUSTOMIZE','MSGPRINTSTAFF','FILEVALID','UPLOADFILE','CHARLIMIT','MULTIPLEIMAGEUPLOAD','IMAGERESOLUTIONMESSAGE','MAXUPLOADNUMBER','WRITEHERE','MYCOMPUTER','GOOGLEPHOTOS','FACEBOOK','INSTAGRAM','CHOOSEPHOTO','CLEAR','BOOKNOTE','NOTE','WRITECAPTIONBOOK','PHOTOBOOKPLACEHOLDER','INVALIDIMAGES','ACCEPTEDIMAGES','OOS','PLEASEWAIT','PLEASEWAITUPLOAD'];
        $productDetailsLabels = getCodesMsg($lang_id, $codes);

        $baseUrl = $this->getBaseUrl();
        $pageName = $productDetailsLabels["PRODUCT"];
        $projectName = $productDetailsLabels["APPNAME"];

        //Get Product Based on Slug
        $product = $this->getProduct($slug, $lang_id);
        if(!$product)
        {
            $default_lang = $this->getDefaultLanguage();
            $product = $this->getProduct($slug, $default_lang);
        }
        // if product details not available or not deleted
        if(!$product){
          return abort(404);
        }
        //Recent view product details start
        $recent_viewed_product_ids_arr = [];
        Session::push('RECENTLY_VIEWED', $product->id);
        $product_id_arr = array_unique(Session::get('RECENTLY_VIEWED'));
        foreach($product_id_arr as $key)
        {
            if($key == $product->id)
            {
                continue;
            }
            else
            {
                $recent_viewed_product_ids_arr[] = $key;
            }
        }

        $recent_viewed_products = $this->getRecentlyViewedProduct($recent_viewed_product_ids_arr, $lang_id);
        // dd($recent_viewed_products);
        if(empty($recent_viewed_products))
        {
            $default_lang = $this->getDefaultLanguage();
            $recent_viewed_products = $this->getRecentlyViewedProduct($recent_viewed_product_ids_arr, $default_lang);
        }
        //Recent view product details over

        //Get Category Based on category_id
        $category = \App\Models\Category::where('id', $product->category_id)->whereNull('deleted_at')->first();
        //Get category array of category path and reverse
        $category_arr = explode(',', $category->category_path);
        $category_path = array_reverse($category_arr);
        $category_details = \App\Models\CategoryDetails::where('language_id', $lang_id)
        ->join('categories','categories.id','=','category_details.category_id')
        ->whereIn('category_details.category_id', $category_path)->whereNull('category_details.deleted_at')->get();
        if($category_details->count() == 0)
        {
            $default_lang = $this->getDefaultLanguage();
            $category_details = \App\Models\CategoryDetails::where('language_id', $default_lang)
            ->join('categories','categories.id','=','category_details.category_id')
            ->whereIn('category_details.category_id', $category_path)->whereNull('category_details.deleted_at')->get();
        }

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

        //Related Products
        $related_products = $this->getRelatedProduct($product->id, $lang_id);

        if(!$related_products)
        {
            $default_lang = $this->getDefaultLanguage();
            $related_products = $this->getRelatedProduct($product->id, $default_lang);
        }

        //Recommendded Products
        $recommended_products = $this->getRecommendedProduct($product->id, $lang_id);

        if(!$recommended_products)
        {
            $default_lang = $this->getDefaultLanguage();
            $recommended_products = $this->getRecommendedProduct($product->id, $default_lang);
        }

        //Get explore link based on default or other language
        //$explore_link = $this->getLinkBasedOnLangId($lang_id);
        $explore_link = $baseUrl.'/product';

        //Get pricing tab data
        $pricing_tab = $this->getPricingTabData($product->id);
        $pricing_tab_qtyrng = $this->getPricingTabQtyRange($product->id);
        $pricing_tab_variations = $this->getVariation($product->id,$lang_id);
        if(empty($pricing_tab_variations))
        {
        $default_lang = $this->getDefaultLanguage();
        $pricing_tab_variations = $this->getVariation($product->id,$default_lang);
        }
        //To get default pricing details on basis of option_id or is_default
        if($option_id)
          $DefaultData = $this->getDefaultDetails($product->id,$option_id);
        else
          $DefaultData = $this->getDefaultDetails($product->id);
        // To create option array like size shape etc...
        $arrProductAttributes=[];
        $arrProductAttributeGroups=[];
        $arrDefaultSelected=[];
        $arrOptions=[];
        if(!empty($DefaultData)){
        if(!$DefaultData->attribute_ids){
           $arrOptions['option_id']=$DefaultData->option_id;
           $arrOptions['sku']=$DefaultData->sku;
           $arrOptions['quantity']=$DefaultData->quantity;
           $arrOptions['selling_price']=$DefaultData->selling_price;
           $arrOptions['offer_price']=$DefaultData->offer_price;
        }
        else{
            $arrDefaultSelected = explode(',', $DefaultData->attribute_ids);
            $AllOptionDataforProduct=$this->getAllDetailsByProductId($product->id);
            $o=0;
            foreach($AllOptionDataforProduct as $data){
              $arrAttrIds = explode(',', $data->attribute_ids);
              $arrAttrGrpIds = array_reverse(explode(',', $data->attribute_group_ids));
              $arrOptions[$o]['option_id'] = $data->id;
              $arrOptions[$o]['sku'] = $data->sku;
              $arrOptions[$o]['quantity'] = $data->quantity;
              $arrOptions[$o]['selling_price'] = $data->selling_price;
              $arrOptions[$o]['offer_price'] = $data->offer_price;
              foreach($arrAttrGrpIds as $GrpIds){
                $arrProductAttributeGroups[$GrpIds]=$GrpIds;
              }
              foreach($arrAttrIds as $AttIds){
                $arrProductAttributes[$AttIds]=$AttIds;
              }
              $o++;
            }
        }
      }
      // To check product oos//
      $attributesActive=1;
        $arrAttributeGroups=[];
        foreach($arrProductAttributeGroups as $AtrGrpIds){
            $ProAttGrpData=$this->getProAttGrpData($AtrGrpIds,$lang_id);
            if(empty($ProAttGrpData))
            $ProAttGrpData=$this->getProAttGrpData($AtrGrpIds,$defaultLanguageId);
            if(!empty($ProAttGrpData) && ($ProAttGrpData['status']==0 || !empty($ProAttGrpData['deleted_at'])))
            $attributesActive=0;
            $arrAttributeGroups[$AtrGrpIds]=$ProAttGrpData;
        }
        foreach($arrDefaultSelected as $attrIds){
          $attr_details = Attribute::select('attribute.status','attribute.deleted_at')
                                    ->where('attribute.id', $attrIds)
                                    ->first();
          if(!empty($attr_details) && ($attr_details['status']==0 || !empty($attr_details['deleted_at'])))
          $attributesActive=0;
        }
      // End To check product oos//

        $productImages=$this->getProductImages($product->id);
        // dd($productImages);

        $langVisibility = GlobalLanguage::checkVisibility($lang_id);
        $customer_id = Session::get('customer_id');
        $GroupPrice=0;
        if($customer_id){
            $customeData=Customer::where("id",$customer_id)->first();
            if($customeData->cust_group_id!=0){
                $custGrpPrice=CustGroupPrice::where("product_id",$product->id)->where('customer_group_id', $customeData->cust_group_id)->first();
                if(!empty($custGrpPrice))
                $GroupPrice=$custGrpPrice->price;
            }
        }
        $PhotoBooks=PhotoBooks::where('link',$product->id)->whereNull('deleted_at')->get();
        return view('frontend.product-details', compact('pageName','projectName','product',
        'explore_link','category','category_details','delevery_date','related_products','baseUrl',
        'recent_viewed_products','megamenuFileName','productDetailsLabels','pricing_tab','pricing_tab_qtyrng','pricing_tab_variations','mobileMegamenuFileName','DefaultData','Currency_symbol','rate','option_id','arrOptions','arrDefaultSelected','arrProductAttributeGroups','arrProductAttributes','arrAttributeGroups','langVisibility','productImages','lang_id','decimalNumber','decimalSeparator','thousandSeparator','GroupPrice','recommended_products','PhotoBooks','attributesActive'));
    }
    // to get delivery date using settings By Nivedita (April 1 2021)
    public function getNextDeliveryDateByajax($qty){
      //Get Delivery Date
      $current_date = date("Y-m-d");
      $qty = $qty;
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
      return $delevery_date = $this->getNextDeliveryDate($reqDays, $current_date,1);
    }
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

    public function getProduct($slug, $lang_id)
    {
        $product = \App\Models\Product::select('products.id','products.product_slug','products.max_images','products.can_giftwrap','product_details.title','products.flexmedia_code','products.sku'
        ,'products.category_id', 'product_details.description', 'product_details.key_features', 'product_pricing.selling_price','product_pricing.offer_start_date','product_pricing.offer_end_date',
        'product_pricing.offer_price', 'images.name as product_image','product_details.meta_description','product_details.meta_title','product_details.meta_keyword','products.is_customized','products.flag_deliverydate','products.design_tool_product_id','products.image_min_height','products.image_min_width','products.image_max_height','products.image_max_width')
        // ->join('product_details', 'product_details.product_id', '=', 'products.id')
        // ->join('product_pricing', 'product_pricing.product_id', '=', 'products.id')
        // ->join('images', 'images.imageable_id', '=', 'products.id')
        ->leftJoin('product_details', function($join) use($lang_id) {
            $join->on('product_details.product_id', '=' , 'products.id');
            $join->where('product_details.language_id','=',$lang_id);
            $join->whereNull('product_details.deleted_at');
        })
        ->leftJoin('product_pricing', function($join) {
            $join->on('product_pricing.product_id', '=' , 'products.id');
            $join->where('product_pricing.is_default','=',1);
            $join->whereNull('product_pricing.deleted_at');
        })
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->whereNull('images.deleted_at');
            // $join->where('images.is_default','=','yes');
        })
        ->where('products.product_slug', $slug)
        ->where('products.status', 'Active')
        // ->where('product_details.language_id', $lang_id)
        // ->where('images.image_type', 'product')
        ->whereNull('products.deleted_at')
        ->first();
        return $product;
    }

    public function getRelatedProduct($product_id, $lang_id)
    {
        $custId = Auth::guard('customer')->user();
        // dd($custId);
        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }

        $related_products = \App\Models\RelatedProduct::select('products.id','products.category_id', 'products.can_giftwrap',
        'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
        'product_pricing.selling_price', 'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date',
        'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity','products.product_slug',DB::raw($group_price))
        ->join('products', 'products.id', '=', 'related_products.related_id')
        ->Join('product_details', function($join) use($lang_id) {
            $join->on('product_details.product_id', '=' , 'products.id');
            $join->where('product_details.language_id','=',$lang_id);
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
        ->where('related_products.product_id', $product_id)
        ->whereNull('products.deleted_at');
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $related_products = $related_products->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                        $join->on('cgp.product_id', '=' , 'products.id');
                                        $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
            });
        }
        $relatedproducts = $related_products->get();
        if(empty($related_products)){
          $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
          $defaultLanguageId = $defaultLanguageData['id'];
          $related_products = \App\Models\RelatedProduct::select('products.id','products.category_id', 'products.can_giftwrap',
          'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
          'product_pricing.selling_price', 'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date',
          'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity','products.product_slug',DB::raw($group_price))
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
          ->where('related_products.product_id', $product_id)
          ->where('product_details.language_id', $defaultLanguageId)
          ->whereNull('products.deleted_at');
          if(!empty($custId) && $custId->cust_group_id != 0)
          {
              $related_products = $related_products->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                          $join->on('cgp.product_id', '=' , 'products.id');
                                          $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
              });
          }
          $relatedproducts = $related_products->get();
        }
          $flagInstock=1;$i=0;
          foreach($relatedproducts as $pro){
            //To get category By Nivedita 19 may 2021 //
            $category = Category::where('id', $pro->category_id)->whereNull('deleted_at')->first();
            if(!empty($category) && $category['photo_upload']!=0)
              $flagInstock=1;
            else{
              if(empty($pro->attribute_group_ids) && empty($pro->attribute_ids))
              {
                  if($pro->quantity >0){
                    $flagInstock=1;
                  }
                  else{
                    $flagInstock=0;
                  }
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
            $relatedproducts[$i]['flagInstock']=$flagInstock;
            $i++;
            //End To get category //
          }
        return $relatedproducts;
    }

    public function getRecommendedProduct($product_id, $lang_id)
    {
        $recommended_products = \App\Models\RecommendedProduct::select('products.id','products.category_id', 'products.can_giftwrap',
        'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
        'product_pricing.selling_price', 'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date','products.product_slug',
        'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
        ->join('products', 'products.id', '=', 'recommended_products.recommended_id')
        ->Join('product_details', function($join) use($lang_id) {
            $join->on('product_details.product_id', '=' , 'products.id');
            $join->where('product_details.language_id','=',$lang_id);
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
        ->where('recommended_products.product_id', $product_id)
        ->where('product_details.language_id', $lang_id)
        ->whereNull('products.deleted_at')
        ->get();
        if(empty($recommended_products) || count($recommended_products)==0){
          $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
          $defaultLanguageId = $defaultLanguageData['id'];
          $recommended_products = \App\Models\RecommendedProduct::select('products.id','products.category_id', 'products.can_giftwrap',
          'product_details.title','products.category_id', 'product_details.description','images.name as product_image',
          'product_pricing.selling_price', 'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date',
          'products.product_slug','product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity')
          ->join('products', 'products.id', '=', 'recommended_products.recommended_id')
          ->Join('product_details', function($join) use($lang_id) {
              $join->on('product_details.product_id', '=' , 'products.id');
              $join->where('product_details.language_id','=',$lang_id);
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
          ->where('recommended_products.product_id', $product_id)
          ->where('product_details.language_id', $defaultLanguageId)
          ->whereNull('products.deleted_at')
          ->get();
        }
        $i=0;
        foreach($recommended_products as $pro){
          $flagInstock=1;
          $customer_id = Session::get('customer_id');
          $GroupPrice=0;
          if($customer_id){
              $customeData=Customer::where("id",$customer_id)->first();
              if($customeData->cust_group_id!=0){
                  $custGrpPrice=CustGroupPrice::where("product_id",$pro->id)->where('customer_group_id', $customeData->cust_group_id)->first();
                  if(!empty($custGrpPrice))
                  $GroupPrice=$custGrpPrice->price;
              }
          }
          //To get category By Nivedita 19 may 2021 //
          $category = Category::where('id', $pro->category_id)->whereNull('deleted_at')->first();
          if(!empty($category) && $category['photo_upload']!=0)
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
          //End To get category //
          if($GroupPrice==0){
            if(!empty($pro->offer_price) && (date("Y-m-d",strtotime($pro->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($pro->offer_end_date))))
            $recommended_products[$i]['price']=$pro->offer_price;
            else
            $recommended_products[$i]['price']=$pro->selling_price;
          }
          else{
            $recommended_products[$i]['price']=$GroupPrice;
          }
          $recommended_products[$i]['flagInstock']=$flagInstock;
          $i++;
        }
        $isAtiveFlag=array();
        foreach($recommended_products as $rpro){
          array_push($isAtiveFlag,$rpro['flagInstock']);
        }
        if (in_array(0, $isAtiveFlag))
        return $recommended_products=[];
        else
        return $recommended_products;
    }

    public function getRecentlyViewedProduct($recent_viewed_product_ids_arr, $lang_id)
    {
        $custId = Auth::guard('customer')->user();
        // dd($custId);
        $group_price = "NULL as group_price";
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $group_price = "cgp.price as group_price";
        }

        $recent_viewed_products = \App\Models\Product::select('products.id','products.category_id',
        'product_details.title','product_details.description', 'product_pricing.selling_price',
        'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date', 'products.product_slug', 'images.name as product_image',
        'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity',DB::raw($group_price))
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->join('product_pricing', 'product_pricing.product_id', '=', 'product_details.product_id')
        ->join('images', 'images.imageable_id', '=', 'products.id')
        ->whereIn('product_details.product_id', $recent_viewed_product_ids_arr)
        ->where('product_pricing.is_default', 1)
        ->where('product_details.language_id', $lang_id)
        ->where('images.image_type', 'product')
        ->where('images.is_default', 'yes')
        ->whereNull('images.deleted_at')
        ->whereNull('products.deleted_at');
        if(!empty($custId) && $custId->cust_group_id != 0)
        {
            $recent_viewed_products = $recent_viewed_products->leftJoin('customer_group_price as cgp', function($join) use($custId){
                                        $join->on('cgp.product_id', '=' , 'products.id');
                                        $join->where('cgp.customer_group_id','=',$custId->cust_group_id);
            });
        }
        $recent_viewed_products = $recent_viewed_products->get();
        if(empty($recent_viewed_products) || count($recent_viewed_products) == 0){
          $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
          $defaultLanguageId = $defaultLanguageData['id'];
          $recent_viewed_products = \App\Models\Product::select('products.id','products.category_id',
          'product_details.title','product_details.description', 'product_pricing.selling_price',
          'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date', 'products.product_slug', 'images.name as product_image',
          'product_pricing.attribute_group_ids','product_pricing.attribute_ids','product_pricing.quantity',DB::raw($group_price))
          ->join('product_details', 'product_details.product_id', '=', 'products.id')
          ->join('product_pricing', 'product_pricing.product_id', '=', 'product_details.product_id')
          ->join('images', 'images.imageable_id', '=', 'products.id')
          ->whereIn('product_details.product_id', $recent_viewed_product_ids_arr)
          ->where('product_pricing.is_default', 1)
          ->where('product_details.language_id', $defaultLanguageId)
          ->where('images.image_type', 'product')
          ->where('images.is_default', 'yes')
          ->whereNull('images.deleted_at')
          ->whereNull('products.deleted_at')
          ->get();
        }
        $flagInstock=1;$i=0;
        foreach($recent_viewed_products as $pro){
          //To get category By Nivedita 19 may 2021 //
          $category = Category::where('id', $pro->category_id)->whereNull('deleted_at')->first();
          if(!empty($category) && $category['photo_upload']!=0)
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
                  if($pro->quantity <=0)
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
          $recent_viewed_products[$i]['flagInstock']=$flagInstock;
          $i++;
          //End To get category //
        }
        return $recent_viewed_products;
    }

    public function getLinkBasedOnLangId($session_language_id)
    {
        $baseUrl = $this->getBaseUrl();
        $default_lang = \App\Models\GlobalLanguage::where('is_default', 1)->where('status', 1)
        ->where('is_deleted', 0)->where('id', $session_language_id)->first();
        if(empty($default_lang)){
            $other_lang = \App\Models\GlobalLanguage::select('world_languages.alpha2 as lang_code')
            ->leftJoin('world_languages','world_languages.id','=','global_language.language_id')
            ->where('status', 1)->where('is_deleted', 0)->where('global_language.id', $session_language_id)->first();
            return $baseUrl.'/'.$other_lang->lang_code."/product";
        }else{
            return $baseUrl.'/product';
        }
    }
    // To check if there are bulk pricing for a product
    public function getPricingTabData($product_id)
    {
        $product_pricing = \App\Models\ProductPricing::where('product_id', $product_id)->pluck('id');
        $product_bulk_pricing = \App\Models\ProductBulkPrices::whereIn('option_id', $product_pricing)->count();
        if($product_bulk_pricing > 0)
        {
            return $product_bulk_pricing;
        }
    }
    // To get pricing tab ranges lables
    public function getPricingTabQtyRange($productId)
    {
       $product_pricing = \App\Models\ProductPricing::select('id')->where('product_id', $productId)
                                                    ->where('is_default', 1)
                                                    ->whereNull('deleted_at')
                                                    ->first();
      $product_bulk_range=array();
        if($product_pricing){
        $product_bulk_range = \App\Models\ProductBulkPrices::select('id','from_quantity','to_quantity','price')
                                                              ->where('option_id', $product_pricing->id)
                                                              ->where('product_id', $productId)
                                                              ->whereNull('deleted_at')
                                                              ->orderBy('from_quantity', 'ASC')
                                                              ->get();
      }
      return $product_bulk_range;
    }
    // To get all variation of a Product
    public function getVariation($product_id,$lang_id)
    {
        $product_pricing = \App\Models\ProductPricing::select('id','attribute_ids','product_id','is_default', 'attribute_group_ids')
                                                      ->where('product_id', $product_id)
                                                      ->whereNull('deleted_at')
                                                      ->orderBy('is_default', 'DESC')
                                                      ->orderBy('id', 'ASC')
                                                      ->get();
        if($product_pricing){
          $i = 0;
          $data=[];

          // Size attribute group id
          $sizeAttribute = config('app.sizeAttribute.size');

          foreach($product_pricing as $pricing){
            if(empty($pricing['attribute_ids'])){
              $data[$i]['option_id']=$pricing['id'];
              $data[$i]['displayname']="DEFAULT";
              $data[$i]['pricedata']=$this->getVariationPrices($product_id,$pricing['id']);
              $data[$i++]['ids']="";
            }
            else{
                $pAttribites = explode(',',$pricing['attribute_ids']);
                $pGroups = explode(',',$pricing['attribute_group_ids']);

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

                    if($k < count($pAttribites))
                    $displayname.=\App\Models\ProductPricing::getOptionVariations($attr,$lang_id).', ';
                    else
                    $displayname.=\App\Models\ProductPricing::getOptionVariations($attr,$lang_id);
                    $k++;
                }

                // rtrim
                $displayname = rtrim($displayname, ", ");

                $data[$i]['option_id']=$pricing['id'];
                $data[$i]['displayname']=$displayname;
                $data[$i]['pricedata']=$this->getVariationPrices($product_id,$pricing['id']);
                $data[$i++]['ids']=$pricing['attribute_ids'];
            }
          }
        }
        return $data;
    }
    //To get prices of all variations of all options
    public function getVariationPrices($product_id,$option_id)
    {
        $product_bulk_price = \App\Models\ProductBulkPrices::select('id','from_quantity','to_quantity','price')
                                                              ->where('option_id', $option_id)
                                                              ->where('product_id', $product_id)
                                                              ->orderBy('from_quantity', 'ASC')
                                                              ->get();
        return $product_bulk_price;
    }
    // To get default pricing data on p[age load of details page
    public function getDefaultDetails($product_id,$option_id="")
    {
        if($option_id){
        $product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku','offer_start_date','offer_end_date','image')
                                                              ->where('id', $option_id)
                                                              ->where('product_id', $product_id)
                                                              ->whereNull('deleted_at')
                                                              ->first();
        }
        else{
        $product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku','offer_start_date','offer_end_date','image')
                                                              ->where('is_default', 1)
                                                              ->where('product_id', $product_id)
                                                              ->whereNull('deleted_at')
                                                              ->first();
        }
        return $product_details;
    }

    // To get all optionsg data of product
    public function getAllDetailsByProductId($product_id)
    {
        $product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price','quantity','attribute_ids','attribute_group_ids','sku')
                                                      ->where('product_id', $product_id)
                                                      ->whereNull('deleted_at')
                                                      ->orderBy('is_default', 'DESC')
                                                      ->orderBy('id', 'ASC')
                                                      ->get();
        return $product_details;
    }

    // Attribute group details
    public function getProAttGrpData($GrpID,$lang_id){
      $group_details = \App\Models\AttributeGroup::select('attribute_groups.id','attribute_groups.sort_order','attribute_groups.attribute_type_id','GD.name','GD.display_name','attribute_groups.status','attribute_groups.deleted_at')
                                                  ->join('attribute_group_details as GD','GD.attr_group_id','=','attribute_groups.id')
                                                  ->where('attribute_groups.id', $GrpID)
                                                  ->where('GD.language_id',$lang_id)
                                                  //->whereNull('attribute_groups.deleted_at')
                                                  ->orderBy('attribute_groups.sort_order','ASC')
                                                  ->first();
      $data=[];
      if(!empty($group_details)){
          $data['attribute_group_id']=$group_details->id;
          $data['display_name']=$group_details->display_name;
          $data['name']=$group_details->name;
          $data['sort_order']=$group_details->sort_order;
          $data['type']=$group_details->attribute_type_id;
          $data['status']=$group_details->status;
          $data['attributes']=$this->getProAttGrpAttrData($GrpID,$lang_id);
       }
      return $data;
      }

      // Attribute group attribute details
      public function getProAttGrpAttrData($GrpID,$lang_id){
      $attribute_details = \App\Models\Attribute::select('attribute.id','attribute.sort_order','attribute.color','attribute.image','AD.name','AD.display_name','attribute.status','attribute.deleted_at')
                                                    ->join('attribute_details as AD','AD.attribute_id','=','attribute.id')
                                                    ->where('AD.attribute_group_id', $GrpID)
                                                    ->where('AD.language_id',$lang_id)
                                                  //  ->whereNull('attribute.deleted_at')
                                                    ->orderBy('sort_order', 'ASC')
                                                    ->get();
      if(empty($attribute_details)){
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $attribute_details = \App\Models\Attribute::select('attribute.id','attribute.sort_order','attribute.color','attribute.image','AD.name','AD.display_name','attribute.status','attribute.deleted_at')
                                                      ->join('attribute_details as AD','AD.attribute_id','=','attribute.id')
                                                      ->where('AD.attribute_group_id', $GrpID)
                                                      ->where('AD.language_id',$defaultLanguageId)
                                                      //->whereNull('attribute.deleted_at')
                                                      ->orderBy('sort_order', 'ASC')
                                                      ->get();
      }
      $i=0;
      $data=[];
      foreach($attribute_details as $val){
        $data[$i]['attribute_id']=$val->id;
        $data[$i]['display_name']=$val->display_name;
        $data[$i]['name']=$val->name;
        $data[$i]['sort_order']=$val->sort_order;
        $data[$i]['color']=$val->color;
        $data[$i]['status']=$val->status;
        $data[$i++]['image']=$val->image;
      }
      return $data;
    }

    public function getFilteredProducts(Request $request)
    {
        if(!empty($_GET['skip']))
            $skip = $_GET['skip'];
        else
            $skip = 0;

        $langId = $request->language_id;
        $sortBy = $request->sort_by;
        $mainCatId = $request->category_id;
        $searchVal = $request->searchVal;
        $codes = ['FILTERBY', 'SORTBY','FROM','EXPLORE','NOPRODAVAILABLE',"OOS"];
        $prodListingLabels = getCodesMsg($langId, $codes);

        $categoryIds = $prices = $attribute_ids = $finalPrice = $brandIds = [];
        $totalFilteredProducts = 0;
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData;

        if(!empty($request->filterQuery))
        {
            foreach($request->filterQuery as $filter)
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
                    else if($filter['attribute_id'] == "Brands")
                    {
                        $brandIds = explode(",",$filter['option_id']);
                        $brandIds =(str_replace('"','', $brandIds));
                        $brandIds = str_replace('[','', $brandIds);
                        $brandIds = str_replace(']','', $brandIds);
                    }
                    else
                    {
                        $attribute_ids[$filter['attribute_id']] = implode(',',explode(",",$filter['option_id']));
                        $attribute_ids[$filter['attribute_id']] =(str_replace('"','', $attribute_ids[$filter['attribute_id']]));
                        $attribute_ids[$filter['attribute_id']] = str_replace('[','', $attribute_ids[$filter['attribute_id']]);
                        $attribute_ids[$filter['attribute_id']] = str_replace(']','', $attribute_ids[$filter['attribute_id']]);
                    }
                }
            }
        }
        // dd($attribute_ids);
        $result = Array();
        $i = 0;
        foreach($attribute_ids as $key => $attributeIds)
        {
            $value = explode(',',$attributeIds);
            $result[$key] = $value;
        }
        // die;
        // dd($result);
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
        $custId = Auth::guard('customer')->user();

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

        if($request->pageName == "searchResultFilter")
        {
            DB::enableQueryLog();
            $prodIds = '';
            if(!empty($request->prodIds))
                $prodIds = $request->prodIds;

            $filteredProducts = Product::select('products.id','pd.title',DB::raw($selling_price),DB::raw($offer_price),'images.name as image','product_slug',DB::raw($offer_end_date),DB::raw($offer_start_date),DB::raw($group_price)
                                    ,DB::raw('IF(cgp.price IS NOT NULL, cgp.price, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, product_pricing.offer_price, product_pricing.selling_price)) as productPrice, IF(cgp.price IS NOT NULL, 1, IF("'.date('Y-m-d').'" BETWEEN product_pricing.offer_start_date AND product_pricing.offer_end_date AND product_pricing.offer_price > 0, 2, 3)) as productSale
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
                                        // $join->where('product_pricing.is_default','=',1);
                                        $join->whereNull('product_pricing.deleted_at');
                                    })
                                    ->whereRaw( "(pd.title like ? OR cd.title like ? OR product_pricing.sku like ? )", array('%'.$searchVal.'%','%'.$searchVal.'%','%'.$searchVal.'%'))
                                    ->whereNull('products.deleted_at')
                                    ->where('products.status','Active')
                                    ->where('products.category_id','!=',null)
                                    ->whereNull('c.deleted_at')
                                    ->where('c.status',1)
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
                                    // if(!empty($priceArr))
                                    // {
                                    //     foreach($priceArr as $price)
                                    //     {
                                    //         $min = $price['min'];
                                    //         $max = $price['max'];
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
                                    if(!empty($priceArr))
                                    {

                                        foreach($priceArr as $price)
                                        {
                                            $min = number_format(($price['min']),3);
                                            $max = number_format(($price['max']),3);
                                            // dd($max);

                                            if(!empty($custId) && $custId->cust_group_id != 0)
                                            {
                                                $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".($min)."' and cgp.price <= '".($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($min)."' and product_pricing.offer_price <='".($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($min)."' and product_pricing.selling_price <='".($max)."')))");
                                            }
                                            else
                                            {
                                                $filteredProducts = $filteredProducts->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($min)."' and product_pricing.offer_price <='".($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($min)."' and product_pricing.selling_price <='".($max)."'))");
                                            }
                                        }
                                    }

                                    if(!empty($result))
                                    {
                                        $filteredProducts = $filteredProducts->join('product_pricing as pp', function($join) {
                                            $join->on('pp.product_id', '=' , 'products.id');
                                            $join->whereNull('pp.deleted_at');
                                        });

                                        foreach($result as $key => $attributeIds)
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
                                        if(!empty($prodIds))
                                            $filteredProducts = $filteredProducts->whereIn('products.id',$prodIds);
                                    }
                                    if(!empty($brandIds))
                                    {
                                        $filteredProducts = $filteredProducts->whereIn('products.manufacturer_id',$brandIds);
                                    }
            $filteredProducts1 = $filteredProducts->get();
            $totalFilteredProducts = count($filteredProducts1);
            $filteredProducts = $filteredProducts->paginate(12);
            // dd(DB::getQueryLog());
        }
        else
        {
            $prodIds = '';
            DB::enableQueryLog();
            if(!empty($request->prodIds))
                $prodIds = $request->prodIds;

            // dd($request->prodIds);
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
                                        // $join->where('product_pricing.is_default','=',1);
                                        $join->whereNull('product_pricing.deleted_at');
                                    })
                                    ->whereRaw("FIND_IN_SET('".$mainCatId."', c.category_path)")
                                    ->whereNull('products.deleted_at')
                                    ->where('products.status','Active')
                                    ->where('products.category_id','!=',null)
                                    ->whereNull('c.deleted_at')
                                    ->where('c.status',1)
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
                                        $filteredProducts = $filteredProducts->orderBy('products.category_id','asc');

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
                                    if(!empty($priceArr))
                                    {

                                        foreach($priceArr as $price)
                                        {
                                            $min = number_format(($price['min']),3);
                                            $max = number_format(($price['max']),3);
                                            // dd($max);

                                            if(!empty($custId) && $custId->cust_group_id != 0)
                                            {
                                                $filteredProducts = $filteredProducts->whereRaw("IF(cgp.price IS NOT NULL ,(cgp.price >='".($min)."' and cgp.price <= '".($max)."'),((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($min)."' and product_pricing.offer_price <='".($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($min)."' and product_pricing.selling_price <='".($max)."')))");
                                            }
                                            else
                                            {
                                                $filteredProducts = $filteredProducts->whereRaw("((product_pricing.offer_price > 0 and product_pricing.offer_price >= '".($min)."' and product_pricing.offer_price <='".($max)."' AND '".date('Y-m-d')."' between product_pricing.offer_start_date and product_pricing.offer_end_date AND product_pricing.offer_start_date != null and product_pricing.offer_end_date != null)
                                                OR (product_pricing.selling_price > 0 and product_pricing.selling_price >= '".($min)."' and product_pricing.selling_price <='".($max)."'))");
                                            }
                                        }
                                    }
                                    if(!empty($result))
                                    {
                                        $filteredProducts = $filteredProducts->join('product_pricing as pp', function($join) {
                                            $join->on('pp.product_id', '=' , 'products.id');
                                            $join->whereNull('pp.deleted_at');
                                        });

                                        foreach($result as $key => $attributeIds)
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
                                        // if(!empty($prodIds))
                                        //     $filteredProducts = $filteredProducts->whereIn('products.id',$prodIds);
                                    }
                                    if(!empty($brandIds))
                                    {
                                        $filteredProducts = $filteredProducts->whereIn('products.manufacturer_id',$brandIds);
                                    }

            $filteredProducts1 = $filteredProducts->get();
            $totalFilteredProducts = count($filteredProducts1);
            $filteredProducts = $filteredProducts->paginate(12);
            // $filteredProducts = $filteredProducts->get();
            // dd(DB::getQueryLog());
        }

        // dd($filteredProducts);
        $resultArr = [];
        $i = 0;
        if(!empty($filteredProducts))
        {
            foreach($filteredProducts as $filteredProduct)
            {
                $prodTitle = $filteredProduct['title'];
                if($prodTitle == null)
                {
                    $prodDetails = ProductDetails::select('title')
                                                ->where('product_id',$filteredProduct['id'])
                                                ->where('language_id',$defaultLanguageId)
                                                ->whereNull('deleted_at')
                                                ->first();
                    $prodTitle =  $prodDetails['title'];
                }
                $resultArr[$i]['id'] = "".$filteredProduct['id']."";
                $resultArr[$i]['title'] = $prodTitle;

                if(empty($filteredProduct['group_price']))
                {
                    if (!empty($filteredProduct['discountedPrice']) && (date("Y-m-d", strtotime($filteredProduct['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d", strtotime($filteredProduct['offer_end_date'])))) {
                        $resultArr[$i]['discountedPrice'] = $filteredProduct['discountedPrice'];
                        $resultArr[$i]['price'] = $filteredProduct['price'];
                        $resultArr[$i]['group_price'] = '';
                    }
                    else if(!empty($filteredProduct['discountedPrice']))
                    {
                        $resultArr[$i]['discountedPrice'] = $filteredProduct['discountedPrice'];
                        $resultArr[$i]['price'] = $filteredProduct['price'];
                        $resultArr[$i]['group_price'] = '';
                    }
                    else
                    {
                        $resultArr[$i]['group_price'] = '';
                        $resultArr[$i]['discountedPrice'] = '';
                        $resultArr[$i]['price'] = $filteredProduct['price'];
                    }
                }
                else
                {
                    $resultArr[$i]['group_price'] = $filteredProduct['group_price'];
                    $resultArr[$i]['discountedPrice'] = '';
                    $resultArr[$i]['price'] = $filteredProduct['price'];
                }
                $resultArr[$i]['slug'] = $filteredProduct['product_slug'];

                if($filteredProduct['image'] != null)
                    $resultArr[$i]['image'] = $this->getBaseUrl().'/public/images/product/'.$filteredProduct['id'] . '/' . $filteredProduct['image'];
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

            $filteredProductsList[] = $filteredProducts;

            $defaultLanguageDataDetails = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;

            // dd($custId);
            // $sideBarOptions = $this->category->getFilterOptions($filteredProductsList,$request->language_id,$defaultLanguageDataDetails,$categoryIds,$brandIds,$attribute_ids,$custId);
            $sideBarOptions = [];
            $minPrice = $maxPrice = 0;
            if(!empty($sideBarOptions['price']))
            {
                $minPrice = number_format(min($sideBarOptions['price'])  * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator);
                $maxPrice = number_format(max($sideBarOptions['price'])  * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator);
            }
        }
        if(!empty($resultArr))
            return response()->json(['status' => true, 'resultArr' => $resultArr, 'labels' => $prodListingLabels,'sideBarOptions'=> $sideBarOptions,'selectedOptions' => $request->filterQuery,'minPrice' => $minPrice,'maxPrice' => $maxPrice,'totalFilteredProductsCount' => $totalFilteredProducts]);
        else
            return response()->json(['status' => false, 'labels' => $prodListingLabels]);
    }

    public function getProductImages($product_id,$limit="")
    {
        if(!empty($limit)){
        $images=\App\Models\Image::select('images.name as product_image','is_default')
                                    ->where('imageable_id',$product_id)
                                    ->where('image_type','product')
                                    ->whereNull('deleted_at')
                                    ->orderByRaw('is_default','DESC')
                                    ->orderBy('sort_order','ASC')
                                    ->limit($limit)
                                    ->get();
        }
        else{
          $images=\App\Models\Image::select('images.name as product_image','is_default')
                                      ->where('imageable_id',$product_id)
                                      ->where('image_type','product')
                                      ->whereNull('deleted_at')
                                      ->orderByRaw('is_default','DESC')
                                      ->orderBy('sort_order','ASC')
                                      ->get();
        }
        return $images;
    }

    public function getSearchResult(Request $request)
    {
        if(!empty($_GET['skip']))
            $skip = $_GET['skip'];
        else
            $skip = 0;

        $pageSize = $pageNo = 0;

        $searchVal = $request->searchVal;
        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();

        $setSessionforLang = setSessionforLang($defaultLanguageData['language_id']);
        $langId = Session::get('language_id');

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $decimalNumber=Session::get('decimal_number');
        $decimalSeparator=Session::get('decimal_separator');
        $thousandSeparator=Session::get('thousand_separator');

        $currencyCode = getCurrSymBasedOnLangId($langId);
        $custId = Auth::guard('customer')->user();
        $defaultCurrency = \App\Models\GlobalCurrency::select('currency.currency_code')
                                                        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                                                        ->where('global_currency.is_deleted', 0)
                                                        ->where('global_currency.is_default', 1)
                                                        ->first();

        $currenyIdFromLang = GlobalLanguage::select('currency.currency_code','global_currency.currency_id','global_currency.id')
                                                ->leftJoin('global_currency','global_currency.id','=','global_language.currency_id')
                                                ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                                                ->where('global_language.id',$langId)
                                                ->where('global_language.is_deleted',0)
                                                ->first();

        $conversionRate = getCurrencyRates($currenyIdFromLang->id);
        $baseUrl = $this->getBaseUrl();

        $codes = ['APPNAME','MOSTRECENT', 'ONSALE', 'PRICELTOH', 'PRICEHTOL','SORTBY','FROM','FILTERBY','PRICE','CATLISTING','SEARCHRESULT','BRAND'
                ,'MIN','MAX','CLEAR_ALL','SHOPNOW','EXPLORE','CATEGORIES','SORT','FILTER','NOPRODAVAILABLE','SEARCHRESULTFOR','LOADMORE','OOS'];
        $productSortLabels = getCodesMsg($langId, $codes);

        DB::enableQueryLog();
        $filteredProducts = $this->product->getSearchedProducts($searchVal,$langId,$custId,$pageSize,$pageNo,$skip,$request);

        $allprod = $this->product->getSearchProductsCount($searchVal,$langId,$custId,$pageSize,$pageNo,$skip,$request);
        $totalProductsCount = count($allprod);


        $allFilteredProducts[] = $allprod;

        $resultFilterArr = $this->category->getFilterOptions($allFilteredProducts,$langId,$defaultLanguageData,$custId);
        // dd($resultFilterArr);
        $minPrice = $maxPrice = 0;
        if(!empty($resultFilterArr['price']))
        {
            $minPrice = number_format(min($resultFilterArr['price'])  * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
            $maxPrice = number_format(max($resultFilterArr['price'])  * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
        }

        $pageName = $productSortLabels['SEARCHRESULT'];
        $projectName = $productSortLabels['APPNAME'];

        // $totalProductsCount = $this->product->getSearchProductsCount($searchVal,$langId,$custId,$pageSize,$pageNo,$skip,$request);

        if(!empty($_GET['ajax']))
        {
            if(str_replace('"','',$_GET['ajax']) == "ajax")
            {
                $allFilteredProducts[] = $filteredProducts;
                return response()->json(['productsList' => array($filteredProducts),'baseUrl' => $baseUrl , 'productSortLabels'=>$productSortLabels]);
            }
        }
        else
        {
            return view('frontend/product/searchedProducts', compact(
                'filteredProducts',
                'baseUrl',
                'conversionRate',
                'megamenuFileName',
                'mobileMegamenuFileName',
                'totalProductsCount',
                'minPrice',
                'maxPrice',
                'currencyCode',
                'productSortLabels',
                'resultFilterArr',
                'pageName',
                'projectName',
                'searchVal',
                'decimalNumber',
                'decimalSeparator',
                'thousandSeparator'
            ));
        }
    }
}
