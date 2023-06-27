<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ReuseFunctionTrait;
use Exception;
use Auth;
use DB;
use App\Models\Cart;
use App\Models\CartMaster;
use App\Models\RecommendedProduct;
use App\Models\CustGroupPrice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\TempCartImages;
use App\Models\Settings;

class CartController extends Controller
{
    use ReuseFunctionTrait;

    public function getMyCart(Request $request)
    {
        // try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => "300",
                'message' => $validator->errors(),
                ],300);
            }

            $lang_id = $request->language_id;
            $language_found = isLanguageExists($lang_id);
            if($language_found == 'false')
            {
                $defaultLang = $this->getDefaultLanguage();
                $codes = ['LANGUAGENOTFOUND'];
                $homePageAPILabels = getCodesMsg($defaultLang, $codes);

                $result['statusCode'] = "300";
                $result['message'] = $homePageAPILabels['LANGUAGENOTFOUND'];
                return response()->json($result,300);
            }

            //Update shipping cost
            if(isset($request->cart_master_id) && !empty($request->cart_master_id))
            {
                check_tier_price($request->cart_master_id);
                update_shippingcost($request->cart_master_id);
            }

            $baseUrl = $this->getBaseUrl();
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM",'OK','SUCCESS','SUCCESSSTATUS',
            'SUBTOTAL','SHIPPINGCOST','DISCOUNT','PRICEDETAILS','GRANDTOTAL','SHOPPINGCARTEMPTY'
            ,'VAT','NET',"CARTDATANOTFOUND"];
            $cartLabels = getCodesMsg($lang_id, $codes);

            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('id', $lang_id)->where('is_deleted', 0)->first();
            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            // $msg = [
            //     // 'cart_master_id.required' => $cartLabels["MASTERCARTIDREQ"],
            //     // 'cart_master_id.numeric' => $cartLabels["MASTERCARTIDNUM"],
            // ];

            // $validator = Validator::make($request->all(), [
            //     'cart_master_id' => 'numeric',
            // ],$msg);

            // if ($validator->fails()) {
            //     return response()->json([
            //     'statusCode' => "300",
            //     'message' => $validator->errors(),
            //     ],300);
            // }

            $purchasedProduct = [];
            $purchasedProduct["componentId"] = "purchasedProduct";
            $purchasedProduct["sequenceId"] = "1";
            $purchasedProduct["isActive"] = "1";

            $cart_arr = [];
            $i = 0;
            $price_sum = [];
            //VAT Calculation
            $totalVat = 0.00;
            $totalSubTotal = 0.00;
            $discount = 0.00;
            $shipping = 0.00;
            $grandTotal = 0.00;

            $get_curr = \App\Models\GlobalLanguage::select('currency_id','tax_type')->where('id', $lang_id)->first();
            $Currency_rate = getCurrencyRates($get_curr->currency_id);
            $curr_symb = getCurrSymBasedOnLangId($lang_id);
            $cart = \App\Models\Cart::where('cart_master_id', $request->cart_master_id)->get();
            if(count($cart) == 0)
            {
                $result['statusCode'] = "300";
                $result['message'] = $cartLabels["CARTDATANOTFOUND"];
                return response()->json($result);
            }
            if(count($cart) == 0 || empty($cart) || !isset($request->cart_master_id) || $request->cart_master_id == '')
            {
                // $price_sum[] = 0.00;
                // $purchasedProduct["purchasedProductData"]["list"] = [];
                $result['status'] = $cartLabels["OK"];
                $result['statusCode'] = "300";
                $result['message'] = $cartLabels["SHOPPINGCARTEMPTY"];
                return response()->json($result, 300);
            }
            else
            {
                foreach ($cart as $data) {
                    $cart_arr[$i]['id'] = (String) $data->id;
                    $images = \App\Models\Image::where('imageable_id', $data->product_id)->where('image_type',
                    "product")->where('is_default','yes')->whereNull('deleted_at')->first();
                    if(!empty($data->image))
                    {
                        $cart_arr[$i]['image'] = (String)$data->image;
                    }
                    elseif($images)
                    {
                        // $cart_arr[$i]['image'] = $baseUrl.$images->upload_path.$images->name;
                        $cart_arr[$i]['image'] = $baseUrl."/public".$images->upload_path.$images->name;
                    }
                    else
                    {
                        $cart_arr[$i]['image'] = $baseUrl."/public/assets/images/no_image.png";
                    }
                    $cart_arr[$i]['itemId'] = (String) $data->product_id;

                    $default_lang_id = $this->getDefaultLanguage();
                    $product_detail = $this->getProductDetails($data->product_id, $lang_id);
                    $product_title = $product_detail ? $product_detail : $this->getProductDetails($data->product_id, $default_lang_id);
                    $cart_arr[$i]['title'] = $product_title ? $product_title->title : "";

                    $product_pricing = \App\Models\ProductPricing::where('id', $data->option_id)->whereNull('deleted_at')->first();
                    $categoryData=Product::select('categories.photo_upload')->leftjoin('categories','categories.id','=','products.category_id')->where("products.id",$data->product_id)->where("products.status",'Active')->whereNull("products.deleted_at")->first();
                    // max qty
                    $max_qty = "1000";

                    if($product_pricing->count() == 0)
                    {
                        $price_sum[] = 0.00;
                        //$purchasedProduct["purchasedProductData"]["list"] = [];
                    }

                    if(isset($product_pricing->quantity))
                    {
                        $max_qty = $product_pricing->quantity;
                    }
                    if($categoryData->photo_upload==1){
                      $max_qty = '999999';
                    }

                    $attribute_group_ids = explode(',',$product_pricing->attribute_group_ids);
                    $attribute_ids = explode(',',$product_pricing->attribute_ids);

                    $qty = ($data->quantity) ? (String) $data->quantity : '0';
                    $product_price = (!empty($product_pricing->selling_price)) ? $product_pricing->selling_price : $product_pricing->offer_price;
                    $price_sum[$i] = str_replace(',', '', number_format(str_replace(',', '', $product_price * $qty) * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                    // $cart_arr[$i]['price'] =  $curr_symb." ".number_format(($product_price * $qty) * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    $cart_arr[$i]['type'] = "2";
                    $cart_arr[$i]['outOfStockFlag'] = "0";
                    $cart_arr[$i]['max_qty'] = (String) $max_qty;
                    $cart_arr[$i]['navigationFlag'] = "1";
                    $cart_arr[$i]['query'] = url('api/v1/getProductDetails')."?language_id=".$lang_id."&product_id=".$data->product_id;
                    $cart_arr[$i]['qty'] = ($data->quantity) ? (String) $data->quantity : "";
                    $cart_arr[$i]['isCouponApplied'] = "0";
                    $cart_arr[$i]['couponCode'] = "";

                    $title = $this->attributeGroupDtl($attribute_group_ids, $lang_id, $default_lang_id);
                    $value = $this->attributeDtl($attribute_ids, $lang_id, $default_lang_id);

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
                                $totalVat += $unitVat * $Currency_rate;
                                $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
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
                                        $totalVat += $subVat * $Currency_rate;
                                        $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                                    }
                                    else
                                    {
                                        // Excluding
                                        $unitVat = ($data->price * $tex_rate->rate) / 100;
                                        $unitPrice = $data->price + $unitVat;
                                        $subTotal = $unitPrice * $data->quantity;
                                        $subVat = $unitVat * $qty;
                                        $totalVat += $subVat * $Currency_rate;
                                        $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                                    }
                                }
                            }
                        }
                        else
                        {
                            $unitPrice = $data->price;
                            $subTotal = $unitPrice * $data->quantity;
                            $unitVat = 0;
                            $totalVat += $unitVat * $Currency_rate;
                            $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                            $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                            $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                        }
                    }
                    //VAT Calculation End

                    $variant = [];
                    foreach ($value as $key => $v) {
                        $variant[$key]['title'] = ($title[$key]['title']) ? $title[$key]['title'] : "";
                        $variant[$key]['value'] = ($v['value']) ? $v['value'] : "";
                    }
                    $cart_arr[$i]['variant'] = (!empty($variant)) ? $variant : [];

                    $i++;
                }

                //Sub Total Calculation
                $grandTotal += $totalSubTotal;

                // //Total VAT Calculation
                // $grandTotal += $totalVat;

                $purchasedProduct["purchasedProductData"]["list"] = $cart_arr;


                //Promo Code
                $promo_code = [];
                $promo_code['componentId'] = "promoCode";
                $promo_code['sequenceId'] = "1";
                $promo_code['isActive'] = "1";
                $cartMaster = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
                $promo_code['couponCode'] = (!empty($cartMaster->promo_code)) ? $cartMaster->promo_code : "";



                //PriceDetails Data
                if($cartMaster)
                {
                    if($cartMaster->shipping_cost > 0)
                    {
                        $shipping = $cartMaster->shipping_cost * $Currency_rate;
                        $grandTotal += $shipping;
                        $shipping_cost = $curr_symb." ".number_format($shipping, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    }
                    else
                    {
                        $shipping_cost = $curr_symb." ".number_format($cartMaster->shipping_cost * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    }

                    if($cartMaster->discount_amount > 0)
                    {
                        $discount = $cartMaster->discount_amount * $Currency_rate;
                        //Calculate 5% of dicsount
                        $calc_five_perc_from_disc = $discount * 5 / 100;
                        $totalVat -= $calc_five_perc_from_disc;
                        $disp_in_net_disc = $discount;
                        $grandTotal -= $discount;
                        $discount = $curr_symb." ".number_format($discount, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    }
                    else
                    {
                        $disp_in_net_disc = $cartMaster->discount_amount * $Currency_rate;
                        $discount = $curr_symb." ".number_format($cartMaster->discount_amount * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    }

                    //Total VAT Calculation
                    $grandTotal += $totalVat;

                    $net = $totalSubTotal - $disp_in_net_disc;
                    // $priceDetails['VAT'] = $curr_symb." ".number_format($totalVat * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    // $priceDetails['sub_total'] = $curr_symb." ".number_format($totalSubTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    // $priceDetails['grand_total'] = $curr_symb." ".number_format($grandTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);

                    //List Data
                    $listData = [
                        array('leftText' => $cartLabels["SUBTOTAL"],
                        'rightText' => $curr_symb." ".number_format($totalSubTotal, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                        array('leftText' => $cartLabels["DISCOUNT"], 'rightText' => $discount),
                        array('leftText' => $cartLabels["NET"], 'rightText' => $curr_symb." ".number_format($net, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                        array('leftText' => $cartLabels["VAT"], 'rightText' => $curr_symb." ".number_format($totalVat, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                        array('leftText' => $cartLabels["SHIPPINGCOST"], 'rightText' => $shipping_cost),

                    ];

                    $priceDetails['priceDetailsData']['listData'] = $listData;
                }
                else
                {
                    $priceDetails['priceDetailsData']['listData'] = [];
                }

                $priceDetails = [];
                $priceDetails['componentId']  = "priceDetails";
                $priceDetails['sequenceId'] = "1";
                $priceDetails['isActive'] = "1";
                $priceDetails['priceDetailsData']['title'] = $cartLabels["PRICEDETAILS"];
                $priceDetails['priceDetailsData']['payableAmountLable'] = $cartLabels["GRANDTOTAL"];
                // $grand_total = array_sum($price_sum) +  (!empty($cartMaster->promo_code) ? number_format($cartMaster->discount_amount * $Currency_rate, 2) :0 )+ (!empty($cartMaster->promo_code) ? number_format($cartMaster->shipping_cost * $Currency_rate, 2):0);
                // $grand_total = (array_sum($price_sum) + (!empty($cartMaster->shipping_cost) ? number_format($cartMaster->shipping_cost * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator) :0 )) - (!empty($cartMaster->discount_amount) ? number_format($cartMaster->discount_amount * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator) :0 );
                // $priceDetails['priceDetailsData']['payableAmount'] = $curr_symb." ".number_format($grand_total, $decimalNumber, $decimalSeparator, $thousandSeparator);

                $priceDetails['priceDetailsData']['payableAmount'] = $curr_symb." ".number_format($grandTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
                $priceDetails['priceDetailsData']['listData'] = (!empty($listData)) ? $listData : [];

                $result['status'] = $cartLabels["OK"];
                $result['statusCode'] = "200";
                $result['message'] = $cartLabels["SUCCESS"];
                $result["cartCount"] = (count($cart) > 0 ) ? (string) count($cart) : "0";
                $result['component'][] = $purchasedProduct;
                $result['component'][] = $promo_code;
                $result['component'][] = $priceDetails;
                return response()->json($result);
            }

        // } catch (\Exception $e) {
        //     return handleServerError($lang_id);
        // }
    }

    public function getProductDetails($product_id, $language_id)
    {
        $product_details = \App\Models\ProductDetails::where('product_id',$product_id)
        ->where('language_id', $language_id)->whereNull('deleted_at')->first();
        if($product_details)
        {
            return $product_details;
        }
    }

    public function attributeGroupDtl($attribute_group_ids, $lang_id, $default_lang_id)
    {
        $title = [];
        $i = 0;
        foreach ($attribute_group_ids as $id) {
            $attrGrougDetails = \App\Models\AttributeGroupDetails::select('display_name')
            ->where('attr_group_id', $id)->where('language_id', $lang_id)->whereNull('deleted_at')->first();
            if($attrGrougDetails)
            {
                $title[$i]['title'] = $attrGrougDetails->display_name;
            }
            else
            {
                $attrGrougDetails = \App\Models\AttributeGroupDetails::select('display_name')
                ->where('attr_group_id', $id)->where('language_id', $default_lang_id)
                ->whereNull('deleted_at')->first();
                if($attrGrougDetails)
                {
                    $title[$i]['title'] = $attrGrougDetails->display_name;
                }
            }
            $i++;
        }
        return $title;
    }

    public function attributeDtl($attribute_ids, $lang_id, $default_lang_id)
    {
        $value = [];
        $k = 0;
        foreach ($attribute_ids as $id) {
            $attrDetails = \App\Models\AttributeDetails::select('display_name')
            ->where('attribute_id', $id)->where('language_id', $lang_id)
            ->whereNull('deleted_at')->first();
            if($attrDetails)
            {
                $value[$k]['value'] = $attrDetails->display_name;
            }
            else
            {
                $attrDetails = \App\Models\AttributeDetails::select('display_name')
                ->where('attribute_id', $id)->where('language_id', $default_lang_id)
                ->whereNull('deleted_at')->first();
                if($attrDetails)
                {
                    $value[$k]['value'] = $attrDetails->display_name;
                }
            }
            $k++;
        }
        return $value;
    }

    public function getCheckoutList(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => "300",
                'message' => $validator->errors(),
                ],300);
            }

            $lang_id = $request->language_id;
            $baseUrl = $this->getBaseUrl();
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM",'OK','SUCCESS','SUCCESSSTATUS',
            'SUBTOTAL','SHIPPINGCOST','DISCOUNT','PRICEDETAILS','GRANDTOTAL','DELIVERY','addressType1',
            'addressType2','STOREPICKUPFREE','VAT','BILLINGTITLE','NET'];
            $cartListLabels = getCodesMsg($lang_id, $codes);

            $msg = [
                'cart_master_id.required' => $cartListLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $cartListLabels["MASTERCARTIDNUM"],
            ];

            $validator = Validator::make($request->all(), [
                'cart_master_id' => 'required|numeric',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => "300",
                'message' => $validator->errors(),
                ],300);
            }

            $language_found = isLanguageExists($lang_id);
            if($language_found == 'false')
            {
                $defaultLang = $this->getDefaultLanguage();
                $codes = ['LANGUAGENOTFOUND'];
                $homePageAPILabels = getCodesMsg($defaultLang, $codes);

                $result['statusCode'] = "300";
                $result['message'] = $homePageAPILabels['LANGUAGENOTFOUND'];
                return response()->json($result,300);
            }

            //Update shipping cost
            if(isset($request->cart_master_id) && !empty($request->cart_master_id))
            {
                check_tier_price($request->cart_master_id);
                update_shippingcost($request->cart_master_id);
            }

            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('id', $lang_id)->where('is_deleted', 0)->first();
            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            $customer_id = Auth::guard('api')->user()->token()->user_id;
            //Address Data
            $address = [];
            $address['componentId'] = "checkoutAddress";
            $address['sequenceId'] = "1";
            $address['isActive'] = "1";
            $address['isFromMyorders'] = "0";
            $address['checkoutAddressData']['title'] = $cartListLabels["DELIVERY"];

            $address_data = [];
            $i = 0;
            $customer_addresses = \App\Models\CustomerAddress::where('customer_id', $customer_id)
            ->where('is_deleted', 0)->get();
            if(!empty($customer_addresses))
            {
                foreach ($customer_addresses as $customer_address) {
                    $address_data[$i]['addressId'] =(String) $customer_address->id;
                    $address_data[$i]['fullName'] = $customer_address->fullname;
                    $address_data[$i]['addressLine1'] = $customer_address->address_1;
                    $address_data[$i]['addressLine2'] = $customer_address->address_2;
                    $address_data[$i]['state'] = $customer_address->state;
                    $address_data[$i]['city'] = $customer_address->city;
                    $countries = DB::table('countries')->where('id', $customer_address->country)->first();
                    $address_data[$i]['countryId'] = (String) $customer_address->country;
                    $address_data[$i]['country'] = $countries->name;
                    $address_data[$i]['postCode'] = $customer_address->pincode;
                    $address_data[$i]['mobile'] = $customer_address->phone1;
                    $address_data[$i]['addressType'] = (String) $customer_address->address_type;
                    $address_data[$i]['addressTypeName'] = ($customer_address->address_type == 1) ? $cartListLabels['addressType1'] : $cartListLabels['addressType2'];
                    $address_data[$i]['isSelected'] = (String) $customer_address->is_default;
                    $i++;
                }
                $address['checkoutAddressData']['address'] = $address_data;
            }
            else
            {
                $address['checkoutAddressData']['address'] = [];
            }

            //Billing Address
            $billingAddress = [];
            $billingAddress['componentId'] = "checkoutBillingAddress";
            $billingAddress['sequenceId'] = "1";
            $billingAddress['isActive'] = "1";
            $billingAddress['isFromMyorders'] = "0";
            $billingAddress['checkoutBillingAddressData']['title'] = $cartListLabels["BILLINGTITLE"];

            $billing_address_data = [];
            $n = 0;
            $customer_billing_addresses = \App\Models\BillingAddress::where('customer_id', $customer_id)
            ->where('is_deleted', 0)->get();
            if(!empty($customer_billing_addresses))
            {
                foreach ($customer_billing_addresses as $customer_address) {
                    $billing_address_data[$n]['addressId'] =(String) $customer_address->id;
                    $billing_address_data[$n]['fullName'] = $customer_address->fullname;
                    $billing_address_data[$n]['addressLine1'] = $customer_address->address_1;
                    $billing_address_data[$n]['addressLine2'] = $customer_address->address_2;
                    $billing_address_data[$n]['state'] = $customer_address->state;
                    $billing_address_data[$n]['city'] = $customer_address->city;
                    $countries = DB::table('countries')->where('id', $customer_address->country)->first();
                    $billing_address_data[$n]['countryId'] = (String) $customer_address->country;
                    $billing_address_data[$n]['country'] = $countries->name;
                    $billing_address_data[$n]['postCode'] = $customer_address->pincode;
                    $billing_address_data[$n]['mobile'] = $customer_address->phone1;
                    $billing_address_data[$n]['addressType'] = (String) $customer_address->address_type;
                    $billing_address_data[$n]['addressTypeName'] = ($customer_address->address_type == 1) ? $cartListLabels['addressType1'] : $cartListLabels['addressType2'];
                    $billing_address_data[$n]['isSelected'] = (String) $customer_address->is_default;
                    $n++;
                }
                $billingAddress['checkoutBillingAddressData']['address'] = $billing_address_data;
            }
            else
            {
                $billingAddress['checkoutBillingAddressData']['address'] = [];
            }

            //Store Pickup
            $storePickUp = [];
            $j = 0;
            $storePickUp['componentId'] = "StorePickup";
            $storePickUp['sequenceId'] = "1";
            $storePickUp['isActive'] = "1";
            $storePickUp['StorePickupData']['title'] = $cartListLabels["STOREPICKUPFREE"];

            //Get Delivery Date
            $current_date = date("Y-m-d");

            //Get Cart Qty
            $cart_qty = DB::table('cart AS c')
            ->leftJoin('products AS p', 'p.id', '=', 'c.product_id')
            ->select(DB::raw('SUM(c.quantity) as cart_qty'))
            ->where('p.flag_deliverydate', '1')
            ->where('c.cart_master_id', $request->cart_master_id)
            ->get()
            ->pluck('cart_qty');

            $qty = $cart_qty[0];
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

            //Get Delivery Date
            $delevery_date = $this->getDeliveryPickUpTime($reqDays, $current_date,1);
            $storePickUp['StorePickupData']['PickUpTime'] = "";

            $store_locations = \App\Models\StoreLocation::where('language_id', $lang_id)->where('is_deleted',0)
            ->get();
            if(!empty($store_locations))
            {
                $store_location_arr = [];
                foreach ($store_locations as $store_location) {
                    $store_location_arr[$j]['store_id'] = $store_location->id;
                    $store_location_arr[$j]['storeName'] = $store_location->title;
                    $store_location_arr[$j]['addressLine1'] = $store_location->address_1;
                    $store_location_arr[$j]['addressLine2'] = $store_location->address_2;
                    $store_location_arr[$j]['mobile'] = $store_location->phone;
                    $store_location_arr[$j]['storeLatitude'] = ($store_location->latitude) ? $store_location->latitude : "";
                    $store_location_arr[$j]['storeLongitude'] = ($store_location->longitude) ? $store_location->longitude : "";
                    $j++;
                }
                $storePickUp['StorePickupData']['address'] = $store_location_arr;
            }
            else
            {
                $storePickUp['StorePickupData']['address'] = [];
            }

            //Message
            $writeMessage = [];
            $writeMessage['componentId'] = "writeMessage";
            $writeMessage['sequenceId'] = "1";
            $writeMessage['isActive'] = "1";
            $cartMaster = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
            if($cartMaster)
            {
                $writeMessage['writeMessageData']['title'] = ($cartMaster->message != '') ? $cartMaster->message : "";
            }
            else
            {
                $writeMessage['writeMessageData']['title'] = "";
            }

            //Purchase Product
            $purchasedProduct = [];
            $purchasedProduct["componentId"] = "purchasedProduct";
            $purchasedProduct["sequenceId"] = "1";
            $purchasedProduct["isActive"] = "1";

            $cart_arr = [];
            $i = 0;
            $price_sum = [];
            //VAT Calculation
            $totalVat = 0.00;
            $totalSubTotal = 0.00;
            $discount = 0.00;
            $shipping = 0.00;
            $grandTotal = 0.00;
            $get_curr = \App\Models\GlobalLanguage::select('currency_id','tax_type')->where('id', $lang_id)->first();
            $Currency_rate = getCurrencyRates($get_curr->currency_id);
            $curr_symb = getCurrSymBasedOnLangId($lang_id);
            $cart = \App\Models\Cart::where('cart_master_id', $request->cart_master_id)->get();
            if(count($cart) == 0)
            {
                $price_sum[] = 0.00;
                $purchasedProduct["purchasedProductData"]["list"] = [];
            }
            else
            {
                foreach ($cart as $data) {
                    $cart_arr[$i]['id'] = (String) $data->id;
                    $images = \App\Models\Image::where('imageable_id', $data->product_id)->where('image_type',
                    "product")->where('is_default','yes')->whereNull('deleted_at')->first();
                    if(!empty($data->image))
                    {
                        $cart_arr[$i]['image'] = (String)$data->image;
                    }
                    elseif($images)
                    {
                        // $cart_arr[$i]['image'] = $baseUrl.$images->upload_path.$images->name;
                        $cart_arr[$i]['image'] = $baseUrl."/public".$images->upload_path.$images->name;
                    }
                    else
                    {
                        $cart_arr[$i]['image'] = $baseUrl."/public/assets/images/no_image.png";
                    }
                    $cart_arr[$i]['itemId'] = (String) $data->product_id;

                    $default_lang_id = $this->getDefaultLanguage();
                    $product_detail = $this->getProductDetails($data->product_id, $lang_id);
                    $product_title = (!empty($product_detail)) ? $product_detail : $this->getProductDetails($data->product_id, $default_lang_id);
                    $cart_arr[$i]['title'] = ($product_title->title) ? $product_title->title : "";

                    $product_pricing = \App\Models\ProductPricing::where('id', $data->option_id)->whereNull('deleted_at')->first();
                    $attribute_group_ids = explode(',',$product_pricing->attribute_group_ids);
                    $attribute_ids = explode(',',$product_pricing->attribute_ids);

                    // max qty
                    $max_qty = "1000";
                    if(isset($product_pricing->quantity))
                    {
                        $max_qty = $product_pricing->quantity;
                    }

                    $qty = ($data->quantity) ? (String) $data->quantity : '0';
                    $product_price = (!empty($product_pricing->selling_price)) ? $product_pricing->selling_price : $product_pricing->offer_price;
                    $price_sum[$i] = str_replace(',', '', number_format(str_replace(',', '', $product_price * $qty) * $Currency_rate, 2));
                    // $cart_arr[$i]['price'] =  $curr_symb." ".number_format(($product_price * $qty) * $Currency_rate, 2);
                    $cart_arr[$i]['type'] = "2";
                    $cart_arr[$i]['outOfStockFlag'] = "0";
                    $cart_arr[$i]['max_qty'] = (String) $max_qty;
                    $cart_arr[$i]['navigationFlag'] = "1";
                    $cart_arr[$i]['query'] = url('api/v1/getProductList')."?language_id=".$lang_id."&product_id=".$data->product_id;
                    $cart_arr[$i]['qty'] = ($data->quantity) ? (String) $data->quantity : "";
                    $cart_arr[$i]['isCouponApplied'] = "0";
                    $cart_arr[$i]['couponCode'] = "";

                    $title = $this->attributeGroupDtl($attribute_group_ids, $lang_id, $default_lang_id);
                    $value = $this->attributeDtl($attribute_ids, $lang_id, $default_lang_id);

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
                                $totalVat += $unitVat * $Currency_rate;
                                $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
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
                                        $totalVat += $subVat * $Currency_rate;
                                        $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                                    }
                                    else
                                    {
                                        // Excluding
                                        $unitVat = ($data->price * $tex_rate->rate) / 100;
                                        $unitPrice = $data->price + $unitVat;
                                        $subTotal = $unitPrice * $data->quantity;
                                        $subVat = $unitVat * $qty;
                                        $totalVat += $subVat * $Currency_rate;
                                        $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                                        $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                                    }
                                }
                            }
                        }
                        else
                        {
                            $unitPrice = $data->price;
                            $subTotal = $unitPrice * $data->quantity;
                            $unitVat = 0;
                            $totalVat += $unitVat * $Currency_rate;
                            $cart_arr[$i]['unitPrice'] = $curr_symb." ".number_format($unitPrice * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                            $cart_arr[$i]['price'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                            $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                        }
                    }
                    //VAT Calculation End

                    $variant = [];
                    foreach ($value as $key => $v) {
                        $variant[$key]['title'] = ($title[$key]['title']) ? $title[$key]['title'] : "";
                        $variant[$key]['value'] = ($v['value']) ? $v['value'] : "";
                    }
                    $cart_arr[$i]['variant'] = (!empty($variant)) ? $variant : [];

                    $i++;
                }

                $purchasedProduct["purchasedProductData"]["list"] = $cart_arr;
            }

            //Sub Total Calculation
            $grandTotal += $totalSubTotal;

            // //Total VAT Calculation
            // $grandTotal += $totalVat;

            //PriceDetails Data
            $cartMaster = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();

            // $grand_total = array_sum($price_sum) + number_format($cartMaster->discount_amount * $Currency_rate, 2) + number_format($cartMaster->shipping_cost * $Currency_rate, 2);
            // $grand_total = (array_sum($price_sum) + (!empty($cartMaster->shipping_cost) ? number_format($cartMaster->shipping_cost * $Currency_rate, 2) :0 )) - (!empty($cartMaster->discount_amount) ? number_format($cartMaster->discount_amount * $Currency_rate, 2) :0 );
            // $priceDetails['priceDetailsData']['payableAmount'] = $curr_symb." ".number_format($grand_total, 2);

            if($cartMaster)
            {
                if($cartMaster->shipping_cost > 0)
                {
                    $shipping = $cartMaster->shipping_cost * $Currency_rate;
                    $grandTotal += $shipping;
                    $shipping_cost = $curr_symb." ".number_format($shipping, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                else
                {
                    $shipping_cost = $curr_symb." ".number_format($cartMaster->shipping_cost * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }

                if($cartMaster->discount_amount > 0)
                {
                    $discount = $cartMaster->discount_amount * $Currency_rate;
                    //Calculate 5% of dicsount
                    $calc_five_perc_from_disc = $discount * 5 / 100;
                    $totalVat -= $calc_five_perc_from_disc;
                    $disp_in_net_disc = $discount;
                    $grandTotal -= $discount;
                    $discount = $curr_symb." ".number_format($discount, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                else
                {
                    $disp_in_net_disc = $cartMaster->discount_amount * $Currency_rate;
                    $discount = $curr_symb." ".number_format($cartMaster->discount_amount * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }

                //Total VAT Calculation
                $grandTotal += $totalVat;

                $net = $totalSubTotal - $disp_in_net_disc;
                // $priceDetails['VAT'] = $curr_symb." ".number_format($totalVat * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                // $priceDetails['sub_total'] = $curr_symb." ".number_format($totalSubTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
                // $priceDetails['grand_total'] = $curr_symb." ".number_format($grandTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);

                //List Data
                $listData = [
                    array('leftText' => $cartListLabels["SUBTOTAL"],
                    'rightText' => $curr_symb." ".number_format($totalSubTotal, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $cartListLabels["DISCOUNT"], 'rightText' => $discount),
                    array('leftText' => $cartListLabels["NET"], 'rightText' => $curr_symb." ".number_format($net, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $cartListLabels["VAT"], 'rightText' => $curr_symb." ".number_format($totalVat, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $cartListLabels["SHIPPINGCOST"], 'rightText' => $shipping_cost),
                ];

                $priceDetails['priceDetailsData']['listData'] = $listData;
            }
            else
            {
                $priceDetails['priceDetailsData']['listData'] = [];
            }

            $priceDetails = [];
            $priceDetails['componentId']  = "priceDetails";
            $priceDetails['sequenceId'] = "1";
            $priceDetails['isActive'] = "1";
            $priceDetails['priceDetailsData']['title'] = $cartListLabels["PRICEDETAILS"];
            $priceDetails['priceDetailsData']['payableAmountLable'] = $cartListLabels["GRANDTOTAL"];
            $priceDetails['priceDetailsData']['payableAmount'] = $curr_symb." ".number_format($grandTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
            $priceDetails['priceDetailsData']['listData'] = (!empty($listData)) ? $listData : [];

            $result['status'] = $cartListLabels["OK"];
            $result['statusCode'] = "200";
            $result['message'] = $cartListLabels["SUCCESS"];
            $result['component'][] = $address;
            $result['component'][] = $billingAddress;
            $result['component'][] = $storePickUp;
            $result['component'][] = $purchasedProduct;
            $result['component'][] = $priceDetails;
            $result['component'][] = $writeMessage;
            return response()->json($result);

        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function getDeliveryPickUpTime($reqDays, $date, $currDay = 1)
    {
        $next_date = date('Y-m-d', strtotime($date .' +1 day'));

        // Get day of date
        $day = date("N", strtotime($next_date));

        // If Friday (as per client)
        if($day == 5/* OR $day == 7*/)
        {
            return $this->getDeliveryPickUpTime($reqDays, $next_date, $currDay);
        }
        else
        {
            // Check if date is in Holiday..Function should return 0 or 1 based on date exists into holiday or not..If exists then 1 else 0
            if($this->checkDateHoliday($next_date))
            {
                return $this->getDeliveryPickUpTime($reqDays, $next_date, $currDay);
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
                    return $this->getDeliveryPickUpTime($reqDays, $next_date, $currDay+1);
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
    public function addToCart(Request $request){
      try {
          $validator = Validator::make($request->all(), [
              'product_id' => 'required|numeric',
              'option_id' => 'required|numeric',
              'quantity' => 'required|numeric',
              'language_id' => 'required|numeric',
          ]);

          if ($validator->fails()) {
              return response()->json([
              'statusCode' => "300",
              'message' => $validator->errors(),
              ],300);
          }

          $baseUrl = $this->getBaseUrl();
          $lang_id = $request->language_id;
          $language_found = isLanguageExists($lang_id);
          if($language_found == 'false')
          {
              $defaultLang = $this->getDefaultLanguage();
              $codes = ['LANGUAGENOTFOUND'];
              $addToCartAPILabels = getCodesMsg($defaultLang, $codes);

              $result['statusCode'] = "300";
              $result['message'] = $homePageAPILabels['LANGUAGENOTFOUND'];
              return response()->json($result,300);
          }

          $codes = ["PRODUCTADDEDTOCART","PRODUCTNOTADDEDTOCART"];
          $cartLabels = getCodesMsg($lang_id, $codes);
          $token = $request->header('Authorization');
          if(!empty($token))
          $customer_id = Auth::guard('api')->user()->token()->user_id;
          else
          $customer_id=0;
          $cart_master_id = $request->cart_master_id;
          $message        = $request->message;
          $option_id      = $request->option_id;
          $product_id     = $request->product_id;
          $qty            = $request->quantity;
          if($request->gift_wrap)
          $gift_wrap      = $request->gift_wrap;
          else
          $gift_wrap      = 0;
          $gift_message   = $request->gift_message;
          if($request->lady_operator)
          $lady_operator  = $request->lady_operator;
          else
          $lady_operator      = 0;
          $staff_msg   = $request->staff_msg;
          $photobook_caption   = $request->photobook_caption;

          $saveflag=0;
          $product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price')
                                                                ->where('id', $option_id)
                                                                ->where('product_id', $product_id)
                                                                ->whereNull('deleted_at')
                                                                ->first();
          $GroupPrice=0;
          if($customer_id!=0){
            $customeData=Customer::where("id",$customer_id)->first();
            if($customeData->cust_group_id!=0){
              $custGrpPrice=CustGroupPrice::where("product_id",$product_id)->where('customer_group_id', $customeData->cust_group_id)->first();
              if(!empty($custGrpPrice))
              $GroupPrice=$custGrpPrice->price;
            }
          }
          if($GroupPrice==0){
          if($product_details['offer_price'])
            $price=  $product_details['offer_price'];
            else
            $price=  $product_details['selling_price'];
          }
          else{
            $price=  $GroupPrice;
          }
          if(empty($cart_master_id)){
            $cartMaster=new CartMaster;
            $cartMaster->user_id=$customer_id;
            $cartMaster->message=$message;
            $cartMaster->save();
          $cart_master_id=$cartMaster->id;
        }
        // $cartCount=0;
        // if(!empty($cart_master_id)){
        // //  $cart_master_id=$_GET['cart_master_id'];
        // $totalCartItems = \App\Models\Cart::select('id')->where('cart_master_id',$cart_master_id)->get();
        // $cartCount = count($totalCartItems);
        // }
          $cart=Cart::where("cart_master_id",$cart_master_id)->where("option_id",$option_id)->first();
          if($request->attachment){
            $data=json_decode($request->attachment);
            if(!empty($data)){
              foreach($data as $key=>$value){
                $tempImage=TempCartImages::find($value->id);
                  $cartQty=(int)$qty;
                  $cartDetails=new Cart();
                  $cartDetails->user_id=$customer_id;
                  $cartDetails->product_id=$product_id;
                  $cartDetails->cart_master_id=$cart_master_id;
                  $cartDetails->option_id=$option_id;
                  $cartDetails->message=$message;
                  $cartDetails->quantity=$cartQty;
                  $cartDetails->price=$price;
                  $cartDetails->created_at=date('Y-m-d H:i:s');
                  $cartDetails->gift_wrap=$gift_wrap;
                  $cartDetails->gift_message=$gift_message;
                  $cartDetails->lady_operator=$lady_operator;
                  $cartDetails->image=$tempImage->temp_cart_image;
                  $cartDetails->message=$staff_msg;
                  $cartDetails->photobook_caption=$photobook_caption;
                  $cartDetails->save();
                  $saveflag=1;
                  $TempCartImages = TempCartImages::where('id', $value->id)->delete();
                }
              }

            }
            elseif($request->image){
              $cartDetails=new Cart;
              $cartDetails->user_id=$customer_id;
              $cartDetails->product_id=$product_id;
              $cartDetails->quantity=$qty;
              $cartDetails->price=$price;
              $cartDetails->cart_master_id=$cart_master_id;
              $cartDetails->option_id=$option_id;
              $cartDetails->message=$message;
              $cartDetails->gift_wrap=$gift_wrap;
              $cartDetails->gift_message=$gift_message;
              $cartDetails->lady_operator=$lady_operator;
              $cartDetails->created_at=date('Y-m-d H:i:s');
              $cartDetails->message=$staff_msg;
              $cartDetails->photobook_caption=$photobook_caption;
            //  $cartDetails->image=$request->image;
              $cartDetails->save();
              $filename = 'socialmedia_'.rand() . '_' . time() . '_' .$cartDetails->id. '.jpg';
              copy($request->image,public_path() . '/assets/images/carts/'.$filename);
              $cartData=Cart::find($cartDetails->id);
              $cartData->image=$baseUrl . '/public/assets/images/carts/'.$filename;
              $cartData->update();
              $saveflag=1;
            }
            else{
              if($cart){
                $cartQty=$cart->quantity+(int)$qty;
                $cartDetails=Cart::find($cart->id);
                $cartDetails->quantity=$cartQty;
                $cartDetails->price=$price;
                $cartDetails->updated_at=date('Y-m-d H:i:s');
                $cartDetails->gift_wrap=$gift_wrap;
                $cartDetails->gift_message=$gift_message;
                $cartDetails->lady_operator=$lady_operator;
                $cartDetails->message=$staff_msg;
                $cartDetails->photobook_caption=$photobook_caption;
                $cartDetails->save();
                $saveflag=1;
              }
              else{
                $cartDetails=new Cart;
                $cartDetails->user_id=$customer_id;
                $cartDetails->product_id=$product_id;
                $cartDetails->quantity=$qty;
                $cartDetails->price=$price;
                $cartDetails->cart_master_id=$cart_master_id;
                $cartDetails->option_id=$option_id;
                $cartDetails->message=$message;
                $cartDetails->gift_wrap=$gift_wrap;
                $cartDetails->gift_message=$gift_message;
                $cartDetails->lady_operator=$lady_operator;
                $cartDetails->created_at=date('Y-m-d H:i:s');
                $cartDetails->message=$staff_msg;
                $cartDetails->photobook_caption=$photobook_caption;
                $cartDetails->save();
                $saveflag=1;
              }
            }

        $cartCount=0;
        if(!empty($cart_master_id)){
        //  $cart_master_id=$_GET['cart_master_id'];
        $totalCartItems = \App\Models\Cart::select('id')->where('cart_master_id',$cart_master_id)->get();
        $cartCount = count($totalCartItems);
        }

          if($saveflag==1){
            $result['statusCode']     = '200';
            $result['message']        = $cartLabels['PRODUCTADDEDTOCART'];
            $result['cartCount']      = "".$cartCount."";
            $result['cart_master_id'] = "".$cart_master_id."";
            return response()->json($result);
          }
          else{
            return response()->json(['status' => "OK",'statusCode' => 500, 'message' => $cartLabels['PRODUCTNOTADDEDTOCART']]);
          }

      } catch (\Exception $e) {
          return handleServerError($lang_id);
      }
    }

    public function removeCartItem(Request $request)
    {
        try {
            //Localization
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","CARTIDREQ","CARTIDNUM",
            "OK","SUCCESS","SUCCESSSTATUS","CARTDATADLTSUCC","CARTDATANOTFOUND"];
            $removeCartLabels = getCodesMsg($request->language_id, $codes);

            $msg = [
                'cart_id.required' => $removeCartLabels["CARTIDREQ"],
                'cart_id.numeric' => $removeCartLabels["CARTIDNUM"],
                'cart_master_id.required' => $removeCartLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $removeCartLabels["MASTERCARTIDNUM"]
            ];

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'cart_id' => 'required|numeric',
                'cart_master_id' => 'required|numeric'
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }

            $Cart = \App\Models\Cart::where('id', $request->cart_id)->where('cart_master_id', $request->cart_master_id)->first();
            if($Cart)
            {
                $Cart->delete();
                $result['statusCode'] = "200";
                $result['message'] = $removeCartLabels['CARTDATADLTSUCC'];
                return response()->json($result);
            }
            else
            {
                $result['statusCode'] = "300";
                $result['message'] = $removeCartLabels["CARTDATANOTFOUND"];
                return response()->json($result, 300);
            }
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function updateItemQTY(Request $request)
    {
        try {
            //Localization
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","CARTIDREQ","CARTIDNUM",
            "OK","SUCCESS","SUCCESSSTATUS","CARTDATADLTSUCC","CARTDATANOTFOUND","QUANTITYREQ",
            "QUANTITYNUM","QUANTITYUPDATED"];
            $updateQTYLabels = getCodesMsg($request->language_id, $codes);

            $msg = [
                'cart_id.required' => $updateQTYLabels["CARTIDREQ"],
                'cart_id.numeric' => $updateQTYLabels["CARTIDNUM"],
                'cart_master_id.required' => $updateQTYLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $updateQTYLabels["MASTERCARTIDNUM"],
                'quantity.required' => $updateQTYLabels["QUANTITYREQ"],
                'quantity.numeric' => $updateQTYLabels["QUANTITYNUM"]
            ];

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'cart_id' => 'required|numeric',
                'cart_master_id' => 'required|numeric',
                'quantity' => 'required|numeric',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }

            $Cart = \App\Models\Cart::where('id', $request->cart_id)->where('cart_master_id', $request->cart_master_id)->first();
            if($Cart)
            {
                $Cart->quantity = $request->quantity;
                $Cart->save();
                $result['statusCode'] = "200";
                $result['message'] = $updateQTYLabels['QUANTITYUPDATED'];
                return response()->json($result);
            }
            else
            {
                $result['statusCode'] = "300";
                $result['message'] = $updateQTYLabels["CARTDATANOTFOUND"];
                return response()->json($result, 300);
            }
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function removePromoCode(Request $request)
    {
        try {
            //Localization
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","CARTDATANOTFOUND","PROMOCODEREMOVE"];
            $updateQTYLabels = getCodesMsg($request->language_id, $codes);

            $msg = [
                'cart_master_id.required' => $updateQTYLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $updateQTYLabels["MASTERCARTIDNUM"]
            ];

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'cart_master_id' => 'required|numeric'
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }

            $CartMaster = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
            if($CartMaster)
            {
                $CartMaster->discount_amount = '0.00';
                $CartMaster->promo_code = null;
                $CartMaster->save();
                $result['statusCode'] = "200";
                $result['message'] = $updateQTYLabels['PROMOCODEREMOVE'];
                return response()->json($result);
            }
            else
            {
                $result['statusCode'] = "300";
                $result['message'] = $updateQTYLabels["CARTDATANOTFOUND"];
                return response()->json($result, 300);
            }
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function getPaymentMethod(Request $request)
    {
        try {
            //Localization
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","CARTDATANOTFOUND","PROMOCODEREMOVE",
            "OK","SUCCESS","PAYONLINECREDIT","PAYONLINEDEBIT"];
            $paymentMethodLabels = getCodesMsg($request->language_id, $codes);

            $msg = [
                'cart_master_id.required' => $paymentMethodLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $paymentMethodLabels["MASTERCARTIDNUM"]
            ];

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'cart_master_id' => 'required|numeric'
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }

            $payment_method = [];
            $payment_method['componentId'] = 'payOnline';
            $payment_method['sequenceId'] = '1';
            $payment_method['isActive'] = '1';

            $credit_card_option = [];
            $credit_card_option['componentId'] = 'payOnline';
            $credit_card_option['sequenceId'] = '1';
            $credit_card_option['isActive'] = '1';

            $credit_card_sub_option = [];
            $credit_card_sub_option["Nevigation"] = "1";
            $credit_card_sub_option["isShowIcon"] = "1";
            $credit_card_sub_option['image'] = url('/public/assets/frontend/img').'/'."jcb_visa_mc.png";
            $credit_card_sub_option['isShowTitle'] = "0";
            $credit_card_sub_option['title'] = $paymentMethodLabels['PAYONLINECREDIT'];
            $credit_card_sub_option['paymentMethodId'] = "1";
            $credit_card_option['payOnlineData'] = $credit_card_sub_option;

            $debit_card_option = [];
            $debit_card_option['componentId'] = 'payOnline';
            $debit_card_option['sequenceId'] = '1';
            $debit_card_option['isActive'] = '1';

            $debit_card_sub_option = [];
            $debit_card_sub_option["Nevigation"] = "2";
            $debit_card_sub_option["isShowIcon"] = "1";
            $debit_card_sub_option['image'] = url('/public/assets/frontend/img').'/'."benefit.jpg";
            $debit_card_sub_option['isShowTitle'] = "0";
            $debit_card_sub_option['title'] = $paymentMethodLabels['PAYONLINEDEBIT'];
            $debit_card_sub_option['paymentMethodId'] = "2";
            $debit_card_option['payOnlineData'] = $debit_card_sub_option;

            $result['status'] = $paymentMethodLabels['OK'];
            $result['statusCode'] = "200";
            $result['message'] = $paymentMethodLabels['SUCCESS'];
            $result['component'][] = $credit_card_option;
            $result['component'][] = $debit_card_option;
            return response()->json($result);
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function updateShippingCheckoutType(Request $request)
    {
        try {
            //Localization
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","CARTDATANOTFOUND",
            "ADDRESSIDREQ","STORELOCATIONIDREQ","STORELOCATIONIDNUM","ADDRESSIDNUM"
            ,"SHIPPINGMETHODUPDATESUCC","BILLINGADDIDREQ","BILLINGADDIDNUM","SHIPADDRFLAGREQ",
            "SHIPADDRFLAGNUM","SHIPADDRFLAGINVALID"];
            $updateShippingMethodTypeLabels = getCodesMsg($request->language_id, $codes);

            $msg = [
                'cart_master_id.required' => $updateShippingMethodTypeLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $updateShippingMethodTypeLabels["MASTERCARTIDNUM"],
                'address_id.required' => $updateShippingMethodTypeLabels["ADDRESSIDREQ"],
                'address_id.numeric' => $updateShippingMethodTypeLabels["ADDRESSIDNUM"],
                'store_location_id.required' => $updateShippingMethodTypeLabels["STORELOCATIONIDREQ"],
                'store_location_id.numeric' => $updateShippingMethodTypeLabels["STORELOCATIONIDNUM"],
                'billing_address_id.required' => $updateShippingMethodTypeLabels["BILLINGADDIDREQ"],
                'billing_address_id.numeric' => $updateShippingMethodTypeLabels["BILLINGADDIDNUM"],
                'same_as_ship_addr_flag.required' => $updateShippingMethodTypeLabels["SHIPADDRFLAGREQ"],
                'same_as_ship_addr_flag.numeric' => $updateShippingMethodTypeLabels["SHIPADDRFLAGNUM"],
                'same_as_ship_addr_flag.in' => $updateShippingMethodTypeLabels["SHIPADDRFLAGINVALID"],
            ];

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'cart_master_id' => 'required|numeric',
                'address_id' => 'required|numeric',
                'store_location_id' => 'required|numeric',
                'billing_address_id' => 'required|numeric',
                'same_as_ship_addr_flag' => 'required|numeric|in:1,0'
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }

            $CartMaster = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
            if($CartMaster)
            {
                $CartMaster->address_id = $request->address_id;
                $CartMaster->store_location_id = $request->store_location_id;
                if(isset($request->address_id) && $request->address_id != '0')
                {
                    $checkout_type = '1';
                }
                if(isset($request->store_location_id) && $request->store_location_id != '0')
                {
                    $checkout_type = '2';
                }
                $CartMaster->checkout_type = $checkout_type;
                $CartMaster->billing_address_id = $request->billing_address_id;
                $CartMaster->same_as_ship_addr = $request->same_as_ship_addr_flag;
                $CartMaster->save();

                check_tier_price($request->cart_master_id);
                update_shippingcost($request->cart_master_id);

                $result['statusCode'] = "200";
                $result['message'] = $updateShippingMethodTypeLabels['SHIPPINGMETHODUPDATESUCC'];
                return response()->json($result);
            }
            else
            {
                $result['statusCode'] = "300";
                $result['message'] = $updateShippingMethodTypeLabels["CARTDATANOTFOUND"];
                return response()->json($result, 300);
            }

        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }

    }

    public function updatePaymentMethod(Request $request)
    {
        try {
            //Localization
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","CARTDATANOTFOUND","INVALIDMETHOD",
            "PAYMENTMETHODSLCTSUCC"];
            $updatePaymentMethodTypeLabels = getCodesMsg($request->language_id, $codes);

            $msg = [
                'cart_master_id.required' => $updatePaymentMethodTypeLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $updatePaymentMethodTypeLabels["MASTERCARTIDNUM"],
                'method.in' => $updatePaymentMethodTypeLabels["INVALIDMETHOD"],
            ];

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'cart_master_id' => 'required|numeric',
                'method' => 'required|in:1,2',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }

            $CartMaster = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
            if($CartMaster)
            {
                $CartMaster->payment_method = $request->method;
                $CartMaster->save();
                $result['statusCode'] = "200";
                $result['message'] = $updatePaymentMethodTypeLabels['PAYMENTMETHODSLCTSUCC'];
                return response()->json($result);
            }
            else
            {
                $result['statusCode'] = "300";
                $result['message'] = $updatePaymentMethodTypeLabels["CARTDATANOTFOUND"];
                return response()->json($result, 300);
            }

        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }
    public function addRecommendedToCart(Request $request){
      try {
          $validator = Validator::make($request->all(), [
              'product_id' => 'required|numeric',
              'language_id' => 'required|numeric',
          ]);

          if ($validator->fails()) {
              return response()->json([
              'statusCode' => "300",
              'message' => $validator->errors(),
              ],300);
          }
          $lang_id = $request->language_id;
          $language_found = isLanguageExists($lang_id);
          if($language_found == 'false')
          {
              $defaultLang = $this->getDefaultLanguage();
              $codes = ['LANGUAGENOTFOUND'];
              $addToCartAPILabels = getCodesMsg($defaultLang, $codes);

              $result['statusCode'] = "300";
              $result['message'] = $homePageAPILabels['LANGUAGENOTFOUND'];
              return response()->json($result,300);
          }

          $codes = ["PRODUCTADDEDTOCART","PRODUCTNOTADDEDTOCART"];
          $cartLabels = getCodesMsg($lang_id, $codes);
          $token = $request->header('Authorization');
          if(!empty($token))
          $customer_id = Auth::guard('api')->user()->token()->user_id;
          else
          $customer_id=0;
          $cart_master_id = $request->cart_master_id;
          $product_id     = $request->product_id;
          $qty            = 1;
          $cartCount=0;
          if(!empty($cart_master_id)){
        //  $cart_master_id=$_GET['cart_master_id'];
          $totalCartItems = \App\Models\Cart::select('id')->where('cart_master_id',$cart_master_id)->get();
          $cartCount = count($totalCartItems);
          }

          $saveflag=0;
          $recommended_products = RecommendedProduct::select('products.id','product_pricing.id as option_id','product_pricing.selling_price', 'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date')
          ->join('products', 'products.id', '=', 'recommended_products.recommended_id')
          ->join('product_pricing', 'product_pricing.product_id', '=', 'products.id')
          ->where('product_pricing.is_default', 1)
          ->where('recommended_products.product_id', $product_id)
          ->whereNull('products.deleted_at')
          ->get();
          foreach($recommended_products as $pro){
            $product_id=$pro->id;
            $option_id=$pro->option_id;
            $GroupPrice=0;
            if($customer_id!=0){
                $customeData=Customer::where("id",$customer_id)->first();
                if($customeData->cust_group_id!=0){
                    $custGrpPrice=CustGroupPrice::where("product_id",$product_id)->where('customer_group_id', $customeData->cust_group_id)->first();
                    if(!empty($custGrpPrice))
                    $GroupPrice=$custGrpPrice->price;
                }
            }
            if($GroupPrice==0){
            if(!empty($pro->offer_price) && (date("Y-m-d",strtotime($pro->offer_start_date)) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($pro->offer_end_date))))
            $price=  $pro->offer_price;
            else
            $price=  $pro->selling_price;
            }
            else{
              $price=$GroupPrice;
            }
            if(!empty($cart_master_id))
            {
              $cart=Cart::where("cart_master_id",$cart_master_id)->where("option_id",$option_id)->first();
              if($cart){
                  $cartQty=$cart->quantity+(int)$qty;
                  $cartDetails=Cart::find($cart->id);
                  $cartDetails->quantity=$cartQty;
                  $cartDetails->price=$price;
                  $cartDetails->updated_at=date('Y-m-d H:i:s');
                  $cartDetails->save();
                  $saveflag=1;
              }
              else{
                $cartDetails=new Cart;
                $cartDetails->user_id=$customer_id;
                $cartDetails->product_id=$product_id;
                $cartDetails->quantity=$qty;
                $cartDetails->price=$price;
                $cartDetails->cart_master_id=$cart_master_id;
                $cartDetails->option_id=$option_id;
                $cartDetails->created_at=date('Y-m-d H:i:s');
                $cartDetails->save();
                $saveflag=1;
              }
            }
            else{
              $cartMaster=new CartMaster;
              $cartMaster->user_id=$customer_id;
              $cartMaster->save();
              $cart_master_id=$cartMaster->id;
              $cart=Cart::where("cart_master_id",$cart_master_id)->where("option_id",$option_id)->first();
              if($cart){
                  $cartQty=$cart->quantity+(int)$qty;
                  $cartDetails=Cart::find($cart->id);
                  $cartDetails->quantity=$cartQty;
                  $cartDetails->price=$price;
                  $cartDetails->updated_at=date('Y-m-d H:i:s');
                  $cartDetails->save();
                  $saveflag=1;
              }
              else{
                    $cartDetails=new Cart;
                    $cartDetails->user_id=$customer_id;
                    $cartDetails->product_id=$product_id;
                    $cartDetails->quantity=$qty;
                    $cartDetails->price=$price;
                    $cartDetails->cart_master_id=$cart_master_id;
                    $cartDetails->option_id=$option_id;
                    $cartDetails->created_at=date('Y-m-d H:i:s');
                    $cartDetails->save();
                    $saveflag=1;
              }
            }
          }
          if($saveflag==1){
            $result['statusCode']     = '200';
            $result['message']        = $cartLabels['PRODUCTADDEDTOCART'];
            $result['cartCount']      = "".$cartCount."";
            $result['cart_master_id'] = "".$cart_master_id."";
            return response()->json($result);
          }
          else{
            return response()->json(['status' => "OK",'statusCode' => 500, 'message' => $cartLabels['PRODUCTNOTADDEDTOCART']]);
          }

      } catch (\Exception $e) { dd($e);
          return handleServerError($lang_id);
      }
    }

    public function applyPromoCode(Request $request)
    {
        // try {
            //Localization
            $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","PROMOCODEREQ","OK"];
            $applyPromoCodeLabels = getCodesMsg($request->language_id, $codes);

            $msg = [
                'cart_master_id.required' => $applyPromoCodeLabels["MASTERCARTIDREQ"],
                'cart_master_id.numeric' => $applyPromoCodeLabels["MASTERCARTIDNUM"],
                'code.required' => $applyPromoCodeLabels["PROMOCODEREQ"],
            ];

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
                'cart_master_id' => 'required|numeric',
                'code' => 'required',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }
            $lang_id = $request->language_id;
            $applied_coupon = apply_promotion($request->code, $request->cart_master_id, $lang_id);
            if($applied_coupon['status'] == 'error')
            {
                $result['status'] = $applyPromoCodeLabels['OK'];
                $result['statusCode'] = "300";
                $result['message'] = $applied_coupon['message'];
                return response()->json($result, 300);
            }

            if($applied_coupon['status'] == 'Success')
            {
                $result['status'] = $applyPromoCodeLabels['OK'];
                $result['statusCode'] = "200";
                $result['message'] = $applied_coupon['message'];
                return response()->json($result);
            }

        // } catch (\Exception $e) {
        //     return handleServerError($lang_id);
        // }
    }
    public function uploadImage(Request $request){
      try {
          $validator = Validator::make($request->all(), [
              'attachement' => 'mimes:jpeg,jpg,png,gif',
          ]);

          if ($validator->fails()) {
              return response()->json([
              'statusCode' => "300",
              'message' => $validator->errors(),
              ],300);
          }
          $baseUrl = $this->getBaseUrl();
          $saveflag=0;
          if($request->file('attachement')){
            $photo = $request->file('attachement');
            $ext = $photo->extension();
            $filename = rand() . '_' . time().'.'. $ext;
            $photo->move(public_path() . '/assets/images/carts/', $filename);
            $tempData=new TempCartImages();
            $tempData->temp_cart_image=$baseUrl . '/public/assets/images/carts/'.$filename;
            $tempData->save();
            $id=$tempData->id;
            $saveflag=1;
          }
          if($request->image_url){
            $filename = 'socialmedia_'.rand() . '_' . time().'.jpg';
            copy($request->image_url,public_path() . '/assets/images/carts/'.$filename);
            $tempData=new TempCartImages();
            $tempData->temp_cart_image=$baseUrl . '/public/assets/images/carts/'.$filename;
            $tempData->save();
            $id=$tempData->id;
            $saveflag=1;
          }
          if($saveflag==1){
            $result['statusCode']     = '200';
            $result['message']        = 'Image uploaded succesfully';
            $result['id'] = "".$id."";
            return response()->json($result);
          }
          else{
            return response()->json(['status' => "OK",'statusCode' => 500, 'message' => 'Unable to upload image.']);
          }

      } catch (\Exception $e) {
          return handleServerError(1);
      }
    }
    // design tool add to cart
    public function designToolAddToCart(Request $request){
      try {
          $validator = Validator::make($request->all(), [
              'product_id' => 'required|numeric',
              'option_id' => 'required|numeric',
              'quantity' => 'required|numeric',
              'language_id' => 'required|numeric'
          ]);

          if ($validator->fails()) {
              return response()->json([
              'statusCode' => "300",
              'message' => $validator->errors(),
              ],300);
          }

          $baseUrl = $this->getBaseUrl();
          $lang_id = $request->language_id;
          $language_found = isLanguageExists($lang_id);
          if($language_found == 'false')
          {
              $defaultLang = $this->getDefaultLanguage();
              $codes = ['LANGUAGENOTFOUND'];
              $addToCartAPILabels = getCodesMsg($defaultLang, $codes);

              $result['statusCode'] = "300";
              $result['message'] = $homePageAPILabels['LANGUAGENOTFOUND'];
              return response()->json($result,300);
          }

          $codes = ["PRODUCTADDEDTOCART","PRODUCTNOTADDEDTOCART"];
          $cartLabels = getCodesMsg($lang_id, $codes);
          $token = $request->header('Authorization');
          if(!empty($token))
          $customer_id = Auth::guard('api')->user()->token()->user_id;
          else
          $customer_id=0;
          $cart_master_id = $request->cart_master_id;
          $message        = $request->message;
          $option_id      = $request->option_id;
          $product_id     = $request->product_id;
          $qty            = $request->quantity;
          if($request->gift_wrap)
          $gift_wrap      = $request->gift_wrap;
          else
          $gift_wrap      = 0;
          $gift_message   = $request->gift_message;
          if($request->lady_operator)
          $lady_operator  = $request->lady_operator;
          else
          $lady_operator      = 0;
          $staff_msg   = $request->staff_msg;
          $photobook_caption   = $request->photobook_caption;
          $image   = $request->image;
          $otherImages   = $request->other_images;
          $printFiles   = $request->print_files;
          $screenshots   = $request->screenshots;

          $cartCount=0;
          if(!empty($cart_master_id)){
        //  $cart_master_id=$_GET['cart_master_id'];
          $totalCartItems = \App\Models\Cart::select('id')->where('cart_master_id',$cart_master_id)->get();
          $cartCount = count($totalCartItems);
          }

          $saveflag=0;
          $product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price')
                                                                ->where('id', $option_id)
                                                                ->where('product_id', $product_id)
                                                                ->whereNull('deleted_at')
                                                                ->first();
          $GroupPrice=0;
          if($customer_id!=0){
            $customeData=Customer::where("id",$customer_id)->first();
            if($customeData->cust_group_id!=0){
              $custGrpPrice=CustGroupPrice::where("product_id",$product_id)->where('customer_group_id', $customeData->cust_group_id)->first();
              if(!empty($custGrpPrice))
              $GroupPrice=$custGrpPrice->price;
            }
          }
          if($GroupPrice==0){
          if($product_details['offer_price'])
            $price=  $product_details['offer_price'];
            else
            $price=  $product_details['selling_price'];
          }
          else{
            $price=  $GroupPrice;
          }
          if(empty($cart_master_id)){
            $cartMaster=new CartMaster;
            $cartMaster->user_id=$customer_id;
            $cartMaster->message=$message;
            $cartMaster->save();
          $cart_master_id=$cartMaster->id;
        }
          $cart=Cart::where("cart_master_id",$cart_master_id)->where("option_id",$option_id)->first();
              if($cart){
                $cartQty=$cart->quantity+(int)$qty;
                $cartDetails=Cart::find($cart->id);
                $cartDetails->quantity=$cartQty;
                $cartDetails->price=$price;
                $cartDetails->updated_at=date('Y-m-d H:i:s');
                $cartDetails->gift_wrap=$gift_wrap;
                $cartDetails->gift_message=$gift_message;
                $cartDetails->lady_operator=$lady_operator;
                $cartDetails->message=$staff_msg;
                $cartDetails->photobook_caption=$photobook_caption;
                $cartDetails->image=$image;
                $cartDetails->other_images=$otherImages;
                $cartDetails->print_files=$printFiles;
                $cartDetails->screenshots_files=$screenshots;
                $cartDetails->save();
                $saveflag=1;
              }
              else{
                $cartDetails=new Cart;
                $cartDetails->user_id=$customer_id;
                $cartDetails->product_id=$product_id;
                $cartDetails->quantity=$qty;
                $cartDetails->price=$price;
                $cartDetails->cart_master_id=$cart_master_id;
                $cartDetails->option_id=$option_id;
                $cartDetails->message=$message;
                $cartDetails->gift_wrap=$gift_wrap;
                $cartDetails->gift_message=$gift_message;
                $cartDetails->lady_operator=$lady_operator;
                $cartDetails->created_at=date('Y-m-d H:i:s');
                $cartDetails->message=$staff_msg;
                $cartDetails->photobook_caption=$photobook_caption;
                $cartDetails->image=$image;
                $cartDetails->other_images=$otherImages;
                $cartDetails->print_files=$printFiles;
                $cartDetails->screenshots_files=$screenshots;
                $cartDetails->save();
                $saveflag=1;
              }

          if($saveflag==1){
            $result['statusCode']     = '200';
            $result['message']        = $cartLabels['PRODUCTADDEDTOCART'];
            $result['cartCount']      = "".$cartCount."";
            $result['cart_master_id'] = "".$cart_master_id."";
            return response()->json($result);
          }
          else{
            return response()->json(['status' => "OK",'statusCode' => 500, 'message' => $cartLabels['PRODUCTNOTADDEDTOCART']]);
          }

      } catch (\Exception $e) {
          return handleServerError($lang_id);
      }
    }
}
