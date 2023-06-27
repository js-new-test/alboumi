<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class EventPhotoOrders extends Model
{
    protected $table = 'event_photo_orders';

    protected $fillable = ['event_enquiry_id','customer_id','photo_ids','order_id','status','payment_id','payment_type','amount','result'];

    public function saveOrder($request)
    {
        // dd($request);
        $lastOrderDetails = EventPhotoOrders::select('id')->orderBy('id','desc')->first();

        if(isset($lastOrderDetails))
        {
            $lastOrderId = $lastOrderDetails->id;
            $orderId = 'ALBEPHO'.(str_pad($lastOrderId + 1, 5, '0', STR_PAD_LEFT));
        }
        else
            $orderId = 'ALBEPHO00001';

        $saveOrder = new EventPhotoOrders;
        $saveOrder->event_enquiry_id = $request['enqId'];
        $saveOrder->customer_id = $request['custId'];
        $saveOrder->photo_ids = $request['checkedImagesIds'];
        $saveOrder->order_id = $orderId;
        $saveOrder->language_id = Session::get('language_id');
        $saveOrder->amount = $request['selectedImagesPrice'];
        $saveOrder->payment_type = $request['payment_type'];
        $saveOrder->created_at = Carbon::now();
        $saveOrder->save();
        
        $lastInsertedId = $saveOrder->id;
        $lastInsertedOrderId = $saveOrder->order_id;

        Session::put('event_order_amount',$saveOrder->amount);
        Session::put('event_order_id',$lastInsertedId);
        Session::put('event_merchant_order_id',$lastInsertedOrderId);

        $baseUrl = url('/');
        Session::put('eventUrlForCancel',url()->previous());
        $merchantOrderId = strtr(base64_encode($lastInsertedOrderId), '+/=', '-_,');
        $amount = strtr(base64_encode($saveOrder->amount), '+/=', '-_,');

        if($request['payment_type'] == 1)
        {
            $merchant = Config('app.CREDIMAX_MERCHANT_ID');
            $action = config('app.CREDIMAX_ACTION');

            $url = "https://credimax.gateway.mastercard.com/api/rest/version/54/merchant/".$merchant."/session";

            $grandTotal = $saveOrder->amount; // amount of event order

            $event_order_id = Session::get('event_order_id'); // last inserted ID
            $event_merchant_order_id =  Session::get('event_merchant_order_id'); // order id generated

            $currency = Session::get('currency_symbol');

            if(!empty($order_id) && !empty($merchant_order_id))
            {
                $returnURl = $baseUrl."/customer/eventorders/credimax/success?order_id=".$order_id."&merchant_order_id=".$merchant_order_id;
                $cancelUrl = $baseUrl."/customer/eventorders/credimax/cancel?order_id=".$order_id."&merchant_order_id=".$merchant_order_id;
            }
            else
            {
                $returnURl = $baseUrl."/customer/eventorders/credimax/success";
                $cancelUrl = $baseUrl."/customer/eventorders/credimax/cancel";
            }
            // $baseUrl = "https://alboumi.magnetoinfotech.com";

            $data = array(
                "apiOperation" => "CREATE_CHECKOUT_SESSION",
                "order" => array(
                    "amount" =>  $grandTotal,
                    "currency" => "BHD",
                    "id" => $event_merchant_order_id
                ),
                "interaction" => array(
                    "operation"=>$action,
                    "returnUrl"=>$returnURl,
					"cancelUrl"=>$cancelUrl,
                    "merchant" => array(
                        "name"=> "ASHRAFS",
                        "logo"=>$baseUrl."/public/assets/frontend/img/Alboumi_Logo.png"
                    ),
                )
            );

            $payload = json_encode($data);
            // dd($payload);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    "authorization: Basic ".Config('app.CREDIMAX_BASIC_AUTH'),
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);

            if($res['result'] == "ERROR")
            {
                $error = $res['error']['explanation'];
                $arrData = array('error' => 1, 'msg' => $error);
            }
            else
            {
                $session_id = $res['session']['id'];
                Session::put('session_id',$session_id);
                $arrData = array('error' => 0, 'msg' => "Success", 'session_id' => $session_id,'event_order_id' => $event_order_id,
                'event_merchant_order_id' => $event_merchant_order_id, 'merchant_id' => $merchant, 'grandTotal' => $grandTotal);
            }
            return json_encode($arrData);
        }

        if($request['payment_type'] == 2)
        {
            // $baseUrl = $this->getBaseUrl();
            header('Location: '.$baseUrl.'/benefits/request.php?merchantOrderId='.$merchantOrderId.'&orderAmount='.$amount);
            exit;
            // dd($baseUrl.'/benefits/request.php?merchantOrderId='.$merchantOrderId.'&orderAmount='.$amount);
            // header('Location: '.$baseUrl.'/benefits/request.php?merchantOrderId='.$merchantOrderId.'&orderAmount='.$amount);
            // exit;
        }
    }
}