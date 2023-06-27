<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Notifications;
use App\Models\GlobalLanguage;
use Config;

class NotificationController extends Controller
{
    public function getNotificationList(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'language_id' => 'required|numeric',
          'user_id' => 'required|numeric',
      ]);
      if ($validator->fails()) {
          return response()->json([
            'statusCode' => 300,
            'message' => $validator->errors(),
          ]);
      }
      $lang_id = $request->language_id;
      $user_id = $request->user_id;
      $codes = ['NOTIFICATIONSNOTFOUND'];
      $notificationLabels = getCodesMsg($lang_id, $codes);
      $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
      $defaultLanguageId = $defaultLanguageData['id'];
      $page = ($request->page)?:1;
      if ($page) {
          $limit = 10;
          $offset = ($page - 1) * $limit;
      }
      $NotificationData = Notifications::select('id','notification_type','order_id','order_number','read_flag','created_at','updated_at')
                                ->where('user_id',$user_id)
                                ->offset($offset)
                                ->limit($limit)
                                ->orderBy('updated_at', 'desc')
                                ->get();

        //Set notification read flag 1
        $notification = \App\Models\Notifications::where('user_id', $user_id)
        ->where('read_flag', 0)->count();
        if($notification >= 1)
        {
            \App\Models\Notifications::where('user_id', $user_id)->update(['read_flag' => 1]);
        }

      if(count($NotificationData) == 0)
      {
        $result['statusCode'] = '300';
        $result['message'] = $notificationLabels['NOTIFICATIONSNOTFOUND'];
        return response()->json($result);
      }
      $i = 0;
      // Portfolio details array  portfolioData//
      $notification_arr = [];
      foreach ($NotificationData as $noti) {
          $notification_arr[$i]['id'] = "".$noti['order_id']."";
          $notification_msg = $this->getNotificationMsg($noti['notification_type'], $noti['order_number']);
          $notification_arr[$i]['titleText'] = $notification_msg['titleText'];
          $notification_arr[$i]['subTitleText'] = $notification_msg['subTitleText'];
        //   $notification_arr[$i]['titleText'] = "Order placed successfully";
        //   $notification_arr[$i]['subTitleText'] = "Your order ".$noti['order_number']." has been placed successfully.";
          $notification_arr[$i]['time']="".$this->get_time_ago(strtotime($noti['updated_at']))."";
          $notification_arr[$i]['type'] = '7';
          $notification_arr[$i]['navigationFlag'] = '1';
          $notification_arr[$i++]['query'] = $this->getBaseUrl()."/api/v1/orderdetails?order_id=".$noti['order_id']."&language_id=".$lang_id;
      }

      //portfolio companent
      $NotificationList=[];
      $NotificationList['list']=$notification_arr;

      // To get component one

      $compnant_one=[];
      $compnant_one['componentId']='notification';
      $compnant_one['sequenceId']='1';
      $compnant_one['isActive']='1';
      $compnant_one['pageSize']='10';
      $compnant_one['pageNo']='1';
      $compnant_one['notificationListData']=$NotificationList;

      //Notification over
      $result['status'] = "OK";
      $result['statusCode'] = 200;
      $result['message'] = "Success";
      $result['component'][] = $compnant_one;
      return response()->json($result);
    }

    //To get the time ago
    public function get_time_ago($time_stamp)
    {
        $timeconstant=config('app.TIMEZONE_DIFF');
        $time_difference = strtotime('now +'.$timeconstant.' minutes') - $time_stamp;

        if ($time_difference >= 60 * 60 * 24 * 365.242199)
        {
            /*
             * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 365.242199 days/year
             * This means that the time difference is 1 year or more
             */
            return $this->get_time_ago_string($time_stamp, 60 * 60 * 24 * 365.242199,'years ago');
        }
        elseif ($time_difference >= 60 * 60 * 24 * 30.4368499)
        {
            /*
             * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 30.4368499 days/month
             * This means that the time difference is 1 month or more
             */
            return $this->get_time_ago_string($time_stamp, 60 * 60 * 24 * 30.4368499,'months ago');
        }
        elseif ($time_difference >= 60 * 60 * 24)
        {
            /*
             * 60 seconds/minute * 60 minutes/hour * 24 hours/day
             * This means that the time difference is 1 day or more
             */
            return $this->get_time_ago_string($time_stamp, 60 * 60 * 24,'days ago');
        }
        elseif ($time_difference >= 60 * 60)
        {
            /*
             * 60 seconds/minute * 60 minutes/hour
             * This means that the time difference is 1 hour or more
             */
            return $this->get_time_ago_string($time_stamp, 60 * 60,'hours ago');
        }
        else
        {
            /*
             * 60 seconds/minute
             * This means that the time difference is a matter of minutes
             */
            return $this->get_time_ago_string($time_stamp, 60,'minutes ago');
        }
    }

    //To get the time ago string

    public function get_time_ago_string($time_stamp, $divisor, $time_unit)
    {
        $timeconstant=config('app.TIMEZONE_DIFF');
        $time_difference = strtotime('now +'.$timeconstant.' minutes') - $time_stamp;
        $time_units      = floor($time_difference / $divisor);

        settype($time_units, 'string');

        if ($time_units === '0')
        {
            return 'less than 1 ' . $time_unit . '';
        }
        elseif ($time_units === '1')
        {
            return '1 ' . $time_unit . '';
        }
        else
        {
            /*
             * More than "1" $time_unit. This is the "plural" message.
             */
            // TODO: This pluralizes the time unit, which is done by adding "s" at the end; this will not work for i18n!
            return $time_units . ' ' . $time_unit . '';
        }
    }

    public function getNotificationMsg($notification_type, $order_number)
    {
        if($notification_type == "OP"){
            $result['titleText'] = "Order placed successfully";
            $result['subTitleText'] = "Your order ".$order_number." has been placed successfully.";
        }
        elseif ($notification_type == "OD") {
            $result['titleText'] = "Order delivered successfully";
            $result['subTitleText'] = "Your order ".$order_number." has been delivered successfully.";
        }
        elseif ($notification_type == "OS") {
            $result['titleText'] = "Order shipped successfully";
            $result['subTitleText'] = "Your order ".$order_number." has been shipped successfully.";
        }
        elseif ($notification_type == "OC") {
            $result['titleText'] = "Order cancelled successfully";
            $result['subTitleText'] = "Your order ".$order_number." has been cancelled successfully.";
        }
        return $result;
    }
}
