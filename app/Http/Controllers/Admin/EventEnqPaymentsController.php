<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventEnq;
use App\Models\GlobalCurrency;
use App\Models\EventEnqOrders;
use Auth;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;

class EventEnqPaymentsController extends Controller
{
    use CommonTrait;
    public function getAllEventEnqPayment()
    {
        $baseUrl = $this->getBaseUrl();
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
                                    ->leftJoin('currency', 'currency.id', '=', 'global_currency.currency_id')
                                    ->where('global_currency.is_default', 1)->first();

        return view('admin.eventEnqPayments.index', compact('baseUrl', 'defaultCurrency'));
    }

    public function getAllEventEnqPaymentList(Request $request)
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;

        $eventEnqPayments = EventEnqOrders::select('event_enquiry_orders.id','event_enquiry_orders.order_id','event_enquiry_orders.created_at','event_enquiry_orders.payment_type',
                            'event_enquiry_orders.amount','event_enquiry_orders.payment_status','full_name','email','event_enquiry_orders.payment_id')
                            ->leftJoin('event_enquiry as ee','ee.id','=','event_enquiry_orders.event_enq_id')
                            ->orderBy('event_enquiry_orders.created_at','desc');
                            if (isset($request['orderId']) && !empty($request['orderId']))
                            {
                                $eventEnqPayments = $eventEnqPayments->where('event_enquiry_orders.order_id','like', '%' . $request['orderId'] . '%');
                            }
                            if (isset($request['paymentId']) && !empty($request['paymentId']))
                            {
                                $eventEnqPayments = $eventEnqPayments->where('event_enquiry_orders.payment_id','like', '%' . $request['paymentId'] . '%');
                            }
                            if (isset($request['custName']) && !empty($request['custName']) )
                            {
                                $eventEnqPayments = $eventEnqPayments->where(function($q) use($request)
                                {
                                    $names = explode(' ',$request['custName']);
                                    foreach($names as $name)
                                    {
                                        $q->where('full_name', 'like', '%' . trim($name) . '%');
                                    }
                                });
                            }
                            if (isset($request['custEmail']) && !empty($request['custEmail']))
                            {
                                $eventEnqPayments = $eventEnqPayments->where('email','like', '%' . $request['custEmail']. '%');
                            }
                            if (isset($request['paymentType'])  && $request['paymentType'] != -1)
                            {
                                $eventEnqPayments = $eventEnqPayments->where('payment_type',$request['paymentType']);
                            }
                            if (isset($request['paymentStatus'])  && $request['paymentStatus'] != -1)
                            {
                                $eventEnqPayments = $eventEnqPayments->where('event_enquiry_orders.payment_status',$request['paymentStatus']);
                            }
                            if($request['startDate'] != "" || $request['endDate'] != "")
                            {
                                $eventEnqPayments = $eventEnqPayments->whereBetween(DB::raw('DATE(event_enquiry_orders.created_at)'),[$request['startDate'], $request['endDate']]);
                            }
        $eventEnqPayments = $eventEnqPayments->get();
        // dd(DB::getQueryLog());
        $i = 0;
        $allPayments = [];

        foreach($eventEnqPayments as $order)
        {
            $allPayments[$i]['id'] = $order['id'];
            $allPayments[$i]['order_id'] = $order['order_id'];

            $allPayments[$i]['full_name'] = $order['full_name'];
            $allPayments[$i]['email'] = $order['email'];
            $allPayments[$i]['amount'] = number_format($order->amount, $decimalNumber, $decimalSeparator, $thousandSeparator);

            $allPayments[$i]['payment_type'] = $order['payment_type'];
            $allPayments[$i]['payment_status'] = $order['payment_status'];
            $allPayments[$i]['payment_id'] = $order['payment_id'];

            $allPayments[$i++]['created_at'] = date('Y-m-d H:i:s', strtotime($order->created_at));
        }
       
        return Datatables::of($allPayments)->make(true);
    }
}
?>