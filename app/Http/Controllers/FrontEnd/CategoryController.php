<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryDetails;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\Product;
use App\Models\GlobalCurrency;
use App\Models\ProductPricing;
use App\Models\AttributeDetails;
use App\Models\AttributeGroupDetails;
use App\Models\Attribute;
use App\Models\Currency;
use DB;
use Illuminate\Support\Facades\Session;
use Auth;

class CategoryController extends Controller
{
    protected $product;
    protected $category;

	public function __construct(Product $product,Category $category) {
        $this->product = $product;
        $this->category = $category;
    }

    public function getCategoryAndProducts($slug,$sortBy = null)
    {       
        if(!empty($_GET['skip']))
            $skip = $_GET['skip'];
        else
            $skip = 0;

        $pageSize = $pageNo = 0;

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultLangId = $defaultLanguageData['id'];

        $setSessionforLang = setSessionforLang($defaultLanguageData['language_id']);
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $decimalNumber=Session::get('decimal_number');
        $decimalSeparator=Session::get('decimal_separator');
        $thousandSeparator=Session::get('thousand_separator');

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $langId = Session::get('language_id');
        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$langId)->first();

        $currencyCode =getCurrSymBasedOnLangId($langId);

        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;
        
        $custId = Auth::guard('customer')->user();
        $codes = ['APPNAME','MOSTRECENT', 'ONSALE', 'PRICELTOH', 'PRICEHTOL','SORTBY','FROM','FILTERBY','PRICE','CATLISTING','OOS','BRAND'
                ,'MIN','MAX','CLEAR_ALL','SHOPNOW','EXPLORE','CATEGORIES','SORT','FILTER','NOPRODAVAILABLE','SEARCHRESULTFOR','LOADMORE'];
        $productSortLabels = getCodesMsg($langId, $codes);
        
        $categoryDetails = Category::select('categories.id','categories.banner_image as main_banner','categories.mobile_banner_image as main_mb_banner','cd.title','cd.description','cd.meta_title',
                                    'cd.meta_description','cd.meta_keywords','cd.banner_image','cd.mobile_banner')
                                    ->join('category_details as cd','cd.category_id','=','categories.id')
                                    ->where('slug',$slug)
                                    ->whereNull('categories.deleted_at')
                                    ->whereNull('cd.deleted_at')
                                    ->where('status',1)
                                    ->where('language_id',$langId)
                                    ->first();
                                    
        if($categoryDetails == null)
        {
            $categoryDetails = Category::select('categories.id','categories.banner_image as main_banner','categories.mobile_banner_image as main_mb_banner','cd.title','cd.description','cd.meta_title',
                                        'cd.meta_description','cd.meta_keywords','cd.banner_image','cd.mobile_banner')
                                        ->join('category_details as cd','cd.category_id','=','categories.id')
                                        ->where('slug',$slug)
                                        ->whereNull('categories.deleted_at')
                                        ->whereNull('cd.deleted_at')
                                        ->where('status',1)
                                        ->where('language_id',$defaultLanguageData['id'])
                                        ->first();
        }
        if($categoryDetails == null)
        {
            return redirect('/');
        }
        $childCatgories = Category::getChildCategories($categoryDetails['id'], $langId);

        $productsList = [];
        $totalProductsCount = $totalCount = 0;
        $product = new Product;
        // if(!empty($childCatgories))
        // {
        //     foreach ($childCatgories as $cat)
        //     {
        //         if(!empty($cat))
        //         {
        //             $products = $this->product->getProductsFromCategory($cat['id'], $langId,$sortBy,$custId,$pageSize,$pageNo,$skip,$_GET);
        //             $productsList[] = $products;
        //             $totalCount += $this->product->getProductsFromCategoryCount($cat['id'], $langId,$sortBy,$custId,$pageSize,$pageNo,$skip,$_GET);
        //         }
        //         $totalProductsCount = $totalCount;
        //     }
        // }
        // else
        // {
            $productsList[] = $this->product->getProductsFromCategory($categoryDetails['id'], $langId,$sortBy,$custId,$pageSize,$pageNo,$skip,$_GET);
            $filteredProducts = $this->product->getProductsFromCategoryCount($categoryDetails['id'], $langId,$sortBy,$custId,$pageSize,$pageNo,$skip,$_GET);
            $totalCount = count($filteredProducts);
        // }
        // dd($categoryDetails);
        if($totalCount > 0)
            $totalProductsCount = $totalCount;
        else
            $totalProductsCount = count($productsList[0]);
        
        $allProducts[] = $filteredProducts;
        $resultFilterArr = $this->category->getFilterOptions($allProducts,$langId,$defaultLanguageData,$custId);
        $baseUrl = $this->getBaseUrl();
        
        $pageName = isset($categoryDetails->meta_title) ? $categoryDetails->meta_title : $productSortLabels['CATLISTING'];
        $projectName = $productSortLabels['APPNAME'];

        $minPrice = $maxPrice = 0;
        // dd($resultFilterArr['category']);
        if(!empty($resultFilterArr['price']))
        {
            $minPrice = number_format(min($resultFilterArr['price'])  * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
            $maxPrice = number_format(max($resultFilterArr['price'])  * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
        }
        
        if(!empty($categoryDetails))
        {  
            if(!empty($_GET['ajax']))
            {
                if(str_replace('"','',$_GET['ajax']) == "ajax")
                {
                    return response()->json(['productsList' => $productsList,'baseUrl' => $baseUrl , 'productSortLabels'=>$productSortLabels]);
                }
            }
            else
            {
                return view('frontend.category.categoryDetails',compact('megamenuFileName','pageName','projectName','baseUrl','langId','maxPrice',
                'categoryDetails','childCatgories','productsList','productSortLabels','conversionRate','currencyCode','mobileMegamenuFileName','resultFilterArr','minPrice',
                'decimalNumber','decimalSeparator','thousandSeparator','totalProductsCount','slug','defaultLangId'));
            }            
            // die;
        }
        else
        {
            $codes = ['404MSG','RETURNHOME','ENQSUCCESS'];
            $notFoundLabels = getCodesMsg($langId, $codes);
        
            $pageName = $productSortLabels['ENQSUCCESS'];
            $projectName = $productSortLabels['APPNAME'];
        
            return view('frontend.404',compact('projectName', 'pageName', 'baseUrl','megamenuFileName','notFoundLabels','mobileMegamenuFileName'));
        }
        
    }
}
?>