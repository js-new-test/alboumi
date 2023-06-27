<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use Auth;
use App\Traits\ReuseFunctionTrait;
use Mail;

class AramexController extends Controller
{
    use ReuseFunctionTrait;

    public function createAramexShipping(Request $request)
    {    
        $soapClient = new SoapClient(base_path().'/aramex/Shipping.wsdl');	    
        $soapClient->__setLocation(config('app.aramex.URL'));
            
        $params = array(
            'Shipments'=> array(
                'Shipment' => array(
                    'Shipper'	=> array(
                        'Reference1' 	=> $request->order_id, //'ORDER_ID',
                        'Reference2' 	=> '',
                        'AccountNumber' => config('app.aramex.AccountNumber'), //'ENV_ACCOUNTNUMBER',
                        'PartyAddress'	=> array(
                            'Line1'					=> $request->line_1, //'ARAMEX_LINE1',
                            'Line2' 				=> $request->line_2, //'ARAMEX_LINE2',
                            'Line3' 				=> '',
                            'City'					=> $request->city, //'ARAMEX_CITY',
                            'StateOrProvinceCode'	=> '',
                            'PostCode'				=> '',
                            'CountryCode'			=> $request->country_code, //'ARAMEX_COUNTRY'
                        ),
                        'Contact'		=> array(
                            'Department'			=> '',
                            'PersonName'			=> $request->contact_name, //'ARAMEX_CONTACTNAME',
                            'Title'					=> '',
                            'CompanyName'			=> $request->company_name, //'ARAMEX_COMPANY',
                            'PhoneNumber1'			=> $request->phone_number, //'ARAMEX_PHONE',
                            'PhoneNumber1Ext'		=> $request->phone_ext, //'ARAMEX_PHONEEXTENSION',
                            'PhoneNumber2'			=> '',
                            'PhoneNumber2Ext'		=> '',
                            'FaxNumber'				=> '',
                            'CellPhone'				=> $request->phone_number, //'ARAMEX_PHONE',
                            'EmailAddress'			=> $request->email, //'ARAMEX_EMAIL',
                            'Type'					=> ''
                        ),
                    ),
                                            
                    'Consignee'	=> array(
                        'Reference1'	=> $request->order_id, //'ORDER_ID',
                        'Reference2'	=> '',
                        'AccountNumber' => '',
                        'PartyAddress'	=> array(
                            'Line1'					=> $request->consignee_address_1, //'ORDER_S_ADDRESS1',
                            'Line2'					=> $request->consignee_address_2, //'ORDER_S_ADDRESS2',
                            'Line3'					=> '',
                            'City'					=> $request->consignee_city, //'ORDER_S_CITY',
                            'StateOrProvinceCode'	=> '',
                            'PostCode'				=> '',
                            'CountryCode'			=> 'BH'
                        ),
                        
                        'Contact'		=> array(
                            'Department'			=> '',
                            'PersonName'			=> $request->order_first_name.''.$request->order_last_name, //'ORDER_FIRSTNAME_LASTNAME',
                            'Title'					=> '',
                            'CompanyName'			=> $request->company_name,
                            'PhoneNumber1'			=> 17217094, //$request->consignee_phone_number, //'ORDER_S_PHONE',
                            'PhoneNumber1Ext'		=> '',
                            'PhoneNumber2'			=> '',
                            'PhoneNumber2Ext'		=> '',
                            'FaxNumber'				=> '',
                            'CellPhone'				=> $request->consignee_phone_number, //'ORDER_S_PHONE',
                            'EmailAddress'			=> $request->consignee_email, //'ORDER_EMAIL',
                            'Type'					=> ''
                        ),
                    ),
                    
                    'ThirdParty' => array(
                        'Reference1' 	=> '',
                        'Reference2' 	=> '',
                        'AccountNumber' => '',
                        'PartyAddress'	=> array(
                            'Line1'					=> '',
                            'Line2'					=> '',
                            'Line3'					=> '',
                            'City'					=> '',
                            'StateOrProvinceCode'	=> '',
                            'PostCode'				=> '',
                            'CountryCode'			=> ''
                        ),
                        'Contact'		=> array(
                            'Department'			=> '',
                            'PersonName'			=> '',
                            'Title'					=> '',
                            'CompanyName'			=> '',
                            'PhoneNumber1'			=> '',
                            'PhoneNumber1Ext'		=> '',
                            'PhoneNumber2'			=> '',
                            'PhoneNumber2Ext'		=> '',
                            'FaxNumber'				=> '',
                            'CellPhone'				=> '',
                            'EmailAddress'			=> '',
                            'Type'					=> ''							
                        ),
                    ),
                    
                    'Reference1' 				=> $request->order_id, //'ORDER_ID',
                    'Reference2' 				=> '',
                    'Reference3' 				=> '',
                    'ForeignHAWB'				=> '',
                    'TransportType'				=> 0,
                    'ShippingDateTime' 			=> time(),
                    'DueDate'					=> time(),
                    'PickupLocation'			=> '',
                    'PickupGUID'				=> '',
                    'Comments'					=> '',
                    'AccountingInstrcutions' 	=> '',
                    'OperationsInstructions'	=> '',
                    
                    'Details' => array(
                        'Dimensions' => array(
                            'Length'    => '',
                            'Width'	    => '',
                            'Height'    => '',
                            'Unit'	    => 'cm',
                        ),
                        
                        'ActualWeight' => array(
                            'Value'					=> (double) $request->actual_weight, //'FORM_WEIGHT',
                            'Unit'					=> 'Kg'
                        ),
                        
                        'ProductGroup' 			=> $request->product_group, //'FORM_PRODUCT_GROUP',
                        'ProductType'			=> $request->product_type, //'FORM_PRODUCT_TYPE',
                        'PaymentType'			=> $request->payment_type, //'FORM_PAYMENT_TYPE',
                        'PaymentOptions' 		=> '',
                        'Services'				=> '',
                        'NumberOfPieces'		=> (int) $request->no_of_pieces, //'FORM_NUMBER_OF_PIECES',
                        'DescriptionOfGoods' 	=> $request->goods_desc, //'FORM_DESCRIPTION_OF_GOODS',
                        'GoodsOriginCountry' 	=> $request->goods_origin_country, //'FORM_GOODS_ORIGIN_COUNTRY',
                        
                        'CashOnDeliveryAmount' 	=> array(
                            'Value'					=> 0,
                            'CurrencyCode'			=> ''
                        ),
                        
                        'InsuranceAmount'		=> array(
                            'Value'					=> 0,
                            'CurrencyCode'			=> ''
                        ),
                        
                        'CollectAmount'			=> array(
                            'Value'					=> 0,
                            'CurrencyCode'			=> ''
                        ),
                        
                        'CashAdditionalAmount'	=> array(
                            'Value'					=> 0,
                            'CurrencyCode'			=> ''							
                        ),
                        
                        'CashAdditionalAmountDescription' => '',
                        
                        'CustomsValueAmount' => array(
                            'Value'					=> 0,
                            'CurrencyCode'			=> ''								
                        ),
                        
                        'Items' 				=> array(
                            
                        )
                    ),
                ),
            ),
            
            'ClientInfo'=> array(
                'AccountCountryCode'	=> config('app.aramex.AccountCountryCode'), //'ENV_ACCOUNT_COUNTRY_CODE',
                'AccountEntity'		 	=> config('app.aramex.AccountEntity'), //'ENV_ACCOUNT_ENTITY',
                'AccountNumber'		 	=> config('app.aramex.AccountNumber'), //'ENV_ACCOUNT_NUMBER',
                'AccountPin'		 	=> config('app.aramex.AccountPin'), //'ENV_ACCOUNT_PIN',
                'UserName'			 	=> config('app.aramex.UserName'), //'ENV_USERNAME',
                'Password'			 	=> config('app.aramex.Password'), //'ENV_PASSWORD',
                'Version'			 	=> config('app.aramex.Version'), //'ENV_VERSION'
            ),

            'Transaction' 			=> array(
                'Reference1'			=> '',
                'Reference2'			=> '', 
                'Reference3'			=> '', 
                'Reference4'			=> '', 
                'Reference5'			=> '',									
            ),

            'LabelInfo'				=> array(
                'ReportID' 				=> 9201,
                'ReportType'			=> 'URL',
            ),
        );
                        
        try {
            $auth_call = $soapClient->CreateShipments($params);
            
            // Success
            if(empty($auth_call->HasErrors) && !empty($auth_call->Shipments->ProcessedShipment->ID) && !empty($auth_call->Shipments->ProcessedShipment->ShipmentLabel->LabelURL))
            {
                $tracking_number = $auth_call->Shipments->ProcessedShipment->ID;
                $label_url = $auth_call->Shipments->ProcessedShipment->ShipmentLabel->LabelURL;

                //Store data in order table
                $order_status_o = \App\Models\OrderStatus::where('slug', 'shipped')->where('status_type', 1)->first();
                $order = \App\Models\Orders::where('id', $request->order_primary_id)->first();
                $order->label_url = $label_url;
                $order->tracking_number = $tracking_number;
                $order->shipdate = date('Y-m-d');
                $order->order_status_id = $order_status_o->id;
                $order->save();

                $order_status_op = \App\Models\OrderStatus::where('slug', 'shipped')->where('status_type', 2)->first();
                $data = [
                    'tracking_number' => $tracking_number,
                    'carrier' => 'Aramex',
                    'order_status_id' => $order_status_op->id, 
                ];

                //Update Order Products Table
                \App\Models\OrderProducts::where('order_id', $request->order_primary_id)->update($data);

                //Update Order Activity Table
                $user_id = Auth::guard('admin')->user()->id;
                $order_activity = new \App\Models\OrderActivity;
                $order_activity->order_id = $request->order_primary_id;
                $order_activity->activity = 'Order shipment was created.';
                $order_activity->created_by = $user_id;
                $order_activity->save();

                //Add Order Items Tracking Data
                $orderProducts = \App\Models\OrderProducts::where('order_id', $request->order_primary_id)->get();
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
                $order = \App\Models\Orders::where('id', $request->order_primary_id)->first();                
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

                // echo "TRACKING NUMBER : " . $tracking_number . "<br />";
                // echo "LABEL URL : " . $label_url . "<br />";
                
                $result['status'] = 'true';
                $result['msg'] = config('message.AramexConfig.AramexSuccMsg');
                return response()->json($result);
            }
            else
            {
                // error                                
                if(!empty($auth_call->HasErrors))
                {
                    if(is_array($auth_call->Shipments->ProcessedShipment->Notifications->Notification))
                    {
                        $result['status'] = 'false';
                        $result['obj_count'] = 'multiple';
                        $result['error'] = $auth_call->Shipments->ProcessedShipment->Notifications->Notification;                        
                    }
                    else
                    {
                        $result['status'] = 'false';
                        $result['obj_count'] = 'single';
                        $result['error'] = $auth_call->Shipments->ProcessedShipment->Notifications->Notification;                        
                    }                    
                }
                
                return response()->json($result);                
            }
        } catch (SoapFault $fault) {
            // Error
            $error = $fault->faultstring;
            return response()->json($error);
        }
    }
    
}
