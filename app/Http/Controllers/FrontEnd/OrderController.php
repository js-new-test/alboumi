<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\GlobalLanguage;
use App\Models\Orders;
use Auth;
use DB;

class OrderController extends Controller
{
    public function getOrders()
    {
        try {
            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            $setSessionforLang=setSessionforLang($defaultLanguageId);

            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
            $defaultCurrId = $defaultCurrData['id'];
            $setSessionforCurr = setSessionforCurr($defaultCurrId);

            $lang_id = Session::get('language_id');
            $baseUrl = $this->getBaseUrl();

            //Localization
            $codes = ['OK','SUCCESS','SUCCESSSTATUS','ORDERID','ITEMS','BILLAMOUNT','BHD',
            'MYORDER','APPNAME','MYORDERDEC','MYORDERKEYWORD','HOME','QTY','VIEW_DETAILS','ORDERNOTFOUND'];
            $ordersLabels = getCodesMsg($lang_id, $codes);

            $pageName = $ordersLabels["MYORDER"];
            $projectName = $ordersLabels["APPNAME"];

            $megamenuFileName = "megamenu_".Session::get('language_code');
            $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

            //Get User Id
            $customer_id = Auth::guard('customer')->user()->id;
            $order_data = \App\Models\Orders::where('user_id', $customer_id)
            ->where('order_status_id', '!=', 45)->orderBy('created_at', 'desc')->get();

            $timezone = \App\Models\CustomerTimezone::where('customer_id', $customer_id)->pluck('zone')->first();

            $k = 0;
            $orders_arr = [];
            if(count($order_data) > 0)
            {
                foreach ($order_data as $orders) {
                    //Orders Data
                    $orders_arr[$k]['orderId'] = (String) $orders->id;
                    $orders_arr[$k]['orderIdText'] = $ordersLabels["ORDERID"].": ".$orders->order_id;

                    $order_products = \App\Models\OrderProducts::where('order_id', $orders->id)->get();
                    $orders_arr[$k]['subText'] = "(".count($order_products)." ".$ordersLabels["ITEMS"].")"." "
                    .$ordersLabels["BILLAMOUNT"].": ".$ordersLabels['BHD']." ".number_format($orders->total, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    $orders_arr[$k]['orderDate'] = date('Y-m-d H:i:s', strtotime($orders->created_at));
                    $orders_arr[$k]['Orderdetails'] = url('customer/orderdetails')."?order_id=".$orders->id;

                    //Items Data
                    $items = [];
                    $i = 0;
                    foreach ($order_products as $order_product) {
                        $items[$i]['itemId'] = (String) $order_product->id;
                        $unserialize = unserialize($order_product->details);
                        $items[$i]['itemName'] = $unserialize['title'];
                        $order_status = \App\Models\OrderStatus::where('id', $orders->order_status_id)->first();
                        $items[$i]['status'] = $order_status->status;
                        $items[$i]['slug'] = $order_status->slug;
                        $images = \App\Models\Image::where('imageable_id', $order_product->product_id)->where('image_type',
                        "product")->where('is_default','yes')->whereNull('deleted_at')->first();
                        if(!empty($unserialize['image']))
                        {
                            $items[$i]['image'] = $unserialize['image'];
                        }
                        elseif($images)
                        {
                            $items[$i]['image'] = $baseUrl."/public".$images->upload_path.$images->name;
                        }
                        else
                        {
                            $items[$i]['image'] = $baseUrl.'/public/assets/images/no_image.png';
                        }
                        $items[$i]['price'] = $ordersLabels['BHD']." ".number_format($order_product->price, $decimalNumber, $decimalSeparator, $thousandSeparator);
                        $items[$i]['type'] = "7";
                        $items[$i]['qty'] = (String) $order_product->quantity;
                        $items[$i]['isShowTrack'] = (String) $order_status->is_showtrack;
                        $items[$i]['navigationFlag'] = "1";
                        if(!empty($unserialize['attributes']))
                        {
                            $varients = [];
                            $j = 0;
                            foreach ($unserialize['attributes'] as $key => $attributes) {
                                $varients[$j]['title'] = $key;
                                $varients[$j]['value'] = $attributes;
                                $j++;
                            }
                            $items[$i]['variant'] = $varients;
                        }
                        else{
                            $items[$i]['variant'] = [];
                        }
                        $items[$i]['query'] = url('customer/orderdetails')."?order_id=".$orders->id."&item_id=".$order_product->id."&language_id=".$lang_id;
                        $i++;
                    }
                    $orders_arr[$k]['items'] = $items;
                    $k++;
                }

                //Component Data
                $componentData = [];
                $componentData['myOrdersData'] = $orders_arr;
            }

            return view('frontend.my-order', compact('baseUrl','pageName','orders_arr',
            'projectName','megamenuFileName','ordersLabels','mobileMegamenuFileName',
            'decimalNumber', 'decimalSeparator','thousandSeparator','timezone'));
        } catch (\Exception $e) {
            return view('errors.500');
        }
    }

    public function getOrdersDetails(Request $request)
    {
        try {
            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            $setSessionforLang=setSessionforLang($defaultLanguageId);

            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
            $defaultCurrId = $defaultCurrData['id'];
            $setSessionforCurr = setSessionforCurr($defaultCurrId);

            $lang_id = Session::get('language_id');
            $item_id = $request->get('item_id') ? $request->get('item_id') : "";
            $order_id = $request->get('order_id') ? $request->get('order_id') : "";
            $baseUrl = $this->getBaseUrl();

            //Localization
            $codes = ['OK','SUCCESS','SUCCESSSTATUS','ORDERID','ITEMS','BILLAMOUNT','BHD',
            'SHIPPINGDETAILS','PRICEDETAILS','AMOUNTPAYABLE','PAYMENTMODE', 'DISCOUNT','HOME',
            'SHIPPING','PRICE','ORDER_DETAILS','ORDERDETAILSDESC','ORDERDETAILSKEYWORD','APPNAME'
            ,'ORDERNOTFOUND','QTY','SHIPPINGDETAILS','PRICEDETAILS','TOTALAMOUNT','VAT','NET'];
            $ordersDetailsLabels = getCodesMsg($lang_id, $codes);

            $pageName = $ordersDetailsLabels["ORDER_DETAILS"];
            $projectName = $ordersDetailsLabels["APPNAME"];

            $megamenuFileName = "megamenu_".Session::get('language_code');
            $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

            //Get User Id
            $customer_id = Auth::guard('customer')->user()->id;
            $orders = \App\Models\Orders::where('user_id', $customer_id)
            ->where('id', $order_id)->where('order_status_id', '!=', 45)->first();

            if(empty($orders))
            {
                return redirect('/customer/my-orders');
            }

            $timezone = \App\Models\CustomerTimezone::where('customer_id', $customer_id)->pluck('zone')->first();

            $i = 0;
            $orders_arr = [];
            if(!empty($orders))
            {
                //Orders Data
                $orderId = (String) $order_id;
                $orderIdText = $ordersDetailsLabels["ORDERID"].": ".$orders->order_id;
                $order_product = \App\Models\OrderProducts::where('order_id', $orders->id)->first();
                $order_status = \App\Models\OrderStatus::where('id', $order_product->order_status_id)->first();
                $orders_status = $order_status->status;
                // $orderDate = date('j M Y', strtotime($orders->created_at));
                $orderDate = date('Y-m-d H:i:s', strtotime($orders->created_at));

                //Items Data
                $items = [];
                $i = 0;
                $item_counts = [];
                $price_sum = [];
                if(empty($item_id))
                {
                    $order_products = \App\Models\OrderProducts::where('order_id', $orders->id)->get();
                    foreach ($order_products as $order_product) {
                        $items[$i]['itemId'] = (String) $order_product->id;
                        $unserialize = unserialize($order_product->details);
                        $items[$i]['itemName'] = $unserialize['title'];
                        $order_status = \App\Models\OrderStatus::where('id', $orders->order_status_id)->first();
                        $items[$i]['status'] = $order_status->status;
                        $items[$i]['slug'] = $order_status->slug;
                        $images = \App\Models\Image::where('imageable_id', $order_product->product_id)->where('image_type',
                        "product")->where('is_default','yes')->whereNull('deleted_at')->first();
                        if(!empty($unserialize['image']))
                        {
                            $items[$i]['image'] = $unserialize['image'];
                        }
                        elseif($images)
                        {
                            $items[$i]['image'] = $baseUrl.'/public'.$images->upload_path.$images->name;
                        }
                        else
                        {
                            $items[$i]['image'] = $baseUrl.'/public/assets/images/no_image.png';
                        }
                        // $price_sum[$i] = $order_product->price;
                        // $price_sum[$i] = number_format(str_replace(',', '', $order_product->price), 2);
                        $price_sum[$i] = str_replace(',', '', number_format(str_replace(',', '', $order_product->price), $decimalNumber, $decimalSeparator, $thousandSeparator));
                        $items[$i]['price'] = $ordersDetailsLabels['BHD']." ".number_format($order_product->price,$decimalNumber, $decimalSeparator, $thousandSeparator);
                        $items[$i]['type'] = "7";
                        $items[$i]['qty'] = (String) $order_product->quantity;
                        $items[$i]['isShowTrack'] = (String) $order_status->is_showtrack;
                        $items[$i]['navigationFlag'] = "1";
                        if(!empty($unserialize['attributes']))
                        {
                            $varients = [];
                            $j = 0;
                            foreach ($unserialize['attributes'] as $key => $attributes) {
                                $varients[$j]['title'] = $key;
                                $varients[$j]['value'] = $attributes;
                                $j++;
                            }
                            $items[$i]['variant'] = $varients;
                        }
                        else{
                            $items[$i]['variant'] = [];
                        }
                        $items[$i]['query'] = url('customer/orderdetails')."?order_id=".$orders->id."&item_id=".$order_product->id."&language_id=".$lang_id;
                        $i++;
                        $item_counts = $i;
                    }
                    $orders_arr['items'] = $items;
                }

                //Address Component
                $address_arr = [
                'fullName' => $orders->s_fullname,
                'addressLine1' => $orders->s_address_line_1,
                'addressLine2' => $orders->s_address_line_2,
                'state' => $orders->s_state,
                'city' => $orders->s_city,
                'country' => $orders->s_country,
                'postCode' => $orders->s_pincode,
                'addressType' => $orders->s_address_type,
                'phone' => $orders->s_phone1];

                //List Data
                $listData = [
                    array('leftText' => $ordersDetailsLabels["PRICE"]."(".$item_counts." ".$ordersDetailsLabels["ITEMS"].")",
                    'rightText' => $ordersDetailsLabels["BHD"]." ".number_format(array_sum($price_sum), $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $ordersDetailsLabels["DISCOUNT"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format($orders->discount_amount, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $ordersDetailsLabels["NET"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format(array_sum($price_sum)- $orders->discount_amount, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $ordersDetailsLabels["VAT"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format($orders->tax_amount,$decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $ordersDetailsLabels["SHIPPING"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format($orders->total_shipping_cost, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                ];

                //Price Details Component
                $price_details_arr = [
                'payableAmount' => $ordersDetailsLabels["BHD"]." ".number_format($orders->total, $decimalNumber, $decimalSeparator, $thousandSeparator),
                'paymentMode' => $ordersDetailsLabels["PAYMENTMODE"].": ".$orders->payment_method,
                'listData' => $listData,
                ];

                return view('frontend.my-order-details', compact('baseUrl','pageName','orders_arr',
                'projectName','megamenuFileName','mobileMegamenuFileName','ordersDetailsLabels',
                'address_arr','price_details_arr','orderId','orderIdText','orders_status','orderDate'
                ,'decimalNumber', 'decimalSeparator','thousandSeparator','timezone'));
            }
            else
            {
                return view('frontend.my-order-details', compact('baseUrl','pageName','orders_arr',
                'projectName','megamenuFileName','mobileMegamenuFileName','ordersDetailsLabels',
                'decimalNumber', 'decimalSeparator','thousandSeparator','timezone'));
            }

        } catch (\Exception $e) {
            return view('errors.500');
        }
    }

    public function showTrackOrdersFrom()
    {
        $langId = Session::get('language_id');
        $codes = ['APPNAME','ORDERID', 'TRACKORDER', 'EMAIL', 'TRACKINGINFO','PRODUCTNAME','QTY','CONTACTUSPAGELABEL3','ORDERIDREQD','EMAILREQ','STATUS'];
        $trackOrderLabels = getCodesMsg($langId, $codes);

        $pageName = $trackOrderLabels['TRACKORDER'];
        $projectName = $trackOrderLabels['APPNAME'];

        $baseUrl = $this->getBaseUrl();

        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $setSessionforLang = setSessionforLang($defaultLanguageData['language_id']);
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        return view('frontend.orders.trackOrder',compact('pageName','projectName','baseUrl','trackOrderLabels','megamenuFileName','mobileMegamenuFileName'));
    }
    public function getTrackOrdersDetails(Request $request)
    {
        $langId = session('language_id');

        $codes = ['TRACKORDERNOTFOUND'];
        $enqSubmitMsg = getCodesMsg($langId, $codes);

        $errorMsg = $enqSubmitMsg['TRACKORDERNOTFOUND'];

        $checkOrderExists = Orders::where('order_id',$request->orderId)
                            ->where('email',$request->email)
                            ->first();

        if(!empty($checkOrderExists))
        {
            $orderDetails = \App\Models\OrderProducts::where('order_id', $checkOrderExists->id)->first();
            $orderData = unserialize($orderDetails->details);
            $orderStatus = \App\Models\OrderStatus::where('id', $orderDetails->order_status_id)->first();

            $productDetails = [];
            $productDetails['prodName'] = $orderData['title'];
            $productDetails['status'] = $orderStatus->status;
            $productDetails['quantity'] = $orderDetails->quantity;

            if(!empty($orderData['attributes']))
            {
                $varients = [];
                $j = 0;
                foreach ($orderData['attributes'] as $key => $attributes)
                {
                    $varients[$j]['title'] = $key;
                    $varients[$j++]['value'] = $attributes;
                }
                $productDetails['variants'] = $varients;
            }
            else
            {
                $productDetails['variants'] = [];
            }

            return response()->json(['status' => true,'productDetails' => $productDetails]);
        }

        else
        {
            return response()->json(['status' => false,'msg'=> $errorMsg]);
        }
    }

    public function orderConfiremed()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $defaultLanguageId = $defaultLanguageData['id'];
        $setSessionforLang=setSessionforLang($defaultLanguageId);

        $defaultCurrData = \App\Models\GlobalCurrency::with('currency')->where('is_default',1)->first();
        $defaultCurrId = $defaultCurrData['id'];
        $setSessionforCurr = setSessionforCurr($defaultCurrId);

        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

        $lang_id = Session::get('language_id');
        $language_found = isLanguageExists($lang_id);
        if($language_found == 'false')
        {
            $defaultLang = $this->getDefaultLanguage();
            $lang_id = $defaultLang;
        }

        $baseUrl = $this->getBaseUrl();
        $codes = ['APPNAME','ORDERCONFIRMATION', 'ORDERCONFIRMATIONMSG', 'CONTINUESHOPPING','ORDERPLACEDSUCC',
        'ORDERID','ORDERCONFIRMFAILEDMSG'];
        $orderCnfLabels = getCodesMsg($lang_id, $codes);
        $pageName = $orderCnfLabels["ORDERCONFIRMATION"];
        $projectName = $orderCnfLabels["APPNAME"];

        $display_merchant_order_id = Session::get('display_merchant_order_id');
        Session::forget(['display_merchant_order_id']);

        return view('frontend.order-confirmation',compact('display_merchant_order_id','orderCnfLabels','baseUrl'
        ,'pageName', 'projectName','mobileMegamenuFileName', 'megamenuFileName'));
    }
}
