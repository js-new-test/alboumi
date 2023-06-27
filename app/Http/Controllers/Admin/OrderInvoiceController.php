<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\GlobalCurrency;
use App\Models\OrderStatus;
use App\Models\OrderProducts;
use App\Models\CustomerGroups;
use App\Models\OrderInvoices;
use App\Models\StoreLocation;
use App\Models\ProductPricing;
use App\Models\Product;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;
use PDF;

class OrderInvoiceController extends Controller
{
    use CommonTrait;
    public function getAllInvoices()
    {
        $baseUrl = $this->getBaseUrl();
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

        return view('admin.orderInvoices.index',compact('baseUrl','defaultCurrency'));
    }

    public function getAllInvoicesList(Request $request)
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;
        DB::enableQueryLog();

        //To get logged in user timezone
        $parent_id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();

        $invoices = OrderInvoices::select('order_invoices.id','order_invoices.invoice_id','order_invoices.created_at as invoiceCreatedDate',
                                    'o.order_id','o.id as orderId','o.first_name','o.last_name','invoice_status','o.total','o.email as customerEmail','o.id as orderIdPri')
                                    ->leftJoin('orders as o','o.id','=','order_invoices.order_id')
                                    ->orderBy('order_invoices.created_at','desc');
                                    if (!empty($request['orderId']) && isset($request['orderId']) )
                                    {
                                        $invoices = $invoices->where('o.order_id','like', '%' . $request['orderId'] . '%');
                                    }
                                    if (!empty($request['invoiceId']) && isset($request['invoiceId']))
                                    {
                                        $invoices = $invoices->where('order_invoices.invoice_id','like', '%' . $request['invoiceId'] . '%');
                                    }
                                    if (!empty($request['custName'])  && isset($request['custName']))
                                    {
                                        $invoices = $invoices->where(function($q) use($request)
                                        {
                                            $names = explode(' ',$request['custName']);
                                            foreach($names as $name)
                                            {
                                                $q->orWhere('o.first_name', 'like', '%' . trim($name) . '%')
                                                ->orWhere('o.last_name', 'like', '%' . trim($name) . '%');
                                            }
                                        });
                                    }
                                  
                                    if (!empty($request['custEmail']) && isset($request['custEmail']))
                                    {
                                        $invoices = $invoices->where('o.email','like', '%' . $request['custEmail']. '%');
                                    }
                                    if (!empty($request['invoiceStatus']) && isset($request['invoiceStatus']))
                                    {
                                        $invoices = $invoices->where('order_invoices.invoice_status',$request['invoiceStatus']);
                                    }
                                    if($request['invoiceStartDate'] != "" || $request['invoiceEndDate'] != "")
                                    {
                                        $invoices = $invoices->whereBetween(DB::raw('DATE(order_invoices.created_at)'),[$request['invoiceStartDate'], $request['invoiceEndDate']]);
                                    }
                                    if($request['orderStartDate'] != "" || $request['orderEndDate'] != "")
                                    {
                                        $invoices = $invoices->whereBetween(DB::raw('DATE(o.created_at)'),[$request['orderStartDate'], $request['orderEndDate']]);
                                    }
        $invoices = $invoices->get();
        $i = 0;
        $allInvoices = [];

        foreach($invoices as $invoice)
        {
            $invoices[$i]['id'] = $invoice['id'];
            $invoices[$i]['invoice_id'] = $invoice['invoice_id'];
            $invoices[$i]['order_id'] = $invoice['order_id'];
            $invoices[$i]['orderIdPri'] = $invoice['orderIdPri'];
            $invoices[$i]['orderId'] = $invoice['orderId'];
            $invoices[$i]['customerEmail'] = $invoice['customerEmail'];

            $invoices[$i]['invoiceCreatedDate'] = $invoice->invoiceCreatedDate;

            $invoices[$i]['first_name'] = $invoice['first_name'];
            $invoices[$i]['last_name'] = $invoice['last_name'];

            $invoices[$i]['status'] = $invoice->invoice_status;
            $invoices[$i++]['total'] = number_format($invoice->total, $decimalNumber, $decimalSeparator, $thousandSeparator);
        }
        return Datatables::of($invoices)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
        //return Datatables::of($invoices)->make(true);
    }

    public function printInvoice($invoiceId)
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
        $orderDetails = OrderInvoices::select('order_invoices.invoice_id','order_invoices.created_at as invoiceDate','orders.id as ordPriId','orders.order_id','orders.created_at as orderDate','payment_mode','total','payment_method','payment_id',
                                'os.status','subtotal','discount_amount','total_shipping_cost','tax_amount','b_address_type','s_address_type',
                                'b_fullname','b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','b_phone1',
                                's_fullname','s_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','s_phone1',
                                'first_name','last_name','first_name','email','promotions','shipping_method','shipping_type','store_location_id')
                                ->join('orders','orders.id','order_invoices.order_id')
                                ->join('order_status as os','os.id','orders.order_status_id')
                                ->where('order_invoices.id',$invoiceId)
                                ->first();
      //To get logged in user timezone_offset$parent_id = Auth::guard('admin')->user()->id;
      $parent_id = Auth::guard('admin')->user()->id;
      $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
      $OrdereDate=convertTimeToTz($orderDetails->invoiceDate,$timezone);
      $orderDetails['invoiceDate']=$OrdereDate;

        $orderProducts = OrderProducts::select('carrier','tracking_number','quantity','price','os.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                                'details','promo_code','lady_operator','gift_wrap','gift_message','message','option_id')
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

        view()->share(['orderDetails'=>$orderDetails,'orderProductDetails'=>$orderProductDetails,'storePickupAddress'=>$storePickupAddress,
        'decimalNumber'=>$decimalNumber,'decimalSeparator'=>$decimalSeparator,'thousandSeparator'=>$thousandSeparator,'defaultCurrency'=>$defaultCurrency,'baseUrl'=>$baseUrl]);
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true ,'chroot' => public_path()])->loadView('admin/orderInvoices/invoicePdf',$orderDetails);
        $filename = rand(10, 100) . '.pdf';
        return $pdf->stream($filename);
    }
    public function printBulkInvoice()
    {
        $invoiceIds=$_POST['id'];
        $baseUrl = $this->getBaseUrl();
        $defaultCurrency = GlobalCurrency::select('currency.currency_code')
									->leftJoin('currency','currency.id','=','global_currency.currency_id')
									->where('global_currency.is_default', 1)->first();

        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
        $decimalNumber = $defaultLanguageData->decimal_number;
        $decimalSeparator = $defaultLanguageData->decimal_separator;
        $thousandSeparator = $defaultLanguageData->thousand_separator;

        DB::statement(DB::raw('set @rownum=0'));
        $dataPdfArr=array();
        $k=0;
        foreach($invoiceIds as $k=>$v){
        $orderDetails = OrderInvoices::select('order_invoices.invoice_id','order_invoices.created_at as invoiceDate','orders.id as ordPriId','orders.order_id','orders.created_at as orderDate','payment_mode','total','payment_method','payment_id',
                                'os.status','subtotal','discount_amount','total_shipping_cost','tax_amount','b_address_type','s_address_type',
                                'b_fullname','b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','b_phone1',
                                's_fullname','s_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','s_phone1',
                                'first_name','last_name','first_name','email','promotions','shipping_method','shipping_type','store_location_id')
                                ->join('orders','orders.id','order_invoices.order_id')
                                ->join('order_status as os','os.id','orders.order_status_id')
                                ->where('order_invoices.id',$v)
                                ->first();
      //To get logged in user timezone_offset$parent_id = Auth::guard('admin')->user()->id;
      $parent_id = Auth::guard('admin')->user()->id;
      $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
      $OrdereDate=convertTimeToTz($orderDetails->invoiceDate,$timezone);
      $orderDetails['invoiceDate']=$OrdereDate;
      $dataPdfArr[$k]['orderDetails']=$orderDetails;
        $orderProducts = OrderProducts::select('carrier','tracking_number','quantity','price','os.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                                'details','promo_code','lady_operator','gift_wrap','gift_message','message')
                                ->join('order_status as os','os.id','order_products.order_status_id')
                                ->where('order_products.order_id',$orderDetails->ordPriId)
                                ->get();

        $orderProductDetails = $prod = [];
        $j = $i = 0;
        foreach($orderProducts as $orderProd)
        {
            $orderProductDetails[$i]['srNo'] = $i+1;
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
        $dataPdfArr[$k]['orderProductDetails']=$orderProductDetails;
        $storePickupAddress = StoreLocation::where('id',$orderDetails->store_location_id)->first();
        $dataPdfArr[$k++]['storePickupAddress']=$storePickupAddress;
      }
         view()->share(['dataPdfArr'=>$dataPdfArr,
         'decimalNumber'=>$decimalNumber,'decimalSeparator'=>$decimalSeparator,'thousandSeparator'=>$thousandSeparator,'defaultCurrency'=>$defaultCurrency,'baseUrl'=>$baseUrl]);
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true ,'chroot' => public_path()])->loadView('admin/orderInvoices/bulkInvoice',$dataPdfArr);
        $filename = rand(10, 100) . '.pdf';
        //return $pdf->stream($filename);
        return $pdf->download($filename);
    }

    public function getInvoiceDetails($invoiceId)
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

        $invoiceOrderId = OrderInvoices::where('id',$invoiceId)->pluck('order_id')->first();

        $orderProducts = OrderProducts::select('carrier','tracking_number','quantity','price','os.status',DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                                        'details','promo_code','lady_operator','gift_wrap','gift_message','message')
                                        ->join('order_status as os','os.id','order_products.order_status_id')
                                        ->where('order_products.order_id',$invoiceOrderId)
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

            $orderProductDetails[$i++]['variants'] = $varients;
        }
        // dd($orderProductDetails);

        $orderDetails = OrderInvoices::select('order_invoices.id','order_invoices.invoice_status','order_invoices.invoice_id','order_invoices.created_at as invoiceDate','orders.order_id','orders.created_at as orderDate','payment_mode','total','payment_method','payment_id',
                                'os.status','subtotal','discount_amount','total_shipping_cost','tax_amount','b_address_type','s_address_type',
                                'b_fullname','b_address_line_1','b_address_line_2','b_city','b_state','b_country','b_pincode','b_phone1',
                                's_fullname','s_address_line_1','s_address_line_2','s_city','s_state','s_country','s_pincode','s_phone1',
                                'orders.first_name','orders.last_name','orders.email','promotions','shipping_method','shipping_type','store_location_id',
                                'cg.group_name')
                                ->join('orders','orders.id','order_invoices.order_id')
                                ->join('order_status as os','os.id','orders.order_status_id')
                                ->leftJoin('customers as c','c.id','=','orders.user_id')
                                ->leftJoin('customer_groups as cg','cg.id','=','c.cust_group_id')
                                ->where('orders.id',$invoiceOrderId)
                                ->first();
                                // dd($orderDetails);
        $storePickupAddress = '';
        //To get logged in user timezone
        $parent_id = Auth::guard('admin')->user()->id;
        $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
        $InvoiceDate=convertTimeToTz($orderDetails->invoiceDate,$timezone);

        if(!empty($orderDetails))
            $storePickupAddress = StoreLocation::where('id',$orderDetails->store_location_id)->first();

        return view('admin/orderInvoices/view',compact('orderProductDetails','orderDetails','defaultCurrency','baseUrl','decimalNumber','decimalSeparator','thousandSeparator','storePickupAddress','InvoiceDate'));
    }
}
?>
