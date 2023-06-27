<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\GlobalCurrency;
use App\Models\OrderStatus;
use App\Models\OrderProducts;
use App\Models\OrderItemsTracking;
use App\Models\StoreLocation;
use App\Models\CustomerGroups;
use App\Models\OrderInvoices;
use App\Models\OrderNotes;
use App\Models\Image;
use App\Models\Notifications;
use App\Models\ProductPricing;
use App\Models\Product;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;
use PDF;
use App\Traits\ExportTrait;
use File;
use ZipArchive;
use Mail;
use Config;

class OrdersController extends Controller
{
    use CommonTrait,ExportTrait;
    public function getAllOrders()
    {
        $baseUrl = $this->getBaseUrl();
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();
        $orderStatus = OrderStatus::where('status_type',1)->get();

        $custGroups = CustomerGroups::select('id','group_name')->whereNull('deleted_at')->get();
        return view('admin.orders.index',compact('baseUrl','defaultCurrency','orderStatus','custGroups'));
    }

    public function getAllOrdersList(Request $request)
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;
        DB::enableQueryLog();
        $orders = Orders::select('orders.id','orders.order_id','orders.created_at as orderCreateddate','orders.payment_method',
                            'orders.total','orders.subtotal','orders.tax_amount','orders.total_shipping_cost','os.slug','label_url',
                            'os.status','orders.first_name','orders.last_name','orders.email as customerEmail','shipping_type','cg.group_name',
                            'b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','shipping_method',
                            's_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','expected_deliverydate','sl.title as storeTitle','sl.address_1 as storeAddr1','sl.address_2 as storeAddr2')
                            ->leftJoin('order_products as op','op.order_id','=','orders.id')
                            ->leftJoin('store_location as sl','sl.id','=','orders.store_location_id')
                            ->leftJoin('customers as c','c.id','=','orders.user_id')
                            ->leftJoin('customer_groups as cg','cg.id','=','c.cust_group_id')
                            ->with('orderProducts')
                            ->join('order_status as os','os.id','orders.order_status_id')
                            ->orderBy('orders.created_at','desc')
                            ->groupBy('orders.order_id');
                            if (!empty($request['orderId']) && isset($request['orderId']) )
                            {
                                $orders = $orders->where('orders.order_id','like', '%' . $request['orderId'] . '%');
                            }
                            if (!empty($request['orderStatus']) && isset($request['orderStatus']))
                            {
                                $orders = $orders->where('os.status',$request['orderStatus']);
                            }
                            if (!empty($request['custName'])  && isset($request['custName']))
                            {
                                $orders = $orders->where(function($q) use($request)
                                {
                                    $names = explode(' ',$request['custName']);
                                    foreach($names as $name)
                                    {
                                        $q->orWhere('orders.first_name', 'like', '%' . trim($name) . '%')
                                        ->orWhere('orders.last_name', 'like', '%' . trim($name) . '%');
                                    }
                                });
                            }
                            if (!empty($request['custEmail']) && isset($request['custEmail']))
                            {
                                $orders = $orders->where('orders.email','like', '%' . $request['custEmail']. '%');
                            }
                            if (!empty($request['paymentMethod']) && isset($request['paymentMethod']))
                            {
                                $orders = $orders->where('orders.payment_method','like','%'. $request['paymentMethod'] . '%');
                            }
                            if($request['startDate'] != "" || $request['endDate'] != "")
                            {
                                $orders = $orders->whereBetween(DB::raw('DATE(orders.created_at)'),[$request['startDate'], $request['endDate']]);
                            }
                            if(!empty($request['custGroup']) && $request['custGroup'] != -1 && $request['custGroup'] != 0)
                            {
                                $orders = $orders->where('cg.id',$request['custGroup']);
                            }
                            if($request['custGroup'] == 0)
                            {
                                $orders = $orders->where('c.cust_group_id',$request['custGroup']);
                            }
                            if(!empty($request['shippingType']) && $request['shippingType'] != 'all' && $request['shippingType'] == 'delivery')
                            {
                                $orders = $orders->where('orders.shipping_type',$request['shippingType']);
                            }
                            if(!empty($request['shippingType']) && $request['shippingType'] != 'all' && $request['shippingType'] == 'store_pickup')
                            {
                                $orders = $orders->where('orders.shipping_type',$request['shippingType']);
                            }
        $orders = $orders->get();
        // dd(DB::getQueryLog());
        // dd($orders);
        $i = 0;
        $allOrders = [];

        foreach($orders as $order)
        {
            $allOrders[$i]['id'] = $order['id'];
            $allOrders[$i]['order_id'] = $order['order_id'];

            $allOrders[$i]['first_name'] = $order['first_name'];
            $allOrders[$i]['last_name'] = $order['last_name'];
            $allOrders[$i]['customerEmail'] = $order['customerEmail'];
            $allOrders[$i]['customer_group'] = isset($order['group_name']) ? $order['group_name'] : 'Not Assigned';

            $allOrders[$i]['b_address_line_1'] = $order['b_address_line_1'];
            $allOrders[$i]['b_address_line_2'] = $order['b_address_line_2'];
            $allOrders[$i]['b_city'] = $order['b_city'];
            $allOrders[$i]['b_state'] = $order['b_state'];
            $allOrders[$i]['b_pincode'] = $order['b_pincode'];
            $allOrders[$i]['b_country'] = $order['b_country'];

            $allOrders[$i]['shipping_method'] = $order['shipping_method'];
            $allOrders[$i]['slug'] = $order['slug'];
            $allOrders[$i]['label_url'] = $order['label_url'];

            if($order['shipping_type'] == 'delivery')
            {
                $allOrders[$i]['s_address_line_1'] = $order['s_address_line_1'];
                $allOrders[$i]['s_address_line_2'] = $order['s_address_line_2'];
                $allOrders[$i]['s_city'] = $order['s_city'];
                $allOrders[$i]['s_state'] = $order['s_state'];
                $allOrders[$i]['s_pincode'] = $order['s_pincode'];
                $allOrders[$i]['s_country'] = $order['s_country'];
            }
            if($order['shipping_type'] == 'store_pickup')
            {
                $allOrders[$i]['s_address_line_1'] = $order['storeTitle'];
                $allOrders[$i]['s_address_line_2'] = $order['storeAddr1'];
                $allOrders[$i]['s_city'] = $order['storeAddr2'];
                $allOrders[$i]['s_state'] = '';
                $allOrders[$i]['s_pincode'] = '';
                $allOrders[$i]['s_country'] = '';
            }

            $allOrders[$i]['payment_method'] = $order['payment_method'];

            $allOrders[$i]['status'] = $order->status;
            $allOrders[$i]['orderCreateddate'] = date('d-M-Y H:i A', strtotime($order->orderCreateddate));
            $allOrders[$i]['payment_method'] = $order->payment_method;
            $allOrders[$i++]['total'] = number_format($order->total, $decimalNumber, $decimalSeparator, $thousandSeparator);
        }
        // die;
        // dd($allOrders);
        return Datatables::of($allOrders)->make(true);
    }

    public function getAllOrdersNotes(Request $request)
    {
      $parent_id = Auth::guard('admin')->user()->id;
      $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
      $orderNotes=OrderNotes::select('order_notes.notes','order_notes.created_at','users.firstname','users.lastname')->join('users','users.id','=','order_notes.created_by')->leftJoin('user_timezone','user_timezone.user_id','=','users.id')->where("order_notes.order_id",$request['orderId'])->orderBy('order_notes.id','DESC')->get();
        // dd(DB::getQueryLog());
        // dd($orders);
        $i = 0;
        $allActivities = [];

        foreach($orderNotes as $activity)
        {
            $allActivities[$i]['rownum'] = $i+1;
            $allActivities[$i]['notes'] = $activity['notes'];

            $allActivities[$i]['createdby'] = $activity['firstname'].' '.$activity['lastname'];
            $allActivities[$i++]['createdat'] = date('Y-m-d H:i:s',strtotime($activity['created_at']));
        }
        // die;
        // dd($allOrders);
        return Datatables::of($allActivities)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    public function getOrderDetails($orderId)
    {
        $baseUrl = $this->getBaseUrl();

        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;

        DB::statement(DB::raw('set @rownum=0'));

        $orderProducts = OrderProducts::select('order_products.id','order_id','carrier','tracking_number','quantity','price','os.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                                        'details','promo_code','lady_operator','gift_wrap','gift_message','message','other_images','product_id','print_files','option_id')
                                        ->join('order_status as os','os.id','order_products.order_status_id')
                                        ->where('order_products.order_id',$orderId)
                                        ->get();

        $orderProductDetails = $prod = [];
        $j = $i = 0;
        foreach($orderProducts as $orderProd)
        {
            $orderProductDetails[$i]['id'] = $orderProd->id;
            $orderProductDetails[$i]['order_id'] = $orderProd->order_id;
            $orderProductDetails[$i]['product_id'] = $orderProd->product_id;
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
            $orderProductDetails[$i]['other_images'] =str_replace(array('[', ']'), array('', ''), explode(',', $orderProd->other_images));
            $orderProductDetails[$i]['print_files'] = $orderProd->print_files;

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
            if(!empty($orderData['image']))
                $orderProductDetails[$i]['prodImage'] = $orderData['image'];
            else
            {
                $prodImage = Image::select('name')
                        ->where('imageable_id',$orderProd['product_id'])
                        ->where('image_type','product')
                        ->where('is_default','yes')
                        ->whereNull('deleted_at')
                        ->first();
                if(!empty($prodImage))
                    $orderProductDetails[$i]['prodImage'] = $baseUrl.'/public/images/product/'.$orderProd['product_id'].'/'.$prodImage['name'];
                else
                    $orderProductDetails[$i]['prodImage'] = '';
            }
            if(!empty($orderData['photobook_caption']))
                $orderProductDetails[$i]['photobook_caption'] = $orderData['photobook_caption'];

            if(!empty($orderProd->option_id))
            {
                $orderProductDetails[$i]['sku'] = '';
                $prodPricingSku = ProductPricing::find($orderProd->option_id);
                if(!empty($prodPricingSku['sku']))
                    $orderProductDetails[$i]['sku'] = $prodPricingSku['sku'];
                else
                {
                    $productSku = Product::find($orderProd->product_id);
                    if(!empty($productSku['sku']))
                        $orderProductDetails[$i]['sku'] = $productSku['sku'];
                }
            }
            $orderProductDetails[$i++]['variants'] = $varients;
        }
        $orderDetails = Orders::select('orders.id','order_id','orders.created_at as orderDate','payment_mode','total','payment_method','payment_id',
                                'os.status','subtotal','discount_amount','total_shipping_cost','tax_amount','b_address_type','s_address_type',
                                'b_fullname','b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','b_phone1',
                                's_fullname','s_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','s_phone1',
                                'orders.first_name','orders.last_name','orders.email','promotions','shipping_method','shipping_type','store_location_id',
                                'cg.group_name','os.slug','label_url','message','loyalty_card','orders.order_status_id')
                                ->join('order_status as os','os.id','orders.order_status_id')
                                ->leftJoin('customers as c','c.id','=','orders.user_id')
                                ->leftJoin('customer_groups as cg','cg.id','=','c.cust_group_id')
                                ->where('orders.id',$orderId)
                                ->first();
        // dd($orderDetails);
        $storePickupAddress = '';
        //To get logged in user timezone
        $parent_id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
        $OrderDate=convertTimeToTz($orderDetails->orderDate,$timezone);
        $orderStatus = OrderStatus::where('status_type',1)->get();
        if(!empty($orderDetails))
            $storePickupAddress = StoreLocation::where('id',$orderDetails->store_location_id)->first();
        return view('admin/orders/view',compact('orderProductDetails','orderDetails','defaultCurrency','baseUrl','decimalNumber','decimalSeparator','thousandSeparator','storePickupAddress','OrderDate','orderStatus'));
    }

    public function updateBillingAddress(Request $request)
    {
        $orderDetails = Orders::find($request->orderId);
        if(!empty($orderDetails))
        {
            $orderDetails->b_fullname = $request->b_fullname;
            $orderDetails->b_address_line_1 = $request->b_address_line_1;
            $orderDetails->b_address_line_2 = $request->b_address_line_2;
            $orderDetails->b_country = $request->b_country;
            $orderDetails->b_state = $request->b_state;
            $orderDetails->b_city = $request->b_city;
            $orderDetails->b_pincode = $request->b_pincode;
            $orderDetails->b_address_type = $request->b_address_type;
            $orderDetails->b_phone1 = $request->b_phone1;
            $orderDetails->b_phone2 = $request->b_phone2;
            if($orderDetails->save())
            {
                $msg = "Billing Address Updated Successfully!";
                $result['status'] = true;
                $result['msg'] = $msg;
                return $result;
            }
            else
            {
                $msg = "Something went wrong !!";
                $result['status'] = false;
                $result['msg'] = $msg;
                return $result;
            }
        }
    }

    public function updateShippingAddress(Request $request)
    {
        $orderDetails = Orders::find($request->orderId);
        if(!empty($orderDetails))
        {
            $orderDetails->s_fullname = $request->s_fullname;
            $orderDetails->s_address_line_1 = $request->s_address_line_1;
            $orderDetails->s_address_line_2 = $request->s_address_line_2;
            $orderDetails->s_country = $request->s_country;
            $orderDetails->s_state = $request->s_state;
            $orderDetails->s_city = $request->s_city;
            $orderDetails->s_pincode = $request->s_pincode;
            $orderDetails->s_address_type = $request->s_address_type;
            $orderDetails->s_phone1 = $request->s_phone1;
            $orderDetails->s_phone2 = $request->s_phone2;
            if($orderDetails->save())
            {
                $msg = "Shipping Address Updated Successfully!";
                $result['status'] = true;
                $result['msg'] = $msg;
                return $result;
            }
            else
            {
                $msg = "Something went wrong !!";
                $result['status'] = false;
                $result['msg'] = $msg;
                return $result;
            }
        }
    }

    public function markOrderAsCancelled(Request $request)
    {
        // dd($request->all());
        $orderCancelStatus = OrderStatus::select('id')
                                    ->where('slug','cancelled')
                                    ->where('status_type',1)
                                    ->first();

        $orderProductCancelStatus = OrderStatus::select('id')
                                    ->where('slug','cancelled')
                                    ->where('status_type',2)
                                    ->first();

        OrderProducts::where('order_id',$request->orderId)
                    ->update(['order_status_id'=>$orderProductCancelStatus->id]);

        Orders::where('id',$request->orderId)
                ->update(['order_status_id'=>$orderCancelStatus->id]);

        $orderProducts = OrderProducts::where('order_id',$request->orderId)->get();

        foreach($orderProducts as $product)
        {
            $orderItemTrack = new OrderItemsTracking;
            $orderItemTrack->order_product_id = $product->id;
            $orderItemTrack->order_status_id = $orderProductCancelStatus->id;
            $orderItemTrack->save();
        }

        // insert into notification table
        $orderDetails = Orders::select('user_id','order_id')->where('id',$request->orderId)->first();
        $notification = new Notifications;
        $notification->user_id = $orderDetails->user_id;
        $notification->notification_type = 'OC';
        $notification->order_id = $request->orderId;
        $notification->order_number = $orderDetails->order_id;
        $notification->read_flag = 0;
        $notification->save();

        if(!empty($request->ajaxReq))
        {
            if($request->ajaxReq == "yes")
            {
                // Send email start
                $order = Orders::where('id', $request->orderId)->first();
                $temp_arr = [];
                $new_user = $this->getEmailTemp();
                foreach($new_user as $code )
                {
                    if($code->code == 'ORDCNLD')
                    {
                        array_push($temp_arr, $code);
                    }
                }

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }

                $replace_data = array(
                    '{{orderid}}' => $order->order_id,
                    '{{firstname}}' => $order->first_name,
                    '{{lastname}}' => $order->last_name,
                    '{{baseUrl}}' => $this->getBaseUrl(),
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);
                $data = [
                    'html' => $html_value,
                ];
                $subject = $temp_arr[0]['subject']." ".$order->order_id;
                $email = $order->email;
                Mail::send('admin.emails.order-cancelled', $data, function ($message) use ($email,$subject) {
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over

                //Update Order Activity Table
                $msg = "Order was marked as Cancelled.";
                $this->updateOrderActivity($request->orderId, $msg);

                $result['status'] = 'true';
                $result['msg'] = 'Order marked as cancelled successfully !!';
                return $result;
            }
        }
        else
        {
            // Send email start
            $order = Orders::where('id', $request->orderId)->first();
            $temp_arr = [];
            $new_user = $this->getEmailTemp();
            foreach($new_user as $code )
            {
                if($code->code == 'ORDCNLD')
                {
                    array_push($temp_arr, $code);
                }
            }

            if(is_array($temp_arr))
            {
                $value = $temp_arr[0]['value'];
            }

            $replace_data = array(
                '{{orderid}}' => $order->order_id,
                '{{firstname}}' => $order->first_name,
                '{{lastname}}' => $order->last_name,
                '{{baseUrl}}' => $this->getBaseUrl(),
            );
            $html_value = $this->replaceHtmlContent($replace_data,$value);
            $data = [
                'html' => $html_value,
            ];
            $subject = $temp_arr[0]['subject']." ".$order->order_id;
            $email = $order->email;
            Mail::send('admin.emails.order-cancelled', $data, function ($message) use ($email,$subject) {
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($email)->subject($subject);
            });
            // Send email over

            //Update Order Activity Table
            $msg = "Order was marked as Cancelled.";
            $this->updateOrderActivity($request->orderId, $msg);

            $notification = array(
                'message' => 'Order marked as cancelled successfully !!',
                'alert-type' => 'success'
            );
            // return redirect('admin/orders')->with($notification);
            return redirect()->back()->with($notification);
        }
    }

    public function markOrderAsDelivered(Request $request)
    {
        // dd($request->all());
        $orderDeliveredStatus = OrderStatus::select('id')
                                    ->where('slug','delivered')
                                    ->where('status_type',1)
                                    ->first();

        $orderProductDeliveredStatus = OrderStatus::select('id')
                                    ->where('slug','delivered')
                                    ->where('status_type',2)
                                    ->first();

        OrderProducts::where('order_id',$request->orderId)
                    ->update(['order_status_id'=>$orderProductDeliveredStatus->id]);

        Orders::where('id',$request->orderId)
                ->update(['order_status_id'=>$orderDeliveredStatus->id]);

        $orderProducts = OrderProducts::where('order_id',$request->orderId)->get();

        foreach($orderProducts as $product)
        {
            $orderItemTrack = new OrderItemsTracking;
            $orderItemTrack->order_product_id = $product->id;
            $orderItemTrack->order_status_id = $orderProductDeliveredStatus->id;
            $orderItemTrack->save();
        }

        // insert into notification table
        $orderDetails = Orders::select('user_id','order_id')->where('id',$request->orderId)->first();
        $notification = new Notifications;
        $notification->user_id = $orderDetails->user_id;
        $notification->notification_type = 'OD';
        $notification->order_id = $request->orderId;
        $notification->order_number = $orderDetails->order_id;
        $notification->read_flag = 0;
        $notification->save();

        if(!empty($request->ajaxReq))
        {
            if($request->ajaxReq == "yes")
            {
                // Send email start
                $order = Orders::where('id', $request->orderId)->first();
                $order_products = OrderProducts::where('order_id',$request->orderId)->first();
                $temp_arr = [];
                $new_user = $this->getEmailTemp();
                foreach($new_user as $code )
                {
                    if($code->code == 'ORDDLVRD')
                    {
                        array_push($temp_arr, $code);
                    }
                }

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }

                $replace_data = array(
                    '{{orderid}}' => $order->order_id,
                    '{{firstname}}' => $order->first_name,
                    '{{lastname}}' => $order->last_name,
                    '{{baseUrl}}' => $this->getBaseUrl(),
                    '{{tracking_number}}' => $order->tracking_number,
                    '{{carrier}}'=> $order_products->carrier,
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);
                $data = [
                    'html' => $html_value,
                ];
                $subject = $temp_arr[0]['subject']." ".$order->order_id;
                $email = $order->email;
                Mail::send('admin.emails.order-delivered', $data, function ($message) use ($email,$subject) {
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over

                //Update Order Activity Table
                $msg = "Order was marked as Delivered.";
                $this->updateOrderActivity($request->orderId, $msg);

                $result['status'] = 'true';
                $result['msg'] = 'Order marked as delivered successfully !!';
                return $result;
            }
        }
        else
        {
            // Send email start
            $order = Orders::where('id', $request->orderId)->first();
            $order_products = OrderProducts::where('order_id', $request->orderId)->first();
            $temp_arr = [];
            $new_user = $this->getEmailTemp();
            foreach($new_user as $code )
            {
                if($code->code == 'ORDDLVRD')
                {
                    array_push($temp_arr, $code);
                }
            }

            if(is_array($temp_arr))
            {
                $value = $temp_arr[0]['value'];
            }

            $replace_data = array(
                '{{orderid}}' => $order->order_id,
                '{{firstname}}' => $order->first_name,
                '{{lastname}}' => $order->last_name,
                '{{baseUrl}}' => $this->getBaseUrl(),
                '{{tracking_number}}' => $order->tracking_number,
                '{{carrier}}'=> $order_products->carrier,
            );
            $html_value = $this->replaceHtmlContent($replace_data,$value);
            $data = [
                'html' => $html_value,
            ];
            $subject = $temp_arr[0]['subject']." ".$order->order_id;
            $email = $order->email;
            Mail::send('admin.emails.order-delivered', $data, function ($message) use ($email,$subject) {
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($email)->subject($subject);
            });
            // Send email over

            //Update Order Activity Table
            $msg = "Order was marked as Delivered.";
            $this->updateOrderActivity($request->orderId, $msg);

            $notification = array(
                'message' => 'Order marked as delivered successfully !!',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }

    public function printOrder($orderId)
    {
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;

        DB::statement(DB::raw('set @rownum=0'));
        $orderDetails = Orders::select('orders.id','order_id','orders.created_at as orderDate','payment_mode','total','payment_method','payment_id',
                                'os.status','subtotal','discount_amount','total_shipping_cost','tax_amount','b_address_type','s_address_type',
                                'b_fullname','b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','b_phone1',
                                's_fullname','s_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','s_phone1','loyalty_card',
                                'first_name','last_name','first_name','email','promotions','shipping_method','shipping_type','store_location_id')
                                ->join('order_status as os','os.id','orders.order_status_id')
                                ->where('orders.id',$orderId)
                                ->first();

        //To get logged in user timezone_offset$parent_id = Auth::guard('admin')->user()->id;
        $parent_id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
        $OrdereDate=convertTimeToTz($orderDetails->orderDate,$timezone);
        $orderDetails['orderDate']=$OrdereDate;
        $orderProducts = OrderProducts::select('carrier','tracking_number','quantity','price','os.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                                'details','promo_code','lady_operator','gift_wrap','gift_message','message','option_id')
                                ->join('order_status as os','os.id','order_products.order_status_id')
                                ->where('order_products.order_id',$orderId)
                                ->get();

        $orderProductDetails = $prod = [];
        $j = $i = 0;
        foreach($orderProducts as $orderProd)
        {
            $orderProductDetails[$i]['srNo'] = $orderProd->rownum;
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
            if(!empty($orderProd->option_id))
            {
                $orderProductDetails[$i]['sku'] = '';
                $prodPricingSku = ProductPricing::find($orderProd->option_id);
                if(!empty($prodPricingSku['sku']))
                    $orderProductDetails[$i]['sku'] = $prodPricingSku['sku'];
                else
                {
                    $productSku = Product::find($orderProd->product_id);
                    if(!empty($productSku['sku']))
                        $orderProductDetails[$i]['sku'] = $productSku['sku'];
                }
            }
            $orderProductDetails[$i++]['variants'] = $varients;
        }

        $storePickupAddress = StoreLocation::where('id',$orderDetails->store_location_id)->first();
        // dd($orderProductDetails);
        view()->share(['orderDetails'=>$orderDetails,'orderProductDetails'=>$orderProductDetails,'storePickupAddress'=>$storePickupAddress,
        'decimalNumber'=>$decimalNumber,'decimalSeparator'=>$decimalSeparator,'thousandSeparator'=>$thousandSeparator,'defaultCurrency'=>$defaultCurrency]);
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf = PDF::loadView('admin/orders/printOrderPdf',$orderDetails);
        $filename = rand(10, 100) . '.pdf';
        return $pdf->stream($filename);
    }

    public function getExportOrders(Request $request)
    {
        try
        {
            $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $decimalNumber = $defaultLanguageData->decimal_number;
            $decimalSeparator = $defaultLanguageData->decimal_separator;
            $thousandSeparator = $defaultLanguageData->thousand_separator;

            $allOrders = Orders::select('orders.id','orders.order_id','orders.created_at as orderCreateddate','orders.payment_method',
                            'orders.total','orders.subtotal','orders.tax_amount','orders.total_shipping_cost',
                            'os.status','orders.first_name','orders.last_name','orders.email as customerEmail','shipping_type','cg.group_name',
                            'b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','shipping_method',
                            's_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','expected_deliverydate','sl.title as storeTitle','sl.address_1 as storeAddr1','sl.address_2 as storeAddr2')
                            ->leftJoin('order_products as op','op.order_id','=','orders.id')
                            ->leftJoin('store_location as sl','sl.id','=','orders.store_location_id')
                            ->leftJoin('customers as c','c.id','=','orders.user_id')
                            ->leftJoin('customer_groups as cg','cg.id','=','c.cust_group_id')
                            ->with('orderProducts')
                            ->join('order_status as os','os.id','orders.order_status_id')
                            ->groupBy('orders.order_id');
                            if (!empty($request['orderId']) )
                            {
                                $allOrders = $allOrders->where('orders.order_id','like', '%' . $request['orderId'] . '%');
                            }
                            if (!empty($request['orderStatus']) )
                            {
                                $allOrders = $allOrders->where('os.status',$request['orderStatus']);
                            }
                            if (!empty($request['custName']))
                            {
                                $orders = $orders->where('orders.first_name', 'like', '%' . $request['custName'] . '%')
                                                ->orWhere('orders.last_name', 'like', '%' . $request['custName'] . '%');
                            }
                            if (!empty($request['custEmail']) )
                            {
                                $orders = $orders->where('orders.email','like', '%' . $request['custEmail']. '%');
                            }
                            if (!empty($request['paymentMethod']) )
                            {
                                $orders = $orders->where('orders.payment_method',$request['paymentMethod']);
                            }
                            if($request['startDate'] != "" || $request['endDate'] != "")
                            {
                                $orders = $orders->whereBetween('orders.created_at',[$request['startDate'], $request['endDate']]);
                            }
                            if(!empty($request['custGroup']) && $request['custGroup'] != -1 && $request['custGroup'] != 0)
                            {
                                $orders = $orders->where('cg.id',$request['custGroup']);
                            }
                            if($request['custGroup'] == 0)
                            {
                                $orders = $orders->where('c.cust_group_id',$request['custGroup']);
                            }
                            if(!empty($request['shippingType']) && $request['shippingType'] != 'all' && $request['shippingType'] == 'delivery')
                            {
                                $orders = $orders->where('orders.shipping_type',$request['shippingType']);
                            }
                            if(!empty($request['shippingType']) && $request['shippingType'] != 'all' && $request['shippingType'] == 'store_pickup')
                            {
                                $orders = $orders->where('orders.shipping_type',$request['shippingType']);
                            }
            $allOrders = $allOrders->get();
            $i = 0;
            $orders = [];

            foreach($allOrders as $order)
            {
                $orders[$i]['order_id'] = $order['order_id'];

                $orders[$i]['first_name'] = $order['first_name'];
                $orders[$i]['last_name'] = $order['last_name'];
                $orders[$i]['customerEmail'] = $order['customerEmail'];
                $orders[$i]['customer_group'] = isset($order['group_name']) ? $order['group_name'] : 'Not Assigned';

                $orders[$i]['b_address_line_1'] = $order['b_address_line_1'];
                $orders[$i]['b_address_line_2'] = $order['b_address_line_2'];
                $orders[$i]['b_city'] = $order['b_city'];
                $orders[$i]['b_state'] = $order['b_state'];
                $orders[$i]['b_pincode'] = $order['b_pincode'];
                $orders[$i]['b_country'] = $order['b_country'];

                $orders[$i]['shipping_method'] = $order['shipping_method'];

                if($order['shipping_type'] == 'delivery')
                {
                    $orders[$i]['s_address_line_1'] = $order['s_address_line_1'];
                    $orders[$i]['s_address_line_2'] = $order['s_address_line_2'];
                    $orders[$i]['s_city'] = $order['s_city'];
                    $orders[$i]['s_state'] = $order['s_state'];
                    $orders[$i]['s_pincode'] = $order['s_pincode'];
                    $orders[$i]['s_country'] = $order['s_country'];
                }
                if($order['shipping_type'] == 'store_pickup')
                {
                    $orders[$i]['s_address_line_1'] = $order['storeTitle'];
                    $orders[$i]['s_address_line_2'] = $order['storeAddr1'];
                    $orders[$i]['s_city'] = $order['storeAddr2'];
                    $orders[$i]['s_state'] = '';
                    $orders[$i]['s_pincode'] = '';
                    $orders[$i]['s_country'] = '';
                }

                $orders[$i]['payment_method'] = $order['payment_method'];

                $orders[$i]['status'] = $order->status;
                $orders[$i]['orderCreateddate'] = date('d-M-Y H:i A', strtotime($order->orderCreateddate));
                $orders[$i]['payment_method'] = $order->payment_method;
                $orders[$i++]['total'] = number_format($order->total, $decimalNumber, $decimalSeparator, $thousandSeparator);
            }

            $sheetTitle = 'Orders';
            return $this->exportOrders($orders, $sheetTitle);
        }
        catch(\Exception $ex)
        {
            return $ex;
            return redirect($request->segment(1).'/orders');
        }
    }

    public function getShippingOrderData(Request $request)
    {
        $orders = \App\Models\Orders::where('id', $request->order_id)->first();
        $aramex_config = \App\Models\AramexConfig::first();
        $result['status'] = 'true';
        $result['orders'] = $orders;
        $result['aramex_config'] = $aramex_config;
        return $result;
    }

    public function generateOrderInvoice(Request $request)
    {
        $lastInvoiceDetails = OrderInvoices::select('invoice_id')->orderBy('id','desc')->first();

        if(isset($lastInvoiceDetails))
        {
            $res = preg_replace("/[^0-9]/", "", $lastInvoiceDetails->invoice_id );
            $invoiceId = 'ALBINV'.(str_pad((int)$res + 1, 5, '0', STR_PAD_LEFT));
        }
        else
            $invoiceId = 'ALBINV00001';

        $getInvoiceId = Orders::where('id',$request['orderId'])->pluck('invoice_id')->first();
        if($getInvoiceId != 0)
        {
            $notification = array(
                'message' => 'Invoice is already generated for this order.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
        else
        {
            $orderInvoice = new OrderInvoices;
            $orderInvoice->invoice_id = $invoiceId;
            $orderInvoice->order_id = $request['orderId'];
            $orderInvoice->created_by = Auth::guard('admin')->user()->id;

            if($request->orderStatus == "Cancelled")
                $orderInvoice->invoice_status = 3;

            if($request->orderStatus == "Order Received" || $request->orderStatus == "Shipped" || $request->orderStatus == "Delivered")
                $orderInvoice->invoice_status = 1;

            else
                $orderInvoice->invoice_status = 2;
            $orderInvoice->save();

            Orders::where('id',$request['orderId'])->update(['invoice_id'=> $orderInvoice->id]);

            //Update Order Activity Table
            $msg = "Order invoice was generated.";
            $this->updateOrderActivity($request['orderId'], $msg);

            $notification = array(
                'message' => 'Invoice generated successfully !!',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }

    public function cancelBulkOrders(Request $request)
    {
        if(!empty($request->checkedValues))
        {
            $cancelOrderIds = [];
            $i = 0;
            $orderRecivedStatus = OrderStatus::select('id')
                                             ->where('slug','order-received')
                                             ->where('status_type',1)
                                             ->first();
            $orderProductCancelStatus = OrderStatus::select('id')
                                        ->where('slug','cancelled')
                                        ->where('status_type',2)
                                        ->first();

            $orderCancelStatus = OrderStatus::select('id')
                                        ->where('slug','cancelled')
                                        ->where('status_type',1)
                                        ->first();

            foreach($request->checkedValues as $orderId)
            {
                $orderStatus = Orders::select('orders.id','os.slug')
                                    ->leftJoin('order_status as os','os.id','=','orders.order_status_id')
                                    ->where('orders.id',$orderId)
                                    ->first();

                if($orderStatus->slug == "order-received")
                {
                    $cancelOrderIds[$i++] = $orderStatus->id;
                }
            }
            if(!empty($cancelOrderIds))
            {
                foreach($cancelOrderIds as $cancelOrderId)
                {
                    OrderProducts::where('order_id',$cancelOrderId)
                                    ->update(['order_status_id'=> $orderProductCancelStatus->id]);

                    Orders::where('id',$cancelOrderId)
                                ->update(['order_status_id'=>$orderCancelStatus->id]);

                    $orderProducts = OrderProducts::where('order_id',$cancelOrderId)->get();

                    foreach($orderProducts as $product)
                    {
                        $orderItemTrack = new OrderItemsTracking;
                        $orderItemTrack->order_product_id = $product->id;
                        $orderItemTrack->order_status_id = $orderProductCancelStatus->id;
                        $orderItemTrack->save();
                    }

                    // insert into notification table
                    $orderDetails = Orders::select('user_id','order_id')->where('id',$cancelOrderId)->first();
                    $notification = new Notifications;
                    $notification->user_id = $orderDetails->user_id;
                    $notification->notification_type = 'OC';
                    $notification->order_id = $cancelOrderId;
                    $notification->order_number = $orderDetails->order_id;
                    $notification->read_flag = 0;
                    $notification->save();

                    // Send email start
                    $order = Orders::where('id', $cancelOrderId)->first();
                    $temp_arr = [];
                    $new_user = $this->getEmailTemp();
                    foreach($new_user as $code )
                    {
                        if($code->code == 'ORDCNLD')
                        {
                            array_push($temp_arr, $code);
                        }
                    }

                    if(is_array($temp_arr))
                    {
                        $value = $temp_arr[0]['value'];
                    }

                    $replace_data = array(
                        '{{orderid}}' => $order->order_id,
                        '{{firstname}}' => $order->first_name,
                        '{{lastname}}' => $order->last_name,
                        '{{baseUrl}}' => $this->getBaseUrl(),
                    );
                    $html_value = $this->replaceHtmlContent($replace_data,$value);
                    $data = [
                        'html' => $html_value,
                    ];
                    $subject = $temp_arr[0]['subject']." ".$order->order_id;
                    $email = $order->email;
                    Mail::send('admin.emails.order-cancelled', $data, function ($message) use ($email,$subject) {
                        $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                        $message->to($email)->subject($subject);
                    });
                    // Send email over
                    //Update Order Activity Table
                    $msg = "Order was marked as Cancelled.";
                    $this->updateOrderActivity($cancelOrderId, $msg);
                }
                $result['status'] = 'true';
                $result['msg'] = 'Order(s) has been marked as cancelled.';
                return $result;
            }
            else
            {
                $result['status'] = 'false';
                $result['msg'] = 'Your selected orders are not eligible for cancellation.';
                return $result;
            }
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = 'Please select at least one order.';
            return $result;
        }
    }

    public function printBulkOrders(Request $request)
    {
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;
        $invoiceIds=$_POST['id'];
        if(!empty($invoiceIds))
        {
            DB::statement(DB::raw('set @rownum=0'));
            $dataPdfArr=array();
            $k=0;
            foreach($invoiceIds as $orderId)
            { echo $orderId;

                $orderDetails = Orders::select('orders.id','order_id','orders.created_at as orderDate','payment_mode','total','payment_method','payment_id',
                                'os.status','subtotal','discount_amount','total_shipping_cost','tax_amount','b_address_type','s_address_type',
                                'b_fullname','b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','b_phone1',
                                's_fullname','s_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','s_phone1',
                                'first_name','last_name','first_name','email','promotions','shipping_method','shipping_type','store_location_id')
                                ->join('order_status as os','os.id','orders.order_status_id')
                                ->where('orders.id',$orderId)
                                ->first();
                $dataPdfArr[$k]['orderDetails']=$orderDetails;
                $orderProducts = OrderProducts::select('carrier','tracking_number','quantity','price','os.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                                'details','promo_code','lady_operator','gift_wrap','gift_message','message')
                                ->join('order_status as os','os.id','order_products.order_status_id')
                                ->where('order_products.order_id',$orderId)
                                ->get();

            $orderProductDetails = $prod = [];
            $j = $i = 0;
              foreach($orderProducts as $orderProd)
              {
                if(!empty($orderProd)){
                  $orderProductDetails[$i]['srNo'] = $i+1;
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
                    }
                    $dataPdfArr[$k]['orderProductDetails']=$orderProductDetails;
                    $storePickupAddress = StoreLocation::where('id',$orderDetails->store_location_id)->first();
                    $dataPdfArr[$k++]['storePickupAddress']=$storePickupAddress;
      }
        view()->share(['dataPdfArr'=>$dataPdfArr,
        'decimalNumber'=>$decimalNumber,'decimalSeparator'=>$decimalSeparator,'thousandSeparator'=>$thousandSeparator,'defaultCurrency'=>$defaultCurrency]);
        $pdf = PDF::loadView('admin/orders/bulkPrintOrderPdf',$dataPdfArr);
        $filename = rand(10, 100) . '.pdf';
        return $pdf->download($filename);
      }
      else
        {
          $result['status'] = 'false';
          $result['msg'] = 'Please select at least one order.';
          return $result;
        }
    }

    public function generateBulkOrderInvoice(Request $request)
    {
      $flag=0;
      if(!empty($request->checkedValues)){
        foreach($request->checkedValues as $orderId){

        $lastInvoiceDetails = OrderInvoices::select('invoice_id')->orderBy('id','desc')->first();

        if(isset($lastInvoiceDetails))
        {
            $res = preg_replace("/[^0-9]/", "", $lastInvoiceDetails->invoice_id );
            $invoiceId = 'ALBINV'.(str_pad((int)$res + 1, 5, '0', STR_PAD_LEFT));
        }
        else
            $invoiceId = 'ALBINV00001';

      $getInvoiceId = Orders::where('id',$orderId)->pluck('invoice_id')->first();
        if($getInvoiceId == 0)
        {
            $order = Orders::select('order_status.slug')->leftjoin('order_status','order_status.id','=','orders.order_status_id')->where('orders.id', $orderId)->first();
            $orderInvoice = new OrderInvoices;
            $orderInvoice->invoice_id = $invoiceId;
            $orderInvoice->order_id = $orderId;
            $orderInvoice->created_by = Auth::guard('admin')->user()->id;

            if($order->slug == "cancelled")
                $orderInvoice->invoice_status = 3;

            if($order->slug == "order-received" || $order->slug == "shipped" || $order->slug == "delivered")
                $orderInvoice->invoice_status = 1;

            else
                $orderInvoice->invoice_status = 2;
            $orderInvoice->save();

            Orders::where('id',$orderId)->update(['invoice_id'=> $orderInvoice->id]);

            //Update Order Activity Table
            $msg = "Order invoice was generated.";
            $this->updateOrderActivity($orderId, $msg);
            $flag=1;
        }
      }
      if($flag==1){
        $result['status'] = 'true';
        $result['msg'] = 'Invoice generated successfully !!';
        return $result;
      }
      else{
        $result['status'] = 'false';
        $result['msg'] = 'Invoice is already generated for this order(s).';
        return $result;
      }
      }
      else
        {
          $result['status'] = 'false';
          $result['msg'] = 'Please select at least one order.';
          return $result;
        }
    }

    public function addNotes(Request $request)
    {
      $parent_id = Auth::guard('admin')->user()->id;
      $activity = new OrderNotes();
      $activity->order_id = $request->orderId;
      $activity->notes = $request->notes;
      $activity->created_by = $parent_id;
      $activity->created_at =  date("Y-m-d H:i:s");

      if($activity->save())
      {
        $result['status'] = 'true';
        $result['msg'] = 'Note add successfully!';
        return $result;
      }
      else{
        $result['status'] = 'false';
        $result['msg'] = 'Note not added!';
        return $result;
      }
    }

    public function updateLoyaltyNumber(Request $request)
    {
        $orderDetails = Orders::find($request->orderId);
        if(!empty($orderDetails))
        {
            $orderDetails->loyalty_card = $request->loyaltyNumber;
            if($orderDetails->save())
            {
                $msg = "Loyalty Number Updated Successfully!";
                $result['status'] = true;
                $result['loyaltynumber'] = $request->loyaltyNumber;
                $result['msg'] = $msg;
                return $result;
            }
            else
            {
                $msg = "Something went wrong !!";
                $result['status'] = false;
                $result['msg'] = $msg;
                return $result;
            }
        }
    }
    /** Developed by : Nivedita **/
    //Note : This function will change status of order and order products
    public function changeStatus(Request $request)
    {
        $orderDetails = Orders::find($request->orderId);
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;
        if(!empty($orderDetails))
        {
            $orderDetails->order_status_id = $request->orderStatus;
            if($orderDetails->save())
            {
                $orderStatus = OrderStatus::where('status_type',1)->where('id',$orderDetails->order_status_id)->first();
                $orderProductStatus = OrderStatus::where('status_type',2)->where('slug',$orderStatus->slug)->first();
                $orderProducts=OrderProducts::where('order_id', $request->orderId)->update(['order_status_id' => $orderProductStatus->id]);
                $order = Orders::where('id', $request->orderId)->first();
                if($orderStatus->slug=='ready-collect'){
                $temp_arr = [];
                $new_user = $this->getEmailTemp();
                foreach($new_user as $code )
                {
                    if($code->code == 'ORDRTD')
                    {
                        array_push($temp_arr, $code);
                    }
                }

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }

                $replace_data = array(
                    '{{orderid}}' => $order->order_id,
                    '{{firstname}}' => $order->first_name,
                    '{{lastname}}' => $order->last_name,
                    '{{baseUrl}}' => $this->getBaseUrl(),
                    '{{paymentmethod}}' => $order->payment_method,
                    '{{paymentid}}'=> $order->payment_id,
                    '{{amount}}'=> number_format($order->total, $decimalNumber, $decimalSeparator, $thousandSeparator),
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);
                $data = [
                    'html' => $html_value,
                ];
                $subject = $temp_arr[0]['subject']." ".$order->order_id;
                $email = $order->email;
                Mail::send('admin.emails.order-ready-collect', $data, function ($message) use ($email,$subject) {
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over

                //Update Order Activity Table
                $msg = "Order was marked as Cancelled.";
                $this->updateOrderActivity($request->orderId, $msg);
              }

                // $result['status'] = 'true';
                // $result['msg'] = 'Order marked as cancelled successfully !!';
                // return $result;
                $msg = "Order Status Updated Successfully!";
                $result['status'] = true;
                $result['oerderstatus'] = $orderStatus->status;
                $result['oerderstatusId'] = $orderStatus->id;
                $result['msg'] = $msg;
                return $result;
            }
            else
            {
                $msg = "Something went wrong !!";
                $result['status'] = false;
                $result['msg'] = $msg;
                return $result;
            }
        }
    }

    /** Developed by : Jignesh **/
    //Note : This function creates because of method collision in CommonTrait and ReuseFunctionTrait
    public function getEmailTemp($language_id = null)
    {
        if($language_id == null || $language_id == '' || !isset($language_id))
        {
            $lang = \App\Models\GlobalLanguage::where('is_default', 1)->first();
            if($lang)
            {
                $selected_lang = $lang->id;
            }
        }
        else
        {
            $lang = \App\Models\GlobalLanguage::where('id', $language_id)->first();
            if($lang)
            {
                $selected_lang = $lang->id;
            }
        }

        $email_template = \App\Models\EmailTemplate::select('email_template.code','email_template.title','email_template_details.value','email_template_details.email_template_id','email_template_details.language_id','email_template_details.subject')
        ->leftJoin('email_template_details','email_template_details.email_template_id','=','email_template.id')
        ->where('email_template_details.language_id',$selected_lang)
        ->get();
        return $email_template;
    }

    /** Developed by : Jignesh **/
    //Note : This function creates because of method collision in CommonTrait and ReuseFunctionTrait
    public function replaceHtmlContent($data,$html_value)
    {
        $html = $html_value;
        foreach ($data as $key => $value) {
            $html = str_replace($key, $value, $html);
        }
        return $html;
    }

    public function updateOrderActivity($order_id, $msg)
    {
        //Update Order Activity Table
        $user_id = Auth::guard('admin')->user()->id;
        $order_activity = new \App\Models\OrderActivity;
        $order_activity->order_id = $order_id;
        $order_activity->activity = $msg;
        $order_activity->created_by = $user_id;
        $order_activity->save();
    }

    public function getOrderActivity(Request $request)
    {
        $parent_id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
        $orderActivity = \App\Models\OrderActivity::select('order_activity.activity',
        'order_activity.created_at','users.firstname','users.lastname')
        ->join('users','users.id','=','order_activity.created_by')
        ->where("order_activity.order_id",$request['orderId'])
        ->orderBy('order_activity.id','DESC')
        ->get();
        $i = 0;
        $allActivities = [];

        foreach($orderActivity as $activity)
        {
            $allActivities[$i]['rownum'] = $i+1;
            $allActivities[$i]['activity'] = $activity['activity'];

            $allActivities[$i]['createdby'] = $activity['firstname'].' '.$activity['lastname'];
            $allActivities[$i++]['createdat'] = date('Y-m-d H:i:s',strtotime($activity['created_at']));
        }
        return Datatables::of($allActivities)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    public function downloadOrderProductFiles($imageId)
    {

        /*$filesToDownload = OrderProducts::select('other_images','details')->where('id',$imageId)->get();

        $orderProductDetails = [];
        $i = 0;

        foreach($filesToDownload as $download)
        {
            $data = preg_replace_callback(
                '!s:(\d+):"(.*?)";!',
                function($m) {
                    return 's:'.strlen($m[2]).':"'.$m[2].'";';
                },
                $download['details']);
            $prodData = unserialize($data);
            if(!empty($prodData['image']))
            {
                $downloadFiles[] = $prodData['image'];
            }
            $downloadFiles1[] = str_replace(array('[', ']','\\'), array('', ''), explode(',', $download->other_images));
        }

        foreach($downloadFiles1 as $otherImages)
        {
            foreach($otherImages as $otherImage)
            {
                if($otherImage[0] != "null" && $otherImage[0] != null)
                    $downloadOtherFiles[$i++] = $otherImage;
            }
        }

        $totalFiles = array_merge($downloadOtherFiles,$downloadFiles);

        foreach($totalFiles as $file)
        {
            if(!empty($file))
            {
                $name = basename($file);
                $publicDirectory = ('public/downloadedFiles/');
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );

                is_dir($publicDirectory) || @mkdir($publicDirectory) || die("Can't Create folder");
                copy(str_replace('"','',$file), $publicDirectory . DIRECTORY_SEPARATOR .str_replace('"','',$name), stream_context_create($arrContextOptions));

                $files[] = ['path' => $publicDirectory.str_replace('"','',$name), 'name' => str_replace('"','',$name)];
            }
        }

        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );
        // if (count($files) == 1)
        // {
        //     return response()->download($files[0]['path'], $files[0]['name'], $headers);
        // }
            // dd($files);
        $zipFileName = 'prodImage' . time() . '.zip';
        $zip = new ZipArchive;
        if ($zip->open($publicDirectory . '/' . $zipFileName, ZipArchive::CREATE) === TRUE)
        {
            foreach ($files as $file)
            {
                $zip->addFile($file['path'], $file['name']);
            }
            $zip->close();
        }
        $filetopath = $publicDirectory . '/' . $zipFileName;
        if (file_exists($filetopath))
        {
            return response()->download($filetopath, $zipFileName, $headers);
        }*/

        $filesToDownload = OrderProducts::select('screenshots_files')->where('id',$imageId)->first();

        $baseUrl = $this->getBaseUrl();
        foreach(explode(',',$filesToDownload->screenshots_files) as $file)
        {
            if(!empty($file))
            {
                $publicDirectory = ('public/downloadedFiles/');
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );

                $name = str_replace('[','',$file);
                $fileName = str_replace(']','',$name);

                is_dir($publicDirectory) || @mkdir($publicDirectory) || die("Can't Create folder");
                copy($baseUrl.'/design-tool/data/screenshots/'.str_replace('"','',$fileName), $publicDirectory . DIRECTORY_SEPARATOR .str_replace('"','',$fileName), stream_context_create($arrContextOptions));

                $files[] = ['path' => $publicDirectory.str_replace('"','',$name), 'name' => str_replace('"','',$fileName)];
            }
        }

        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );

        $zipFileName = 'productfiles_' . time() . '.zip';
        $zip = new ZipArchive;
        if ($zip->open($publicDirectory . '/' . $zipFileName, ZipArchive::CREATE) === TRUE)
        {
            foreach ($files as $file)
            {
                $zip->addFile(str_replace(']','',$file['path']), $file['name']);
            }
            $zip->close();
        }
        $filetopath = $publicDirectory . '/' . $zipFileName;
        if (file_exists($filetopath))
        {
            return response()->download($filetopath, $zipFileName, $headers);
        }
    }

    public function downloadOrderProdImages($imageId)
    {
        $filesToDownload = OrderProducts::select('print_files')->where('id',$imageId)->first();

        $baseUrl = $this->getBaseUrl();
        foreach(explode(',',$filesToDownload->print_files) as $file)
        {
            if(!empty($file))
            {
                $publicDirectory = ('public/downloadedFiles/');
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );

                $name = str_replace('[','',$file);
                $fileName = str_replace(']','',$name);

                is_dir($publicDirectory) || @mkdir($publicDirectory) || die("Can't Create folder");
                copy($baseUrl.'/design-tool/data/printfiles/'.str_replace('"','',$fileName), $publicDirectory . DIRECTORY_SEPARATOR .str_replace('"','',$fileName), stream_context_create($arrContextOptions));

                $files[] = ['path' => $publicDirectory.str_replace('"','',$name), 'name' => str_replace('"','',$fileName)];
            }
        }

        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );

        $zipFileName = 'printfiles_' . time() . '.zip';
        $zip = new ZipArchive;
        if ($zip->open($publicDirectory . '/' . $zipFileName, ZipArchive::CREATE) === TRUE)
        {
            foreach ($files as $file)
            {
                $zip->addFile(str_replace(']','',$file['path']), $file['name']);
            }
            $zip->close();
        }
        $filetopath = $publicDirectory . '/' . $zipFileName;
        if (file_exists($filetopath))
        {
            return response()->download($filetopath, $zipFileName, $headers);
        }
    }

    // developed by Pallavi
    public function markOrderAsShippedWOAramex(Request $request)
    {
        //Store data in order table
        $order_status_o = \App\Models\OrderStatus::where('slug', 'shipped')->where('status_type', 1)->first();

        $order = \App\Models\Orders::where('id', $request->orderId)->first();
        $order->tracking_number = $request->trackingNumber;
        $order->shipdate = date('Y-m-d');
        $order->order_status_id = $order_status_o->id;
        $order->save();

        $order_status_op = \App\Models\OrderStatus::where('slug', 'shipped')->where('status_type', 2)->first();
        $data = [
            'tracking_number' => $request->trackingNumber,
            'carrier' => $request->carrierName,
            'order_status_id' => $order_status_op->id,
        ];

        //Update Order Products Table
        \App\Models\OrderProducts::where('order_id', $request->orderId)->update($data);

        //Update Order Activity Table
        $user_id = Auth::guard('admin')->user()->id;
        $order_activity = new \App\Models\OrderActivity;
        $order_activity->order_id = $request->orderId;
        $order_activity->activity = 'Order shipment was created.';
        $order_activity->created_by = $user_id;
        $order_activity->save();

        //Add Order Items Tracking Data
        $orderProducts = \App\Models\OrderProducts::where('order_id', $request->orderId)->get();
        if($orderProducts)
        {
            foreach($orderProducts as $product)
            {
                $orderItemTrack = new \App\Models\OrderItemsTracking;
                $orderItemTrack->order_product_id = $product->id;
                $orderItemTrack->order_status_id = $order_status_op->id;
                $orderItemTrack->save();
            }
        }

        // Send email start
        $order = \App\Models\Orders::where('id', $request->orderId)->first();
        $temp_arr = [];
        $new_user = $this->getEmailTemp();
        foreach($new_user as $code )
        {
            if($code->code == 'ORDSPD')
            {
                array_push($temp_arr, $code);
            }
        }

        if(is_array($temp_arr))
        {
            $value = $temp_arr[0]['value'];
        }

        $replace_data = array(
            '{{orderid}}' => $order->order_id,
            '{{firstname}}' => $order->first_name,
            '{{lastname}}' => $order->last_name,
            '{{baseUrl}}' => $this->getBaseUrl(),
            '{{paymentmethod}}' => $order->payment_method,
            '{{paymentid}}'=> $order->payment_id,
            '{{tracking_number}}' => empty($order->tracking_number) ? "N/A" : $order->tracking_number,
            '{{carrier}}'=> empty($order_products->carrier) ? "N/A" : $order_products->carrier,
        );
        $html_value = $this->replaceHtmlContent($replace_data,$value);
        $data = [
            'html' => $html_value,
        ];
        $subject = $temp_arr[0]['subject']." ".$order->order_id;
        $email = $order->email;
        Mail::send('admin.emails.order-shipped', $data, function ($message) use ($email,$subject) {
            $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
            $message->to($email)->subject($subject);
        });
        // Send email over

        //Add Notification Data
        notification($type = 'OS', $order->user_id, $order->id, $order->order_id);

        if(!empty($request->ajaxReq))
        {
            if($request->ajaxReq == "yes")
            {
                $result['status'] = 'true';
                $result['msg'] = config('message.AramexConfig.AramexSuccMsg');
                return response()->json($result);
            }
        }
        else
        {
            $successNotification = array(
                'message' => config('message.AramexConfig.AramexSuccMsg'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($successNotification);
        }
    }

    // download print files PDF ZIP - Pallavi (July 30, 2021)
    public function downloadPrintFilesPdf($orderProdId)
    {
        $filesToDownload = OrderProducts::select('print_files')->where('id',$orderProdId)->first();

        $files = explode(',',str_replace('"','',$filesToDownload->print_files));
        $files = str_replace('[','',$files);
        $files = str_replace(']','',$files);

        $imgHeight = Config::get('app.imageHeight');
        $imgWidth = Config::get('app.imageWidth');

        view()->share(['filesToDownload'=>$files,'imgHeight' => $imgHeight, 'imgWidth' => $imgWidth]);
        $pdf = PDF::loadView('admin/orders/printFilePdf',$files);
        $filename = 'printFile_'.rand(10, 100) . '.pdf';
        return $pdf->download($filename);
    }
}
