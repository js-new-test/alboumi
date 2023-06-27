<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use DataTables;
use App\Models\Promotions;
use App\Models\PromotionConditions;
use App\Models\Manufacturer;
use App\Traits\CommonTrait;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategoryDetails;
use DB;

class PromotionsController extends Controller
{
	use CommonTrait;

	protected $category;

	function __construct(Category $category)
	{
		$this->category = $category;
		$this->projectName = "Alboumi";
	}


	public function getPromotions()
	{
		$projectName = $this->projectName;
		$pageTitle = 'Promotions';
		$baseUrl = $this->getBaseUrl();
		return view('admin.promotions.promotions', compact('projectName', 'pageTitle', 'baseUrl'));
	}

	public function getPromotionList(Request $request)
	{
        $promotions = Promotions::whereNull('deleted_at');
		// dd($promotions);
        return DataTables::of($promotions)
        ->filter(function ($query) use ($request) {
            if ($request->has('promotionTitle')) {
                $query->where('promotions.title', 'like', "%{$request->get('promotionTitle')}%");
            }
            if ($request->has('promotionCode')) {
                $query->where('promotions.coupon_code', 'like', "%{$request->get('promotionCode')}%");
            }
            if ($request->has('promotionStatus') && $request['promotionStatus'] != "") {
                $query->where('promotions.status', '=', $request->get('promotionStatus'));
            }
        })
        ->make(true);
    }

	public function getAddPromotion()
	{
		$baseUrl = $this->getBaseUrl();
		$projectName = $this->projectName;
		$pageTitle = 'Add Promotion';
		return view('admin.promotions.addPromotion', compact('projectName', 'pageTitle', 'baseUrl'));	
	}

	public function postAddPromotion(Request $request)
	{
		$data = $request->all();
		$promotion = new Promotions;
		$promotion->title = $data['couponTitle'];
		$promotion->terms_conditions = $data['termsConditions'];
		$promotion->coupon_code = $data['couponCode'];
		$promotion->discount_amount = $data['discountAmount'];
		$promotion->discount_type = $data['discountType'];
		$promotion->coupon_usage_limit = $data['couponUsageLimit'];
		$promotion->startdate = date_format(date_create($data['activeFrom']),"Y-m-d");
		$promotion->enddate = date_format(date_create($data['activeTill']),"Y-m-d");
		$promotion->coupon_use_time = $data['activeTill'];
		$promotion->visible_product_page = isset($data['visibleOnProductPage']) ? $data['visibleOnProductPage'] : "";
		$promotion->available_on_offer_price = isset($data['availableOnPrice']) ? $data['availableOnPrice'] : "";
		$promotion->status = $data['status'];
		$promotion->custom_title = $data['customTitle'];
		$promotion->coupon_user_types = $data['discountUserType'];
		$promotion->save();

		$notification = array(
            'message' => 'Promotion added successfully!', 
            'alert-type' => 'success'
        );

		return redirect('admin/promotions')->with($notification);
	}

	public function getEditPromotion($id)
	{
		$projectName = $this->projectName;
		$pageTitle = 'Edit Promotion';

		$baseUrl = $this->getBaseUrl();
		$promotion = Promotions::find($id);

		$conditions = PromotionConditions::where('promotion_id',$id)->get();
		return view('admin/promotions/editPromotion', compact('projectName', 'pageTitle', 'promotion', 'baseUrl','conditions'));
	}

	public function postEditPromotion(Request $request)
	{
		$data = $request->all();
		$promotion = Promotions::find($data['promotionId']);
		$promotion->title = $data['couponTitle'];
		$promotion->terms_conditions = $data['termsConditions'];
		$promotion->coupon_code = $data['couponCode'];
		$promotion->discount_amount = $data['discountAmount'];		
		$promotion->discount_type = $data['discountType'];
		$promotion->coupon_usage_limit = $data['couponUsageLimit'];
		$promotion->startdate = date_format(date_create($data['activeFrom']),"Y-m-d");
		$promotion->enddate = date_format(date_create($data['activeTill']),"Y-m-d");
		$promotion->coupon_use_time = $data['activeTill'];
		$promotion->visible_product_page = isset($data['visibleOnProductPage']) ? $data['visibleOnProductPage'] : "";
		$promotion->available_on_offer_price = isset($data['availableOnPrice']) ? $data['availableOnPrice'] : "";
		$promotion->status = $data['status'];
		$promotion->custom_title = $data['customTitle'];
		$promotion->coupon_user_types = $data['discountUserType'];
		$promotion->save();

		$notification = array(
            'message' => 'Promotion updated successfully!', 
            'alert-type' => 'success'
        );

		return redirect('admin/promotions')->with($notification);
	}

	public function postUpdatePromotion(Request $request)
	{
		$data = $request->all();
		$promotion = Promotions::find($data['promotionId']);
		if ($data['part'] == 'STATUS') {
			$status = 'Active';
			if ($request['promotionStatus'] == 0) {
				$status = 'Inactive';
			}
			$promotion->status = $status;
			$promotion->save();
			$message = "Status Changed Successfully";

		}elseif ($data['part'] == 'ADMINAPPROVE') {
			$adminApproved = 'Yes';
			if ($request['promotionStatus'] == 0) {
				$adminApproved = 'No';
			}
			$promotion->is_admin_approved = $adminApproved;
			$promotion->save();
			$message = "Approval Status Changed Successfully";
		}

		return array(
					'success' => true,
					'message' => $message
				);
	}

	public function postPromotionConditions(Request $request)
	{
		$data = $request->all();
		$count = count($data['promotionOn']);

		$Previouspromotions = PromotionConditions::where('promotion_id',$data['promotionId'])->delete();
		for ($i=0; $i <$count ; $i++) 
		{ 
			if ($data['promotionOn'][$i] != null || $data['conditionType'][$i] != null || !empty($data['promotionValue'][$i])) 
			{
				$promotionConditions = new PromotionConditions;
				$promotionConditions->promotion_id = $data['promotionId'];
				$promotionConditions->promotion_on = $data['promotionOn'][$i];
				$promotionConditions->condition_type = $data['conditionType'][$i];
				$promotionConditions->promotion_on_value = $data['promotionValue'][$i];
				$promotionConditions->save();
			}

		}

		$notification = array(
            'message' => 'Promotion Condition added successfully!', 
            'alert-type' => 'success'
        );

		return redirect('admin/promotions')->with($notification);
	}

	public function getPromotionManufactuerList(Request $request)
	{
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        $brands = Manufacturer::select('manufacturers.id', 'manufacturers.status', 'brand_details.name as brandName')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->where('brand_details.language_id', $defaultLanguageId)
        ->whereNull('manufacturers.deleted_at');

        return DataTables::of($brands)
        ->filter(function ($query) use ($request) {
            if ($request->has('name')) {
                $query->where('brand_details.name', 'like', "%{$request->get('name')}%");
            }
        })
        ->make(true);
	}

	public function getPromotionProductList(Request $request)
	{
        // $product = product::whereNotNull('title');
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        $products = Product::select('products.id', 'products.sku', 'products.category_id', DB::raw("date_format(products.created_at,'%Y-%m-%d %h:%i:%s') as productCreatedDate"), 'product_details.title as title', 'category_details.title as categoryTitle', 'product_details.id as productDetailsId', 'brand_details.name as brandName')
        ->join('product_details', 'product_details.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->join('category_details', 'category_details.category_id', '=', 'categories.id')
        ->join('manufacturers', 'manufacturers.id', '=', 'products.manufacturer_id')
        ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
        ->where('product_details.language_id', $defaultLanguageId)
        ->where('brand_details.language_id', $defaultLanguageId)
        ->where('products.status', '=', "Active");

        return DataTables::of($products)
        ->filter(function ($query) use ($request) {
            if ($request->has('title')) {
                $query->where('products.title', 'like', "%{$request->get('category_details.title')}%");
            }
            if ($request->has('category_id')) {
                $query->where('products.category_id', 'like', "%{$request->get('selectCategory')}%");
            }
            if ($request->has('brandId')) {
                $query->where('products.manufacturer_id', 'like', "%{$request->get('brandId')}%");
            }
            if ($request->has('status')) {
                $query->where('products.status', 'like', "%{$request->get('status')}%");
            }
        })
        ->make(true);
	}

	public function getGenerateAutoPromotionCode()
	{
		$result = [];
		$result['success'] = true;
		$result['couponCode'] = $this->UniqueRandomNumbersWithinRange();

		return $result;
	}

	public function postDeletePromotion(Request $request)
	{
		$promotion = Promotions::find($request['promotionId']);
		$promotion->deleted_at = now();
		$promotion->save();

		return array(
					'success' => true,
					'message' => "Promotion Deleted Successfully."
				);

	}

	public function getPromotionBrands()
	{
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $languageId = $defaultLanguageData['id'];
        
        $brands = Manufacturer::select('manufacturers.id', 'brand_details.name as brandName')
                ->join('brand_details', 'brand_details.brand_id', '=', 'manufacturers.id')
                ->where('language_id',$languageId)
                ->whereNull('manufacturers.deleted_at')
                ->get();

        return $brands;
	}

	public function getPromotionCategories()
	{
		return $this->category->categoryDropDown("");
	}

	public function getPromotionCategoryList()
	{
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $languageId = $defaultLanguageData['id'];

        $categories = Category::select('categories.id', 'categories.parent_id', 'categories.category_path')
                                ->where('flag_category', 0)
                                ->whereOr('flag_product',1)
                                ->whereNull('categories.deleted_at')
                                ->get();
        $resultArr = [];
        $index = 0;
        foreach ($categories as $category) {

            $ids = explode (",", $category->category_path);
            $temp = "";

            $categoryDetails = CategoryDetails::where('language_id',$languageId)
                                            ->whereIn('category_id',$ids)
                                            ->orderBy('id', 'DESC')
                                            ->get();

            for ($i=0; $i <= count($categoryDetails)-1; $i++) {
                if (count($categoryDetails)-1 == $i) {
                    $temp = $temp . " > " . $categoryDetails[$i]->title;
                } else {
                    if ($i == 0)  {
                        $temp = $categoryDetails[$i]->title;
                    } else {
                        $temp = $temp . " - " . $categoryDetails[$i]->title;
                    }
                }
            }
            $resultArr[$index]['id'] = $category->id;
            $resultArr[$index++]['category'] = $temp;
        }
        return $resultArr;
	}

	public function uploadCKeditorPromotionImage(Request $request)
    {
        $folder_name = 'ckeditor-promotion-image';
        uploadCKeditorImage($request, $folder_name);
    }

}
