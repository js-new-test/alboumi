<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Agent;
use Auth;
use DB;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;

class MyAccountController extends Controller
{
    use ReuseFunctionTrait;

    public function getMyAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
                ], 300);
            }
            $lang_id = ($request->language_id == '') ? $this->getDefaultLanguage() : $request->language_id;

            //Localization
            $codes = ['MYORDER','SAVEDCARDS','MYADDRESSES','HELPCENTER','MYPROFILE','MYEVENTGALLERY'
            ,'CHANGELANGUAGE','ABOUTUS','CONTACTUS','PRIVACYPOLICY', 'POLICY','SIDEBARLABEL7','SUCCESS','SUCCESSSTATUS',
            'SIDEBARLABEL5','MYBILLINGADDRESS'];
            $myAccountLabels = getCodesMsg($lang_id, $codes);

            $global_language = \App\Models\GlobalLanguage::where('id', $request->language_id)->where('is_default', 1)
            ->where('status', 1)->first();
            
            if(empty($global_language))
            {
                $about_us_url = url('/page/about-us')."?language_id=".$request->language_id;
                $privacy_policy_url = url('/page/privacy-policy')."?language_id=".$request->language_id;
            }
            else
            {
                $about_us_url = url('/page/about-us');
                $privacy_policy_url = url('/page/privacy-policy');
            }

            $my_acc_arr = [
                [$myAccountLabels["MYPROFILE"], "MyProfile", $this->getBaseUrl()."/public/app-icon/myprofile.png","1"],
                [$myAccountLabels["MYORDER"], "MyOrders", $this->getBaseUrl()."/public/app-icon/myorder.png","2"],
                [$myAccountLabels["MYADDRESSES"], "MyAddresses", $this->getBaseUrl()."/public/app-icon/myaddress.png","3"],
                [$myAccountLabels["MYBILLINGADDRESS"],"MyBillingAddresses", $this->getBaseUrl()."/public/app-icon/myaddress.png","4"],
                [$myAccountLabels["SIDEBARLABEL5"],"MyEnquiries", $this->getBaseUrl()."/public/app-icon/myeventgallery.png","5"],
                [$myAccountLabels["MYEVENTGALLERY"],"MyEventsGallery", $this->getBaseUrl()."/public/app-icon/myeventgallery.png","6"],
                [$myAccountLabels["CHANGELANGUAGE"],"ChangeLanguage", $this->getBaseUrl()."/public/app-icon/launguage.png","7"],
                [$myAccountLabels["ABOUTUS"],$about_us_url, $this->getBaseUrl()."/public/app-icon/aboutus.png","8"],
                [$myAccountLabels["HELPCENTER"], "HelpCenter", $this->getBaseUrl()."/public/app-icon/helpcenter.png","9"],                
                //[$myAccountLabels["CONTACTUS"], $contact_us_url, $this->getBaseUrl()."/public/app-icon/helpcenter.png"],                                
                // [$myAccountLabels["PRIVACYPOLICY"], $privacy_policy_url, $this->getBaseUrl()."/public/app-icon/helpcenter.png","10"],                                
                [$myAccountLabels["POLICY"], "Legal", $this->getBaseUrl()."/public/app-icon/privacypolicy.png","10"],                                
                [$myAccountLabels["SAVEDCARDS"], "SavedCards", $this->getBaseUrl()."/public/app-icon/savedcard.png","12"],                
                [$myAccountLabels["SIDEBARLABEL7"],"popup", $this->getBaseUrl()."/public/app-icon/logout.png","11"]                
            ];

            //Move logout at last position
            foreach ($my_acc_arr as $key => $value) {
                if($value[0] == $myAccountLabels["SIDEBARLABEL7"]) {
                    moveElement($my_acc_arr, 10, 11);
                }
            }

            $i = 0;
            $my_acc_details = [];
            foreach ($my_acc_arr as $key => $value) {                                             
                $my_acc_details[$i]['sequenceId'] = $value[3];
                $my_acc_details[$i]['isActive'] = $value[1] == "SavedCards" ? "0" : "1";
                $my_acc_details[$i]['icon'] = $value[2];
                if($value[0] == $myAccountLabels["ABOUTUS"]){
                    $my_acc_details[$i]['title'] = $value[0];
                    $my_acc_details[$i]['type'] = "WEB";
                }
                // elseif ($value[0] == $myAccountLabels["PRIVACYPOLICY"]) {
                //     $my_acc_details[$i]['title'] = $value[0];
                //     $my_acc_details[$i]['type'] = "WEB";
                // }
                elseif ($value[0] == $myAccountLabels["SIDEBARLABEL7"]) {
                    $my_acc_details[$i]['title'] = $value[0];
                    $my_acc_details[$i]['type'] = "POPUP";
                }
                else{
                    $my_acc_details[$i]['title'] = $value[0];
                    $my_acc_details[$i]['type'] = "NATIVE";
                }
                $my_acc_details[$i]['navigation'] = $value[1];
                $i++;
            }     

            $component = [];
            $component['componentId'] = 'listElements';
            $component['sequenceId'] = "1";
            $component['isActive'] = "1";
            $component['listElementsData'] = $my_acc_details;

            $result['status'] = $myAccountLabels['SUCCESSSTATUS'];
            $result['statusCode'] = '200';
            $result['message'] = $myAccountLabels["SUCCESS"];
            $result['component'][] = $component;
            return response()->json($result);
        } 
        catch (\Exception $th) {
            return handleServerError($lang_id);
        }
    }


    public function myProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                  'statusCode' => 300,
                  'message' => $validator->errors(),
                ],300);
            }
            //Localization
            $codes = ['CUSTOMERIDREQ','CUSTOMERNOTFOUND','SUCCESS','SUCCESSSTATUS'];
            $myProfileLabels = getCodesMsg($request->language_id, $codes);

            $lang_id = $request->language_id;
            $msg = [
                'customerId.required' => $myProfileLabels["CUSTOMERIDREQ"],
            ];

            $validator = Validator::make($request->all(), [
                'customerId' => 'required',
            ],$msg);

            if ($validator->fails()) {
                return response()->json([
                    'statusCode' => 300,
                    'message' => $validator->errors(),
                ], 300);
            }

            $customer = \App\Models\Customer::where('id', $request->customerId)->first();
            if(!$customer)
            {
                $result['statusCode'] = '300';
                $result['message'] = $myProfileLabels['CUSTOMERNOTFOUND'];
                return response()->json($result, 300);
            }

            $result['status'] = $myProfileLabels['SUCCESSSTATUS'];
            $result['statusCode'] = '200';
            $result['message'] = $myProfileLabels["SUCCESS"];
            $result['firstName'] = $customer->first_name;
            $result['lastName'] = $customer->last_name;
            $result['gender'] = $customer->gender;
            $result['genderId'] = ($customer->gender == "Male") ? 1 : 0;
            $result['mobileCode'] = "+966";
            $result['mobile'] = $customer->mobile;
            $result['dateOfBirth'] = $customer->date_of_birth;
            $result['email'] = $customer->email;
            $result['loyalty_number'] = $customer->loyalty_number ? $customer->loyalty_number : "";
            $result['loyalty_flag'] = (string) $customer->loyalty_flag;
            return response()->json($result);
        } catch (\Exception $th) {
            return handleServerError($lang_id);
        }
    }

    public function updateProfile(Request $request)
    {
        $codes = ['CUSTOMERIDREQ','CUSTOMERPROFILEUPDATESUCC','EMAILALREADYEXISTS',
        'DATENOTINPROPFORMAT','NOTVALIDEMAIL','MOBILENUM','GENDERINVLID','MOBILEMUSTBE8DIGIT'];
        $updateProfileLabels = getCodesMsg($request->language_id, $codes);

        $msg = [
            'customerId.required' => $updateProfileLabels["CUSTOMERIDREQ"],
            'email.email' => $updateProfileLabels["NOTVALIDEMAIL"],
            'mobile.regex' => $updateProfileLabels["MOBILENUM"],
            'mobile.min' => $updateProfileLabels["MOBILEMUSTBE8DIGIT"],
            // 'dateOfBirth.date_format' => $updateProfileLabels["DATENOTINPROPFORMAT"],
            'gender.in' => $updateProfileLabels["GENDERINVLID"],
        ];

        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
            'customerId' => 'required|numeric',
            'mobile' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'email' => 'email',
            // 'dateOfBirth' => 'date_format:Y-m-d',
            'gender' => 'in:Male,Female'
        ],$msg);

        if ($validator->fails()) {
            return response()->json([
            'statusCode' => 300,
            'message' => $validator->errors(),
            ],300);
        }

        $customer = \App\Models\Customer::where('email', $request->email)->where('id','<>',$request->customerId)
        ->where('is_deleted', 0)->first();
        if($customer)
        {
            $result['statusCode'] = '300';
            $result['message'] = $updateProfileLabels['EMAILALREADYEXISTS'];
            return response()->json($result, 300);
        }

        $customer = \App\Models\Customer::where('id', $request->customerId)
        ->where('is_deleted', 0)->first();
        $customer->first_name = $request->firstName;
        $customer->last_name = $request->lastName;
        $customer->date_of_birth = $request->dateOfBirth;
        $customer->email = $request->email;
        $customer->mobile = $request->mobile;
        $customer->gender = $request->gender;
        $customer->loyalty_number = $request->loyalty_number ? $request->loyalty_number : "";
        $customer->save();
        $result['statusCode'] = '200';
        $result['message'] = $updateProfileLabels['CUSTOMERPROFILEUPDATESUCC'];
        return response()->json($result);
    }
}

function moveElement(&$array, $a, $b) {
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
}