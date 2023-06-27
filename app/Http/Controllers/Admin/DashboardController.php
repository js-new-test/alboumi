<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;

class DashboardController extends Controller
{
    /* ###########################################
    // Function: Dashboard
    // Description: Display analytical data for admin  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function dashboard()
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        
        $decimalNumber=$defaultLanguageData->decimal_number;
        $decimalSeparator=$defaultLanguageData->decimal_separator;
        $thousandSeparator=$defaultLanguageData->thousand_separator;

        //Section 1
        $date = date('Y-m-d');
        $total_customers_today = \App\Models\Customer::whereDate('created_at', $date)->where('is_deleted', 0)->count();
        $total_customers = \App\Models\Customer::where('is_deleted', 0)->count();
        $total_today_orders = \App\Models\Orders::whereDate('created_at', $date)
        ->whereNotIn('order_status_id', [2,7,45])->count();
        $total_today_sales = \App\Models\Orders::whereDate('created_at', $date)
        ->whereNotIn('order_status_id', [2,7,45])->sum('total');

        //Section 2
        $pending_orders = \App\Models\Orders::whereIn('order_status_id', [1])->count();
        $total_orders = \App\Models\Orders::whereNotIn('order_status_id', [2,7,45])->count();  
        $total_sales = \App\Models\Orders::whereNotIn('order_status_id', [2,7,45])->sum('total');      
        $pending_enquiry = \App\Models\EventEnq::where('status', 0)->whereNull('deleted_at')->count();
        $total_enquiry = \App\Models\EventEnq::whereNull('deleted_at')->count();  
                               
        //Section 4
        $pending_enquiries = \App\Models\EventEnq::select('event_enquiry.created_at','event_enquiry.event_date'
        ,'customers.first_name', 'customers.last_name','events.event_name','packages.package_name','additional_service.name')
        ->leftJoin('events','events.id','=','event_enquiry.event_id')
        ->leftJoin('packages','packages.id','=','event_enquiry.package_id')
        ->leftJoin('customers','customers.id','=','event_enquiry.customer_id')
        ->leftJoin('additional_service','additional_service.id','=','event_enquiry.additional_pkg_ids')
        ->whereNull('event_enquiry.deleted_at')->where('event_enquiry.status', 0)->get();

        $currency = \App\Models\GlobalCurrency::select('currency.currency_code')
        ->leftJoin('currency','currency.id','=','global_currency.currency_id')
        ->where('global_currency.is_default', 1)->where('global_currency.is_deleted', 0)->first();
        $baseUrl = $this->getBaseUrl();

        return view('admin.dashboard',compact('currency','total_customers_today','total_customers',
        'total_today_orders','total_today_sales','pending_orders','total_orders','total_sales','total_enquiry'
        ,'pending_enquiry','decimalNumber','decimalSeparator','thousandSeparator','pending_enquiries','baseUrl'));        
    }

    public function getDailySalesData()
    {
        //Section 3
        $today =  new DateTime();
        $copy = clone $today;
        $begin = $copy->sub(new DateInterval('P12D'));

        $interval = new DateInterval('P1D'); // 1 Day
        $dateRange = new DatePeriod($begin, $interval, $today);

        $range = [];
        foreach ($dateRange as $date) {
            $range[] = $date->format('Y-m-d');
        }        

        $dates = [];        

        for ($i=0; $i < count($range); $i++) { 
            $dates[$range[$i]] = array(
                "total_sales" => "0.00",
                "total_qty" => "0",
            );
        }
        
        $master_date_arr = json_decode(json_encode($dates), true);        

        $dateFrom = Carbon::now()->subDays(30);
        $dateTo = Carbon::now();

        $total = DB::table('orders')
            ->select(DB::raw('SUM(orders.total) total_sales'), DB::raw('DATE(orders.created_at) as order_date'),
            DB::raw('SUM(order_products.quantity) as total_qty'))
            ->leftJoin('order_products','order_products.order_id','=','orders.id')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->whereNotIn('orders.order_status_id', [2,7,45])
            ->groupBy('order_date')
            ->get();
            
        foreach ($total as $value) {
            if(array_key_exists($value->order_date, $dates))
            {
                $total_sales = $value->total_sales;
                $total_qty = $value->total_qty;                
                array_walk_recursive($dates[$value->order_date], function (&$v, $k) use($total_sales, $total_qty){ 
                    if($k == 'total_sales'){ 
                        $v = $total_sales; 
                    } 
                    if($k == 'total_qty'){ 
                        $v = $total_qty; 
                    } 
                });
            }            
        }

        $dates_arr = [];
        $total_sales_arr = [];
        $total_qty_arr = [];
        foreach ($dates as $key => $value) {
            array_push($dates_arr, date('d M Y', strtotime($key)));
            if(is_array($value))
            {
                array_push($total_sales_arr, (Int) $value['total_sales']);
                array_push($total_qty_arr, (Int) $value['total_qty']);
            }
        }

        $result['status'] = 'true';
        $result['dates_arr'] = $dates_arr;
        $result['total_sales_arr'] = $total_sales_arr;
        $result['total_qty_arr'] = $total_qty_arr;
        return $result;
    }

    public function getTotalCount(Request $request)
    {
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        
        $decimalNumber=$defaultLanguageData->decimal_number;
        $decimalSeparator=$defaultLanguageData->decimal_separator;
        $thousandSeparator=$defaultLanguageData->thousand_separator;

        $from_date = $request->from_date;
        $to_date = $request->to_date;

        //Section 2
        $pending_orders = \App\Models\Orders::whereIn('order_status_id', [1,6])
        ->whereDate('created_at','>=',$from_date)
        ->whereDate('created_at','<=',$to_date)
        ->count();
        $total_orders = \App\Models\Orders::whereNotIn('order_status_id', [2,7])
        ->whereDate('created_at','>=',$from_date)
        ->whereDate('created_at','<=',$to_date)
        ->count();  
        $total_sales = \App\Models\Orders::whereNotIn('order_status_id', [2,7])
        ->whereDate('created_at','>=',$from_date)
        ->whereDate('created_at','<=',$to_date)
        ->sum('total');              
        $total_enquiry = \App\Models\EventEnq::whereNull('deleted_at')
        ->whereDate('created_at','>=',$from_date)
        ->whereDate('created_at','<=',$to_date)
        ->count();
        
        $result['status'] = 'true';
        $result['pending_orders'] = $pending_orders;
        $result['total_orders'] = $total_orders;
        $result['total_sales'] = number_format($total_sales, $decimalNumber, $decimalSeparator, $thousandSeparator);
        $result['total_enquiry'] = $total_enquiry;        
        return $result;
    }
}
