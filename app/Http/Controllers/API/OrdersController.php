<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Exception;
use App\Models\OrderActivity;
use App\Models\OrderInvoices;
use App\Models\Orders;
use App\Models\GlobalCurrency;
use App\Models\OrderProducts;
use App\Models\StoreLocation;
use PDF;
use DB;
use Carbon\Carbon;

class OrdersController extends Controller
{
    protected $OrderActivity;
    public function __construct(OrderActivity $OrderActivity)
    {
        $this->OrderActivity = $OrderActivity;
    }

    public function getMyOrders(Request $request)
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

            //Localization
            $codes = ['OK','SUCCESS','SUCCESSSTATUS','ORDERID','ITEMS','BILLAMOUNT','BHD',
            "PAGENO","PAGESIZE"];
            $ordersLabels = getCodesMsg($lang_id, $codes);

            $msg = [
                'pageNo.required' => $ordersLabels["PAGENO"],
                'pageSize.required' => $ordersLabels["PAGESIZE"],
            ];

            $validator = Validator::make($request->all(), [
                'pageNo' => 'required',
                'pageSize' => 'required',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                    'statusCode' => 300,
                    'message' => $validator->errors(),
                ], 300);
            }

            // Number Format Decimal Point
            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            //Get User Id
            $customer_id = Auth::guard('api')->user()->token()->user_id;
            $order_data = \App\Models\Orders::where('user_id', $customer_id)
            ->where('order_status_id', '!=', 45)->orderBy('created_at', 'desc')->paginate($request->pageSize,$columns = ['*'],$pageName = 'page',$request->pageNo);
            $customer_timezone = \App\Models\CustomerTimezone::where('customer_id', $customer_id)->pluck('timezone')->first();
            if(count($order_data) > 0)
            {
                $k = 0;
                $orders_arr = [];
                foreach ($order_data as $orders) {
                    //Orders Data
                    $orders_arr[$k]['orderId'] = (String) $orders->id;
                    $orders_arr[$k]['orderIdText'] = $ordersLabels["ORDERID"].":".$orders->order_id;

                    $order_products = \App\Models\OrderProducts::where('order_id', $orders->id)->get();
                    $orders_arr[$k]['subText'] = "(".count($order_products)." ".$ordersLabels["ITEMS"].")"." "
                    .$ordersLabels["BILLAMOUNT"].": ".$ordersLabels['BHD']." ".number_format($orders->total, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    $orders_arr[$k]['orderDate'] = $orders->created_at->setTimezone($customer_timezone)->format('d F, Y H:i');
                    $orders_arr[$k]['Orderdetails'] = url('/api/v1/orderdetails')."?order_id=".$orders->id."&language_id=".$lang_id;

                    //Items Data
                    $items = [];
                    $i = 0;
                    foreach ($order_products as $order_product) {
                        $items[$i]['itemId'] = (String) $order_product->id;
                        $unserialize = unserialize($order_product->details);
                        $items[$i]['itemName'] = $unserialize['title'];
                        $order_status = \App\Models\OrderStatus::where('id', $orders->order_status_id)->first();
                        $items[$i]['status'] = $order_status->status;
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
                        $items[$i]['query'] = url('/api/v1/orderdetails')."?order_id=".$orders->id."&item_id=".$order_product->id."&language_id=".$lang_id;
                        $i++;
                    }
                    $orders_arr[$k]['items'] = $items;
                    $k++;
                }

                //Component Data
                $componentData = [];
                $componentData['componentId'] = "myOrders";
                $componentData['sequenceId'] = "1";
                $componentData['isActive'] = "1";
                $componentData['myOrdersData'] = $orders_arr;
            }
            else{
                //Component Data
                $componentData = [];
                $componentData['componentId'] = "myOrders";
                $componentData['sequenceId'] = "1";
                $componentData['isActive'] = "1";
                $componentData['myOrdersData'] = [];
            }

            $result['status'] = $ordersLabels["OK"];
            $result['statusCode'] = "200";
            $result['id'] = (String) $customer_id;
            $result['langId'] = (String) $lang_id;
            $result['message'] = $ordersLabels["SUCCESS"];
            $result['component'][] = $componentData;
            return response()->json($result);
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function getMyOrderDetails(Request $request)
    {
        try {
            $lang_id = $request->get('language_id') ? $request->get('language_id') : "";
            $item_id = $request->get('item_id') ? $request->get('item_id') : "";
            $order_id = $request->get('order_id') ? $request->get('order_id') : "";
            $baseUrl = $this->getBaseUrl();

            //Localization
            $codes = ['OK','SUCCESS','SUCCESSSTATUS','ORDERID','ITEMS','BILLAMOUNT','BHD',
            'SHIPPINGDETAILS','PRICEDETAILS','AMOUNTPAYABLE','PAYMENTMODE', 'DISCOUNT',
            'SHIPPING','PRICE','VAT','NET'];
            $ordersDetailsLabels = getCodesMsg($lang_id, $codes);

            // Number Format Decimal Point
            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            //Get User Id
            $customer_id = Auth::guard('api')->user()->token()->user_id;
            $orders = \App\Models\Orders::where('user_id', $customer_id)->where('order_status_id', '!=', 45)
            ->where('id', $order_id)->first();
            $customer_timezone = \App\Models\CustomerTimezone::where('customer_id', $customer_id)->pluck('timezone')->first();
            if(!empty($orders))
            {
                $i = 0;
                $orders_arr = [];

                //Orders Data
                $orders_arr['orderId'] = (String) $order_id;
                $orders_arr['orderIdText'] = $ordersDetailsLabels["ORDERID"].":".$orders->order_id;
                $order_product = \App\Models\OrderProducts::where('order_id', $orders->id)->first();
                $order_status = \App\Models\OrderStatus::where('id', $orders->order_status_id)->first();
                $orders_arr['status'] = $order_status->status;
                $orders_arr['orderDate'] = $orders->created_at->setTimezone($customer_timezone)->format('d F, Y H:i');

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
                        $price_sum[$i] = str_replace(',', '', number_format(str_replace(',', '', $order_product->price), $decimalNumber, $decimalSeparator, $thousandSeparator));
                        $items[$i]['price'] = $ordersDetailsLabels['BHD']." ".number_format($order_product->price, $decimalNumber, $decimalSeparator, $thousandSeparator);
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
                        $items[$i]['query'] = url('/api/v1/orderdetails')."?order_id=".$orders->id."&item_id=".$order_product->id."&language_id=".$lang_id;
                        $i++;
                        $item_counts = $i;
                    }
                    $orders_arr['items'] = $items;
                }
                else
                {
                    $order_product = \App\Models\OrderProducts::where('order_id', $orders->id)->where('id', $item_id)->first();
                    $items['itemId'] = (String) $order_product->id;
                    $unserialize = unserialize($order_product->details);
                    $items['itemName'] = $unserialize['title'];
                    $order_status = \App\Models\OrderStatus::where('id', $orders->order_status_id)->first();
                    $items['status'] = $order_status->status;
                    $images = \App\Models\Image::where('imageable_id', $order_product->product_id)->where('image_type',
                    "product")->where('is_default','yes')->whereNull('deleted_at')->first();
                    if(!empty($unserialize['image']))
                    {
                        $items['image'] = $unserialize['image'];
                    }
                    elseif($images)
                    {
                        $items['image'] = $baseUrl.'/public'.$images->upload_path.$images->name;
                    }
                    else
                    {
                        $items['image'] = "";
                    }
                    // $price_sum[] = $order_product->price;
                    $price_sum[] = str_replace(',', '', number_format(str_replace(',', '', $order_product->price), $decimalNumber, $decimalSeparator, $thousandSeparator));
                    $items['price'] = $ordersDetailsLabels['BHD']." ".number_format($order_product->price, $decimalNumber, $decimalSeparator, $thousandSeparator);
                    $items['type'] = "7";
                    $items['qty'] = (String) $order_product->quantity;
                    $items['isShowTrack'] = (String) $order_status->is_showtrack;
                    $items['navigationFlag'] = "1";
                    if(!empty($unserialize['attributes']))
                    {
                        $varients = [];
                        foreach ($unserialize['attributes'] as $key => $attributes) {
                            $varients['title'] = $key;
                            $varients['value'] = $attributes;
                        }
                        $items['variant'][] = $varients;
                    }
                    else{
                        $items['variant'] = [];
                    }
                    $items['query'] = url('/api/v1/orderdetails')."?order_id=".$orders->id."&item_id=".$order_product->id."&language_id=".$lang_id;
                    $orders_arr['items'][] = $items;
                    $item_counts = 1;
                }

                //Address Component
                $address = [];
                $address['componentId'] = "address";
                $address['sequenceId'] = "1";
                $address['isActive'] = "1";
                $address['isFromMyorders'] = "1";
                $address['componentTitle'] = $ordersDetailsLabels["SHIPPINGDETAILS"];

                $address_arr = [];
                $address_arr['fullName'] = $orders->s_fullname;
                $address_arr['addressLine1'] = $orders->s_address_line_1;
                $address_arr['addressLine2'] = $orders->s_address_line_2;
                $address_arr['state'] = $orders->s_state;
                $address_arr['city'] = $orders->s_city;
                $address_arr['country'] = $orders->s_country;
                $address_arr['postCode'] = $orders->s_pincode;
                $address_arr['addressType'] = $orders->s_address_type;
                $address_arr['phone'] = $orders->s_phone1;
                $address['addressData']['address'][] = $address_arr;

                //Price Details Component
                $price_details = [];
                $price_details['componentId'] = "priceDetails";
                $price_details['sequenceId'] = "1";
                $price_details['isActive'] = "1";

                $price_details_arr = [];
                $price_details_arr['title'] = $ordersDetailsLabels["PRICEDETAILS"];
                $price_details_arr['payableAmountLable'] = $ordersDetailsLabels["AMOUNTPAYABLE"];
                $price_details_arr['payableAmount'] = $ordersDetailsLabels["BHD"]." ".number_format($orders->total, $decimalNumber, $decimalSeparator, $thousandSeparator);
                $price_details_arr['paymentMode'] = $ordersDetailsLabels["PAYMENTMODE"].": ".$orders->payment_mode;

                //List Data
                $listData = [
                    array('leftText' => $ordersDetailsLabels["PRICE"]."(".$item_counts." ".$ordersDetailsLabels["ITEMS"].")",
                    'rightText' => $ordersDetailsLabels["BHD"]." ".array_sum($price_sum)),
                    array('leftText' => $ordersDetailsLabels["DISCOUNT"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format($orders->discount_amount, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $ordersDetailsLabels["NET"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format(array_sum($price_sum) - $orders->discount_amount, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $ordersDetailsLabels["VAT"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format($orders->tax_amount,$decimalNumber, $decimalSeparator, $thousandSeparator)),
                    array('leftText' => $ordersDetailsLabels["SHIPPING"], 'rightText' => $ordersDetailsLabels["BHD"]." ".number_format($orders->total_shipping_cost, $decimalNumber, $decimalSeparator, $thousandSeparator)),
                ];
                $price_details_arr['listData'] = $listData;

                $price_details['priceDetailsData'] = $price_details_arr;

                //Component Data
                $componentData = [];
                $componentData['componentId'] = "myOrders";
                $componentData['sequenceId'] = "1";
                $componentData['isActive'] = "1";
                $componentData['myOrdersData'][] = $orders_arr;

                $result['status'] = $ordersDetailsLabels["OK"];
                $result['statusCode'] = '200';
                $result['component'][] = $componentData;
                $result['component'][] = $address;
                $result['component'][] = $price_details;
            }else{

                //Component Data
                $componentData = [];
                $componentData['componentId'] = "myOrders";
                $componentData['sequenceId'] = "1";
                $componentData['isActive'] = "1";
                $componentData['myOrdersData'] = [];

                $result['status'] = $ordersDetailsLabels["OK"];
                $result['statusCode'] = '200';
                $result['component'][] = $componentData;
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function getTrackItem(Request $request)
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

            //Localization
            $codes = ['SUCCESS','SUCCESSSTATUS','ORDERIDREQ','ORDERIDNUM','ITEMIDREQ',
            'ITEMIDNUM','ORDERID','TRACKINGNUMBER','ORDERNOTFOUND','PRODUCTNOTFOUND','TRACKRECORDNOTFOUND'];
            $trackItemLabels = getCodesMsg($lang_id, $codes);

            $msg = [
                'order_id.required' => $trackItemLabels["ORDERIDREQ"],
                'order_id.numeric' => $trackItemLabels["ORDERIDNUM"],
                'item_id.required' => $trackItemLabels["ITEMIDREQ"],
                'item_id.numeric' => $trackItemLabels["ITEMIDNUM"],
            ];

            $validator = Validator::make($request->all(), [
                'order_id' => 'required|numeric',
                'item_id' => 'required|numeric',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => "300",
                'message' => $validator->errors(),
                ],300);
            }

            $customer_id = Auth::guard('api')->user()->token()->user_id;
            $order = \App\Models\Orders::where('id', $request->order_id)->where('user_id', $customer_id)->first();
            if($order)
            {
                $order_product = \App\Models\OrderProducts::where('id', $request->item_id)->where('order_id', $request->order_id)->first();
                if(empty($order_product))
                {
                    $result['status'] = "300";
                    $result['message'] = $trackItemLabels["PRODUCTNOTFOUND"];
                    return response()->json($result, 300);
                }

                $result['status'] = $trackItemLabels["SUCCESS"];
                $result['statusCode'] = "200";
                $result['orderId'] = $trackItemLabels["ORDERID"].":".$order->order_id;
                $result['trackingNumber'] = $trackItemLabels["TRACKINGNUMBER"].":".$order_product->tracking_number;
                $result['shipBy'] = ($order_product->carrier != '') ? $order_product->carrier : "";

                //Order Tracking
                $orderItemsTracking = \App\Models\OrderItemsTracking::select('order_items_tracking.created_at',
                'order_status.status','order_status.is_showtrack')
                ->leftJoin('order_status','order_status.id','=','order_items_tracking.order_status_id')
                ->where('order_items_tracking.order_product_id', $request->item_id)
                ->orderBy('order_items_tracking.created_at', 'desc')
                ->get();
                if(!empty($orderItemsTracking))
                {
                    $tack_items_arr = [];
                    $i = 0;
                    foreach ($orderItemsTracking as $trackRecord) {
                        $tack_items_arr[$i]['title'] = $trackRecord->status;
                        $tack_items_arr[$i]['dateTime'] = date('j M Y', strtotime($trackRecord->created_at));
                        $tack_items_arr[$i]['trackStatus'] = (String) $trackRecord->is_showtrack;
                        $i++;
                    }
                }

                //Address
                $address_arr = [];
                $address_arr['fullName'] = $order->s_fullname;
                $address_arr['addressLine1'] = $order->s_address_line_1;
                $address_arr['addressLine1'] = $order->s_address_line_2;
                $address_arr['state'] = $order->s_state;
                $address_arr['city'] = $order->s_city;
                $address_arr['country'] = $order->s_country;
                $address_arr['postCode'] = $order->s_pincode;
                $address_arr['addressType'] = $order->s_address_type;
                $address_arr['phone'] = $order->s_phone1;

                $result['track'] = (!empty($tack_items_arr)) ? $tack_items_arr : $trackItemLabels["TRACKRECORDNOTFOUND"];
                $result['address'] = $address_arr;
                return response()->json($result);
            }
            else
            {
                $result['status'] = "300";
                $result['message'] = $trackItemLabels["ORDERNOTFOUND"];
                return response()->json($result, 300);
            }
        } catch (\Exception $e) {
            return handleServerError($lang_id);
        }
    }

    public function createOrder(Request $request)
    {
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

        //Localization
        $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM","ITEMLESSORNOTAVAILABLE","CARTDATANOTFOUND"];
        $ordersLabels = getCodesMsg($lang_id, $codes);

        $msg = [
            'cart_master_id.required' => $ordersLabels["MASTERCARTIDREQ"],
            'cart_master_id.numeric' => $ordersLabels["MASTERCARTIDNUM"],
        ];

        $validator = Validator::make($request->all(), [
            'cart_master_id' => 'required|numeric',
        ],$msg);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
            ], 300);
        }

        $cart_master_id = $request->cart_master_id;
        //Display less or not available products Start
        $arrayNotAvailableProducts = [];
        $arrayLessAvailableProducts = [];
        $product_list = [];
        $flagAvailable = true;
        $i = 0;
        $k = 0;
        $cart = \App\Models\Cart::where('cart_master_id', $cart_master_id)->get();
        if(count($cart) == 0)
        {
            $result['statusCode'] = "300";
            $result['message'] = $ordersLabels["CARTDATANOTFOUND"];
            return response()->json($result);
        }

        $language_id =$lang_id;
        foreach ($cart as $item) {
            $product = \App\Models\Product::where('id', $item->product_id)->whereNull('deleted_at')->first();
            if($product)
            {
                $category = \App\Models\Category::where('id', $product->category_id)->whereNull('deleted_at')->first();
                if($category->photo_upload == 0)
                {
                    $product_pricing = \App\Models\ProductPricing::where('id', $item->option_id)
                    ->where('product_id', $item->product_id)->whereNull('deleted_at')->first();

                    //Get product details
                    $product_detail = \App\Models\ProductDetails::where('product_id', $item->product_id)
                    ->where('language_id', $language_id)->whereNull('deleted_at')->first();
                    if($product_pricing->quantity <= 0)
                    {
                        $arrayNotAvailableProducts[$i]['not_avlb_product_name'] = ($product_detail) ? $product_detail->title : "";
                        $i++;
                        $flagAvailable = false;
                    }
                    elseif($item->quantity > $product_pricing->quantity)
                    {
                        $arrayLessAvailableProducts[$k]['less_avlb_product_name'] = ($product_detail) ? $product_detail->title : "";
                        $arrayLessAvailableProducts[$k]['qty'] = ($product_pricing) ? $product_pricing->quantity : "";
                        $k++;
                        $flagAvailable = false;
                    }
                }
            }
        }

        if($flagAvailable === false)
        {
            $prod_err_msg = [];
            foreach ($arrayNotAvailableProducts as $key => $value) {
                array_push($prod_err_msg, $arrayNotAvailableProducts[$key]['not_avlb_product_name']." (Out Of Stock)");
            }
            foreach ($arrayLessAvailableProducts as $key => $value) {
                array_push($prod_err_msg, $arrayLessAvailableProducts[$key]['less_avlb_product_name']." (Qty : ".$arrayLessAvailableProducts[$key]['qty']." Available)");
            }
            $result['statusCode'] = "300";
            $result['message'] = $ordersLabels["ITEMLESSORNOTAVAILABLE"]." ".implode(', ', $prod_err_msg);
            return response()->json($result);
        }
        //Display less or not available products Over

        $response = create_order($cart_master_id, $lang_id);

        $order_id = $response['order_id'];
        $merchant_order_id = $response['merchant_order_id'];

        $cart_master = \App\Models\CartMaster::where('id', $cart_master_id)->first();
        if($cart_master->payment_method == 1)
        {
            $decode = create_credimax_session($order_id, $merchant_order_id);
            $response = json_decode($decode, true);
            if($response['error'] == 0)
            {
                $session_id = $response['session_id'];

                // paymentURL
                $paymentURL = (!empty($session_id)) ? "https://credimax.gateway.mastercard.com/checkout/pay/".$session_id : '';

                $result['statusCode'] = "200";
                $result['order_id'] = $order_id;
                $result['merchant_order_id'] = $merchant_order_id;
                $result['paymentURL'] = $paymentURL;
                $result['message'] = '';
                $result["cartCount"] = (count($cart) > 0) ? (string) count($cart) : "0";
                return response()->json($result);
            }
            else
            {
                $msg = $response['msg'];
                $result['statusCode'] = "300";
                $result['order_id'] = $order_id;
                $result['merchant_order_id'] = $merchant_order_id;
                $result['paymentURL'] = '';
                $result['message'] = $msg;
                $result["cartCount"] = (count($cart) > 0) ? (string) count($cart) : "0";
                return response()->json($result, 300);
            }
        }

        if($cart_master->payment_method == 2)
        {
            $order = \App\Models\Orders::where('id', $order_id)->first();
            $baseUrl = $this->getBaseUrl();

            $merchantOrderId = strtr(base64_encode($response['merchant_order_id']), '+/=', '-_,');
            $amount = strtr(base64_encode($order->total), '+/=', '-_,');

            $result['statusCode'] = "200";
            $result['order_id'] = (string) $order_id;
            $result['merchant_order_id'] = $merchant_order_id;
            $result['paymentURL'] = $baseUrl.'/benefits/request_order.php?merchantOrderId='.$merchantOrderId.'&mobile=1&amount='.$amount;
            $result['message'] = '';
            $result["cartCount"] = (count($cart) > 0) ? (string) count($cart) : "0";
            return response()->json($result);
        }
    }

    public function generateOrderInvoice(Request $request)
    {
        // dd($request->all());
        $customerId = Auth::guard('api')->user()->token()->user_id;
        // dd($customerId);
        $lastInvoiceDetails = OrderInvoices::select('invoice_id')->orderBy('id','desc')->first();

        if(isset($lastInvoiceDetails))
        {
            $res = preg_replace("/[^0-9]/", "", $lastInvoiceDetails->invoice_id );
            $invoiceId = 'ALBINV'.(str_pad((int)$res + 1, 5, '0', STR_PAD_LEFT));
        }
        else
            $invoiceId = 'ALBINV00001';

        $getInvoiceId = Orders::where('id',$request['orderId'])->pluck('invoice_id')->first();

        if($getInvoiceId == 0)
        {
            $orderInvoice = new OrderInvoices;
            $orderInvoice->invoice_id = $invoiceId;
            $orderInvoice->order_id = $request['orderId'];
            $orderInvoice->created_by = $customerId;

            if($request->orderStatus == "Cancelled")
                $orderInvoice->invoice_status = 3;

            if($request->orderStatus == "Order Received" || $request->orderStatus == "Shipped" || $request->orderStatus == "Delivered")
                $orderInvoice->invoice_status = 1;

            else
                $orderInvoice->invoice_status = 2;
            $orderInvoice->save();

        }
        else
        {
            $orderInvoice = OrderInvoices::select('id')->where('order_id',$request['orderId'])->first();
            // dd($orderInvoice);
        }

            Orders::where('id',$request['orderId'])->update(['invoice_id'=> $orderInvoice->id]);

            //Update Order Activity Table
            $msg = "Order invoice was generated.";
            $this->updateOrderActivity($request['orderId'], $msg,$customerId);

            $baseUrl = $this->getBaseUrl();
            $defaultCurrency = GlobalCurrency::select('currency.currency_code')
                                        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
                                        ->where('global_currency.is_default', 1)->first();

            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            DB::statement(DB::raw('set @rownum=0'));

            $orderDetails = OrderInvoices::select('order_invoices.invoice_id','order_invoices.created_at as invoiceDate','orders.id as ordPriId','orders.order_id','orders.created_at as orderDate','payment_mode','total','payment_method','payment_id',
                                    'os.status','subtotal','discount_amount','total_shipping_cost','tax_amount','b_address_type','s_address_type',
                                    'b_fullname','b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','b_phone1',
                                    's_fullname','s_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','s_phone1',
                                    'first_name','last_name','first_name','email','promotions','shipping_method','shipping_type','store_location_id')
                                    ->join('orders','orders.id','order_invoices.order_id')
                                    ->join('order_status as os','os.id','orders.order_status_id')
                                    ->where('order_invoices.id',$orderInvoice->id)
                                    ->first();

            $timezone = \App\Models\CustomerTimezone::where('customer_id', $customerId)->pluck('zone')->first();
            if($timezone == null)
            {
                $timezone = "+03:00";
            }
            $OrdereDate=convertTimeToTz($orderDetails->invoiceDate,$timezone);
            $orderDetails['invoiceDate']=$OrdereDate;

            $orderProducts = OrderProducts::select('carrier','tracking_number','quantity','price','os.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                                    'details','promo_code','lady_operator','gift_wrap','gift_message','message')
                                    ->join('order_status as os','os.id','order_products.order_status_id')
                                    ->where('order_products.order_id',$orderDetails->ordPriId)
                                    ->get();

            $orderProductDetails = $prod = [];
            $j = $i = 0;
            foreach($orderProducts as $orderProd)
            {
                $orderProductDetails[$i]['srNo'] = $orderProd->rownum;
                $orderProductDetails[$i]['invoice_id'] = $orderProd->invoice_id;
                $orderProductDetails[$i]['invoiceDate'] = $orderProd->invoiceDate;
                $orderProductDetails[$i]['carrier'] = $orderProd->carrier;
                $orderProductDetails[$i]['tracking_number'] = $orderProd->tracking_number;
                $orderProductDetails[$i]['quantity'] = $orderProd->quantity;
                $orderProductDetails[$i]['price'] = number_format($orderProd->price, $decimalNumber, $decimalSeparator, $thousandSeparator);
                $orderProductDetails[$i]['status'] = $orderProd->status;
                $orderProductDetails[$i]['promo_code'] = $orderProd->promo_code;
                $orderProductDetails[$i]['lady_operator'] = $orderProd->lady_operator;
                $orderProductDetails[$i]['gift_wrap'] = $orderProd->gift_wrap;
                $orderProductDetails[$i]['gift_message'] = $orderProd->gift_message;
                $orderProductDetails[$i]['message'] = $orderProd->message;

                $data = preg_replace_callback(
                    '!s:(\d+):"(.*?)";!',
                    function($m) {
                        return 's:'.strlen($m[2]).':"'.$m[2].'";';
                    },
                    $orderProd['details']);
                $orderData = unserialize($data);

                if(!empty($orderData['attributes']))
                {
                    $varients = [];
                    $j = 0;
                    foreach ($orderData['attributes'] as $key => $attributes)
                    {
                        $varients[$j]['title'] = $key;
                        $varients[$j++]['value'] = $attributes;
                    }
                }
                else
                {
                    $varients[$j++] = [];
                }
                $orderProductDetails[$i]['product_name'] = $orderData['title'];

                $orderProductDetails[$i++]['variants'] = $varients;
            }

            $storePickupAddress = StoreLocation::where('id',$orderDetails->store_location_id)->first();

            view()->share(['orderDetails'=>$orderDetails,'orderProductDetails'=>$orderProductDetails,'storePickupAddress'=>$storePickupAddress,
            'decimalNumber'=>$decimalNumber,'decimalSeparator'=>$decimalSeparator,'thousandSeparator'=>$thousandSeparator,'defaultCurrency'=>$defaultCurrency,'baseUrl'=>$baseUrl]);
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true ,'chroot' => public_path()])->loadView('admin/orderInvoices/invoicePdf',$orderDetails);
            $filename = rand(10, 100) . '.pdf';

            $pdfPath = Config('app.invoicePdfPath');
            $currentTimestamp = date('His',strtotime(Carbon::now()));

            file_put_contents($pdfPath."/invoice_pdf_".$request['orderId']."_".$currentTimestamp.".pdf", $pdf->output());
            $invoiceUrl = $baseUrl.'/'.$pdfPath."/invoice_pdf_".$request['orderId']."_".$currentTimestamp.".pdf";
            return response()->json(['status' => "OK","statusCode" => 200,'message' => "Invoice genenrated successfully",'invoiceUrl' => $invoiceUrl]);

    }
    public function updateOrderActivity($order_id, $msg,$customerId)
    {
        //Update Order Activity Table
        $order_activity = new \App\Models\OrderActivity;
        $order_activity->order_id = $order_id;
        $order_activity->activity = $msg;
        $order_activity->created_by = $customerId;
        if($order_activity->save())
            return true;
        else
            return false;

    }
}
