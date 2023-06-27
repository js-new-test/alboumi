<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Facades\Session;
use Agent;
use Auth;
use DB;
use App\Models\Cart;
use App\Models\CartMaster;
use App\Models\CustGroupPrice;
use App\Models\Customer;

class CartController extends Controller
{
    use ReuseFunctionTrait;

    public function getShpCartData($cartMasterId='')
    {
        if($cartMasterId)
        Session::put('cart_master_id',base64_decode(strtr($cartMasterId, '-_,' ,'+/=')));
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);
        $decimalNumber=Session::get('decimal_number');
        $decimalSeparator=Session::get('decimal_separator');
        $thousandSeparator=Session::get('thousand_separator');

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $lang_id = Session::get('language_id');
        $cart_master_id = Session::get('cart_master_id');

        //Update shipping cost
        if(isset($cart_master_id) && !empty($cart_master_id))
        {
            check_tier_price($cart_master_id);
            update_shippingcost($cart_master_id);

            // update promo code
            $cartMaster_promo = \App\Models\CartMaster::where('id', $cart_master_id)->first();
            if($cartMaster_promo)
            {
                if(!empty($cartMaster_promo->promo_code))
                {
                    $code = $cartMaster_promo->promo_code;
                    $promoResponse =  apply_promotion($code, $cart_master_id, $lang_id);

                    // remove code and discoubt if error
                    if(!empty($promoResponse['status']) && $promoResponse['status'] == "error")
                    {
                        $cart_master_remove = \App\Models\CartMaster::where('id', $cart_master_id)->first();
                        if($cart_master_remove)
                        {
                            $cart_master_remove->promo_code = NULL;
                            $cart_master_remove->discount_amount = 0.000;
                            $cart_master_remove->save();
                        }
                    }
                }
            }
        }

        $language_found = isLanguageExists($lang_id);
        if($language_found == 'false')
        {
            $defaultLang = $this->getDefaultLanguage();
            $lang_id = $defaultLang;
        }

        $baseUrl = $this->getBaseUrl();
        $codes = ['SUBTOTAL','SHIPPINGCOST','DISCOUNT','PRICEDETAILS','GRANDTOTAL','APPNAME','MYCART',
        'SHOPPINGCARTKEYWORD','SHOPPINGCARTDESC','HOME','ITEMS','PRODUCTNAME','DELIVERYDETAILS'
        ,'QUANTITY','SUBTOTAL','SHOPPINGCARTEMPTY','CONTINUESHOPPING','APPLY','ENTER_COUPON_CODE','PLACEORDER'
        ,'REMOVE','AREYOUSURE','CONFIRMATION','NO','YES','UNITPRICE','VAT','ENTERPROMOCODE','APPLYINGPROMOCODE'
        ,'MESSAGE','REMOVEPROMOCODE','NET'];
        $cartLabels = getCodesMsg($lang_id, $codes);
        $pageName = $cartLabels["MYCART"];
        $projectName = $cartLabels["APPNAME"];

        //Shopping Cart
        if(isset($cart_master_id) && $cart_master_id != "")
        {
            $cart_arr = [];
            $i = 0;
            $price_sum = [];
            $get_curr = \App\Models\GlobalLanguage::select('currency_id', 'tax_type')->where('id', $lang_id)->first();
            $Currency_rate = getCurrencyRates($get_curr->currency_id);
            $curr_symb = getCurrSymBasedOnLangId($lang_id);
            $cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();

            //VAT Calculation
            $totalVat = 0.00;
            $totalSubTotal = 0.00;
            $discount = 0.00;
            $shipping = 0.00;
            $grandTotal = 0.00;

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

                $qty = ($data->quantity) ? (String) $data->quantity : '0';
                $product_price = (!empty($product_pricing->selling_price)) ? $product_pricing->selling_price : $product_pricing->offer_price;
                $price_sum[$i] = str_replace(',', '', number_format(str_replace(',', '', $product_price * $qty) * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                $cart_arr[$i]['price'] =  $curr_symb." ".number_format(($product_price * $qty) * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                $cart_arr[$i]['type'] = "2";
                $cart_arr[$i]['outOfStockFlag'] = "0";
                $cart_arr[$i]['navigationFlag'] = "1";
                // $cart_arr[$i]['query'] = url('api/v1/getProductList')."?language_id=".$lang_id."&product_id=".$data->product_id;
                $cart_arr[$i]['qty'] = ($data->quantity) ? (String) $data->quantity : "";
                $cart_arr[$i]['isCouponApplied'] = "0";
                $cart_arr[$i]['couponCode'] = "";

                $title = $this->attributeGroupDtl($attribute_group_ids, $lang_id, $default_lang_id);
                $value = $this->attributeDtl($attribute_ids, $lang_id, $default_lang_id);

                $variant = [];
                foreach ($value as $key => $v) {
                    $variant[$key]['title'] = ($title[$key]['title']) ? $title[$key]['title'] : "";
                    $variant[$key]['value'] = ($v['value']) ? $v['value'] : "";
                }
                $cart_arr[$i]['variant'] = (!empty($variant)) ? $variant : [];

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
                            $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
                                    $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
                                    $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
                        $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                        $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                    }
                }
                //VAT Calculation End
                $cart_arr[$i]['slug'] = $product->product_slug;
                $i++;
            }

            //Sub Total Calculation
            $grandTotal += $totalSubTotal;

            // //Total VAT Calculation
            // $grandTotal += $totalVat;

            $priceDetails = [];
            $cartMaster = \App\Models\CartMaster::where('id', $cart_master_id)->first();
            if($cartMaster)
            {
                if($cartMaster->shipping_cost > 0)
                {
                    $shipping = $cartMaster->shipping_cost * $Currency_rate;
                    $grandTotal += $shipping;

                    $priceDetails['shipping_cost'] = $curr_symb." ".number_format($shipping, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                else
                {
                    $priceDetails['shipping_cost'] = $curr_symb." ".number_format($cartMaster->shipping_cost * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }

                if($cartMaster->discount_amount > 0)
                {
                    $discount = $cartMaster->discount_amount * $Currency_rate;
                    //Calculate 5% of dicsount
                    $calc_five_perc_from_disc = $discount * 5 / 100;
                    $totalVat -= $calc_five_perc_from_disc;
                    $grandTotal -= $discount;
                    $priceDetails['discount'] = $curr_symb." ".number_format($discount, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                else
                {
                    $priceDetails['discount'] = $curr_symb." ".number_format($cartMaster->discount_amount * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }

                //Total VAT Calculation
                $grandTotal += $totalVat;

                $priceDetails['net'] = $curr_symb." ".number_format($totalSubTotal - $discount, $decimalNumber, $decimalSeparator, $thousandSeparator);

                $priceDetails['VAT'] = $curr_symb." ".number_format($totalVat, $decimalNumber, $decimalSeparator, $thousandSeparator);
                // $priceDetails['sub_total'] = $curr_symb." ".number_format(array_sum($price_sum), 2);
                $priceDetails['sub_total'] = $curr_symb." ".number_format($totalSubTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
                // $priceDetails['discount'] = $curr_symb." ".number_format($cartMaster->discount_amount * $Currency_rate, 2);
                // $priceDetails['shipping_cost'] = $curr_symb." ".number_format($cartMaster->shipping_cost * $Currency_rate, 2);
                // $grand_total = array_sum($price_sum) + number_format($cartMaster->discount_amount * $Currency_rate, 2) + number_format($cartMaster->shipping_cost * $Currency_rate, 2);
                // $grand_total = (array_sum($price_sum) + (!empty($cartMaster->shipping_cost) ? number_format($cartMaster->shipping_cost * $Currency_rate, 2) :0 )) - (!empty($cartMaster->discount_amount) ? number_format($cartMaster->discount_amount * $Currency_rate, 2) :0 );
                $priceDetails['grand_total'] = $curr_symb." ".number_format($grandTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
                $priceDetails['promo_code'] = $cartMaster->promo_code;
            }
            else
            {
                $priceDetails['shipping_cost'] = "0.000";
                $priceDetails['discount'] = "0.000";
                $priceDetails['VAT'] = "0.000";
                $priceDetails['sub_total'] = "0.000";
                $priceDetails['grand_total'] = "0.000";
                $priceDetails['promo_code'] = "";
                // $priceDetails['priceDetailsData']['listData'] = [];
            }
            return view('frontend.shopping-cart',compact('baseUrl','cartLabels','megamenuFileName','mobileMegamenuFileName'
            ,'pageName','projectName','cart_arr','curr_symb','priceDetails','decimalNumber','decimalSeparator','thousandSeparator'
            ,'cart_master_id'));
        }
        else
        {
            $curr_symb = getCurrSymBasedOnLangId($lang_id);
            $cart_arr = [];
            return view('frontend.shopping-cart',compact('baseUrl','cartLabels','megamenuFileName','mobileMegamenuFileName'
            ,'pageName','projectName','cart_arr','curr_symb','decimalNumber','decimalSeparator','thousandSeparator',
            'cart_master_id'));
        }
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

    public function addRemoveQty(Request $request)
    {
        if(isset($request->remove_qty)){
            $cartMaster = \App\Models\Cart::where('id', $request->cart_id)->first();
            if($cartMaster)
            {
                $cartMaster->quantity = $request->remove_qty;
                $cartMaster->save();
                $result['status'] = 'true';
                $result['qty'] = $cartMaster->quantity;
                return $result;
            }
        }
        elseif (isset($request->add_qty)) {
            $cartMaster = \App\Models\Cart::where('id', $request->cart_id)->first();
            if($cartMaster)
            {
                $cartMaster->quantity = $request->add_qty;
                $cartMaster->save();
                $result['status'] = 'true';
                $result['qty'] = $cartMaster->quantity;
                return $result;
            }
        }
    }

    public function removeProductFromCart(Request $request)
    {
        $lang_id = Session::get('language_id');
        $cart_master_id = Session::get('cart_master_id');
        $decimalNumber=Session::get('decimal_number');
        $decimalSeparator=Session::get('decimal_separator');
        $thousandSeparator=Session::get('thousand_separator');

        $codes = ['CARTDATADLTSUCC','SHOPPINGCARTEMPTY'];
        $cartLabels = getCodesMsg($lang_id, $codes);

        //VAT Calculation
        $totalVat = 0.00;
        $totalSubTotal = 0.00;
        $discount = 0.00;
        $shipping = 0.00;
        $grandTotal = 0.00;

        $cart_arr = [];
        $i = 0;
        $price_sum = [];
        $Cart = \App\Models\Cart::where('id', $request->cart_id)->first();
        if($Cart)
        {
            $Cart->delete();
            $result['status'] = 'true';
            $result['msg'] = $cartLabels['CARTDATADLTSUCC'];
            $cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();

            $get_curr = \App\Models\GlobalLanguage::select('currency_id','tax_type')->where('id', $lang_id)->first();
            $Currency_rate = getCurrencyRates($get_curr->currency_id);
            $curr_symb = getCurrSymBasedOnLangId($lang_id);
            $i = 0;

            // Reapply discounts
            // update promo code
            $cartMaster_promo = \App\Models\CartMaster::where('id', $cart_master_id)->first();
            if($cartMaster_promo)
            {
                if(!empty($cartMaster_promo->promo_code))
                {
                    $code = $cartMaster_promo->promo_code;
                    $promoResponse =  apply_promotion($code, $cart_master_id, $lang_id);

                    // remove code and discoubt if error
                    if(!empty($promoResponse['status']) && $promoResponse['status'] == "error")
                    {
                        $cart_master_remove = \App\Models\CartMaster::where('id', $cart_master_id)->first();
                        if($cart_master_remove)
                        {
                            $cart_master_remove->promo_code = NULL;
                            $cart_master_remove->discount_amount = 0.000;
                            $cart_master_remove->save();
                        }
                    }
                }
            }

            foreach ($cart as $data) {
                $product_pricing = \App\Models\ProductPricing::where('id', $data->option_id)->whereNull('deleted_at')->first();

                $qty = ($data->quantity) ? (String) $data->quantity : '0';
                $product_price = (!empty($product_pricing->selling_price)) ? $product_pricing->selling_price : $product_pricing->offer_price;
                $price_sum[$i] = str_replace(',', '', number_format(str_replace(',', '', $product_price * $qty) * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));

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
                            $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
                                    $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
                                    $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
                        $cart_arr[$i]['subTotal'] = $curr_symb." ".number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                        $totalSubTotal += str_replace(',','', number_format($subTotal * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator));
                    }
                }
                //VAT Calculation End

                $i++;
            }//Sub Total Calculation
            $grandTotal += $totalSubTotal;

            // //Total VAT Calculation
            // $grandTotal += $totalVat;

            $priceDetails = [];
            $cartMaster = \App\Models\CartMaster::where('id', $cart_master_id)->first();
            if($cartMaster)
            {
                // $result['sub_total'] = $curr_symb." ".number_format(array_sum($price_sum), 2);
                // $result['discount'] = $curr_symb." ".number_format($cartMaster->discount_amount * $Currency_rate, 2);
                // $result['shipping_cost'] = $curr_symb." ".number_format($cartMaster->shipping_cost * $Currency_rate, 2);
                // $grand_total = array_sum($price_sum) + number_format($cartMaster->discount_amount * $Currency_rate, 2) + number_format($cartMaster->shipping_cost * $Currency_rate, 2);
                // $result['grand_total'] = $curr_symb." ".number_format($grand_total, 2);
                if($cartMaster->shipping_cost > 0)
                {
                    $shipping = $cartMaster->shipping_cost * $Currency_rate;
                    $grandTotal += $shipping;
                    $result['shipping_cost'] = $curr_symb." ".number_format($shipping, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                else
                {
                    $result['shipping_cost'] = $curr_symb." ".number_format($cartMaster->shipping_cost * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }

                if($cartMaster->discount_amount > 0)
                {
                    $discount = $cartMaster->discount_amount * $Currency_rate;
                    //Calculate 5% of dicsount
                    $calc_five_perc_from_disc = $discount * 5 / 100;
                    $totalVat -= $calc_five_perc_from_disc;
                    $grandTotal -= $discount;
                    $result['discount'] = $curr_symb." ".number_format($discount, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }
                else
                {
                    $result['discount'] = $curr_symb." ".number_format($cartMaster->discount_amount * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
                }

                //Total VAT Calculation
                $grandTotal += $totalVat;

                $result['net'] = $curr_symb." ".number_format($totalSubTotal - $discount, $decimalNumber, $decimalSeparator, $thousandSeparator);
                $result['vat'] = $curr_symb." ".number_format($totalVat, $decimalNumber, $decimalSeparator, $thousandSeparator);
                $result['sub_total'] = $curr_symb." ".number_format($totalSubTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);

                $result['grand_total'] = $curr_symb." ".number_format($grandTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
            }
            else
            {
                $result['sub_total'] = '0.00';
                $result['discount'] = '0.00';
                $result['shipping_cost'] = '0.00';
                $result['grand_total'] = '0.00';
                $result['vat'] = '0.00';
            }

            $result['cart_empty_msg'] = $cartLabels['SHOPPINGCARTEMPTY'];
            $result['Cart_Count'] = count($cart);
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            return $result;
        }
    }

    public function postAddToCart(Request $request)
  	{
        // dd($request->all());
      $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
      $defaultLanguageId = $defaultLanguageData['id'];
      $setSessionforLang=setSessionforLang($defaultLanguageId);
      $lang_id = Session::get('language_id');
      $baseUrl = $this->getBaseUrl();
      //Localization
       $codes = ['PRODUCTADDEDTOCART'];
       $cartLabels = getCodesMsg($lang_id, $codes);
  		  $option_id=$request->option_id;
        $qty=$request->qty;
        $product_id=$request->product_id;
        $shoppingmsg=$request->shoppingmsg;
        $formsg=$request->formsg;
        $ladyoperator=$request->ladyoperator;
        $printstaffmsg=$request->printstaffmsg;
        $caption=$request->caption;
        $customer_id = Session::get('customer_id');
        if(empty($customer_id))
        $customer_id=0;
        $GroupPrice=0;
        if($customer_id){
            $customeData=Customer::where("id",$customer_id)->first();
            if($customeData->cust_group_id!=0){
                $custGrpPrice=CustGroupPrice::where("product_id",$product_id)->where('customer_group_id', $customeData->cust_group_id)->first();
                if(!empty($custGrpPrice))
                $GroupPrice=$custGrpPrice->price;
            }
        }
        $product_details = \App\Models\ProductPricing::select('id','selling_price','offer_price','offer_start_date','offer_end_date')
                                                              ->where('id', $option_id)
                                                              ->where('product_id', $product_id)
                                                              ->whereNull('deleted_at')
                                                              ->first();

        //    dd($product_details);
        if($GroupPrice==0){
          if(!empty($product_details['offer_price']) && (date("Y-m-d",strtotime($product_details['offer_start_date'])) <= date('Y-m-d')) && (date('Y-m-d') <= date("Y-m-d",strtotime($product_details['offer_end_date']))))
          $price=  $product_details['offer_price'];
          else
          $price=  $product_details['selling_price'];
        }
        else{
          $price=$GroupPrice;
        }
       $cart_master_id=Session::get('cart_master_id');
        if(empty($cart_master_id))
        {
          $cartMaster=new CartMaster;
          $cartMaster->user_id=$customer_id;
          $cartMaster->message=$shoppingmsg;
          $cartMaster->save();
          $cart_master_id=Session::put('cart_master_id',$cartMaster->id);
        }
        $cart_master_id=Session::get('cart_master_id');
        $cart=Cart::where("cart_master_id",$cart_master_id)->where("option_id",$option_id)->first();
        if($request->file('image')){
          $cartQty=(int)$qty;
          $cartDetails=new Cart();
          $cartDetails->user_id=$customer_id;
          $cartDetails->product_id=$product_id;
          $cartDetails->cart_master_id=Session::get('cart_master_id');
          $cartDetails->option_id=$option_id;
          $cartDetails->quantity=$cartQty;
          $cartDetails->price=$price;
          $cartDetails->gift_message=$shoppingmsg;
          $cartDetails->gift_wrap=$formsg;
          $cartDetails->lady_operator=$ladyoperator;
          $cartDetails->message=$printstaffmsg;
          $cartDetails->photobook_caption=$caption;
          $cartDetails->image=$request->image;
          $cartDetails->updated_at=date('Y-m-d H:i:s');
          $cartDetails->save();
          //Image upload
          if($request->file('image')){
            $photo = $request->file('image');
            $ext = $photo->extension();
            $filename = rand() . '_' . time() . '_' .$cartDetails->id. '.' . $ext;
            $photo->move(public_path() . '/assets/images/carts/', $filename);
            $cartData=Cart::find($cartDetails->id);
            $cartData->image=$baseUrl . '/public/assets/images/carts/'.$filename;
            $cartData->update();
          }
        }
        elseif($request->image){
          $cartQty=(int)$qty;
          $cartDetails=new Cart();
          $cartDetails->user_id=$customer_id;
          $cartDetails->product_id=$product_id;
          $cartDetails->cart_master_id=Session::get('cart_master_id');
          $cartDetails->option_id=$option_id;
          $cartDetails->quantity=$cartQty;
          $cartDetails->price=$price;
          $cartDetails->gift_message=$shoppingmsg;
          $cartDetails->gift_wrap=$formsg;
          $cartDetails->lady_operator=$ladyoperator;
          $cartDetails->message=$printstaffmsg;
          $cartDetails->photobook_caption=$caption;
          $cartDetails->updated_at=date('Y-m-d H:i:s');
          $cartDetails->save();
          $filename = 'socialmedia_'.rand() . '_' . time() . '_' .$cartDetails->id. '.jpg';
          copy($request->image,public_path() . '/assets/images/carts/'.$filename);
          $cartData=Cart::find($cartDetails->id);
          $cartData->image=$baseUrl . '/public/assets/images/carts/'.$filename;
          $cartData->update();
        }
        else{
          if($cart){
            $cartQty=$cart->quantity+$qty;
            $cartDetails=Cart::find($cart->id);
            $cartDetails->quantity=$cartQty;
            $cartDetails->price=$price;
            $cartDetails->gift_message=$shoppingmsg;
            $cartDetails->gift_wrap=$formsg;
            $cartDetails->lady_operator=$ladyoperator;
            $cartDetails->message=$printstaffmsg;
            $cartDetails->photobook_caption=$caption;
            $cartDetails->updated_at=date('Y-m-d H:i:s');
            $cartDetails->save();
          }
          else{
            $cartDetails=new Cart;
            $cartDetails->user_id=$customer_id;
            $cartDetails->product_id=$product_id;
            $cartDetails->quantity=$qty;
            $cartDetails->price=$price;
            $cartDetails->cart_master_id=Session::get('cart_master_id');
            $cartDetails->option_id=$option_id;
            $cartDetails->gift_message=$shoppingmsg;
            $cartDetails->gift_wrap=$formsg;
            $cartDetails->lady_operator=$ladyoperator;
            $cartDetails->message=$printstaffmsg;
            $cartDetails->photobook_caption=$caption;
            $cartDetails->created_at=date('Y-m-d H:i:s');
            $cartDetails->save();
          }
        }

        // Get total items
        $cart_master_id = Session::get('cart_master_id');
        $totalCartItems = Cart::select('id')->where('cart_master_id',$cart_master_id)->get();
        $totalCartItemsCount = count($totalCartItems);

        return response()->json(['status' => true,'msg'=> $cartLabels['PRODUCTADDEDTOCART'],'count'=> $totalCartItemsCount]);
  	}
    public function postAddRecommendedToCart(Request $request)
  	{
        $product_id=$request->product_id;
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);
        $lang_id = Session::get('language_id');
        $baseUrl = $this->getBaseUrl();
        //Localization
         $codes = ['PRODUCTADDEDTOCART'];
         $cartLabels = getCodesMsg($lang_id, $codes);
        $recommended_products = \App\Models\RecommendedProduct::select('products.id','product_pricing.id as option_id','product_pricing.selling_price', 'product_pricing.offer_price','product_pricing.offer_start_date','product_pricing.offer_end_date')
        ->join('products', 'products.id', '=', 'recommended_products.recommended_id')
        ->join('product_pricing', 'product_pricing.product_id', '=', 'products.id')
        ->where('product_pricing.is_default', 1)
        ->where('recommended_products.product_id', $product_id)
        ->whereNull('products.deleted_at')
        ->get();

        foreach($recommended_products as $pro){
            $option_id=$pro->option_id;
            $qty=1;
            $product_id=$pro->id;
            $customer_id = Session::get('customer_id');
            if(empty($customer_id))
            $customer_id=0;
            $GroupPrice=0;
            if($customer_id){
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
            $cart_master_id=Session::get('cart_master_id');
            if(empty($cart_master_id)){
              $cartMaster=new CartMaster;
              $cartMaster->user_id=$customer_id;
              $cartMaster->save();
              $cart_master_id=Session::put('cart_master_id',$cartMaster->id);
              $cart=Cart::where("cart_master_id",$cart_master_id)->where("option_id",$option_id)->first();
              if($cart){
                  $cartQty=$cart->quantity+$qty;
                  $cartDetails=Cart::find($cart->id);
                  $cartDetails->quantity=$cartQty;
                  $cartDetails->price=$price;
                  $cartDetails->updated_at=date('Y-m-d H:i:s');
                  $cartDetails->save();
              }
              else{
                    $cartDetails=new Cart;
                    $cartDetails->user_id=$customer_id;
                    $cartDetails->product_id=$product_id;
                    $cartDetails->quantity=$qty;
                    $cartDetails->price=$price;
                    $cartDetails->cart_master_id=Session::get('cart_master_id');
                    $cartDetails->option_id=$option_id;
                    $cartDetails->created_at=date('Y-m-d H:i:s');
                    $cartDetails->save();
              }
            }
            else{
              $cart=Cart::where("cart_master_id",$cart_master_id)->where("option_id",$option_id)->first();
              if($cart){
                    $cartQty=$cart->quantity+$qty;
                    $cartDetails=Cart::find($cart->id);
                    $cartDetails->quantity=$cartQty;
                    $cartDetails->price=$price;
                    $cartDetails->updated_at=date('Y-m-d H:i:s');
                    $cartDetails->save();
              }
              else{
                $cartDetails=new Cart;
                $cartDetails->user_id=$customer_id;
                $cartDetails->product_id=$product_id;
                $cartDetails->quantity=$qty;
                $cartDetails->price=$price;
                $cartDetails->cart_master_id=Session::get('cart_master_id');
                $cartDetails->option_id=$option_id;
                $cartDetails->created_at=date('Y-m-d H:i:s');
                $cartDetails->save();

              }
            }
        }

        // Get total items
        $cart_master_id = Session::get('cart_master_id');
        $totalCartItems = Cart::select('id')->where('cart_master_id',$cart_master_id)->get();
        $totalCartItemsCount = count($totalCartItems);
        return response()->json(['status' => true,'msg'=> $cartLabels['PRODUCTADDEDTOCART'],'count'=> $totalCartItemsCount]);
  	}

    public function removePromoCode(Request $request)
    {
        $lang_id = Session::get('language_id');
        $codes = ['PROMOCODEREMOVE','APPLY','ENTER_COUPON_CODE'];
        $cartLabels = getCodesMsg($lang_id, $codes);

        $language_found = isLanguageExists($lang_id);
        if($language_found == 'false')
        {
            $defaultLang = $this->getDefaultLanguage();
            $lang_id = $defaultLang;
        }

        $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
        if($cart_master)
        {
            $cart_master->promo_code = NULL;
            $cart_master->discount_amount = 0.000;
            $cart_master->save();
            $result['status'] = 'true';
            $result['Apply'] = $cartLabels['APPLY'];
            $result['Placeholder'] = $cartLabels['ENTER_COUPON_CODE'];
            $result['msg'] = $cartLabels['PROMOCODEREMOVE'];
            return $result;
        }
    }

    public function applyPromoCode(Request $request)
    {
        $lang_id = Session::get('language_id');
        $code = $request->coupon_code;
        $cart_master_id = $request->cart_master_id;
        return apply_promotion($code, $cart_master_id, $lang_id);
    }

    public function deleteUnusedCart()
    {
        $getCartDatas = \App\Models\CartMaster::select('cart_master.id as cart_master_id')
        ->join('cart', 'cart.cart_master_id', '=', 'cart_master.id')
        ->whereDate('cart_master.created_at', '>=', DB::raw('CURRENT_DATE - INTERVAL 8 DAY'))
        ->whereDate('cart_master.created_at', '<=', DB::raw('CURRENT_DATE - INTERVAL 1 DAY'))
        ->where('flag_complete', 0)
        ->whereNotNull('cart.image')
        ->groupBy('cart_master.id')
        ->get();

        foreach ($getCartDatas as $getCartData) {
            $order = \App\Models\Orders::where('cart_master_id', $getCartData->cart_master_id)->first();
            if($order || !empty($order))
            {
                continue;
            }
            else
            {
                $carts = \App\Models\Cart::where('cart_master_id', $getCartData->cart_master_id)
                ->whereNotNull('image')
                ->get();
                $domain_host = request()->getSchemeAndHttpHost();
                if($carts)
                {
                    foreach ($carts as $cart) {
                        //Remove Image
                        if(!empty($cart->image) || $cart->image != '')
                        {
                            $img_path = str_replace($domain_host, "", $cart->image);
                            $image_path = base_path().$img_path;
                            if(file_exists($image_path))
                            {
                                unlink($image_path);
                            }
                        }
                        //Remove Other Images
                        if(!empty($cart->other_images))
                        {
                            if(is_array($cart->other_images))
                            {
                                foreach ($cart->other_images as $other_image) {
                                    $other_image_path = str_replace($domain_host, "", $other_image);
                                    $other_images_path = base_path().$other_image_path;
                                    if(file_exists($other_images_path))
                                    {
                                        unlink($other_images_path);
                                    }
                                }
                            }
                        }
                        //Remove Screenshots Files
                        if(!empty($cart->screenshots_files))
                        {
                            if(is_array($cart->screenshots_files))
                            {
                                foreach ($cart->screenshots_files as $screenshots_file) {
                                    $screenshots_files_path = base_path().'/design-tool/data/screenshots/'.$screenshots_file;
                                    if(file_exists($screenshots_files_path))
                                    {
                                        unlink($screenshots_files_path);
                                    }
                                }
                            }
                        }
                        //Remove Print Files
                        if(!empty($cart->print_files))
                        {
                            if(is_array($cart->print_files))
                            {
                                foreach ($cart->print_files as $print_file) {
                                    $print_files_path = base_path().'/design-tool/data/printfiles/'.$print_file;
                                    if(file_exists($print_files_path))
                                    {
                                        unlink($print_files_path);
                                    }
                                }
                            }
                        }
                    }
                }
                //Delete Cart Records From Cart Table
                \App\Models\Cart::where('cart_master_id', $getCartData->cart_master_id)->delete();

                //Delete Cart Master Records From cart_master Table
                \App\Models\CartMaster::where('id', $getCartData->cart_master_id)->delete();
            }
        }
        return true;
    }
}
