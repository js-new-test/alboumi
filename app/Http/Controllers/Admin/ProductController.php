<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Traits\ExportTrait;
use App\Traits\CommonTrait;
use App\Models\Manufacturer;
use App\Models\Image as ImageModel;
use App\Models\Category;
use App\Models\CategoryDetails;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\RecommendedProduct;
use App\Models\RelatedProduct;
use App\Models\GlobalLanguage;
use DataTables;
use Storage;
use Auth;
use App\Models\UserTimezone;
use DB;
use Image;
use Config;
use App\Models\AttributeGroup;
use App\Models\AttributeTypes;
use App\Models\Attribute;
use App\Models\ProductBulkPrices;
use App\Models\ProductPricing;
use App\Models\TaxClass;
use App\Models\CustomerGroups;
use App\Models\CustGroupPrice;
use App\Models\LumiseProducts;

class ProductController extends Controller
{
	use ExportTrait, CommonTrait;
    // use CommonTrait;
	/**
     *
     * Creating an Object of model
     */
	protected $product;
	protected $category;
	protected $manufacturer;
	protected $image;
	protected $taxclass;
	protected $CustomerGroups;

	public function __construct(Category $category, Product $product, Manufacturer $manufacturer, Image $image, TaxClass $taxclass, CustomerGroups $CustomerGroups) {
        $this->category = $category;
        $this->product = $product;
        $this->manufacturer = $manufacturer;
        $this->image = $image;
				$this->taxclass = $taxclass;
				$this->CustomerGroups = $CustomerGroups;
        $this->projectName = "Alboumi";
    }


	public function getProduct()
	{
		$baseUrl = $this->getBaseUrl();
        $projectName = $this->projectName;
        $pageTitle = 'Products';
        $languages = GlobalLanguage::join('world_languages as wl','wl.id','=','language_id')
                                    ->select('global_language.id','alpha2','langEN','is_default')
                                    ->where('status',1)
                                    ->where('is_deleted',0)
                                    ->get();
		return view('admin.product.products', compact('baseUrl', 'projectName', 'pageTitle', 'languages'));
	}

	public function getAddProduct()
	{
        $projectName = $this->projectName;
        $pageTitle = 'Add Product';
		    $baseUrl = $this->getBaseUrl();

        $page = isset($_GET['page']) ? $_GET['page'] : "addProduct";
        $productId = isset($_GET['productId']) ? $_GET['productId'] : "";
        $formTitle = "Add Product";

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguage = $defaultLanguageData['language']['langEN'];
        $defaultLanguageId = $defaultLanguageData['id'];

        if (!empty($productId)) {
            $ProductDetails = ProductDetails::where(['product_id'=>$productId, 'language_id'=>$defaultLanguageId])->whereNull('deleted_at')->first();
            $productName = $ProductDetails->title;
        }

        $language = "";
        if ($page == "anotherLanguage") {
            //get existing language ids
            $existingLanguageIds = ProductDetails::where('product_id', $productId)->whereNull('deleted_at')->get()->pluck('language_id')->toArray();

            $language = GlobalLanguage::select('global_language.id as globalLanguageId', 'world_languages.langEN as languageName')
            ->Join('world_languages', 'world_languages.id', '=', 'global_language.language_id')
            ->where('global_language.status', 1)
            ->where('global_language.is_deleted', 0)
            ->whereNotIn('global_language.id', $existingLanguageIds)
            ->get()->toArray();

            $formTitle = "Add Product - Other Language ($productName)";
        }
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();

		return view('admin.product.addProduct', compact('baseUrl', 'projectName', 'pageTitle', 'defaultLanguage', 'defaultLanguageId', 'otherLanguages', 'page', 'productId', 'language'));
	}

    public function getBrands(Request $request)
    {
        $languageId = $request['languageId'] ?? "";
        if (empty($languageId)) {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $languageId = $defaultLanguageData['id'];
        }
        $brands = Manufacturer::select('manufacturers.id', 'brand_details.name as brandName')
                ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
                ->where('language_id',$languageId)
                ->whereNull('manufacturers.deleted_at')
                ->whereNull('brand_details.deleted_at')
                ->get();

        return $brands;
    }

    public function getCategoryForProduct(Request $request)
    {
        return $this->category->categoryDropDown($request['languageId']);
    }
		public function getCustGroups(Request $request)
    {
        return $this->CustomerGroups->groupDropDown($request['product_id']);
    }
		public function getTaxClassForProduct(Request $request)
    {
			$Taxclass = TaxClass::select('id', 'name')
							->whereNull('deleted_at')
							->get();

			return $Taxclass;
    }
		public function getLumiseProduct(Request $request)
    {
		$lumiseProduct=LumiseProducts::select("id","name")->where("active",1)->get();

			return $lumiseProduct;
    }

	public function postAddProduct(Request $request)
	{
		// try {
        $data = $request->all();
        // dd($data);
        if ($data['page'] == "addProduct") {


            // if(!isset($data['categoryId']) || $data['categoryId'] == '') {
            //     return array(
            //         'success' => false,
            //         'message' => trans('messages.error.product.category_required')
            //     );
            // }
            $productSlug = Product::where('product_slug', '=', $data['productSlug'])->first();
            if(!empty($productSlug)){
                return array(
                    'success' => false,
                    'message' => 'Product Slug is already exists'
                );
            }

            if($data['length'] == 0){
                return array(
                    'success' => false,
                    'message' => 'Length cannot be zero'
                );
            }
            if($data['width'] == 0){
                return array(
                    'success' => false,
                    'message' => 'Width cannot be zero'
                );
            }

            if($data['height'] == 0){
                return array(
                    'success' => false,
                    'message' => 'Height cannot be zero'
                );
            }
        }
            $product = $this->product->saveProduct($data);


            if($product != '') {

                return array(
                    'success' => true,
                    'productId' => $product,
                    'message' => "Product added Successfully"
                );
            } else {
                return array(
                    'success' => false,
                    'message' => trans('messages.error.product.product_add')
                );
            }



        // } catch (\Exception $ex) {

        //     return array(
        //         'success' => false,
        //         'message' => trans('messages.error.product.product_add')
        //     );
        // }
	}

	public function postEditProduct(Request $request) {
		$data = $request;

		switch ($data['editPage']) {
			case 'GENERALINFO':
				return $this->product->editProductGeneralDetails($data);
				break;

			case 'INVENTORY':
				return $this->product->editProductInventoryDetails($data);
				break;

			case 'VIDEO':
				return $this->product->editProductVideoDetails($data);
				break;

			case 'SPECIFICATION':
				return $this->product->editProductSpecificationDetails($data);
				break;

      case 'BULKPRICING':
        return $this->product->editProductBulkPricing($data);
        break;
		 case 'ADVANCEPRICING':
	 			 return $this->product->addProductAdvancePricing($data);
	 			 break;
		case 'EDITADVANCEPRICING':
		 		return $this->product->editProductAdvancePricing($data);
		 		break;

			default:

				break;
		}
	}
	public function postEditPricingOption(Request $request) {
		$data = $request;
		$id= $this->product->editProductSpecificationDetails($data);
		$notification = array(
				'message' => 'Pricing data updated successfully!',
				'alert-type' => 'success'
		);

		return redirect('admin/product/editProduct/'.$id)->with($notification);

	}

	public function postDownloadPincodeSample(Request $request) {
        return $this->downloadPincode($request['part'], $request['productId']);
	}

    public function getProductImages(Request $request) {
    	$images = ImageModel::where('imageable_type', "App\Models\Product")->where('imageable_id',$request['productId'])->whereNull('deleted_at')->get();
    	return DataTables::of($images)->make();
    }

    public function getRelatedProducts(Request $request) {

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData['id'];

    	$relatedProducts = RelatedProduct::where('product_id',$request['productId'])->get()->pluck('related_id');

    	$products = Product::select('products.id', 'products.sku', 'product_details.title', 'images.upload_path as uploadPath', 'images.name as imageName')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
						$join->whereNull('images.deleted_at');
        })
        ->where('product_details.language_id', $defaultLanguageId)
				->whereNull('products.deleted_at')
        ->whereNotIn('products.id',$relatedProducts);

    	return DataTables::of($products)
        ->filter(function ($query) use ($request) {
            if ($request['categoryId'] != "") {
                $query->where('products.category_id', '=', $request->get('categoryId'));
            }
            if ($request['brandId'] != "") {
                $query->where('products.manufacturer_id', '=', $request->get('brandId'));
            }
        })
        ->make();
    }

    public function getRecomendedProducts(Request $request) {

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData['id'];

    	$recomendedProducts = RecommendedProduct::where('product_id',$request['productId'])->get()->pluck('recommended_id');

        $products = Product::select('products.id', 'products.sku', 'product_details.title', 'images.upload_path as uploadPath', 'images.name as imageName')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
						$join->whereNull('images.deleted_at');
        })
        ->where('product_details.language_id', $defaultLanguageId)
				->whereNull('products.deleted_at')
        ->whereNotIn('products.id',$recomendedProducts);

    	return DataTables::of($products)->filter(function ($query) use ($request) {
            if ($request['categoryId'] != "") {
                $query->where('products.category_id', '=', $request->get('categoryId'));
            }
            if ($request['brandId'] != "") {
                $query->where('products.manufacturer_id', '=', $request->get('brandId'));
            }
        })
        ->make();
    }

		public function getAdvancePricing(Request $request) {
        $advancePricing = CustGroupPrice::select('customer_group_price.id as pricingId', 'customer_group_price.price', 'customer_groups.group_name')
        ->join('customer_groups', 'customer_groups.id', '=', 'customer_group_price.customer_group_id')
        ->where('customer_group_price.product_id', $request['productId'])->get();
    	return DataTables::of($advancePricing)->make();
    }

    public function getSelectedCategoriesUsingIds(Request $request) {
    	$categories = Category::whereIn('id',$request['categoryIds'])->get();
    	return $categories;
    }

    public function getAllProducts(Request $request) {
        $id = Auth::guard('admin')->user()->id;
        $timezone = UserTimezone::where('user_id', $id)->pluck('zone')->first();

    	$products = Product::select('products.id', 'product_pricing.sku','products.product_slug' ,'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle', 'product_details.id as productDetailsId', 'images.upload_path as uploadPath', 'images.name as imageName', 'brand_details.name as brandName', 'product_pricing.selling_price as sellingPrice','products.is_customized','products.design_tool_product_id')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->join('category_details', 'category_details.category_id', '=', 'categories.id')
        ->join('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->LeftJoin('product_pricing', function($join) {
            $join->on('product_pricing.product_id', '=' , 'products.id');
            $join->where('product_pricing.is_default','=',1);
            $join->whereNull('product_pricing.deleted_at');
        })
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
            $join->whereNull('images.deleted_at');
        })
        ->where('product_details.language_id',$request['languageId'])
        ->where('brand_details.language_id',$request['languageId'])
				->where('category_details.language_id',$request['languageId'])
        ->whereNull('product_details.deleted_at')
        ->whereNull('products.deleted_at');

        if (!empty($request['categoryId']) && $request['categoryId'] != "all") {
            $products = $products->where('products.category_id',$request['categoryId']);
        }
        if (!empty($request['brandId']) && $request['brandId'] != "all") {
            $products = $products->where('products.manufacturer_id',$request['brandId']);
        }
        if (!empty($request['status'])) {
            $products = $products->where('products.status',$request['status']);
        }
        if (!empty($request['stock']) && $request['stock'] != "all") {
            $products = $products->where('products.out_of_stock',$request['stock']);
        }
				if ($request['customize'] != "all") {
            $products = $products->where('products.is_customized',$request['customize']);
        }
			  $products=$products->groupBy('products.id');
        $products = $products->get();

    	return DataTables::of($products)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make();
    }

    public function getAllActiveProducts(Request $request) {
        $id = Auth::guard('admin')->user()->id;
        $timezone = UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $products = Product::select('products.id', 'product_pricing.sku','products.product_slug', 'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle', 'product_details.id as productDetailsId','images.upload_path as uploadPath', 'images.name as imageName', 'brand_details.name as brandName', 'product_pricing.selling_price as sellingPrice','products.is_customized','products.design_tool_product_id')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->join('category_details', 'category_details.category_id', '=', 'categories.id')
        ->join('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->LeftJoin('product_pricing', function($join) {
            $join->on('product_pricing.product_id', '=' , 'products.id');
            $join->where('product_pricing.is_default','=',1);
        })
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
        })
        ->where('product_details.language_id',$request['languageId'])
        ->where('brand_details.language_id',$request['languageId'])
				->where('category_details.language_id',$request['languageId'])
        ->where('products.status', '=', "Active")
        ->where('products.out_of_stock', '=', "No")
        ->whereNull('product_details.deleted_at')
        ->whereNull('products.deleted_at');

				if (!empty($request['categoryId']) && $request['categoryId'] != "all") {
            $products = $products->where('products.category_id',$request['categoryId']);
        }
        if (!empty($request['brandId']) && $request['brandId'] != "all") {
            $products = $products->where('products.manufacturer_id',$request['brandId']);
        }
        if (!empty($request['status'])) {
            $products = $products->where('products.status',$request['status']);
        }
        if (!empty($request['stock']) && $request['stock'] != "all") {
            $products = $products->where('products.out_of_stock',$request['stock']);
        }
				if ($request['customize'] != "all") {
            $products = $products->where('products.is_customized',$request['customize']);
        }
				$products=$products->groupBy('products.id');
        $products = $products->get();

        return DataTables::of($products)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make();
    }

    public function getAllInactiveProducts(Request $request) {
        $id = Auth::guard('admin')->user()->id;
        $timezone = UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $products = Product::select('products.id', 'product_pricing.sku','products.product_slug', 'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle', 'product_details.id as productDetailsId','images.upload_path as uploadPath', 'images.name as imageName', 'brand_details.name as brandName', 'product_pricing.selling_price as sellingPrice','products.is_customized','products.design_tool_product_id')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->join('category_details', 'category_details.category_id', '=', 'categories.id')
        ->join('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->LeftJoin('product_pricing', function($join) {
            $join->on('product_pricing.product_id', '=' , 'products.id');
            $join->where('product_pricing.is_default','=',1);
        })
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
        })
        ->where('product_details.language_id',$request['languageId'])
        ->where('brand_details.language_id',$request['languageId'])
				->where('category_details.language_id',$request['languageId'])
        ->where('products.status', '=', "Inactive")
        ->where('products.out_of_stock', '=', "No")
        ->whereNull('product_details.deleted_at')
        ->whereNull('products.deleted_at');

				if (!empty($request['categoryId']) && $request['categoryId'] != "all") {
						$products = $products->where('products.category_id',$request['categoryId']);
				}
				if (!empty($request['brandId']) && $request['brandId'] != "all") {
						$products = $products->where('products.manufacturer_id',$request['brandId']);
				}
				if (!empty($request['status'])) {
						$products = $products->where('products.status',$request['status']);
				}
				if (!empty($request['stock']) && $request['stock'] != "all") {
						$products = $products->where('products.out_of_stock',$request['stock']);
				}
				if ($request['customize'] != "all") {
						$products = $products->where('products.is_customized',$request['customize']);
				}
				$products=$products->groupBy('products.id');
        $products = $products->get();

        return DataTables::of($products)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make();
    }

    public function getAllRejectedProducts(Request $request) {
        $id = Auth::guard('admin')->user()->id;
        $timezone = UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $products = Product::select('products.id', 'products.sku', 'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle', 'product_details.id as productDetailsId','images.upload_path as uploadPath', 'images.name as imageName', 'brand_details.name as brandName', 'product_pricing.selling_price as sellingPrice','products.is_customized')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->join('category_details', 'category_details.category_id', '=', 'categories.id')
        ->join('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->LeftJoin('product_pricing', function($join) {
            $join->on('product_pricing.product_id', '=' , 'products.id');
            $join->where('product_pricing.is_default','=',1);
        })
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
        })
        ->where('product_details.language_id',$request['languageId'])
        ->where('brand_details.language_id',$request['languageId'])
        ->where('products.status', '=', "Rejected")
        ->where('products.out_of_stock', '=', "No")
        ->whereNull('product_details.deleted_at')
        ->whereNull('products.deleted_at');

        if (!empty($request['categoryId'])) {
            $products = $products->where('products.category_id',$request['categoryId']);
        }
        if (!empty($request['brandId'])) {
            $products = $products->where('products.manufacturer_id',$request['brandId']);
        }
        if (!empty($request['status'])) {
            $products = $products->where('products.status',$request['status']);
        }
        if (!empty($request['stock']) && $request['stock'] != "all") {
            $products = $products->where('products.out_of_stock',$request['stock']);
        }
        $products = $products->get();

        return DataTables::of($products)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make();

    }

    public function getAllOutOfStockProducts(Request $request) {
        $id = Auth::guard('admin')->user()->id;
        $timezone = UserTimezone::where('user_id', $id)->pluck('zone')->first();

        $products = Product::select('products.id', 'products.sku', 'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle', 'product_details.id as productDetailsId','images.upload_path as uploadPath', 'images.name as imageName', 'brand_details.name as brandName', 'product_pricing.selling_price as sellingPrice')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->join('category_details', 'category_details.category_id', '=', 'categories.id')
        ->join('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->LeftJoin('product_pricing', function($join) {
            $join->on('product_pricing.product_id', '=' , 'products.id');
            $join->where('product_pricing.is_default','=',1);
        })
        ->leftJoin('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
        })
        ->where('product_details.language_id',$request['languageId'])
        ->where('brand_details.language_id',$request['languageId'])
        ->where('products.out_of_stock', '=', "Yes")
        ->whereNull('product_details.deleted_at')
        ->whereNull('products.deleted_at');

        if (!empty($request['categoryId'])) {
            $products = $products->where('products.category_id',$request['categoryId']);
        }
        if (!empty($request['brandId'])) {
            $products = $products->where('products.manufacturer_id',$request['brandId']);
        }
        if (!empty($request['status'])) {
            $products = $products->where('products.status',$request['status']);
        }
        if (!empty($request['stock']) && $request['stock'] != "all") {
            $products = $products->where('products.out_of_stock',$request['stock']);
        }
        $products = $products->get();

        return DataTables::of($products)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make();
    }

    //save related products
    public function postRelatedProduct(Request $request)
    {
    	$data = $request->all();
    	foreach ($data['selectedProducts'] as $key) {
    		$relatedProduct = new RelatedProduct;
    		$relatedProduct->product_id = $data['productId'];
    		$relatedProduct->related_id = $key;
    		$relatedProduct->save();
    	}
        return array(
                'success' => true,
                'message' => trans('Related Products added successfully')
            );
    }

    public function postRecomendedProduct(Request $request)
    {
        $data = $request->all();

    	foreach ($data['selectedProducts'] as $key) {
    		$recommendedProduct = new RecommendedProduct;
    		$recommendedProduct->product_id 	= $data['productId'];
    		$recommendedProduct->recommended_id = $key;
    		$recommendedProduct->save();
    	}
        return array(
                'success' => true,
                'message' => trans('Recommended Products added successfully')
            );
    }

    public function getRelatedProduct(Request $request) // changed by Pallavi (March 9, 2021)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $languageId = $defaultLanguageData['id'];

        // DB::enableQueryLog();
        $relatedProducts = RelatedProduct::select('related_products.id as relatedId','products.id', 'products.sku', 'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle','images.upload_path as uploadPath', 'images.name as imageName')
        ->join('products', 'products.id', '=', 'related_products.related_id')
				->Join('product_details', function($join) use($languageId) {
						$join->on('product_details.product_id', '=' , 'products.id');
						$join->where('product_details.language_id','=',$languageId);
						$join->whereNull('product_details.deleted_at');
				})
				->Join('categories',function ($join) {
            $join->on('categories.id', '=' , 'products.category_id');
        })
				->Join('category_details',function ($join) use($languageId) {
            $join->on('category_details.category_id', '=' , 'categories.id');
						$join->where('category_details.language_id','=',$languageId);
						$join->whereNull('category_details.deleted_at');
        })
        //->join('product_details', 'product_details.product_id', '=', 'related_products.related_id')
        //->join('categories', 'categories.id', '=',  'products.category_id')
        //->join('category_details', 'category_details.category_id', '=',  'categories.id')
        ->Join('images',function ($join) {
            $join->on('images.imageable_id', '=' , 'products.id');
            $join->where('images.image_type','=','product');
            $join->where('images.is_default','=','yes');
						$join->whereNull('images.deleted_at');
        })
        ->where('related_products.product_id', $request['productId'])
        //->where('product_details.language_id',$languageId)
        //->whereNull('product_details.deleted_at')
        ->whereNull('products.deleted_at')
        ->get();
      // dd($relatedProducts);
    	return DataTables::of($relatedProducts)->make();
    }

    public function getRecommendedProduct(Request $request)// changed by Pallavi (March 9, 2021)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $languageId = $defaultLanguageData['id'];

        $recommendedProducts = RecommendedProduct::select('recommended_products.id as recommendedId','products.id', 'products.sku', 'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle','images.upload_path as uploadPath', 'images.name as imageName')
        ->join('products', 'products.id', '=', 'recommended_products.recommended_id')
				->Join('product_details', function($join) use($languageId) {
						$join->on('product_details.product_id', '=' , 'products.id');
						$join->where('product_details.language_id','=',$languageId);
						$join->whereNull('product_details.deleted_at');
				})
				->Join('categories',function ($join) {
						$join->on('categories.id', '=' , 'products.category_id');
				})
				->Join('category_details',function ($join) use($languageId) {
						$join->on('category_details.category_id', '=' , 'categories.id');
						$join->where('category_details.language_id','=',$languageId);
						$join->whereNull('category_details.deleted_at');
				})
				//->join('product_details', 'product_details.product_id', '=', 'related_products.related_id')
				//->join('categories', 'categories.id', '=',  'products.category_id')
				//->join('category_details', 'category_details.category_id', '=',  'categories.id')
				->Join('images',function ($join) {
						$join->on('images.imageable_id', '=' , 'products.id');
						$join->where('images.image_type','=','product');
						$join->where('images.is_default','=','yes');
						$join->whereNull('images.deleted_at');
				})
        ->where('recommended_products.product_id', $request['productId'])//->get();
        //->where('product_details.language_id',$languageId)
      //  ->whereNull('product_details.deleted_at')
        ->whereNull('products.deleted_at');
    	return DataTables::of($recommendedProducts)->make();
    }

    public function getEditProduct($id)
    {
        $projectName = $this->projectName;
        $pageTitle = 'Edit Product';
        $baseUrl = $this->getBaseUrl();
        $page = isset($_GET['page']) ? $_GET['page'] : "editProduct";

        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;

        $productDetails = ProductDetails::with('product')->with('productVideo')->where('id', $id)->first();

        $category = Category::find($productDetails->product->category_id);
        $quantityMatrix = $category->qty_matrix ?? 0;

        $productId = $productDetails->product_id;

        $existingLanguageIds = ProductDetails::where('product_id', $productId)->whereNull('deleted_at')->get()->pluck('language_id')->toArray();
				$pricingOption = $this->ProductPricingOptionData($productId);
				$categoryAttrData=$this->CategoryAttribute($productDetails['product']->category_id);

        $language = GlobalLanguage::select('global_language.id as globalLanguageId', 'world_languages.langEN as languageName')
        ->Join('world_languages', 'world_languages.id', '=', 'global_language.language_id')
        ->where('global_language.status', 1)
        ->whereIn('global_language.id', $existingLanguageIds)
        ->get()->toArray();

        return view('admin.product.editProduct', compact('projectName', 'pageTitle', 'baseUrl', 'page', 'defaultLanguageId', 'language', 'productDetails', 'quantityMatrix','pricingOption','categoryAttrData'));
    }

    public function getDeleteProduct(Request $request)
    {
        $defaultLanguage = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguage->id;

        $productDetails = ProductDetails::find($request->productDetailId);
        $catDetails = Product::find($productDetails->product_id);

        if ($productDetails->language_id == $defaultLanguageId)
        {
            $temp = ['deleted_at'=>now()];
            $updatebrands = ProductDetails::where('product_id', $productDetails->product_id)->update($temp);
            $productDelete = Product::find($productDetails->product_id);
            $productDelete->deleted_at = now();
            $productDelete->save();

            $productsFromcategory = Product::whereNull('deleted_at')->where('category_id',$catDetails->category_id)->get();
            if($productsFromcategory->isEmpty())
            {
                Category::where('id',$catDetails->category_id)->update(['flag_product'=>0]);
            }
            return array(
                'success' => true,
                'message' => trans('Product Deleted Successfully')
            );
        }
        else
        {
            $productDetails->deleted_at = now();
            $productDetails->save();
            $productsFromcategory = Product::whereNull('deleted_at')->where('category_id',$catDetails->category_id)->get();
            if($productsFromcategory->isEmpty())
            {
                Category::where('id',$catDetails->category_id)->update(['flag_product'=> 0]);
            }
            return array(
                'success' => true,
                'message' => trans('Product Deleted Successfully')
            );
        }
    }

    // delete related product - by Pallavi (March 9, 2021)
    public function deleteRelatedProduct(Request $request)
    {
        $relatedProduct = RelatedProduct::where('id',$request->relatedId)->delete();
        if($relatedProduct == 1)
        {
            return array(
                'success' => true,
                'message' => trans('Related Product Deleted Successfully')
            );
        }
        else
        {
            return array(
                'success' => false,
                'message' => trans('Something went wrong !!')
            );
        }
    }

    // delete recommended product - by Pallavi (March 9, 2021)
    public function deleteRecommendedProduct(Request $request)
    {
        $recommendedProduct = RecommendedProduct::where('id',$request->recommendedId)->delete();
        if($recommendedProduct == 1)
        {
            return array(
                'success' => true,
                'message' => trans('Recommended Product Deleted Successfully')
            );
        }
        else
        {
            return array(
                'success' => false,
                'message' => trans('Something went wrong !!')
            );
        }
    }

		// delete Advance Price
    public function deleteAdvancePrice(Request $request)
    {
        $CustGroupPrice = CustGroupPrice::where('id',$request->pricingId)->delete();
        if($CustGroupPrice == 1)
        {
            return array(
                'success' => true,
                'message' => trans('Advance Price Deleted Successfully')
            );
        }
        else
        {
            return array(
                'success' => false,
                'message' => trans('Something went wrong !!')
            );
        }
    }

    public function getLanguageData(Request $request)
    {
        $productDetails = ProductDetails::with('product')->with('productVideo')->where('product_id', $request['productId'])->where('language_id', $request['languageId'])->orderby('id','DESC')->first();

        return $productDetails;
    }

    public function postImageUpload(Request $request)
    {
        $data = $request->all();
        $productId = $data['productId'];
        $reqdImgWidth = Config::get('app.products.width');
        $reqdImgHeight = Config::get('app.products.height');
				$productImages = ImageModel::where('image_type', "product")->where('imageable_id', $productId)->whereNull('deleted_at')->get();
        if ($data['TotalImages'] > 0) {
					 $sortorder = ImageModel::where('image_type', "product")->where('imageable_id', $productId)->orderBy('id', 'desc')->value('sort_order');
           $total=$data['TotalImages'];
            for($j=0; $j<$total;$j++){
                $temp = "images".$j;
                $images = $data[$temp];

                $imagesName = $images->getClientOriginalName();
                $randonName = rand(1, 200);

                $image_resize = Image::make($images->getRealPath());
                $image_resize->resize($reqdImgWidth, $reqdImgHeight);
                $images->move(public_path('/images/product/'.$productId.'/'), $imagesName);
                $image_resize->save(public_path('images/product/'.$productId.'/' .$imagesName));

                // $images->move(public_path('/images/product/'.$productId.'/'), $ransdonName . '.jpg');

                $imageObj = new ImageModel;
                $imageObj->name = $imagesName;
                // $imageObj->small_image = $manufacturer->id.$smallImageName;
                // $imageObj->thumb_image = $manufacturer->id.$thumbImageName;
                $imageObj->original_filename = $imagesName;
                $imageObj->created_at = Carbon::now();
                $imageObj->updated_at = Carbon::now();
                $imageObj->upload_path = '/images/product/'.$productId.'/';
                $imageObj->image_type = 'product';
                $imageObj->label = 'product';
                $imageObj->mime = 'jpg';
                $imageObj->sort_order = $sortorder+$j+1;
                $imageObj->imageable_id = $productId;
                $imageObj->imageable_type = "App\Models\Product";
                $imageObj->tags = " ";
                $imageObj->description = " ";
                $imageObj->save();
            }
						if(count($productImages)==0){
	            $productImage = ImageModel::where('image_type', "product")->where('imageable_id', $productId)->whereNull('deleted_at')->first();
	            $productImage->is_default = 'yes';
	            $productImage->update();
					 }
            // return response()->json($randonName);
            return array(
                'success' => true,
                'message' => trans('Product Images uploaded successfully')
            );
        }
    }

    public function getCategoryAttribute(Request $request)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        $attributeGroups = AttributeGroup::select('attribute_groups.id', 'attribute_groups.attribute_type_id', 'attribute_groups.category_ids', 'attributeGroupDetails.display_name')
        ->join('attribute_group_details as attributeGroupDetails', 'attributeGroupDetails.attr_group_id', '=', 'attribute_groups.id')
        ->whereNull('attribute_groups.deleted_at')
        ->whereNotNull('attribute_groups.category_ids')->get();

        $attributeCategories = array();
        $a = array();
        $attributeGroupIds = array();
        foreach ($attributeGroups as $attributeGroup) {
            $temp = explode(",", $attributeGroup->category_ids);
            if (in_array($request['categoryId'], $temp)) {
                array_push($attributeCategories, $attributeGroup->attribute_type_id);
                array_push($a, $attributeGroup->display_name);
                array_push($attributeGroupIds, $attributeGroup->id);
            }
        }

        $attributeTypes = AttributeTypes::whereIn('id', $attributeCategories)->get();

        $attributes = Attribute::select('attribute.id', 'ad.display_name', 'ad.name', 'agd.name as group_name', 'ag.id as agid')
                            ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
                            ->join('attribute_groups as ag','ag.id','=','ad.attribute_group_id')
                            ->join('attribute_group_details as agd','agd.attr_group_id','=','ag.id')
                            ->where('ad.language_id', $defaultLanguageId)
                            ->where('agd.language_id', $defaultLanguageId)
                            ->whereIn('ad.attribute_group_id', $attributeGroupIds)
                            ->whereNull('ad.deleted_at')
                            ->get()->toArray();

        $groups = array_unique(array_column($attributes, "group_name"));
        $groupIds = array_unique(array_column($attributes, "agid"));
        // return array_combine($groupIds, $groups);
        // return $groupIds;
        $result = [];
        foreach ($groups as $group) {
            $index = 0;
            foreach ($attributes as $attribute) {
                if (in_array($group, $attribute)) {
                    $result[$group][$index++] = ['id' => $attribute['id'], 'name' => $attribute['display_name']];
                    // $result[$group]['name'][$index++] = $attribute['display_name'];
                }
            }
        }

        $resultArr = [];
        $resultArr['attributeGroup'] = array_combine($groupIds, $groups);//$groups;
        $resultArr['attributes'] = $result;
        return $resultArr;

    }
		public function CategoryAttribute($id)
    {
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        $attributeGroups = AttributeGroup::select('attribute_groups.id', 'attribute_groups.attribute_type_id', 'attribute_groups.category_ids', 'attributeGroupDetails.display_name')
        ->join('attribute_group_details as attributeGroupDetails', 'attributeGroupDetails.attr_group_id', '=', 'attribute_groups.id')
        ->whereNull('attribute_groups.deleted_at')
        ->whereNotNull('attribute_groups.category_ids')->get();

        $attributeCategories = array();
        $a = array();
        $attributeGroupIds = array();
        foreach ($attributeGroups as $attributeGroup) {
            $temp = explode(",", $attributeGroup->category_ids);
            if (in_array($id, $temp)) {
                array_push($attributeCategories, $attributeGroup->attribute_type_id);
                array_push($a, $attributeGroup->display_name);
                array_push($attributeGroupIds, $attributeGroup->id);
            }
        }

        $attributeTypes = AttributeTypes::whereIn('id', $attributeCategories)->get();

        $attributes = Attribute::select('attribute.id', 'ad.display_name', 'ad.name', 'agd.name as group_name', 'ag.id as agid')
                            ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
                            ->join('attribute_groups as ag','ag.id','=','ad.attribute_group_id')
                            ->join('attribute_group_details as agd','agd.attr_group_id','=','ag.id')
                            ->where('ad.language_id', $defaultLanguageId)
                            ->where('agd.language_id', $defaultLanguageId)
                            ->whereIn('ad.attribute_group_id', $attributeGroupIds)
                            ->whereNull('ad.deleted_at')
                            ->get()->toArray();
        $groups = array_unique(array_column($attributes, "group_name"));
        $groupIds = array_unique(array_column($attributes, "agid"));
        // return array_combine($groupIds, $groups);
        // return $groupIds;
        $result = [];
        foreach ($groups as $group) {
            $index = 0;
            foreach ($attributes as $attribute) {
                if (in_array($group, $attribute)) {
                    $result[$group][$index++] = ['id' => $attribute['id'], 'name' => $attribute['display_name']];
                    // $result[$group]['name'][$index++] = $attribute['display_name'];
                }
            }
        }

        $resultArr = [];
        $resultArr['attributeGroup'] = array_combine($groupIds, $groups);//$groups;
        $resultArr['attributes'] = $result;
        return $resultArr;

    }

    public function getBulkSellingPrice(Request $request)
    {
        $productBulkPrices = array();
        $quantityRange = [];
        if (isset($request['productId'])) {
            // $productBulkPrices = ProductBulkPrices::where('product_id', $request['productId'])->get();
            $productPricingOptions = ProductPricing::select('id','attribute_ids','product_id','is_default')
		                                                      ->where('product_id', $request['productId'])
		                                                      ->whereNull('deleted_at')
		                                                      ->orderBy('is_default', 'DESC')
		                                                      ->orderBy('id', 'ASC')
		                                                      ->get();
            // return $bulkPrices;
            foreach ($productPricingOptions as $productPricingOption) {

                $a = [];
                $i = 0;
                $bulkPrices = ProductBulkPrices::where('option_id', $productPricingOption->id)->get();
                $a['optionId']  = $productPricingOption->id;
                $temp = [];
                foreach ($bulkPrices as $bulkPrice) {
                    $temp[$i]['range'] = $bulkPrice->from_quantity;
                    $temp[$i++]['value'] = $bulkPrice->price;
                }
                $a['rangeValue']  = $temp;
                // $a['optionId1'] = array_column($bulkPrices, 'from_quantity');
                // $a['optionId2'] = array_column($bulkPrices, 'price');
                // foreach ($bulkPrices as $bulkPrice) {
                //     $range = $bulkPrice->from_quantity ."-". $bulkPrice->to_quantity;
                //     $a[$i]['range'] = $range;
                //     $a[$i++]['value'] = $bulkPrice->price;

                // }
                array_push($productBulkPrices, $a);

            }

        }
        $quantityRange['bulkPrice'] = $productBulkPrices;

        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        $category = Category::find($request['categoryId']);

        $quantityMatrix = $category->qty_matrix ?? 0;
        $range = [];
        if($category->qty_range != null)
            $range = explode(",",$category->qty_range);

        $headers = array();
        $headersRange = array();

        if ($quantityMatrix != 0) {
            array_push($headers, "Variants");
            array_push($headersRange, "headerRange");
        }
        if(!empty($range))
        {
            for ($i=0; $i <= count($range); $i++) {
                if ($i == 0) {
                    array_push($headers, "1-".$range[$i]);
                    array_push($headersRange, "1_".$range[$i]);
                } else {
                    if ($i == count($range)) {
                        array_push($headers, $range[$i-1]+1 . "+");
                        array_push($headersRange, $range[$i-1]+1 ."_0");
                    } else {
                        array_push($headers, $range[$i - 1]+1 . "-" . $range[$i]);
                        array_push($headersRange, $range[$i - 1]+1 . "_" . $range[$i]);
                    }
                }
            }
        }


        $quantityRange['headers'] = $headers;
        $quantityRange['headersRange'] = $headersRange;

        $pricingOptions = ProductPricing::where('category_id',$request['categoryId'])->whereNull('deleted_at')->get();
        $variantOptions = [];
        $index = 0;
        foreach ($productPricingOptions as $pricingOption) {
            $variantOptions[$index]['optionId'] = $pricingOption->id;
						if(empty($pricingOption->attribute_ids)){
							$variantOptions[$index++]['attributes']='DEFAULT';
						}
						else{
            $tempArray = explode(',',$pricingOption->attribute_ids);
						$displayname='';
						$k=1;
						foreach($tempArray as $attr){
						$var= Attribute::select('attribute.id as attribute_id','ad.display_name')
			      ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
			      ->where('attribute.id',$attr)
			      ->where('ad.language_id',$defaultLanguageId)
			      ->whereNull('attribute.deleted_at')
			      ->whereNull('ad.deleted_at')
			      ->first();
						if(!empty($var)){
						if($k < count($tempArray))
						$displayname.=$var['display_name'].', ';
						else
						$displayname.=$var['display_name'];
					}
						$k++;
					}

				   $variantOptions[$index++]['attributes']=$displayname;
        //  $variantOptions[$index++]['attributes'] = $result;
			}
    }

        $quantityRange['variantOptions'] = $variantOptions;

        return $quantityRange;
    }

    public function getProductPricingOptionData(Request $request)
    {
        $productPricing = ProductPricing::where('product_id', $request['productId'])->whereNull('deleted_at')->get();
        return $productPricing;
    }
		public function ProductPricingOptionData($id)
    {
        $productPricing = ProductPricing::where('product_id', $id)->whereNull('deleted_at')->get();
        return $productPricing;
    }
		public function getProducts(Request $request)
    {
        $languageId = $request['languageId'] ?? "";
        if (empty($languageId)) {
            $defaultLanguageData = $this->getDefaultLanguage(null);
            $languageId = $defaultLanguageData['id'];
        }
				$products = Product::select('products.id','pd.title')
                                            ->leftjoin('product_details as pd','pd.product_id','=','products.id')
                                            ->whereNull('products.deleted_at')
                                            ->where('pd.language_id',$languageId)
                                            ->get()->toArray();

        return $products;
    }

    public function getDeleteImages(Request $request)
    {
        $imageIds = explode (",", $request['imageIds']);
        foreach ($imageIds as $imageId) {
            $image = ImageModel::find($imageId);
            $image->deleted_at = now();
            $image->save();
        }
        return array(
                'success' => true,
                'message' => trans('Images Deleted successfully')
            );
    }
		public function postUpdateImageSortOrder(Request $request)
		{
			  $id=$request->id;
				$sortValue=$request->sortValue;
				$image = ImageModel::find($id);
				$image->sort_order = $sortValue;
				if($image->save()){
				return array(
								'success' => true,
								'message' => trans('Sort order updated successfully!')
						);
				}
				else{
					return array(
					'success' => false,
					'message' => trans('Unable to update sort order!')
				);
				}
		}
		//To update image is_default
		public function postUpdateImageIsDefault(Request $request)
		{
			  $id=$request->id;
				$product_id=$request->product_id;
				$images=ImageModel::where('imageable_id',$product_id)->where('is_default','yes')->whereNull('deleted_at')->first();
				if($images){
				$images->is_default = 'no';
				$images->save();
			}
				$image = ImageModel::find($id);
				$image->is_default = 'yes';
				if($image->save()){
				return array(
								'success' => true,
								'message' => trans('Default image updated successfully!')
						);
				}
				else{
					return array(
					'success' => false,
					'message' => trans('Unable to update default image!')
				);
				}
		}

		//To delete pricicng options
		public function deleteProductPricingOptionData(Request $request)
		{
			  $id=$request->optionId;
				$productPricing = ProductPricing::find($id);
				$productPricing->deleted_at = now();
				if($productPricing->save()){
				return array(
								'success' => true,
								'message' => trans('Pricing option deleted successfully!')
						);
				}
				else{
					return array(
					'success' => false,
					'message' => trans('Unable to delete pricicng option!')
				);
				}
		}
		//To delete pricicng options
		public function deleteProductPricingOptionImage(Request $request)
		{
			  $id=$request->optionId;
				$productPricing = ProductPricing::find($id);
				$productPricing->image = NULL;
				if($productPricing->save()){
				return array(
								'success' => true,
								'message' => trans('Pricing option image deleted successfully!')
						);
				}
				else{
					return array(
					'success' => false,
					'message' => trans('Unable to delete pricicng option image!')
				);
				}
		}

		//to get advance pricing
		public function getAdvancePricingData(Request $request)
		{
				$id=$request->pricingId;
				$CustGroupPrice = CustGroupPrice::select('customer_group_price.id','customer_group_price.product_id','customer_group_price.price','customer_groups.group_name')
													->join('customer_groups','customer_groups.id','=','customer_group_price.customer_group_id')
													->where('customer_group_price.id',$id)
													->first();
				return $CustGroupPrice;
		}

    public function uploadProdImage(Request $request)
    {
        $folder_name = 'ckeditor-prod-image';
        uploadCKeditorImage($request, $folder_name);
    }

    public function copyProduct(Request $request)
    {
        // 1. product table
        $productTableDetails = Product::where('id',$request->prodId)
                                ->whereNull('deleted_at')
                                ->first();

        // 1. copy products table
        $newProd = $this->product->copyProductTableData($productTableDetails);
        
        if(!empty($newProd))
        {
            // 2. product details table
            $prodDetails = ProductDetails::where('product_id',$request->prodId)
                                        ->whereNull('deleted_at')
                                        ->first();
            if(!empty($prodDetails))
            {
                // 2.copy product details table
                $newProdDetails = $this->product->copyProdDetailsTable($prodDetails,$newProd->id);
            }

            // 3. product pricing table
            $productPricingDetails = ProductPricing::where('product_id',$request->prodId)
                                                    ->whereNull('deleted_at')
                                                    ->get();
            if(!empty($productPricingDetails))
            {
                // 3. copy product pricing table
                $newProdPricingDetails = $this->product->copyProductPricingTable($productPricingDetails,$newProd->id);
            }
           
            // 4. recomm product table
            $recommProductDetails = RecommendedProduct::where('product_id',$request->prodId)->get();

            if(!empty($recommProductDetails))
            {
                // 4. copy recommended products
                $newRecommendedProdDetails = $this->product->copyRecommendedProdTable($recommProductDetails,$newProd->id);
            }
            
            // 5. Related product table
            $relatedProdDetails = RelatedProduct::where('product_id',$request->prodId)->get();

            if(!empty($relatedProdDetails))
            {
                // 5. Copy related products
                $newRelatedProdDetails = $this->product->copyRelatedProdTable($relatedProdDetails,$newProd->id);
            }
          
            // 6. Product bulk pricing table
            $bulkPricingDetails = ProductBulkPrices::where('product_id',$request->prodId)
                                                    ->whereNull('deleted_at')
                                                    ->get();

            if(!empty($bulkPricingDetails))
            {
                // 6. copy bulk pricing data ($newProdPricingDetails is id from pricing table - as a option id)
                $newBulkPricingDetails = $this->product->copyBulkPricingTable($bulkPricingDetails,$newProd->id,$newProdPricingDetails);
            }
            
            // 7. Copy product images table
            $originalProdImages = \App\Models\Image::where('imageable_id',$request->prodId)
                                        ->where('image_type','product')
                                        ->whereNull("deleted_at")
                                        ->get();

            if(!empty($originalProdImages))
            {
                // 7. copy image data
                $newImageData = $this->product->copyProductImages($originalProdImages,$newProd->id);
            }

            $result['status'] = true;
            $result['msg'] = "Product Copied Successfully!";
            return response()->json($result);
        }
        else
        {
            $result['status'] = false;
            $result['msg'] = "Error while copying product!!";
            return response()->json($result);
        }
    }
}
