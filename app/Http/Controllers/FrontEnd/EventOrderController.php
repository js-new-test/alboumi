<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Events;
use App\Models\GlobalLanguage;
use App\Models\EventEnq;
use App\Models\EventEnqUploadedImages;
use App\Models\EventPhotoOrders;
use App\Models\EventEnqOrders;
use App\Models\Customer;
use DB;
use Validator;
use App\Traits\CommonTrait;
use Auth;
use App\Traits\ReuseFunctionTrait;
use Mail;

class EventOrderController extends Controller
{
    protected $eventGallery;
    use ReuseFunctionTrait;

	public function __construct(EventEnqUploadedImages $eventGallery) {
        $this->eventGallery = $eventGallery;
    }
    // crediamx
    public function createEventOrderPayment()
    {
        $codes = ['APPNAME','EVENTORDERCEREDI'];
        $eventOrderPayment = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $eventOrderPayment['EVENTORDERCEREDI'];
        $projectName = $eventOrderPayment['APPNAME'];
        $baseUrl = $this->getBaseUrl();
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        return view('frontend.events.createEventPayment', compact('pageName','projectName','baseUrl','megamenuFileName',
        'mobileMegamenuFileName'));
    }

    function credimax_order_details($order_id)
    {
        $merchant = config('app.CREDIMAX_MERCHANT_ID');

        $url = "https://credimax.gateway.mastercard.com/api/rest/version/54/merchant/".$merchant."/order/".$order_id;

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

    function credimaxSuccess()
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
        $codes = ['SIDEBARLABEL6', 'APPNAME','HOME','PAYBUTTON','SELECTED','PRINT','QUANTITY','PRODUCT','ADDTOCART','BUYNOW',
        'PAYMENMETHOD','CONTINUE','EVENTORDERSUCCESS','EVENTORDERERROR'];

        $orderCnfLabels = getCodesMsg($lang_id, $codes);
        $pageName = $orderCnfLabels["EVENTORDERSUCCESS"];
        $projectName = $orderCnfLabels["APPNAME"];

        $req_order_id = request()->get('event_order_id');
        $order_id = isset($req_order_id) ? $req_order_id : Session::get('event_order_id');

        $event_merchant_order_id = request()->get('event_merchant_order_id');
        $event_order_id = isset($event_merchant_order_id) ? $event_merchant_order_id : Session::get('event_merchant_order_id');

        // if called from mobile
        $isMobile = request()->get('isMobile');

        $response = $this->credimax_order_details($event_order_id);

        $result = json_decode($response, true);

        if (isset($result['result']) && $result['result'] == 'SUCCESS')
        {
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

            $photo_ids = EventPhotoOrders::select('photo_ids')->where('id',$order_id)->first();

            $photo_order = \App\Models\EventPhotoOrders::where('id', $order_id)->first();
            $photo_order->status = 1;
            $photo_order->payment_id = $payment_id;
            $photo_order->result = $response;
            $photo_order->save();

            $photo_ids_temp = explode(",", $photo_ids->photo_ids);

            EventEnqUploadedImages::whereIn('id',$photo_ids_temp)->update(['flag_purchased' => 1]);

            // send email to customer
            $custDetails = Customer::where('id',$photo_order->customer_id)->first();
            $eventId = EventEnq::select('event_id')->where('id',$photo_order->event_enquiry_id)->first();
            $eventName = Events::select('event_name')->where('id',$eventId->event_id)->first();

            $temp_arr = [];
            $new_user = $this->getEmailTemp();
            foreach($new_user as $code )
            {
                if($code->code == 'EPORDPLCD')
                {
                    array_push($temp_arr, $code);
                }
            }

            if(is_array($temp_arr))
            {
                $value = $temp_arr[0]['value'];
            }
            if($photo_order->payment_type == 1)
                $paymentMethod = 'Credit card';
            if($photo_order->payment_type == 2)
                $paymentMethod = 'Debit card';

            $replace_data = array(
                '{{orderid}}' => $photo_order->order_id,
                '{{firstname}}' => $custDetails->first_name,
                '{{lastname}}' => $custDetails->last_name,
                '{{baseUrl}}' => $this->getBaseUrl(),
                '{{paymentmethod}}' => $paymentMethod,
                '{{paymentid}}'=> $photo_order->payment_id,
                '{{amount}}' => $photo_order->amount,
                '{{eventName}}'=> $eventName->event_name
            );
            $html_value = $this->replaceHtmlContent($replace_data,$value);
            $data = [
                'html' => $html_value,
            ];
            $subject = $temp_arr[0]['subject']." ".$photo_order->order_id;
            $email = $custDetails->email;
            Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject) {
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($email)->subject($subject);
            });
            // Send email over

            //Send Email To System Admin
            $settings = \App\Models\Settings::first();
            $emails = explode(",", $settings->email);
            Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject, $emails) {
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($emails)->subject($subject);
            });

            Session::forget(['event_order_id','event_merchant_order_id','selectedImagesToDownload','selectedImagesPrice']);

            $notification = array(
                'message' => $orderCnfLabels['EVENTORDERSUCCESS'],
                'alert-type' => 'success'
            );
            $orderDetails = EventPhotoOrders::select('id','customer_id','event_enquiry_id')->where('id',$order_id)->first();
            $eventUrlForCancel = $this->eventGallery->getEventGalleryListingByCustId($orderDetails['customer_id']);

            if(isset($isMobile) && $isMobile == 1 && !empty($isMobile))
            {
                return redirect('/checkout/success');
            }
            else
            {
                return redirect('/customer/eventGallery/'.$orderDetails['event_enquiry_id'].'/'.$eventUrlForCancel[0]['isPayable'])->with($notification);
            }

        }
        else
        {
            EventPhotoOrders::where('id',$order_id)->update(['status' => 2, 'result' => $response]);

            Session::forget(['event_order_id','event_merchant_order_id']);

            $notification = array(
                'message' => $orderCnfLabels['EVENTORDERERROR'],
                'alert-type' => 'error'
            );
            $orderDetails = EventPhotoOrders::select('id','customer_id','event_enquiry_id')->where('id',$order_id)->first();
            $eventUrlForCancel = $this->eventGallery->getEventGalleryListingByCustId($orderDetails['customer_id']);

            if(isset($isMobile) && $isMobile == 1 && !empty($isMobile))
            {
                return redirect('/checkout/failure');
            }
            else
            {
                return redirect('/customer/eventGallery/'.$orderDetails['event_enquiry_id'].'/'.$eventUrlForCancel[0]['isPayable'])->with($notification);
            }
        }
    }

    function credimaxCancel()
    {
        $lang_id = Session::get('language_id');
        $language_found = isLanguageExists($lang_id);
        if($language_found == 'false')
        {
            $defaultLang = $this->getDefaultLanguage();
            $lang_id = $defaultLang;
        }

        $codes = ['EVENTORDERERROR'];
        $orderCnfLabels = getCodesMsg($lang_id, $codes);

        $req_order_id = request()->get('event_order_id');
        $order_id = isset($req_order_id) ? $req_order_id : Session::get('event_order_id');
        // dd($order_id);
        $event_merchant_order_id = request()->get('event_merchant_order_id');
        $event_order_id = isset($event_merchant_order_id) ? $event_merchant_order_id : Session::get('event_merchant_order_id');

        // if called from mobile
        $isMobile = request()->get('isMobile');

        $response = $this->credimax_order_details($event_order_id);
        EventPhotoOrders::where('id',$order_id)
                        ->update(['status' => 2, 'result' => $response]);

        Session::forget(['event_order_id','event_merchant_order_id']);
        $notification = array(
            'message' => $orderCnfLabels['EVENTORDERERROR'],
            'alert-type' => 'error'
        );
        $orderDetails = EventPhotoOrders::select('id','customer_id','event_enquiry_id')->where('id',$order_id)->first();
        $eventUrlForCancel = $this->eventGallery->getEventGalleryListingByCustId($orderDetails['customer_id']);

        if(isset($isMobile) && $isMobile == 1 && !empty($isMobile))
        {
            return redirect('/checkout/failure');
        }
        else
        {
            return redirect('/customer/eventGallery/'.$orderDetails['event_enquiry_id'].'/'.$eventUrlForCancel[0]['isPayable'])->with($notification);
        }

    }

    // benefits
    function benefitsSuccess()
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
        $codes = ['SIDEBARLABEL6', 'APPNAME','HOME','PAYBUTTON','SELECTED','PRINT','QUANTITY','PRODUCT','ADDTOCART','BUYNOW',
        'PAYMENMETHOD','CONTINUE','EVENTORDERSUCCESS','EVENTORDERERROR'];

        $orderCnfLabels = getCodesMsg($lang_id, $codes);
        $pageName = $orderCnfLabels["EVENTORDERSUCCESS"];
        $projectName = $orderCnfLabels["APPNAME"];

        $order_id = EventPhotoOrders::select('id','customer_id','event_enquiry_id','language_id')->where('order_id',$_REQUEST['track_id'])->first();

        if(!empty($order_id->language_id))
        {
            Session::put('language_id',$order_id->language_id);
        }

        $user_id = $order_id->customer_id;

        // Get customer object
        $customer = \App\Models\Customer::where('id', $user_id)->first();
        Auth::guard('customer')->login($customer);
        Session::put('customer_id',$user_id);

        $event_order_id = $_REQUEST['track_id'];

        $photo_ids = EventPhotoOrders::select('photo_ids')->where('id',$order_id->id)->first();

        $photo_order = \App\Models\EventPhotoOrders::where('id', $order_id->id)->first();
        $photo_order->status = 1;
        $photo_order->payment_id = $_REQUEST['payment_id'];
        $photo_order->save();

        /*EventPhotoOrders::update(['status' => 1, 'payment_id' => $payment_id, 'result' => $response])
                        ->where('id',$order_id);*/

        $photo_ids_temp = explode(",", $photo_ids->photo_ids);

        EventEnqUploadedImages::whereIn('id',$photo_ids_temp)->update(['flag_purchased' => 1]);

        // send email to customer
        $custDetails = Customer::where('id',$photo_order->customer_id)->first();
        $eventId = EventEnq::select('event_id')->where('id',$photo_order->event_enquiry_id)->first();
        $eventName = Events::select('event_name')->where('id',$eventId->event_id)->first();

        $temp_arr = [];
        $new_user = $this->getEmailTemp();
        foreach($new_user as $code )
        {
            if($code->code == 'EPORDPLCD')
            {
                array_push($temp_arr, $code);
            }
        }

        if(is_array($temp_arr))
        {
            $value = $temp_arr[0]['value'];
        }
        if($photo_order->payment_type == 1)
            $paymentMethod = 'Credit card';
        if($photo_order->payment_type == 2)
            $paymentMethod = 'Debit card';

        $replace_data = array(
            '{{orderid}}' => $photo_order->order_id,
            '{{firstname}}' => $custDetails->first_name,
            '{{lastname}}' => $custDetails->last_name,
            '{{baseUrl}}' => $this->getBaseUrl(),
            '{{paymentmethod}}' => $paymentMethod,
            '{{paymentid}}'=> $photo_order->payment_id,
            '{{amount}}' => $photo_order->amount,
            '{{eventName}}'=> $eventName->event_name
        );
        $html_value = $this->replaceHtmlContent($replace_data,$value);
        $data = [
            'html' => $html_value,
        ];
        $subject = $temp_arr[0]['subject']." ".$photo_order->order_id;
        $email = $custDetails->email;
        Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject) {
            $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
            $message->to($email)->subject($subject);
        });
        // Send email over

        //Send Email To System Admin
        $settings = \App\Models\Settings::first();
        $emails = explode(",", $settings->email);
        Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject, $emails) {
            $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
            $message->to($emails)->subject($subject);
        });

        Session::forget(['event_order_id','event_merchant_order_id']);

        $notification = array(
            'message' => $orderCnfLabels['EVENTORDERSUCCESS'],
            'alert-type' => 'success'
        );
        $redirectDetails = $this->eventGallery->getEventGalleryListingByCustId($order_id->customer_id);

        if(!empty($_REQUEST['isMobile']) && $_REQUEST['isMobile'] == 1)
        {
            return redirect('/checkout/success');
        }
        else
        {
            return redirect('/customer/eventGallery/'.$order_id->event_enquiry_id.'/'.$redirectDetails[0]['isPayable'])->with($notification);
        }


    }

    function benefitsCancel()
    {
        $order_id = EventPhotoOrders::select('id','customer_id','event_enquiry_id','language_id')->where('order_id',$_REQUEST['track_id'])->first();
        $eventPhotoOrderDetails = EventPhotoOrders::find($order_id->id);
        $eventPhotoOrderDetails->status = 2;
        $eventPhotoOrderDetails->result = $_REQUEST['error'];
        $eventPhotoOrderDetails->save();

        $user_id = $order_id->customer_id;

        if(!empty($order_id->language_id))
        {
            Session::put('language_id',$order_id->language_id);
        }

        // Get customer object
        $customer = \App\Models\Customer::where('id', $user_id)->first();
        Auth::guard('customer')->login($customer);
        Session::put('customer_id',$user_id);

        Session::forget(['event_order_id','event_merchant_order_id']);
        $notification = array(
            'message' => $_REQUEST['error'],
            'alert-type' => 'error'
        );
        $redirectDetails = $this->eventGallery->getEventGalleryListingByCustId($order_id->customer_id);

        if(!empty($_REQUEST['isMobile']) && $_REQUEST['isMobile'] == 1)
        {
            return redirect('/checkout/success');
        }
        else
        {
            return redirect('/customer/eventGallery/'.$order_id->event_enquiry_id.'/'.$redirectDetails[0]['isPayable'])->with($notification);
        }

    }

    // event enquiry payment
    public function createEventEnqOrderPayment()
    {
        $codes = ['APPNAME','EVENTENQCREDI'];
        $eventEnqOrderPayment = getCodesMsg(Session::get('language_id'), $codes);

        $pageName = $eventEnqOrderPayment['EVENTENQCREDI'];
        $projectName = $eventEnqOrderPayment['APPNAME'];
        $baseUrl = $this->getBaseUrl();
        $megamenuFileName = "megamenu_".Session::get('language_code');
        $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');
        return view('frontend.create-payment', compact('pageName','projectName','baseUrl','megamenuFileName',
        'mobileMegamenuFileName'));
    }

    public function getEventEnqPayment($eventEnqId)
    {
        $eventEnquiryId = base64_decode(strtr($eventEnqId, '-_', '+/'));
        $idExists = EventEnq::find($eventEnquiryId);

        if($idExists != null)
        {
            $paymentStatus = EventEnq::select('payment_status')->where('id',$eventEnquiryId)->first();

            if($paymentStatus->payment_status == 1)
            {
                $notification = array(
                    'message' => 'Payment for this event inquiry already paid.',
                    'alert-type' => 'error'
                );
                return redirect('/')->with($notification);
            }
            if($paymentStatus->payment_status == 0)
            {
                $codes = ['HOME','APPNAME',
                'DELIVERY','STOREPICKUPFREE','CONTINUESHOPPING','EDIT_ADDRESS_TITLE','PAYMENMETHOD','EVENTENQPAYMENT',
                'DELIVERYHERE','MYADDRESSES1','SET_AS_DEFAULT_ADDRESS','MYADDRESSES11','MYADDRESSES12',
                'PINCODE_HINT','ADDRESS_LINE1_HINT','ADDRESS_LINE2_HINT','MYACCOUNTLABEL3','FULL_NAME',
                'addressType1','addressType2','SELECTCOUNTRY','CONFIRMATION','YES','NO','AREYOUSURE','CANCEL'
                ,'EDIT_ADDRESS_TITLE','FULLNAMEREQ', 'ADDRESS1REQ', 'ADDRESS2REQ','MOBILEREQ','MOBILENUM',
                'COUNTRYREQ','PINCODEREQ','PAYONLINECREDIT','PAYONLINEDEBIT','PLACEORDER','CONTINUE'];
                $paymentMethodLabels = getCodesMsg(Session::get('language_id'), $codes);

                $pageName = $paymentMethodLabels['EVENTENQPAYMENT'];
                $projectName = $paymentMethodLabels['APPNAME'];
                $baseUrl = $this->getBaseUrl();

                $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->where('is_deleted',0)->first();
                $defaultLanguageId = $defaultLanguageData['id'];
                $setSessionforLang=setSessionforLang($defaultLanguageId);

                $lang_id = Session::get('language_id');

                $megamenuFileName = "megamenu_".Session::get('language_code');
                $mobileMegamenuFileName = "mobileMegamenu_".Session::get('language_code');

                Session::put('eventEnqEmailUrl',url()->current());
                return view('frontend.events.eventEnqPayment',compact('pageName','projectName','baseUrl','paymentMethodLabels','megamenuFileName','mobileMegamenuFileName','eventEnquiryId'));
            }
        }
        else
        {
            $notification = array(
                'message' => 'Invalid URL. Please try again.',
                'alert-type' => 'error'
            );
            return redirect('/')->with($notification);
        }
    }

    public function paymentOfEventEnq(Request $request)
    {
        $lastOrderDetails = EventEnqOrders::select('id')->orderBy('id','desc')->first();

        if(isset($lastOrderDetails))
        {
            $lastOrderId = $lastOrderDetails->id;
            $orderId = 'ALBENQ'.(str_pad($lastOrderId + 1, 5, '0', STR_PAD_LEFT));
        }
        else
            $orderId = 'ALBENQ00001';

        $eventEnqUpdatedDetails = EventEnq::find($request->enqId);
        $eventEnqUpdatedDetails->order_id = $orderId;
        $eventEnqUpdatedDetails->save();

        $eventEnqOrder = new EventEnqOrders;
        $eventEnqOrder->event_enq_id = $eventEnqUpdatedDetails->id;
        $eventEnqOrder->order_id = $orderId;
        $eventEnqOrder->language_id = Session::get('language_id');
        $eventEnqOrder->payment_type = $request->payment_type;
        $eventEnqOrder->amount = $eventEnqUpdatedDetails->advance_payment;
        $eventEnqOrder->save();

        $baseUrl = $this->getBaseUrl();
        $eventEnqId = $eventEnqUpdatedDetails->id; // last inserted ID
        $eventEnqOrderId = $eventEnqUpdatedDetails->order_id; // order id generated

        Session::put('event_enq_id',$eventEnqId);
        Session::put('merchant_order_id',$eventEnqOrderId);
        Session::put('order_amount',$eventEnqUpdatedDetails->advance_payment);

        $merchantOrderId = strtr(base64_encode($eventEnqOrderId), '+/=', '-_,');
        $amount = strtr(base64_encode($eventEnqUpdatedDetails->advance_payment), '+/=', '-_,');

        if($request['payment_type'] == 1)
        {
            $merchant = Config('app.CREDIMAX_MERCHANT_ID');
            $action = config('app.CREDIMAX_ACTION');

            $url = "https://credimax.gateway.mastercard.com/api/rest/version/54/merchant/".$merchant."/session";
            // $baseUrl = "https://alboumi.magnetoinfotech.com";

            $currency = Session::get('currency_symbol');
            $data = array(
                "apiOperation" => "CREATE_CHECKOUT_SESSION",
                "order" => array(
                    "currency" => "BHD",
                    "id" => $eventEnqOrderId
                ),
                "interaction" => array(
                    "operation"=>$action,
                    "returnUrl"=>$baseUrl."/eventEnq/credimax/success",
                    "cancelUrl"=>$baseUrl."/eventEnq/credimax/cancel",
                    "merchant" => array(
                        "name"=> "ASHRAFS",
                        "logo"=>$baseUrl."/public/assets/frontend/img/Alboumi_Logo.png"
                    ),
                )
            );

            $payload = json_encode($data);

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
                $arrData = array('error' => 0, 'msg' => "Success", 'session_id' => $session_id,'merchant_order_id' => $eventEnqOrderId,
                'order_amount' =>  Session::get('order_amount'));
            }
            return json_encode($arrData);
        }

        if($request['payment_type'] == 2)
        {
            header('Location: '.$baseUrl.'/benefits/request_eventEnq.php?merchantOrderId='.$merchantOrderId.'&orderAmount='.$amount);
            exit;
        }
    }

    function eventEnqCredimaxCancel()
    {
        $lang_id = Session::get('language_id');
        $language_found = isLanguageExists($lang_id);
        if($language_found == 'false')
        {
            $defaultLang = $this->getDefaultLanguage();
            $lang_id = $defaultLang;
        }

        $codes = ['EVENTORDERERROR'];
        $orderCnfLabels = getCodesMsg($lang_id, $codes);

        $order_id = Session::get('event_enq_id');
        $event_order_id = Session::get('merchant_order_id');

        $event_order = EventEnqOrders::select('*')->where('order_id',$event_order_id)->first();

        $response = $this->credimax_order_details($event_order_id);

        EventEnq::where('id',$order_id)
                        ->update(['payment_status' => 0]);

        EventEnqOrders::where('id',$event_order->id)
                        ->update(['payment_status' => 2,'result' => $response]);

        Session::forget(['event_enq_id','merchant_order_id','order_amount']);
        $notification = array(
            'message' => $orderCnfLabels['EVENTORDERERROR'],
            'alert-type' => 'error'
        );
        $redirectUrl = Session::get('eventEnqEmailUrl');
        return redirect($redirectUrl)->with($notification);
    }

    function eventEnqCredimaxSuccess()
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
        $codes = ['EVENTENQORDERSUCCESS','RETURNHOME','ORDERID','APPNAME','EVENTORDERERROR'];

        $orderCnfLabels = getCodesMsg($lang_id, $codes);
        $pageName = $orderCnfLabels["EVENTENQORDERSUCCESS"];
        $projectName = $orderCnfLabels["APPNAME"];

        $order_id = Session::get('event_enq_id');
        $event_order_id = Session::get('merchant_order_id');

        $response = $this->credimax_order_details($event_order_id);

        $result = json_decode($response, true);

        if (isset($result['result']) && $result['result'] == 'SUCCESS')
        {
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

            EventEnq::where('id',$order_id)
                        ->update(['payment_status' => 1, 'payment_id' => $payment_id]);

            $event_order = EventEnqOrders::select('*')->where('order_id',$event_order_id)->first();

            EventEnqOrders::where('id',$event_order->id)
                        ->update(['payment_status' => 1, 'payment_id' => $payment_id,'result' => $response]);

                // send email to customer
                $custId = EventEnq::select('customer_id')->where('id',$event_order->event_enq_id)->first();
                $custDetails = Customer::where('id',$custId->customer_id)->first();
                $eventEnqId = EventEnq::select('event_id')->where('id',$event_order->event_enq_id)->first();
                $eventName = Events::select('event_name')->where('id',$eventEnqId->event_id)->first();
                   
                $temp_arr = [];
                $new_user = $this->getEmailTemp();
                foreach($new_user as $code )
                {
                    if($code->code == 'EENQPAY')
                    {
                        array_push($temp_arr, $code);
                    }
                }

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }
                if($event_order->payment_type == 1)
                    $paymentMethod = 'Credit card';
                if($event_order->payment_type == 2)
                    $paymentMethod = 'Debit card';


                $replace_data = array(
                    '{{orderid}}' => $event_order->order_id,
                    '{{firstname}}' => $custDetails->first_name,
                    '{{lastname}}' => $custDetails->last_name,
                    '{{baseUrl}}' => $this->getBaseUrl(),
                    '{{paymentmethod}}' => $paymentMethod,
                    '{{paymentid}}'=> $event_order->payment_id,
                    '{{amount}}' => $event_order->amount,
                    '{{eventName}}'=> $eventName->event_name
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);
                $data = [
                    'html' => $html_value,
                ];
                $subject = $temp_arr[0]['subject']." ".$event_order->order_id;
                $email = $custDetails->email;
                Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject) {
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over

                //Send Email To System Admin
                $settings = \App\Models\Settings::first();
                $emails = explode(",", $settings->email);
                Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject, $emails) {
                    $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                    $message->to($emails)->subject($subject);
                });

            Session::forget(['event_enq_id','merchant_order_id','order_amount']);
            return view('frontend.events.eventEnqOrderSuccess',compact('event_order_id','baseUrl','orderCnfLabels','megamenuFileName','mobileMegamenuFileName',
            'pageName','projectName'));
        }
        else
        {
            Session::forget(['event_enq_id','merchant_order_id','order_amount']);

            $notification = array(
                'message' => $orderCnfLabels['EVENTORDERERROR'],
                'alert-type' => 'error'
            );

            $redirectUrl = Session::get('eventEnqEmailUrl');
            return redirect($redirectUrl)->with($notification);
        }
    }

    function eventEnqBenefitsSuccess()
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
        $codes = ['SIDEBARLABEL6', 'APPNAME','HOME','PAYBUTTON','SELECTED','PRINT','QUANTITY','PRODUCT','ADDTOCART','BUYNOW',
        'PAYMENMETHOD','CONTINUE','EVENTORDERSUCCESS','EVENTORDERERROR','EVENTENQORDERSUCCESS','ORDERID','RETURNHOME'];

        $orderCnfLabels = getCodesMsg($lang_id, $codes);
        $pageName = $orderCnfLabels["EVENTORDERSUCCESS"];
        $projectName = $orderCnfLabels["APPNAME"];

        $order_id = EventEnqOrders::select('id','event_enq_id','language_id')->where('order_id',$_REQUEST['track_id'])->first();
        $event_order_id = $_REQUEST['track_id'];
        $encodedOrderId = rtrim(strtr(base64_encode($order_id->event_enq_id), '+/', '-_'), '=');

        if(!empty($order_id->language_id))
        {
            Session::put('language_id',$order_id->language_id);
        }

        // Get customer id
        $customer = EventEnq::select('id','customer_id')->where('id',$order_id->event_enq_id)->first();
        $user_id = $customer->customer_id;

        // Get customer object
        $customer = \App\Models\Customer::where('id', $user_id)->first();
        Auth::guard('customer')->login($customer);
        Session::put('customer_id',$user_id);

        /*EventEnq::where('id',$order_id->event_enq_id)
                        ->update(['payment_status' => 1, 'payment_id' => $payment_id]);*/

        $EventEnq = \App\Models\EventEnq::where('id', $order_id->event_enq_id)->first();
        $EventEnq->payment_status = 1;
        $EventEnq->payment_id = $_REQUEST['payment_id'];
        $EventEnq->save();

        /*EventEnqOrders::where('id',$order_id->id)
                        ->update(['payment_status' => 1, 'payment_id' => $_REQUEST['payment_id']]);*/


        $event_order_id = $_REQUEST['track_id'];

        $EventEnqOrders = \App\Models\EventEnqOrders::where('id', $order_id->id)->first();
        $EventEnqOrders->payment_status = 1;
        $EventEnqOrders->payment_id = $_REQUEST['payment_id'];
        $EventEnqOrders->save();

        // send email to customer
        $event_order = EventEnqOrders::select('*')->where('order_id',$_REQUEST['track_id'])->first();

        $custId = EventEnq::select('customer_id')->where('id',$order_id->event_enq_id)->first();
        $custDetails = Customer::where('id',$custId->customer_id)->first();
        $eventEnqId = EventEnq::select('event_id')->where('id',$event_order->event_enq_id)->first();
        $eventName = Events::select('event_name')->where('id',$eventEnqId->event_id)->first();

        $temp_arr = [];
        $new_user = $this->getEmailTemp();
        foreach($new_user as $code )
        {
            if($code->code == 'EENQPAY')
            {
                array_push($temp_arr, $code);
            }
        }

        if(is_array($temp_arr))
        {
            $value = $temp_arr[0]['value'];
        }
        if($event_order->payment_type == 1)
            $paymentMethod = 'Credit card';
        if($event_order->payment_type == 2)
            $paymentMethod = 'Debit card';


        $replace_data = array(
            '{{orderid}}' => $event_order->order_id,
            '{{firstname}}' => $custDetails->first_name,
            '{{lastname}}' => $custDetails->last_name,
            '{{baseUrl}}' => $this->getBaseUrl(),
            '{{paymentmethod}}' => $paymentMethod,
            '{{paymentid}}'=> $event_order->payment_id,
            '{{amount}}' => $event_order->amount,
            '{{eventName}}'=> $eventName->event_name
        );
        $html_value = $this->replaceHtmlContent($replace_data,$value);
        $data = [
            'html' => $html_value,
        ];
        $subject = $temp_arr[0]['subject']." ".$event_order->order_id;
        $email = $custDetails->email;
        Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject) {
            $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
            $message->to($email)->subject($subject);
        });
        // Send email over

        //Send Email To System Admin
        $settings = \App\Models\Settings::first();
        $emails = explode(",", $settings->email);
        Mail::send('admin.emails.order-placed', $data, function ($message) use ($email,$subject, $emails) {
            $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
            $message->to($emails)->subject($subject);
        });

        Session::forget(['event_enq_id','merchant_order_id','order_amount']);
        $notification = array(
            'message' => $orderCnfLabels['EVENTORDERSUCCESS'],
            'alert-type' => 'success'
        );

        //return redirect('eventEnq/payment/'.$encodedOrderId)->with($notification);

        return view('frontend.events.eventEnqOrderSuccess',compact('event_order_id','baseUrl','orderCnfLabels','megamenuFileName','mobileMegamenuFileName',
            'pageName','projectName'));
    }

    function eventEnqBenefitsCancel()
    {
        $order_id = EventEnqOrders::select('id','event_enq_id','language_id')->where('order_id',$_REQUEST['track_id'])->first();

        $encodedOrderId = rtrim(strtr(base64_encode($order_id->event_enq_id), '+/', '-_'), '=');

        $EventEnq = \App\Models\EventEnq::where('id', $order_id->event_enq_id)->first();
        $EventEnq->payment_status = 0;
        $EventEnq->save();

        if(!empty($order_id->language_id))
        {
            Session::put('language_id',$order_id->language_id);
        }

        /*EventEnqOrders::where('id',$order_id->id)
                        ->update(['payment_status' => 1, 'payment_id' => $_REQUEST['payment_id']]);*/


        $EventEnqOrders = \App\Models\EventEnqOrders::where('id', $order_id->id)->first();
        $EventEnqOrders->payment_status = 2;
        $EventEnqOrders->result = $_REQUEST['error'];
        $EventEnqOrders->save();

        // Get customer id
        $customer = EventEnq::select('id','customer_id')->where('id',$order_id->event_enq_id)->first();
        $user_id = $customer->customer_id;

        // Get customer object
        $customer = \App\Models\Customer::where('id', $user_id)->first();
        Auth::guard('customer')->login($customer);
        Session::put('customer_id',$user_id);

        Session::forget(['event_enq_id','merchant_order_id','order_amount']);

        $notification = array(
            'message' => $_REQUEST['error'],
            'alert-type' => 'error'
        );
        return redirect('eventEnq/payment/'.$encodedOrderId)->with($notification);
    }
}
?>