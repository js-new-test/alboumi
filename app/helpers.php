<?php

use App\Models\Role;
use App\Models\Permission;
use App\Models\GlobalLanguage;
use App\Models\GlobalCurrency;
use App\Models\WorldLanguage;
use App\Models\Cart;
use App\Models\CartMaster;
use App\Models\Customer;
use App\Models\CustGroupPrice;
use App\Models\EventPhotoOrders;
use App\Traits\ReuseFunctionTrait;

/**
 * This function is use to checked role based permissions
 * @param array $role_type
 * @param $permission_slug
 * @return bool
 */
function whoCanCheck($role_type = array(), $permission_slug)
{
		// return true;
		// $segment = Request::segment(1);
		$current_user = (Auth::guard('admin')->check()) ? Auth::guard('admin')->user()->id : Auth::guard('photographer')->user()->id;
		$obj = new Role;
		$role_type = $obj->getCurrentRole('role_type');
		// $role_type = Auth::$segment()->get()->role->first()->toArray();
		$results = Permission::select('roles.role_type', 'permissions.permission_slug')
				->join('permission_role', function ($join) use ($permission_slug) {
						$join->on('permissions.id', '=', 'permission_role.permission_id');
				})
				->join('role_user', function ($join) {
						$join->on('role_user.role_id', '=', 'permission_role.role_id');
				})
				->join('roles', function ($join) {
						$join->on('roles.id', '=', 'permission_role.role_id');
				})
				->where('permissions.permission_slug', '=', $permission_slug)
				->where('roles.role_type', '=', $role_type)
				->where('role_user.admin_id', '=', $current_user)
				->get()
				->toArray();

		// echo '<pre>';
		// print_r($results);
		// echo '</pre>';
		// exit;
		if (count($results) > 0) {
				foreach ($results as $result) {
						if (in_array($result['role_type'], (array) $role_type)) {
								return true;
						} else {
								return false;
						}
				}
		} else {
				return false;
		}
}

/**
 * This function is used to generate Unique Id by getting last id from database table
 * @param $last_unique_id
 * @param string $prefix
 * @return mixed|string
 */
function getIdByLastUniqueId($last_unique_id, $prefix = '')
{
		if (!empty($last_unique_id)) {
				$uniqueId = preg_replace("/[^0-9]/", "", $last_unique_id);
				$uniqueId = $prefix . str_pad($uniqueId + 1, 5, '0', STR_PAD_LEFT);
		} else {
				$uniqueId = $prefix . str_pad(1, 5, '0', STR_PAD_LEFT);
		}
		return $uniqueId;
}

/** Developed by : Jignesh **/
// function getCodesMsg($language_id, $locale_code)
// {
//     $codes = \App\Models\Locale::where('is_active', 0)->pluck('code');
//     $msg_codes = ReuseFunctionTrait::getLocaleDetailsForLang($codes, $language_id);
//     $i = 0;
//     $codes = [];
//     foreach ($msg_codes as $code) {
//         if($code->code == $locale_code)
//         {
//             $codes['msg'] = $code->value;
//         }
//     }
//     return $codes['msg'];
// }

/** Developed by : Jignesh **/
function getCodesMsg($language_id, $locale_code)
{
		$codes[] = $locale_code;
		$msg_codes = ReuseFunctionTrait::getLocaleDetailsForLang($locale_code, $language_id);
		return $msg_codes;
}

/** Developed by : Jignesh **/
function authResponse($lang_id)
{
		$result['statusCode'] = '300';
		$code = "AUTHFAILED";
		$msg = getCodesMsg($lang_id, $code);
		$result['message'] = $msg;
		return response()->json($result);
}

/** Developed by : Jignesh **/
function isLanguageExists($lang_id)
{
		$global_language = \App\Models\GlobalLanguage::where('id', $lang_id)->where('is_deleted', 0)->first();
		return (!$global_language) ? "false" : "true";
}

/** Developed by : Jignesh **/
function handleServerError($lang_id)
{
		//Localization
		$codes = ['SERVERERR'];
		$serverErrLabels = getCodesMsg($lang_id, $codes);

		$result['statusCode'] = '500';
		$result['message'] = $serverErrLabels["SERVERERR"];
		return response()->json($result, 500);
}

/** Developed By Nivedita (29-jan-2021) **/
/** function to laguage session and set laguage sessions **/
function setSessionforLang($defaultLanguageId)
{
		if(empty(Session::get('language_id'))) {
			$languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName','global_language.currency_id','global_language.decimal_number','global_language.decimal_separator','global_language.thousand_separator')
															->join('world_languages as wl','wl.id','=','language_id')
															->where('is_default',1)
															->where('status',1)
															->where('is_deleted',0)
															->first();
			Session::put('language_id',$languageData['id']);
			Session::put('language_code',$languageData['Code']);
			Session::put('language_name',$languageData['langName']);
			Session::put('default_lang_id',$defaultLanguageId);
			Session::put('decimal_number',$languageData['decimal_number']);
			Session::put('decimal_separator',$languageData['decimal_separator']);
			Session::put('thousand_separator',$languageData['thousand_separator']);
		}
		else{
			$languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName','global_language.currency_id','global_language.decimal_number','global_language.decimal_separator','global_language.thousand_separator')
																 ->join('world_languages as wl','wl.id','=','language_id')
																->where('global_language.id',Session::get('language_id'))
																 ->where('status',1)
																 ->where('is_deleted',0)
																 ->first();
				 Session::put('language_code',$languageData['Code']);
				 Session::put('language_name',$languageData['langName']);
				 Session::put('default_lang_id',$defaultLanguageId);
				 Session::put('decimal_number',$languageData['decimal_number']);
				 Session::put('decimal_separator',$languageData['decimal_separator']);
				 Session::put('thousand_separator',$languageData['thousand_separator']);
		}
		return true;
}


/** Developed By Jignesh (02-feb-2021) **/
function setSessionforCurr($defaultCurrencyId)
{
		if(empty(Session::get('currency_id'))) {
				$currencyData = \App\Models\GlobalCurrency::select('global_currency.id','currency.name',
						'currency.code','currency.currency_symbol')
						->leftJoin('currency','currency.id','=','global_currency.currency_id')
						->where('global_currency.is_default', 1)
						->where('global_currency.is_deleted', 0)
						->first();
				Session::put('currency_id',$currencyData['id']);
				Session::put('currency_symbol',$currencyData['currency_symbol']);
				Session::put('currency_name',$currencyData['name']);
				Session::put('default_curr_id',$defaultCurrencyId);
		}
		else{
				$currencyData = \App\Models\GlobalCurrency::select('global_currency.id','currency.name','currency.code','currency.currency_symbol')
						->leftJoin('currency','currency.id','=','global_currency.currency_id')
						->where('global_currency.id', Session::get('currency_id'))
						->where('global_currency.is_deleted', 0)
						->first();
				if($currencyData)
				{
						Session::put('currency_symbol',$currencyData['currency_symbol']);
						Session::put('currency_name',$currencyData['name']);
						Session::put('default_curr_id',$defaultCurrencyId);
				}
		}
		return true;
}

function getCurrencyRates($currency_id = NULL)
{
		$currencyConRates = \App\Models\GlobalCurrency::select('global_currency.id','currency_conversion_rate.to_currency_id',
		'currency_conversion_rate.rate')->leftJoin('currency_conversion_rate','currency_conversion_rate.from_currency_id'
		,'=','global_currency.id')
		->where('global_currency.is_default', 1)
		->where('global_currency.is_deleted', 0)
		->get();
		$i = 0;
		$currRates = [];
		foreach ($currencyConRates as $currRate) {

				if(!empty($currency_id) && $currency_id == $currRate->id) return "1";
				if(!empty($currency_id) && $currency_id == $currRate->to_currency_id) return $currRate->rate;

				$currRates[$currRate->id] = "1";
				$currRates[$currRate->to_currency_id] = $currRate->rate;
		}
		return $currRates;
}

function getAllCurrency()
{
		$currencies = \App\Models\GlobalCurrency::select('global_currency.id','currency.currency_code','currency.currency_name')
		->leftJoin('currency','currency.id','=','global_currency.currency_id')->where('global_currency.is_deleted', 0)->get();
		return $currencies;
}

function getCurrSymBasedOnLangId($language_id)
{
		$currSymbol = \App\Models\GlobalLanguage::select('currency.currency_code')
		->leftJoin('global_currency','global_currency.id','=','global_language.currency_id')
		->leftJoin('currency','currency.id','=','global_currency.currency_id')
		->where('global_language.id',$language_id)
		->where('global_language.is_deleted',0)
		->first();
		return $currSymbol->currency_code;
}

function getUserRole($id)
{
		$user_role = \App\Models\RoleUser::select('roles.role_title')->leftJoin('roles','roles.id',
		'=','role_user.role_id')->where('role_user.admin_id', $id)->where('roles.is_deleted', 0)->first();
		return $user_role;
}

// To manage cart after login By Nivedita(18-02-2021)
function setCartMasterId($customer_id,$cart_master_id=''){
		if(empty($cart_master_id) && $cart_master_id=='' ){
				//To check if there is any cart item which is not complete
				$cartMasterData=CartMaster::where('user_id',$customer_id)->where('flag_complete',0)->first();
				// To set cart_master id in session if record found
				if(!empty($cartMasterData)){
				Session::put('cart_master_id',$cartMasterData->id);
				$cart_master_id=$cartMasterData->id;
			}
		}
		else{
				//$cart_master_id=Session::get('cart_master_id');
				// updating cart master with login user_id
				$cartMaster=CartMaster::find($cart_master_id);
				$cartMaster->user_id=$customer_id;
				$cartMaster->save();

				//To check if there is any cart master which is not complete of old cart master
				$cartMasterDataOld=CartMaster::where('user_id',$customer_id)->where('flag_complete',0)->where('id','!=',$cart_master_id)->first();
				// To merge the cart items if data found.
				if(!empty($cartMasterDataOld)){
					// to find item in previouse cart
				$cartData=Cart::where('cart_master_id',$cartMasterDataOld->id)->get();
				//loop for merging art items
				if(!empty($cartData)){
				foreach($cartData as $data){
						$cartDataNew=Cart::where('cart_master_id',$cart_master_id)->where('option_id',$data->option_id)->where('product_id',$data->product_id)->first();

						// To get customer group price for product
						$GroupPrice=0;
						if($customer_id){
								$customeData=Customer::where("id",$customer_id)->first();
								if($customeData->cust_group_id!=0){
										$custGrpPrice=CustGroupPrice::where("product_id",$data->product_id)->where('customer_group_id', $customeData->cust_group_id)->first();
										if(!empty($custGrpPrice))
										$GroupPrice=$custGrpPrice->price;
								}
						}

						//if cart item found with same option and product in new cart and old cart OR if image is not empty then insert new record
						if(!empty($cartDataNew) && empty($cartDataNew->image)){
							$cartDataNew->user_id=$cart_master_id;
							$cartDataNew->quantity=$cartDataNew->quantity+$data->quantity;
							if($GroupPrice==0)
							$cartDataNew->price=$cartDataNew->price;
							else
							$cartDataNew->price=$GroupPrice;
							$cartDataNew->save();
						}
						else{
							$cart=new Cart;
							$cart->user_id=$data->user_id;
							$cart->product_id=$data->product_id;
							$cart->quantity=$data->quantity;
							if($GroupPrice==0)
							$cart->price=$data->price;
							else
							$cart->price=$GroupPrice;
							$cart->cart_master_id=$cart_master_id;
							$cart->option_id=$data->option_id;
							$cart->message=$data->message;

							$cart->gift_wrap=$data->gift_wrap;
							$cart->gift_message=$data->gift_message;
							$cart->lady_operator=$data->lady_operator;
							$cart->image=$data->image;
							$cart->other_images=$data->other_images;
							$cart->promo_code=$data->promo_code;
							$cart->save();
						}
				}
			}
			// to complete the old cart master
			$cartMasterDataOldSession=CartMaster::where('user_id',$customer_id)->where('flag_complete',0)->where('id','!=',$cart_master_id)->first();
			$cartMasterDataOldSession->flag_complete=1;
			$cartMasterDataOldSession->save();
			}
			//To get update user id to cart once login
			$cartMasterCart=Cart::where('cart_master_id',$cart_master_id)->update(array('user_id'=>$customer_id));
		}
		return $cart_master_id;
}

function update_shippingcost($cart_master_id)
{
		$total_price = 0.000;
		$cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();
		foreach ($cart as $data) {
				$total_price += $data->price * $data->quantity;
		}
		$settings = \App\Models\Settings::first();
		if($total_price <= $settings->min_order_amt){
				$order_shipping = $settings->shipping_cost;
		}
		else{
				$order_shipping = 0.000;
		}
		$cart_master = \App\Models\CartMaster::where('id', $cart_master_id)->first();
		if(!empty($cart_master)){
		if($cart_master->checkout_type == 2)
		{
				$cart_master->shipping_cost = 0;
		}
		else
		{
				$cart_master->shipping_cost = $order_shipping;
		}
		$cart_master->save();
	}
}

function check_tier_price($cart_master_id)
{
		$cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();
		foreach ($cart as $data) {

				$customer_id = Session::get('customer_id');
				$GroupPrice=0;
				if(empty($customer_id))
				{
					$customer_id=0;
				}

				// if customer id set
				if(!empty($customer_id)){
					$customeData=Customer::where("id",$customer_id)->first();
					if($customeData->cust_group_id!=0){
						$custGrpPrice=CustGroupPrice::where("product_id",$data->product_id)->where('customer_group_id', $customeData->cust_group_id)->first();
						
						if(!empty($custGrpPrice))
						{
							$GroupPrice=$custGrpPrice->price;
						}
					}
				}
				
				$product_bulk_price = DB::select( DB::raw("SELECT * FROM product_bulk_prices WHERE product_id = '".$data->product_id."' AND option_id = '".$data->option_id."' AND (('".$data->quantity."' >= from_quantity AND '".$data->quantity."' <= to_quantity) OR ('".$data->quantity."' >= from_quantity AND '0' = to_quantity))") );				

				// if bulk price found but group price not set then and then apply bulk qty price....
				if($GroupPrice == 0 && $product_bulk_price && $product_bulk_price[0]->price > 0)
				{
					$cart = \App\Models\Cart::where('id', $data->id)->where('product_id', $data->product_id)
						->where('option_id', $data->option_id)->first();
					$cart->price = $product_bulk_price[0]->price;
					$cart->save();
				}
				// reset to original price
				else
				{
						/*$customer_id = Session::get('customer_id');
						$GroupPrice=0;
						if(empty($customer_id))
						{
								$customer_id=0;
						}
						if(!empty($customer_id)){
								$customeData=Customer::where("id",$customer_id)->first();
								if($customeData->cust_group_id!=0){
										$custGrpPrice=CustGroupPrice::where("product_id",$data->product_id)->where('customer_group_id', $customeData->cust_group_id)->first();
										if(!empty($custGrpPrice))
										{
												$GroupPrice=$custGrpPrice->price;
										}
								}
						}*/

						// Fetch Product Option data
						$product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price','offer_start_date','offer_end_date')
																																	->where('id', $data->option_id)
																																	->where('product_id', $data->product_id)
																																	->whereNull('deleted_at')
																																	->first();

						if($GroupPrice==0)
						{
							if($product_details)
							{
								if(!empty($product_details['offer_price']) && (date("Y-m-d",strtotime($product_details['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($product_details['offer_end_date']))))
								{
									$price=  $product_details['offer_price'];
								}
								else
								{
									$price=  $product_details['selling_price'];
								}
							}
							else
							{
								$price=$GroupPrice;
							}
						}
						else
						{
							$price=$GroupPrice;
						}

						$cart = \App\Models\Cart::where('id', $data->id)->where('product_id', $data->product_id)
						->where('option_id', $data->option_id)->first();
						$cart->price = $price;
						$cart->save();
				}
		}
		return true;
}

function apply_promotion($code, $cart_master_id, $lang_id)
{
		$defaultLanguageData = \App\Models\GlobalLanguage::select('global_currency.decimal_number',
		'global_currency.decimal_separator','global_currency.thousand_separator', 'global_language.currency_id')
		->leftJoin('global_currency','global_currency.id','=','global_language.currency_id')
		->where('global_language.id', $lang_id)
		->where('global_currency.is_deleted', 0)
		->where('global_language.is_deleted', 0)
		->first();

		$decimalNumber=$defaultLanguageData->decimal_number;
		$decimalSeparator=$defaultLanguageData->decimal_separator;
		$thousandSeparator=$defaultLanguageData->thousand_separator;

		$codes = ['PROMOCODEALREADYUSED','ERROR','PROMOCODEINVALID','PROMOCODENOTAPPLICABLE',
		'PROMOCODETOTALNOTMATCH','SUCCESS','PROMOCODEAPPLIED','PROMOCODENOTFOUND'];
		$promoLabels = getCodesMsg($lang_id, $codes);

		//Step - 2
		$code = $code;
		$discount_amount = 0.000;
		$grand_total = 0.000;
		$item_amount = 0.000;

		//Step - 2.1
		$today_date = date('Y-m-d');
		$promotion = \App\Models\Promotions::where('coupon_code', $code)
		->where('status', 'Active')->whereNull('deleted_at')->first();

		if(empty($promotion))
		{
				$result['status'] = "error";
				$result['message'] = $promoLabels['PROMOCODENOTFOUND'];
				$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
				return $result;
		}

		if($today_date >= $promotion->startdate && $today_date <= $promotion->enddate)
		{
				$promomtion_id = $promotion->id;
		$promotion_discount_type = $promotion->discount_type;
		$promotion_discount_amount = $promotion->discount_amount;
		$coupon_usage_limit = $promotion->coupon_usage_limit;
		$coupon_user_types = $promotion->coupon_user_types;

				$count = \App\Models\Orders::where('promotions', $code)->whereNotIn('order_status_id', [2,7])->count();
				if($count > 0 && $coupon_user_types == 'Single Use')
				{
						$result['status'] = "error";
						$result['message'] = $promoLabels['PROMOCODEALREADYUSED'];
						$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
						return $result;
				}

				if($count > 0 && $coupon_user_types == 'Multiple Use' && $coupon_usage_limit > 0 && $count >= $coupon_usage_limit)
				{
						$result['status'] = "error";
						$result['message'] = $promoLabels['PROMOCODEALREADYUSED'];
						$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
						return $result;
				}
		}
		else
		{
				$result['status'] = "error";
				$result['message'] = $promoLabels['PROMOCODEINVALID'];
				$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
				return $result;
		}

		//Step - 2.2
		$promotion_conditions = \App\Models\PromotionConditions::where('promotion_id', $promomtion_id)
		->groupBy('promotion_on')->get();
		$promo_condition = array();
		if($promotion_conditions)
		{
				foreach ($promotion_conditions as $query) {
						$promo_condition[$query->promotion_on][$query->condition_type] = $query->promotion_on_value;
				}

		}

		$get_curr = \App\Models\GlobalLanguage::select('currency_id','tax_type')->where('id', $lang_id)->first();
		$Currency_rate = getCurrencyRates($get_curr->currency_id);

		//Step - 2.3
		$cart_items = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();
		$arrApplicableItems = array();
		foreach ($cart_items as $cart) {
				$product_id = $cart->product_id;
				$category = \App\Models\Product::select('products.category_id')
				->leftJoin('categories','categories.id','=','products.category_id')
				->where('products.id', $cart->product_id)
				->first();
				$category_id = $category->category_id;

				// Calculate price without VAT for promotion
				//VAT Calculation Start
				$product = \App\Models\Product::where('id', $cart->product_id)->whereNull('deleted_at')->first();
				if($product)
				{
						if($product->tax_class_id > 0 && $product->tax_class_id != '' && !empty($product->tax_class_id))
						{
								$tex_class = \App\Models\TaxClass::where('id', $product->tax_class_id)->whereNull('deleted_at')->first();
								$tex_rate = \App\Models\TaxRate::where('id', $tex_class->tax_rate_ids)->whereNull('deleted_at')->first();

								//Case 1
								if(empty($product->tax_class_id) || $tex_rate->rate == 0.000)
								{
										$price = $cart->price;
								}
								else
								{
										//Case 2
										if($tex_rate->rate > 0)
										{
												if($get_curr->tax_type == 1)
												{
														// Including
														$price = (($cart->price * 100) / (100 + $tex_rate->rate));
												}
												else
												{
														// Excluding
														$price = $cart->price;
												}
										}
								}
						}
						else
						{
								$price = $cart->price;
						}
				}
				//VAT Calculation End

				$qty = $cart->quantity;
				$grand_total += $price * $cart->quantity;

				if(!empty($promo_condition['Category']['Equals To']))
				{
						$arrCatIds = explode(",", $promo_condition['Category']['Equals To']);

						if(in_array($category_id, $arrCatIds))
						{
								$arrApplicableItems[$cart->id] = $price * $qty;
						}
				}

				if(!empty($promo_condition['Category']['Not Equals To']))
				{
						$arrNotCatIds = explode(",", $promo_condition['Category']['Not Equals To']);

						if(!in_array($category_id, $arrNotCatIds))
						{
								$arrApplicableItems[$cart->id] = $price * $qty;
						}
				}

				if(!empty($promo_condition['Product']['Equals To']))
				{
						$arrProdIds = explode(",", $promo_condition['Product']['Equals To']);

						if(in_array($product_id, $arrProdIds))
						{
								$arrApplicableItems[$cart->id] = $price * $qty;
						}
				}

				if(!empty($promo_condition['Product']['Not Equals To']))
				{
						$arrNotProdIds = explode(",", $promo_condition['Product']['Not Equals To']);

						if(!in_array($product_id, $arrNotProdIds))
						{
								$arrApplicableItems[$cart->id] = $price * $qty;
						}
				}
		}

		if(!empty($promo_condition['Category']) || !empty($promo_condition['Product']))
		{
				if(empty($arrApplicableItems))
				{
						$result['status'] = "error";
						$result['message'] = $promoLabels['PROMOCODENOTAPPLICABLE'];
						$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
						return $result;
				}
				else
				{
						if(!empty($promo_condition['Grand_Total']))
						{
								if(!empty($promo_condition['Grand_Total']['Equals To']) && $grand_total < $promo_condition['Grand_Total']['Equals To'])
								{
										$result['status'] = "error";
										$result['message'] = $promoLabels['PROMOCODETOTALNOTMATCH'];;
										$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
										return $result;
								}

								if(!empty($promo_condition['Grand_Total']['Not Equals To']) && $grand_total < $promo_condition['Grand_Total']['Not Equals To'])
								{
										$result['status'] = "error";
										$result['message'] = $promoLabels['PROMOCODETOTALNOTMATCH'];;
										$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
										return $result;
								}
						}

						// Calculate Discount
						if($promotion_discount_type == "Percentage")
						{
								$item_total = 0.000;
								foreach($arrApplicableItems as $k => $item_price)
								{
										$item_total += $item_price;
								}

								$discount_amount = $item_total * $promotion_discount_amount / 100;

						}
						elseif($promotion_discount_type == "Fixed")
						{
								$discount_amount = $promotion_discount_amount;
						}
				}
		}
		else
		{
				if(!empty($promo_condition['Grand_Total']))
				{
						if(!empty($promo_condition['Grand_Total']['Equals To']) && $grand_total < $promo_condition['Grand_Total']['Equals To'])
						{
								$result['status'] = "error";
								$result['message'] = $promoLabels['PROMOCODETOTALNOTMATCH'];;
								$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
								return $result;
						}

						if(!empty($promo_condition['Grand_Total']['Not Equals To']) && $grand_total < $promo_condition['Grand_Total']['Not Equals To'])
						{
								$result['status'] = "error";
								$result['message'] = $promoLabels['PROMOCODETOTALNOTMATCH'];;
								$result['amount'] = number_format('0', $decimalNumber, $decimalSeparator, $thousandSeparator);
								return $result;
						}
				}


				// Calculate Discount
				if($promotion_discount_type == "Percentage")
				{
						$discount_amount = $grand_total * $promotion_discount_amount / 100;

				}
				elseif($promotion_discount_type == "Fixed")
				{
						$discount_amount = $promotion_discount_amount;
				}
		}

		//Step - 2.4
		$data = [
				'discount_amount' => $discount_amount,
				'promo_code' => $code
		];
		\App\Models\CartMaster::where('id', $cart_master_id)->update($data);
		if(!empty($arrApplicableItems))
		{
				foreach($arrApplicableItems as $cart_id => $item_price)
				{
						\App\Models\Cart::where('id', $cart_id)->update(['promo_code' => $code]);
				}
		}
		$Currency_rate = getCurrencyRates($defaultLanguageData->currency_id);
		$converted_disc = $discount_amount * $Currency_rate;
		$discount_amount = number_format($converted_disc, $decimalNumber, $decimalSeparator, $thousandSeparator);

		$result['status'] = $promoLabels['SUCCESS'];
		$result['message'] = $promoLabels['PROMOCODEAPPLIED'];
		$result['amount'] = $discount_amount;
		return $result;

}

function getIdByLastUniqueOrderId($last_unique_id, $prefix = '')
{
		if (!empty($last_unique_id)) {
				$uniqueId = preg_replace("/[^0-9]/", "", $last_unique_id);
				$uniqueId = $prefix . str_pad($uniqueId + 1, 5, '0', STR_PAD_LEFT);
		} else {
				$uniqueId = $prefix . str_pad(1, 5, '0', STR_PAD_LEFT);
		}
		return $uniqueId;
}

function create_order($cart_master_id, $language_id = null)
{
		$defaultLanguageData = \App\Models\GlobalLanguage::where('is_default', 1)
		->where('is_deleted', 0)->first();
		$decimalNumber = $defaultLanguageData->decimal_number;
		$decimalSeparator = $defaultLanguageData->decimal_separator;
		$thousandSeparator = $defaultLanguageData->thousand_separator;

		//Get Language Id
		$lang_id = (!empty($language_id)) ? $language_id : Session::get('language_id');

		$cart_master = \App\Models\CartMaster::where('id', $cart_master_id)->first();
		$customer_id = (Auth::guard('customer')->check()) ? Auth::guard('customer')->user()->id : $cart_master->user_id;
		$customer = \App\Models\Customer::where('id', $customer_id)->where('is_deleted', 0)->first();

		// Unique Order ID
		$merchant_order_id_temp = \App\Models\Orders::getOrderUniqueId();

		// Generate order data start
		$order = new \App\Models\Orders;
		$order->language_id = $lang_id;
		$order->is_parent_order = 'yes';
		$order->parent_order_id = '0';
		$order->user_id = $customer_id;
		$order->loyalty_card = $customer->loyalty_number;
		$order->cart_master_id = $cart_master_id;
		$order->order_id = $merchant_order_id_temp;
		//$order->ip_address = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
		$order->ip_address = "";
		// $order->shipping_method = ($cart_master->checkout_type == 1) ? "Delivery" : "StorePickup";
		$order->shipping_type = ($cart_master->checkout_type == 1) ? "delivery" : "store_pickup";
		if($cart_master->checkout_type == 1)
		{
			$order->shipping_method = "Aramex - Fixed Rate";	
		}
		else
		{
			$order->shipping_method = "N/A - Store Pickup";
		}
		$order->store_location_id = $cart_master->store_location_id ? $cart_master->store_location_id : '0';
		$order->payment_method = ($cart_master->payment_method == 1) ? "CreditCard" : 'DebitCard';
		$order->notes = $cart_master->message ? $cart_master->message : NULL;
		$order->promotions = $cart_master->promo_code ? $cart_master->promo_code : NULL;
		$order->first_name = $customer->first_name;
		$order->last_name = $customer->last_name;
		$order->phone = $customer->mobile;
		$order->email = $customer->email;

		//Get billing default address
		if($cart_master->same_as_ship_addr > 0)
		{
			$billign_address = \App\Models\CustomerAddress::where('customer_id', $customer_id)
			->where('id', $cart_master->address_id)->where('is_deleted', 0)->first();    
		}
		else
		{		
			if($cart_master->billing_address_id > 0)
			{
					$billign_address = \App\Models\BillingAddress::where('customer_id', $customer_id)
					->where('id', $cart_master->billing_address_id)->where('is_deleted', 0)->first();    
			}
			else
			{
					$billign_address = \App\Models\BillingAddress::where('customer_id', $customer_id)
					->where('is_default', 1)->where('is_deleted', 0)->first();
			}
		}

		if($billign_address)
		{
				$order->b_fullname = $billign_address->fullname;
				$order->b_address_line_1 = $billign_address->address_1;
				$order->b_address_line_2 = $billign_address->address_2;
				$order->b_city = $billign_address->city;
				$order->b_state = $billign_address->state;
				$countries = \App\Models\Country::where('id', $billign_address->country)->first();
				$order->b_country = $countries->name;
				$order->b_pincode = $billign_address->pincode;
				$order->b_address_type = ($billign_address->address_type == 1) ? 'Home' : 'Office';
				$order->b_phone1 = $billign_address->phone1;
		}
		else
		{
				$customer_address = \App\Models\CustomerAddress::where('customer_id', $customer_id)
				->where('is_default', 1)->where('is_deleted', 0)->first();
				$order->s_fullname = $customer_address->fullname;
				$order->s_address_line_1 = $customer_address->address_1;
				$order->s_address_line_2 = $customer_address->address_2;
				$order->s_city = $customer_address->city;
				$order->s_state = $customer_address->state;
				$countries = \App\Models\Country::where('id', $customer_address->country)->first();
				$order->s_country = $countries->name;
				$order->s_pincode = $customer_address->pincode;
				$order->s_address_type = ($customer_address->address_type == 1) ? 'Home' : 'Office';
				$order->s_phone1 = $customer_address->phone1;
		}

		//Customer Address
		$customer_address = \App\Models\CustomerAddress::where('customer_id', $customer_id)
		->where('id', $cart_master->address_id)->where('is_deleted', 0)->first();
		if($customer_address)
		{
				$order->s_fullname = $customer_address->fullname;
				$order->s_address_line_1 = $customer_address->address_1;
				$order->s_address_line_2 = $customer_address->address_2;
				$order->s_city = $customer_address->city;
				$order->s_state = $customer_address->state;
				$countries = \App\Models\Country::where('id', $customer_address->country)->first();
				$order->s_country = $countries->name;
				$order->s_pincode = $customer_address->pincode;
				$order->s_address_type = ($customer_address->address_type == 1) ? 'Home' : 'Office';
				$order->s_phone1 = $customer_address->phone1;
		}
		else
		{			
			$customer_address = \App\Models\CustomerAddress::where('customer_id', $customer_id)
			->where('is_default', 1)->where('is_deleted', 0)->first();
			$order->s_fullname = $customer_address->fullname;
			$order->s_address_line_1 = $customer_address->address_1;
			$order->s_address_line_2 = $customer_address->address_2;
			$order->s_city = $customer_address->city;
			$order->s_state = $customer_address->state;
			$countries = \App\Models\Country::where('id', $customer_address->country)->first();
			$order->s_country = $countries->name;
			$order->s_pincode = $customer_address->pincode;
			$order->s_address_type = ($customer_address->address_type == 1) ? 'Home' : 'Office';
			$order->s_phone1 = $customer_address->phone1;
		}

		$order->prepaid = 'yes';
		$order->order_status_id = '45';
		$order->created_at = date('Y-m-d H:i:s');


		//VAT Calculation
		$totalVat = 0.00;
		$totalSubTotal = 0.00;
		$discount = 0.00;
		$shipping = 0.00;
		$grandTotal = 0.00;
		$cart_arr = [];
		$i = 0;
		$cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();
		$get_curr = \App\Models\GlobalLanguage::select('currency_id', 'tax_type')
		->where('is_default', 1)->where('is_deleted', 0)->first();
		$Currency_rate = getCurrencyRates($get_curr->currency_id);
		$curr_symb = getCurrSymBasedOnLangId($defaultLanguageData->id);
		foreach ($cart as $data) {
				//VAT Calculation Start
				$product = \App\Models\Product::where('id', $data->product_id)->whereNull('deleted_at')->first();
				if($product)
				{
						if($product->tax_class_id > 0 && $product->tax_class_id != '' && !empty($product->tax_class_id))
						{
								$tex_class = \App\Models\TaxClass::where('id', $product->tax_class_id)->whereNull('deleted_at')->first();
								$tex_rate = \App\Models\TaxRate::where('id', $tex_class->tax_rate_ids)->whereNull('deleted_at')->first();

								//Case 1
								if(empty($product->tax_class_id) || $tex_rate->rate == 0.000)
								{
										$unitPrice = $data->price;
										$subTotal = $unitPrice * $data->quantity;
										$unitVat = 0;
										$totalVat += $unitVat;
										$totalSubTotal += str_replace(',','', number_format($subTotal, $decimalNumber, $decimalSeparator, $thousandSeparator));
								}
								else
								{
										//Case 2
										if($tex_rate->rate > 0)
										{
												if($get_curr->tax_type == 1)
												{
														// Including
														$unitPrice = (($data->price * 100) / (100 + $tex_rate->rate));
														$unitVat = $data->price - $unitPrice;
														$subTotal = $unitPrice * $data->quantity;
														$subVat = $unitVat * $data->quantity;
														$totalVat += $subVat;
														$totalSubTotal += str_replace(',','', number_format($subTotal, $decimalNumber, $decimalSeparator, $thousandSeparator));
												}
												else
												{
														// Excluding
														$unitVat = ($data->price * $tex_rate->rate) / 100;
														$unitPrice = $data->price + $unitVat;
														$subTotal = $unitPrice * $data->quantity;
														$subVat = $unitVat * $data->quantity;
														$totalVat += $subVat;
														$totalSubTotal += str_replace(',','', number_format($subTotal, $decimalNumber, $decimalSeparator, $thousandSeparator));
												}
										}
								}
						}
						else
						{
								$unitPrice = $data->price;
								$subTotal = $unitPrice * $data->quantity;
								$unitVat = 0;
								$totalVat += $unitVat;
								$totalSubTotal += str_replace(',','', number_format($subTotal, $decimalNumber, $decimalSeparator, $thousandSeparator));
						}
				}
		}

		//Sub Total Calculation
		$grandTotal += $totalSubTotal;		

		$discount = $cart_master->discount_amount;
		//Calculate 5% of dicsount
		$calc_five_perc_from_disc = $discount * 5 / 100;
		$totalVat -= $calc_five_perc_from_disc;
		$grandTotal -= $discount;

		//Total VAT Calculation
		$grandTotal += $totalVat;

		if($cart_master->checkout_type == 2)
		{
				$order->total_shipping_cost = number_format(0, $decimalNumber, $decimalSeparator, $thousandSeparator);
				$grandTotal += number_format(0, $decimalNumber, $decimalSeparator, $thousandSeparator);
		}
		else
		{
				$order->total_shipping_cost = $cart_master->shipping_cost;
				$grandTotal += $cart_master->shipping_cost;
		}
		$order->discount_amount = $cart_master->discount_amount;
		$order->tax_amount = $totalVat;
		$order->subtotal = $totalSubTotal;
		$order->total = $grandTotal;
		$order->save();
		// Generate order data over

		// Generate order products data start
		$order_id = $order->id;
		foreach($cart as $data)
		{
				$order_products = new \App\Models\OrderProducts;
				$order_products->order_status_id = '44';
				$order_products->admin_verified = 'Yes';
				$order_products->seller_verified = NULL;
				$order_products->order_id = $order_id;
				$order_products->product_id = $data->product_id;
				$order_products->option_id = $data->option_id;
				$order_products->gift_wrap = $data->gift_wrap;
				$order_products->gift_message = $data->gift_message;
				$order_products->lady_operator = $data->lady_operator;
				$order_products->message = $data->message;
				$product = \App\Models\Product::where('id', $data->product_id)->whereNull('deleted_at')->first();
				$order_products->category_id = $product->category_id;
				$order_products->seller_id = '0';
				$order_products->quantity = $data->quantity;

				//VAT Calculation Start
				if($product)
				{
						if($product->tax_class_id > 0 && $product->tax_class_id != '' && !empty($product->tax_class_id))
						{
								$tex_class = \App\Models\TaxClass::where('id', $product->tax_class_id)->whereNull('deleted_at')->first();
								$tex_rate = \App\Models\TaxRate::where('id', $tex_class->tax_rate_ids)->whereNull('deleted_at')->first();

								//Case 1
								if(empty($product->tax_class_id) || $tex_rate->rate == 0.000)
								{
										$unitPrice = $data->price;
										$order_products->price = $unitPrice;
								}
								else
								{
										//Case 2
										if($tex_rate->rate > 0)
										{
												if($get_curr->tax_type == 1)
												{
														// Including
														$unitPrice = (($data->price * 100) / (100 + $tex_rate->rate));
														$order_products->price = $unitPrice;
												}
												else
												{
														// Excluding (no need to add vat as we are storing excluding vat price)
														//$unitVat = ($data->price * $tex_rate->rate) / 100;
														//$unitPrice = $data->price + $unitVat;
														$order_products->price = $data->price;
												}
										}
								}
						}
						else
						{
								$unitPrice = $data->price;
								$order_products->price = $unitPrice;
						}
				}
				$order_products->shipping_cost = '0';
				$order_products->shipping_cost = '0';
				$order_products->promo_code = $data->promo_code;
				$order_products->created_at = date('Y-m-d H:i:s');

				$order_products->other_images = $data->other_images;

				$order_products->screenshots_files = $data->screenshots_files;
				$order_products->print_files = $data->print_files;

				//Array Serialize
				$product_details = \App\Models\ProductDetails::where('product_id', $data->product_id)->whereNull('deleted_at')->first();
				$product_pricing = \App\Models\ProductPricing::where('id', $data->option_id)->whereNull('deleted_at')->first();
				$attribute_group_ids = explode(',',$product_pricing->attribute_group_ids);
				$attribute_ids = explode(',',$product_pricing->attribute_ids);

				$title = [];
				$j = 0;
				foreach ($attribute_group_ids as $id) {
						$attrGrougDetails = \App\Models\AttributeGroupDetails::select('display_name')
						->where('attr_group_id', $id)->where('language_id', $defaultLanguageData->id)->whereNull('deleted_at')->first();
						if($attrGrougDetails)
						{
								$title[$j]['title'] = $attrGrougDetails->display_name;
						}
						$j++;
				}

				$value = [];
				$k = 0;
				foreach ($attribute_ids as $id) {
						$attrDetails = \App\Models\AttributeDetails::select('display_name')
						->where('attribute_id', $id)->where('language_id', $defaultLanguageData->id)
						->whereNull('deleted_at')->first();
						if($attrDetails)
						{
								$value[$k]['value'] = $attrDetails->display_name;
						}
						$k++;
				}

				$attributes = array();
				foreach ($value as $key => $v) {
						$attr_key = ($title[$key]['title']) ? $title[$key]['title'] : "";
						$attr_val = ($v['value']) ? $v['value'] : "";
						$attributes[$attr_key] = $attr_val;
				}

				$details = array(
						'id' => $data->id,
						'category_id' => $product->category_id,
						'manufacturer_id' => $product->manufacturer_id,
						'title' => $product_details->title,
						'description' => $product_details->description,
						'seller_sku' => $product_pricing->sku,
						'product_slug' => $product->product_slug,
						'mrp_price' => $product_pricing->mrp,
						'selling_price' => $product_pricing->selling_price,
						'image' => $data->image,
						'photobook_caption' => !empty($data->photobook_caption) ? $data->photobook_caption : "",
						'attributes' => $attributes,
				);
				$serialize_data = serialize($details);
				$order_products->details = $serialize_data;
				$order_products->save();
		}
		// Generate order products data over

		Session::put('order_id', $order_id);
		Session::put('merchant_order_id', $merchant_order_id_temp);

		$result['status'] = 'true';
		$result['message'] = 'Order placed successfully!';
		$result['order_id'] = $order_id;
		$result['merchant_order_id'] = $merchant_order_id_temp;
		return $result;
}

function create_credimax_session($order_id = null, $merchant_order_id = null)
{
		$baseUrl = url('/');
		$isMobile = false;
		if(!empty($order_id) && !empty($merchant_order_id))
		{
			$isMobile = true;
		}
		$merchant_order_id = (!empty($merchant_order_id)) ? $merchant_order_id : Session::get('merchant_order_id');
		$merchant_id = config('app.CREDIMAX_MERCHANT_ID');
		$action = config('app.CREDIMAX_ACTION');
		$url = "https://credimax.gateway.mastercard.com/api/rest/version/54/merchant/".$merchant_id."/session";

		$order_id = (!empty($order_id)) ? $order_id : Session::get('order_id');
		$grandTotal = \App\Models\Orders::where('id', $order_id)->first();

		// Success and Failure URL
		if($isMobile)
		{
			$returnURl = $baseUrl."/mobile/credimax/success?order_id=".$order_id."&merchant_order_id=".$merchant_order_id."&isMobile=1";
			$cancelUrl = $baseUrl."/mobile/credimax/cancel?order_id=".$order_id."&merchant_order_id=".$merchant_order_id."&isMobile=1";
		}
		else
		{
			$returnURl = $baseUrl."/customer/credimax/success";
			$cancelUrl = $baseUrl."/customer/credimax/cancel";
		}

		$data = array(
				"apiOperation" => "CREATE_CHECKOUT_SESSION",
				"order" => array(
						"amount" =>  $grandTotal->total,
						"currency" => "BHD",
						"id" => $merchant_order_id
				),
				"interaction" => array(
						"operation"=>$action,
						"returnUrl"=>$returnURl,
						"cancelUrl"=>$cancelUrl,
						"merchant" => array(
								"name"=> "ASHRAFS",
								"logo"=>$baseUrl."/public/assets/frontend/img/Alboumi_Logo.png"
						),
				)
		);

		$payload = json_encode($data);

		$curl = curl_init();
		curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $payload,
				CURLOPT_HTTPHEADER => array(
						"authorization: Basic ".config('app.CREDIMAX_BASIC_AUTH'),
						"cache-control: no-cache",
						"content-type: application/json"
				),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$res = json_decode($response, true);

		if($res['result'] == "ERROR")
		{
				$error = $res['error']['explanation'];
				$arrData = array('error' => 1, 'msg' => $error);
		}
		else
		{
				$session_id = $res['session']['id'];
				Session::put('session_id', $session_id);
				$arrData = array('error' => 0, 'msg' => "Success", 'session_id' => $session_id,
				'merchant_order_id' => $merchant_order_id, 'merchant_id' => $merchant_id, 'grandTotal' => $grandTotal->total);
		}

		return json_encode($arrData);
}

function createCredimaxSessionForEventOrders($order_id = null, $merchant_order_id = null)
{
		$baseUrl = url('/');
		$isMobile = false;
		if(!empty($order_id) && !empty($merchant_order_id))
		{
			$isMobile = true;
		}
		$merchant_order_id = (!empty($merchant_order_id)) ? $merchant_order_id : Session::get('merchant_order_id');
		$merchant_id = config('app.CREDIMAX_MERCHANT_ID');
		$action = config('app.CREDIMAX_ACTION');
		$url = "https://credimax.gateway.mastercard.com/api/rest/version/54/merchant/".$merchant_id."/session";

		$order_id = (!empty($order_id)) ? $order_id : Session::get('order_id');
		$grandTotal = EventPhotoOrders::select('amount')->where('id', $order_id)->first();
	
		// Success and Failure URL
		if($isMobile)
		{
			$returnURl = $baseUrl."/mobile/eventorders/credimax/success?order_id=".$order_id."&merchant_order_id=".$merchant_order_id."&isMobile=1";
			$cancelUrl = $baseUrl."/mobile/eventorders/credimax/cancel?order_id=".$order_id."&merchant_order_id=".$merchant_order_id."&isMobile=1";
		}
		else
		{
			$returnURl = $baseUrl."/customer/eventorders/credimax/success";
			$cancelUrl = $baseUrl."/customer/eventorders/credimax/cancel";
		}

		$data = array(
				"apiOperation" => "CREATE_CHECKOUT_SESSION",
				"order" => array(
						"amount" =>  $grandTotal->amount,
						"currency" => "BHD",
						"id" => $merchant_order_id
				),
				"interaction" => array(
						"operation"=>$action,
						"returnUrl"=>$returnURl,
						"cancelUrl"=>$cancelUrl,
						"merchant" => array(
								"name"=> "ASHRAFS",
								"logo"=>$baseUrl."/public/assets/frontend/img/Alboumi_Logo.png"
						),
				)
		);

		$payload = json_encode($data);

		$curl = curl_init();
		curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $payload,
				CURLOPT_HTTPHEADER => array(
						"authorization: Basic ".config('app.CREDIMAX_BASIC_AUTH'),
						"cache-control: no-cache",
						"content-type: application/json"
				),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$res = json_decode($response, true);

		if($res['result'] == "ERROR")
		{
				$error = $res['error']['explanation'];
				$arrData = array('error' => 1, 'msg' => $error);
		}
		else
		{
				$session_id = $res['session']['id'];
				Session::put('session_id', $session_id);
				$arrData = array('error' => 0, 'msg' => "Success", 'session_id' => $session_id,
				'merchant_order_id' => $merchant_order_id, 'merchant_id' => $merchant_id, 'grandTotal' => $grandTotal->amount);
		}
		return json_encode($arrData);
}

function convertTimeToTz($datetime,$timezone) {
				$given = new DateTime($datetime, new DateTimeZone("UTC"));
				$given->setTimezone(new DateTimeZone($timezone));
				$output = $given->format("Y-m-d"); //can change as per your requirement
				return $output;
			}
function getParentCategories($defaultLang)
{
		$categories = \App\Models\Category::select('categories.id as parent_cat_id','category_details.title')
		->leftJoin('category_details','category_details.category_id','='
		,'categories.id')->where('categories.parent_id','=', 0)
		->where('category_details.language_id',$defaultLang)
		->whereNull('categories.deleted_at')->get();
		return $categories;
}

function updateinventory($cart_master_id)
{
		$cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)
		->where(function($query)
		{
				$query->whereNull('image')
				->orWhere('image','=','');
		})
		->get();
		if($cart)
		{
				foreach ($cart as $data) {
						$product_pricing = \App\Models\ProductPricing::where('id', $data->option_id)->first();
						if($product_pricing)
						{
								$updated_qty = $product_pricing->quantity - $data->quantity;
								$product_pricing->quantity = $updated_qty;
								$product_pricing->save();            
						}
				}        
		}   
		return true; 
}

function uploadCKeditorImage($request, $folder_name)
{
	if($request->hasFile('upload')) {		 
		$originName = $request->file('upload')->getClientOriginalName();
		$fileName = pathinfo($originName, PATHINFO_FILENAME);
		$extension = $request->file('upload')->getClientOriginalExtension();
		$fileName = $fileName.'_'.time().'.'.$extension;
	
		$request->file('upload')->move(public_path('/images/'.$folder_name.'/'), $fileName);

		$CKEditorFuncNum = $request->input('CKEditorFuncNum');
		$url = asset('/public/images/'.$folder_name.'/'.$fileName); 
		$msg = 'Image uploaded successfully'; 
		$response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
		   
		@header('Content-type: text/html; charset=utf-8'); 
		echo $response;
	}
}

function updateCustomerTimezone($ip_address, $customer_id)
{
	if($ip_address != '' && !empty($ip_address))
	{ 
		$geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ip_address")); 
		if(isset($geo["geoplugin_timezone"]) && $geo["geoplugin_timezone"] != '' && !is_null($geo["geoplugin_timezone"]))
		{
			date_default_timezone_set($geo["geoplugin_timezone"]);
			$Offset = date('O');
			date_default_timezone_set(config('app.timezone'));
			$zone_time = substr($Offset,0,3).':'.substr($Offset,3,3);           

			$customer_timezone = \App\Models\CustomerTimezone::where('customer_id', $customer_id)->first();
			if($customer_timezone)
			{
				$customer_timezone->timezone = $geo["geoplugin_timezone"];
				$customer_timezone->zone = $zone_time;
				$customer_timezone->save();
			}
			else
			{
				$customer_timezone = new \App\Models\CustomerTimezone;
				$customer_timezone->customer_id = $customer_id;
				$customer_timezone->timezone = $geo["geoplugin_timezone"];
				$customer_timezone->zone = $zone_time;
				$customer_timezone->save();
			}
		}		
	}
	return true;  
}

function notification($type, $user_id, $o_id, $order_id)
{
	$notifications = new \App\Models\Notifications;
	$notifications->user_id = $user_id;
	$notifications->notification_type = $type;
	$notifications->order_id = $o_id;
	$notifications->order_number = $order_id;
	$notifications->save();
	return true;
}