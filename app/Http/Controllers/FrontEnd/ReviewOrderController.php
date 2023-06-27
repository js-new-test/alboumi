<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Traits\ReuseFunctionTrait;
use Auth;
use DB;

class ReviewOrderController extends Controller
{
    use ReuseFunctionTrait;

    public function getReviewOrder()
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
        ,'REMOVE','AREYOUSURE','CONFIRMATION','NO','YES','LOGINEMAIL','SHIPPINGADDRESS','PAYMENMETHOD',
        'REVIEWORDER','CHECKOUT','PRICE','ORDERSUMMERY','REVIEWORDERHEADING','CHANGE','REVIEW/PLACEORDER'
        ,'BILLINGADDRESS','CREDITCARD','DEBITCARD','UNITPRICE','VAT','STOREPICKUPADDRESS','ADDNEWBILLADDRESS',
        'FULL_NAME', 'ADDRESS_LINE1_HINT', 'ADDRESS_LINE2_HINT','ADDRESS1REQ','FULLNAMEREQ','ADDRESS_LINE2_HINT',
        'MYACCOUNTLABEL3','MOBILEREQ','MOBILENUM','addressType1','addressType2','SELECTCOUNTRY','MYADDRESSES11',
        'MYADDRESSES12','PINCODE_HINT','PINCODEREQ','SET_AS_DEFAULT_ADDRESS','CANCEL','BILLINGADDRESSNOTFOUND','MYADDRESSES10',
        'MYADDRESSES3','MOBILEMUSTBE8DIGIT','ADDRESS2REQ','STATEREQ','CITYREQ','NET','MESSAGE','PLSWAITFORWHILE',
        "ADD_ADDRESS","FULL_NAME","FULLNAMEREQ","ADDRESS_LINE1_HINT","ADDRESS1REQ","MYADDRESSES3","ADDRESS2REQ","MYACCOUNTLABEL3",
        "MOBILEREQ","MOBILENUM","MOBILEMUSTBE8DIGIT","addressType1","addressType2","MYADDRESSES11","STATEREQ","MYADDRESSES12",
        "CITYREQ","MYADDRESSES10","PINCODEREQ","SET_AS_DEFAULT_ADDRESS","EDIT_ADDRESS_TITLE"];
        $reviewOrderLabels = getCodesMsg($lang_id, $codes);
        $pageName = $reviewOrderLabels["REVIEWORDERHEADING"];
        $projectName = $reviewOrderLabels["APPNAME"];

        //Shopping Cart
        if(isset($cart_master_id) && $cart_master_id != "")
        {
            $cart_arr = [];
            $i = 0;
            $price_sum = [];
            $get_curr = \App\Models\GlobalLanguage::select('currency_id', 'tax_type')->where('id', $lang_id)->where('is_deleted', 0)->first();
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
                $cart_arr[$i]['actual_price'] =  $curr_symb." ".number_format($product_price * $Currency_rate, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
            $shipping_address = $checkouttype ='';
            $cartMaster = \App\Models\CartMaster::where('id', $cart_master_id)->first();
            if($cartMaster)
            {
                // $priceDetails['sub_total'] = $curr_symb." ".number_format(array_sum($price_sum), 2);
                // $priceDetails['discount'] = $curr_symb." ".number_format($cartMaster->discount_amount * $Currency_rate, 2);
                // $priceDetails['shipping_cost'] = $curr_symb." ".number_format($cartMaster->shipping_cost * $Currency_rate, 2);
                // // $grand_total = (array_sum($price_sum) + number_format($cartMaster->shipping_cost * $Currency_rate, 2)) - number_format($cartMaster->discount_amount * $Currency_rate, 2);
                // $grand_total = (array_sum($price_sum) + (!empty($cartMaster->shipping_cost) ? number_format($cartMaster->shipping_cost * $Currency_rate, 2) :0 )) - (!empty($cartMaster->discount_amount) ? number_format($cartMaster->discount_amount * $Currency_rate, 2) :0 );
                // $priceDetails['grand_total'] = $curr_symb." ".number_format($grand_total, 2);
                
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
                $priceDetails['sub_total'] = $curr_symb." ".number_format($totalSubTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);                
                $priceDetails['grand_total'] = $curr_symb." ".number_format($grandTotal, $decimalNumber, $decimalSeparator, $thousandSeparator);
            }
            else
            {
                $priceDetails['priceDetailsData']['listData'] = [];
            }

            if($cartMaster->checkout_type == 1)
            {
                $checkouttype = 1;
                if(isset($cartMaster->address_id) && $cartMaster->address_id > 0)
                {
                    $shipping_address = \App\Models\CustomerAddress::where('id', $cartMaster->address_id)->where('is_deleted', 0)->first();
                }
                else
                {
                    $shipping_address = \App\Models\CustomerAddress::where('is_default', 1)->where('is_deleted', 0)->first();
                }
            }
            
            if($cartMaster->checkout_type == 2)
            {
                $checkouttype = 2;
                if(isset($cartMaster->store_location_id) && $cartMaster->store_location_id > 0)
                {
                    $shipping_address = \App\Models\StoreLocation::where('id', $cartMaster->store_location_id)
                    ->where('language_id', $lang_id)->where('is_deleted', 0)->first();
                    if(empty($shipping_address))
                    {
                        $defaultLang = $this->getDefaultLanguage();
                        $shipping_address = \App\Models\StoreLocation::where('id', $cartMaster->store_location_id)
                        ->where('language_id', $defaultLang)->where('is_deleted', 0)->first();
                    }
                }                
            }
            
            $customer_id = Auth::guard('customer')->user()->id;
            $billing_address = \App\Models\BillingAddress::where('is_default', 1)->where('is_deleted', 0)->where('customer_id', $customer_id)->first();
            $countries = \App\Models\Country::where('id', 17)->get();
            return view('frontend.review-order',compact('baseUrl','reviewOrderLabels','megamenuFileName','mobileMegamenuFileName'
            ,'pageName','projectName','cart_arr','curr_symb','priceDetails','cartMaster','shipping_address'
            ,'decimalNumber', 'decimalSeparator', 'thousandSeparator','checkouttype','billing_address','countries','cartMaster'));
        }
        else
        {
            $curr_symb = getCurrSymBasedOnLangId($lang_id);
            $cart_arr = [];
            return view('frontend.review-order',compact('baseUrl','reviewOrderLabels','megamenuFileName','mobileMegamenuFileName'
            ,'pageName','projectName','cart_arr','curr_symb','decimalNumber', 'decimalSeparator', 'thousandSeparator'));
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

    public function saveBillingAddress(Request $request)
    {       
        //Localization
        $codes = ['FULLNAMEREQ', 'ADDRESS1REQ', 'ADDRESS2REQ','MOBILEREQ','MOBILENUM','COUNTRYREQ'
        ,'PINCODEREQ', 'ADDRESSADDEDSUCC', 'ADDRESSUPDATEDSUCC', 'ADDRESSNOTADDED'];
        $addressLabels = getCodesMsg(Session::get('language_id'), $codes);

        $lang_id = Session::get('language_id');
        $msg = [                        
            'full_name.required' => $addressLabels["FULLNAMEREQ"],            
            'address_1.required' => $addressLabels["ADDRESS1REQ"],                      
            'mobile.required' => $addressLabels["MOBILEREQ"],
            'mobile.numeric' => $addressLabels["MOBILENUM"],  
            'country.required' => $addressLabels["COUNTRYREQ"],  
            // 'states.required' => getCodesMsg($lang_id, $code = "STATEREQ"),  
            // 'cities.required' => getCodesMsg($lang_id, $code = "CITYREQ"),  
            'pincode.required' => $addressLabels["PINCODEREQ"],  
        ]; 

        $validator = Validator::make($request->all(), [   
            'full_name' => 'required',         
            'address_1' => 'required',               
            'mobile' => 'required|numeric',              
            'country' => 'required',
            // 'states' => 'required',
            // 'cities' => 'required',  
            'pincode' => 'required'          
        ],$msg);
            
        if($validator->fails()) {            
            return redirect('/customer/review-order')
            ->withErrors($validator)
            ->withInput();
        }

        if(isset($request->address_id))
        {            
            $customer_address = \App\Models\BillingAddress::updateCustomerAddress($request);
            $msg = $addressLabels["ADDRESSUPDATEDSUCC"];
        }
        else
        {            
            $customer_address = \App\Models\BillingAddress::saveCustomerAddress($request);              
            $msg = $addressLabels["ADDRESSADDEDSUCC"];
        }
        
        if($customer_address['status'] == 'true')
        {            
            $result['status'] = 'true';
            return $result;            
        }        
        else
        {
            $result['status'] = 'false';
            $result['msg'] = $addressLabels["ADDRESSNOTADDED"];
            return $result;            
        }
    }

    public function placeOrder()
    {
        $cart_master_id = Session::get('cart_master_id');  
        
        //Display less or not available products Start
        $arrayNotAvailableProducts = [];
        $arrayLessAvailableProducts = [];
        $flagAvailable = true;
        $i = 0;
        $k = 0;
        $cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();
        $language_id = Session::get('language_id');
        foreach ($cart as $item) {
            $product = \App\Models\Product::where('id', $item->product_id)->whereNull('deleted_at')->first();
            if($product)
            {
                $category = \App\Models\Category::where('id', $product->category_id)->whereNull('deleted_at')->first();
                if($category->photo_upload == 0)
                {
                    $product_pricing = \App\Models\ProductPricing::where('id', $item->option_id)
                    ->where('product_id', $item->product_id)->whereNull('deleted_at')->first();
                                        
                    if($product_pricing->quantity <= 0)
                    {            
                        //Get product details
                        $product_detail = \App\Models\ProductDetails::where('product_id', $item->product_id)
                        ->where('language_id', $language_id)->whereNull('deleted_at')->first();            
                        $arrayNotAvailableProducts[$i]['product_name'] = ($product_detail) ? $product_detail->title : "";
                        $i++;   
                        $flagAvailable = false;
                    }
                    elseif($item->quantity > $product_pricing->quantity)		
                    {
                        //Get product details
                        $product_detail = \App\Models\ProductDetails::where('product_id', $item->product_id)
                        ->where('language_id', $language_id)->whereNull('deleted_at')->first();
                        $arrayLessAvailableProducts[$k]['product_name'] = ($product_detail) ? $product_detail->title : "";
                        $arrayLessAvailableProducts[$k]['qty'] = ($product_pricing) ? $product_pricing->quantity : "";
                        $k++;   
                        $flagAvailable = false;
                    }
                }
            }                     
        }

        if($flagAvailable === false)
        {            
            $result['arrayNotAvailableProducts'] = $arrayNotAvailableProducts;                
            $result['arrayLessAvailableProducts'] = $arrayLessAvailableProducts;
            return $result;
        }
        //Display less or not available products Over

        //Create new order
        $cart_master = \App\Models\CartMaster::where('id', $cart_master_id)->first();
        if($cart_master->set_bill_addr_as_ship_addr > 0)
        {
            $customer_id = Auth::guard('customer')->user()->id;
            $billign_address = \App\Models\CustomerAddress::where('customer_id', $customer_id)
			->where('id', $cart_master->address_id)->where('is_deleted', 0)->first();
            $cust_address = new \App\Models\BillingAddress;
            $cust_address->city = $billign_address->city;
            $cust_address->state = $billign_address->state;        
            $cust_address->customer_id = $customer_id;
            $cust_address->fullname = $billign_address->fullname;
            $cust_address->address_1 = $billign_address->address_1;
            $cust_address->address_2 = $billign_address->address_2;        
            $cust_address->country = $billign_address->country;
            $cust_address->address_type = $billign_address->address_type;
            $cust_address->pincode = $billign_address->pincode;
            $cust_address->phone1 = $billign_address->phone1;                        
            $cust_address->is_default = 1;
            $cust_address->save();

        }        
        create_order($cart_master_id, $language_id);
        

        $merchant_order_id = Session::get('merchant_order_id');
        $_SESSION['merchant_order_id'] = $merchant_order_id;
        
        $order_id = Session::get('order_id');
        $order = \App\Models\Orders::where('id', $order_id)->first();        
        //Store Message In Order Table
        if($order)
        {
            $order->message = $cart_master->message;
            $order->save();
        }        

        Session::put('order_amount', $order->total);
        $_SESSION['order_amount'] = $order->total;

        if($cart_master->payment_method == 1)
        {
            $order_id = Session::get('order_id');
            $response = create_credimax_session();
            return $response;            
        }        

        $merchantOrderId = strtr(base64_encode($_SESSION['merchant_order_id']), '+/=', '-_,');
        $amount = strtr(base64_encode($_SESSION['order_amount']), '+/=', '-_,');

        if($cart_master->payment_method == 2)
        {
            $baseUrl = $this->getBaseUrl();
            header('Location: '.$baseUrl.'/benefits/request_order.php?merchantOrderId='.$merchantOrderId.'&amount='.$amount);
            exit;
        }	
    }

    public function storeOrderMessage(Request $request)
    {
        $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
        if($cart_master)
        {
            $cart_master->message = $request->message;
            $cart_master->save();
            return true;
        }
    }

    public function editBillingAddress(Request $request)
    {
        $customer_address = \App\Models\BillingAddress::where('id',$request->bill_address_id)
        ->where('is_deleted', 0)->first();
        if($customer_address)
        {
            $countries = \App\Models\Country::whereNull('deleted_at')->get();
            $result['status'] = 'true';
            $result['customer_address'] = $customer_address;
            $result['country'] = $countries;
            return $result;
        }        
    }

    public function updateBillingAddress(Request $request)
    {
        if($request->method('post') && $request->ajax())
        {        
            $lang_id = Session::get('language_id');        
            
            //Localization
            $codes = ['ADDRESSUPDATEDSUCC','ADDRESSADDEDSUCC','SOMETHINGWRONG', 'BILLINGADDRESS'
            ,'CHANGE'];
            $shippingAddressLabels = getCodesMsg($lang_id, $codes);

            if(isset($request->address_id))
            {            
                $customer_address = \App\Models\BillingAddress::updateCustomerAddress($request);
                $msg = $shippingAddressLabels["ADDRESSUPDATEDSUCC"];
            }
            else
            {            
                $customer_address = \App\Models\BillingAddress::saveCustomerAddress($request);              
                $msg = $shippingAddressLabels["ADDRESSADDEDSUCC"];
            }
            
            if($customer_address['status'] == 'true')
            {                
                $address = \App\Models\BillingAddress::where('customer_id', $request->customer_id)
                ->where('is_deleted', 0)->where('id', $request->address_id)->first();                                                            
                $country = \App\Models\Country::where('id', $address->country)->first();
                $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();

                $result['status'] = 'true';
                $result['msg'] = $msg;
                $result['address'] = $address;
                $result['country'] = $country->name;
                $result['checkouttype'] = $cart_master->checkout_type;
                $result['same_as_ship_addr'] = $cart_master->same_as_ship_addr;
                $result['BILLINGADDRESS'] = $shippingAddressLabels['BILLINGADDRESS'];
                $result['CHANGE'] = $shippingAddressLabels['CHANGE'];
                return $result;

                // $result['status'] = 'true';
                // $result['msg'] = $msg;            
                // return $result;
            }        
            else
            {
                $result['status'] = 'false';
                $result['msg'] = $shippingAddressLabels["SOMETHINGWRONG"];
                return $result;
            }
        }
    }

    public function setShippingAddress(Request $request)
    {
        $lang_id = Session::get('language_id');        
            
        //Localization
        $codes = ['SETBILLADDRETOSHIPADDR'];
        $shippingAddressLabels = getCodesMsg($lang_id, $codes);

        $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
        if($cart_master)
        {
            $cart_master->same_as_ship_addr = $request->is_checked;
            $cart_master->save();
            if($cart_master->same_as_ship_addr == 1)
            {
                $result['status'] = 'true';
                $result['msg'] = $shippingAddressLabels['SETBILLADDRETOSHIPADDR'];
                return $result;
            }            
        }
    }

    public function setDeliveryAddress(Request $request)
    {
        $lang_id = Session::get('language_id');        
            
        //Localization
        $codes = ['SETBILLADDRETOSHIPADDR'];
        $shippingAddressLabels = getCodesMsg($lang_id, $codes);

        $cart_master = \App\Models\CartMaster::where('id', $request->cart_master_id)->first();
        if($cart_master)
        {
            $cart_master->set_bill_addr_as_ship_addr = $request->is_checked;
            $cart_master->save();
            if($cart_master->set_bill_addr_as_ship_addr == 1)
            {
                $result['status'] = 'true';
                $result['msg'] = $shippingAddressLabels['SETBILLADDRETOSHIPADDR'];
                return $result;
            }            
        }
    }
}
