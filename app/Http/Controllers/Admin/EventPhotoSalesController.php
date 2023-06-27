<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\GlobalCurrency;
use App\Models\EventPhotoOrders;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;
use PDF;

class EventPhotoSalesController extends Controller
{
    use CommonTrait;
    public function getAlleventPhotoSales()
    {
        $baseUrl = $this->getBaseUrl();
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
                                    ->leftJoin('currency', 'currency.id', '=', 'global_currency.currency_id')
                                    ->where('global_currency.is_default', 1)->first();

        return view('admin.eventPhotoSales.index', compact('baseUrl', 'defaultCurrency'));
    }

    public function getAllEventPhotoSalesList(Request $request)
    {
        // dd($request->all());
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;

        DB::enableQueryLog();
        $eventPhotoSales = EventPhotoOrders::select('event_photo_orders.id','event_photo_orders.order_id','event_photo_orders.created_at','event_photo_orders.payment_type',
                            'event_photo_orders.amount','payment_type','payment_id','status','c.first_name','c.last_name','c.email')
                            ->leftJoin('customers as c','c.id','=','event_photo_orders.customer_id')
                            ->orderBy('event_photo_orders.created_at','desc');
                            if (isset($request['orderId']) && !empty($request['orderId']))
                            {
                                $eventPhotoSales = $eventPhotoSales->where('event_photo_orders.order_id','like', '%' . $request['orderId'] . '%');
                            }
                            if (isset($request['status']) && $request['status'] != -1)
                            {
                                $eventPhotoSales = $eventPhotoSales->where('status',$request['status']);
                            }
                            if (isset($request['custName']) && !empty($request['custName']) )
                            {
                                $eventPhotoSales = $eventPhotoSales->where(function($q) use($request)
                                {
                                    $names = explode(' ',$request['custName']);
                                    foreach($names as $name)
                                    {
                                        $q->orWhere('c.first_name', 'like', '%' . trim($name) . '%')
                                        ->orWhere('c.last_name', 'like', '%' . trim($name) . '%');
                                    }
                                });
                            }
                            if (isset($request['custEmail']) && !empty($request['custEmail']))
                            {
                                $eventPhotoSales = $eventPhotoSales->where('c.email','like', '%' . $request['custEmail']. '%');
                            }
                            if (isset($request['paymentType'])  && $request['paymentType'] != -1)
                            {
                                $eventPhotoSales = $eventPhotoSales->where('payment_type',$request['paymentType']);
                            }
                            if($request['startDate'] != "" || $request['endDate'] != "")
                            {
                                $eventPhotoSales = $eventPhotoSales->whereBetween(DB::raw('DATE(event_photo_orders.created_at)'),[$request['startDate'], $request['endDate']]);
                            }
        $eventPhotoSales = $eventPhotoSales->get();
        // dd(DB::getQueryLog());
        // dd($orders);
        $i = 0;
        $allOrders = [];

        foreach($eventPhotoSales as $order)
        {
            $allOrders[$i]['id'] = $order['id'];
            $allOrders[$i]['order_id'] = $order['order_id'];

            $allOrders[$i]['first_name'] = $order['first_name'];
            $allOrders[$i]['last_name'] = $order['last_name'];
            $allOrders[$i]['email'] = $order['email'];
            $allOrders[$i]['total'] = number_format($order->amount, $decimalNumber, $decimalSeparator, $thousandSeparator);

            $allOrders[$i]['payment_type'] = $order['payment_type'];
            $allOrders[$i]['payment_id'] = $order['payment_id'];

            $allOrders[$i]['status'] = $order->status;
            $allOrders[$i++]['created_at'] = date('Y-m-d H:i:s', strtotime($order->created_at));
        }
        // die;
        // dd($allOrders);
        return Datatables::of($allOrders)->make(true);
    }
}
?>