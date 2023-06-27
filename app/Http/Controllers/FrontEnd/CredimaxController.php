<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Traits\ReuseFunctionTrait;
use Mail;

class CredimaxController extends Controller
{
    use ReuseFunctionTrait;

    public function createPayment()
    {
        $pageName = "Credimax Payment";
        $projectName = "Alboumi";
        $baseUrl = $this->getBaseUrl();
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        return view('frontend.create-payment', compact('pageName','projectName','baseUrl','megamenuFileName',
        'mobileMegamenuFileName'));
    }

    public function Success()
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

        $req_order_id = request()->get('order_id');
        $order_id = isset($req_order_id) ? $req_order_id : Session::get('order_id');
        
        $req_merchant_order_id = request()->get('merchant_order_id');
        $merchant_order_id = isset($req_merchant_order_id) ? $req_merchant_order_id : Session::get('merchant_order_id');

        // if called from mobile
        $isMobile = request()->get('isMobile');

        // retrive order details
        $response = $this->credimax_order_details($merchant_order_id);

        $result = json_decode($response, true);

        if (isset($result['result']) && $result['result'] == 'SUCCESS') {
            // get transaction id
            $payment_id = "";
            if(isset($result['transaction'][0]['transaction']['acquirer']['transactionId']) && !empty($result['transaction'][0]['transaction']['acquirer']['transactionId']))
            {
                $payment_id = $result['transaction'][0]['transaction']['acquirer']['transactionId'];
            }
            elseif(isset($result['transaction'][1]['transaction']['acquirer']['transactionId']) && !empty($result['transaction'][1]['transaction']['acquirer']['transactionId']))
            {
                $payment_id = $result['transaction'][1]['transaction']['acquirer']['transactionId'];
            }
            elseif(isset($result['transaction'][2]['transaction']['acquirer']['transactionId']) && !empty($result['transaction'][2]['transaction']['acquirer']['transactionId']))
            {
                $payment_id = $result['transaction'][2]['transaction']['acquirer']['transactionId'];
            }

            // Get order_id from orders table
            $order = \App\Models\Orders::where('id', $order_id)->first();

            // Set cartmaster
            $cart_master_id = $order->cart_master_id;
            // $merchant_order_id = select order_id from orders where id = session order_id (e.g./ ALB00001, ALB00002 etc)


            // update following things
            // 1. update orders.order_status_id = 1, payment_id = $payment_id where id = session order_id
            // 4. update orders.result = $response where id = session order_id
            $order->order_status_id = '1';
            $order->payment_id = $payment_id;
            $order->result = $response;
            $order->save();

            // 2. update cartmaster.flag_complete = '1' where id = session cart_master_id            
            $cart_master = \App\Models\CartMaster::where('id', $cart_master_id)->first();
            $cart_master->flag_complete = '1'; 
            $cart_master->save();
            
            // 3. update order_products.order_status_id = '5' where order_id = session order_id
            // $order_products = \App\Models\OrderProducts::where('order_id', $order_id)->first();
            // $order_products->order_status_id = '5';
            // $order_products->save();

            \App\Models\OrderProducts::where('order_id', $order_id)->update(['order_status_id' => '5']);

            $display_merchant_order_id = $merchant_order_id;

            //Update Product QTY
            updateinventory($cart_master_id);

            // Send email start
            $order = \App\Models\Orders::where('id', $order_id)->first();                
            $temp_arr = [];
            $new_user = $this->getEmailTemp();
            foreach($new_user as $code )
            {
                if($code->code == 'ORDPLCD')
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
                '{{amount}}'=> $order->total,  
            );
            $html_value = $this->replaceHtmlContent($replace_data,$value);
            $data = [
                'html' => $html_value,
            ];
            $subject = $temp_arr[0]['subject']." ".$order->order_id;
            $email = $order->email;
            Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject) {
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($email)->subject($subject);
            });
            // Send email over

            //Send Email To System Admin
            $settings = \App\Models\Settings::first();        
            $emails = explode(",", $settings->email);
            Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject,$emails) {
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($emails)->subject($subject);
            });

            //Add Notification Data
            notification($type = 'OP', $order->user_id, $order->id, $order->order_id);
            
            // 5. unset session cart_master_id
            // 6. unset session order_id
            // 7. unset session merchant_order_id 
            Session::forget(['cart_master_id','order_id','merchant_order_id']);
                                    
            // 8. load view order confirmation "order-confirmation.html" with $merchant_order_id
            // return view('frontend.order-confirmation', compact('display_merchant_order_id','orderCnfLabels','baseUrl'
            // ,'pageName', 'projectName','mobileMegamenuFileName', 'megamenuFileName'));
            if(isset($isMobile) && $isMobile == 1 && !empty($isMobile))
            {
                return redirect('/checkout/success');
            }
            else
            {
                return redirect('/order-confirmation')->with(['display_merchant_order_id' => $display_merchant_order_id]);
            }
        }
        else
        {
            // update following things
            // 1. update orders.order_status_id = 2, where id = session order_id
            $order = \App\Models\Orders::where('id', $order_id)->first();
            $order->order_status_id = '2';            
            $order->save();

            // 2. unset session order_id
            // 3. unset session merchant_order_id 
            Session::forget(['order_id','merchant_order_id']);

            $notification = array(
                'message' => $orderCnfLabels['ORDERCONFIRMFAILEDMSG'],
                'alert-type' => 'success'
            );

            if(isset($isMobile) && $isMobile == 1 && !empty($isMobile))
            {
                return redirect('/checkout/failure');
            }
            else
            {
                // 4. Redirect user to order review page again with message "Sorry, your order did not place. Please try again."            
                return redirect('/customer/review-order')->with($notification);
            }
            
        }
    }

    public function Cancel()
    {
        $lang_id = Session::get('language_id');
        $language_found = isLanguageExists($lang_id);
        if($language_found == 'false')
        {
            $defaultLang = $this->getDefaultLanguage();
            $lang_id = $defaultLang;
        }

        $codes = ['ORDERCONFIRMFAILEDMSG'];
        $orderCnfLabels = getCodesMsg($lang_id, $codes);        

        // $order_id = session order_id;        
        $req_order_id = request()->get('order_id');
        $order_id = isset($req_order_id) ? $req_order_id : Session::get('order_id');

        // if called from mobile
        $isMobile = request()->get('isMobile');

        // update following things
        // 1. update orders.order_status_id = 2, where id = session order_id
        $order = \App\Models\Orders::where('id', $order_id)->first();
        $order->order_status_id = '2';            
        $order->save();

        // 2. unset session order_id
        // 3. unset session merchant_order_id 
        Session::forget(['order_id','merchant_order_id']);

        // 4. Redirect user to order review page again with message "Sorry, your order did not place. Please try again."
        $notification = array(
            'message' => $orderCnfLabels['ORDERCONFIRMFAILEDMSG'],
            'alert-type' => 'error'
        );

        if(isset($isMobile) && $isMobile == 1 && !empty($isMobile))
        {
            return redirect('/checkout/failure');
        }
        else
        {
            return redirect('/customer/review-order')->with($notification);
        }
        
    }

    public function credimax_order_details($marchant_order_id)
    {
        $merchant = config('app.CREDIMAX_MERCHANT_ID');

        $url = "https://credimax.gateway.mastercard.com/api/rest/version/54/merchant/".$merchant."/order/".$marchant_order_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic ".config('app.CREDIMAX_BASIC_AUTH'),
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);	
        curl_close($curl);

        return $response;        
    }
}
