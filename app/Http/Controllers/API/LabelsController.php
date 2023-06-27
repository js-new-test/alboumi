<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use DB;
use Illuminate\Support\Str;
use Exception;

class LabelsController extends Controller
{
    public function getLabels(Request $request)
    {
        $validator = Validator::make($request->all(), [            
            'language_id' => 'required|numeric',                                    
        ]);
            
        if ($validator->fails()) {
            return response()->json([
              'statusCode' => 300,
              'message' => $validator->errors(),
            ],300);
        }

        $codes = ["OK"];
        $responseLabels = getCodesMsg($request->language_id, $codes);

        //Error Codes Labels
        $codes = [
        "error501","error502","error503","error504","error505","error506","error507","error508",
        "error509","error510","error511","error512","error513","error514","error515","error516",
        "error517","error518","error519","error520","error521","error522","error523","error524",
        "error525","error526","error527","error528","error529","error530","error531","error532",
        "error533","error534","error535","error536","error537","error538","error539","error540",
        "error541","error542","error543","error544","error545","error546","error547","error548",
        "error549","error550","error551","error552","error553","error554","error555","error556",
        "error557","error558","error559","error560","error561","error562","error563","error564",
        "error565","error566","error567","error568","error569","error570","error571","error572",
        "error573","error574","error575","error576","error577","error578","error579","error580",
        "error581","error582","error583","error584","error585","error586","error587","error588",
        "error589","error590","error591","error592","error593","error594","error595","error596",
        "error597","error598","error599"
        ];
                                        
        $errorCodesLabels = getCodesMsg($request->language_id, $codes);

        //Message Labels    
        $codes = ["status501","status502"];
        $messageLabels = getCodesMsg($request->language_id, $codes);

        //Change Password Labels
        $codes = [
        "CHANGE_PASSWORD","OLD_PASSWORD","NEW_PASSWORD","CONFIRM_PASSWORD","SAVE",
        "NOTE_TITLE","NOTE1","NOTE2","NOTE3"
        ];
        $changePassLabels = getCodesMsg($request->language_id, $codes);

        //Event Enquiry Labels    
        $codes = [
            "PERSONAL_INFO_TITLE","DESCRIPTION","YOUR_NAME_HINT","EMAIL_HINT","SELECT_TIME_HINT","SELECT_DATE_HINT",
            "NO_OF_PHOTOGRAPHER","FEMALE_HINT","NO_OF_VIDEOGRAPHER_HINT","ADDITIONAL_SERVICES_TITLE","PHOTOBOOTH",
            "CHOCOLATES","DIGITAL_ALBUM","FLOWERS","GIVEAWAY_GIFTS","MUSIC","PHOTO_FRAMES","CAKE","SEND_AN_ENQUIRY",
            "PRICE_PACKAGE","ADD_TO_ENQUIRY","SELECT_GENDER","REMOVE_FROM_ENQUIRY"
        ];
        $eventEnquiryLabels = getCodesMsg($request->language_id, $codes);

        //General Labels    
        $codes = [
            "COUNTRY","STATE","ADDRESSTYPE","QTY","APPNAME","CREATE","SKIP_BUTTON","NEW","HOME","NOTIFICATIONS",
            "CATEGORIES","CART","YOU","CLOSE","APPLY","TryAgain","LOGOUT","LOGOUTQUESTION","PAYMENT","MESSAGE",
            "SEND","DONE","YES","NO","SEARCH","REMOVE","DISCARD","APPLYCHANGES","DOWNLOAD","SELECTED","VIEW",
            "UPLOAD","PHOTOGRAPHER_TITLE","PHOTOBOOK_TITLE"
        ];
        $generalLabels = getCodesMsg($request->language_id, $codes);

        //Select Language Labels        
        $codes = ["LANGUAGE","SELECT_LANGUAGE","SAVE"];
        $selectLangaugeLabels = getCodesMsg($request->language_id, $codes);

        
        //Signin Labels        
        $codes = ["SIGNIN","SKIP","EMAILADDRESS","PASSWORD","FORGOT_PASSWORD","SIGNINBUTTON","OR","FACEBOOK",
        "GOOGLE","APPLE","NEW_HERE","CREATE_ACCOUNT","ALREADY_HAVE_ACCOUNT"];
        $signinLabels = getCodesMsg($request->language_id, $codes);

        //Signup Labels        
        $codes = ["REGSIER","FIRST_NAME","LAST_NAME","EMAIL","PASSWORD","CONFIRM_PASSWORD","SIGN_UP","REGSIER_CONFIRM",
        "LOYALTYNUMBER","REGISTER","FACEBOOK","GOOGLE","OR","APPLE","HAVE_AN_ACCOUNT","SIGNIN_BUTTON",
        "REGISTER_TITLE","SKIP"];
        $signupLabels = getCodesMsg($request->language_id, $codes);

        //ForgetPassword Labels        
        $codes = ["NEW_PASSWORD_TITLE","FORGOTPASSWORD","FORGOTPASSWORD_DESCRIPTION","EMAIL_ADDRESS","SUBMIT"];
        $forgotPassLabels = getCodesMsg($request->language_id, $codes);

        //Product List Labels        
        $codes = ["SORT","FILTER","FILTERS","CLEAR_ALL"];
        $productListLabels = getCodesMsg($request->language_id, $codes);

        //My Profil Labels        
        $codes = ["PROFILE_TITLE","PHONE_NO","WHATSAPP","EMAIL_ADDRESS","SUBMIT_BUTTON","CHANGE_PASSWORD",
        "FIRST_NAME","LAST_NAME","GENDER","BIRTHDAY"];
        $myProfileLabels = getCodesMsg($request->language_id, $codes);
        
        //Address Labels        
        $codes = ["MY_ADDRESS_TITLE","MY_ADDRESS_ADD","MY_ADDRESS_EDIT","MY_ADDRESS_DELETE","EDIT_ADDRESS_TITLE",
        "ADD_ADDRESS_TITLE","FULL_NAME","ADDRESS_LINE1_HINT","ADDRESS_LINE2_HINT","COUNTRY_HINT",
        "STATE_HINT","CITY_HINT","PINCODE_HINT","ADDRESS_TYPE_HINT","PHONE_HINT","SET_AS_DEFAULT_ADDRESS",
        "SAVE_AND_CONTINUE","FLATNO_HINT","ROAD_HINT","BUILDING_HINT","BLOCK_HINT"];
        $addressLabels = getCodesMsg($request->language_id, $codes);    
        
        //Home Labels        
        $codes = ["MYPHOTOS","MYPROJECTS","SEARCHPRODUCT"];
        $homeLabels = getCodesMsg($request->language_id, $codes);        

        //My order Labels        
        $codes = ["VIEW_DETAILS","ITEM_DETAILS","WRITE_REVIEW","ORDER_DETAILS","INVOICE","MYORDER"];
        $myOrderLabels = getCodesMsg($request->language_id, $codes);
        
        //Event and Gallery Labels        
        $codes = ["MYEVENTGALLERY","PAYBUTTON","EVENTSANDGALLARY"];
        $eventGalleryLabels = getCodesMsg($request->language_id, $codes);
        
        //Event and Occasion Labels        
        $codes = ["EVENTOCCASIONS","CHOOSEPLAN"];
        $eventOccasionLabels = getCodesMsg($request->language_id, $codes);

        //My Cart Labels        
        $codes = ["MYCART","CONTINUE","REMOVE","MOVE_TO_WISHLIST","APPLY_COUPON","HAVE_COUPONCODE","MESSAGE_SELLER",
        "QTY","CHECKOUT","PRICE_DETAILS","AMOUNT_PAYABLE","CHANGE_ADD_ADDRESS","APPLY_BUTTON","ENTER_COUPON_CODE",
        "COMMENT_FOR_SELLER","ENTER_CODE","PAYMENT","PLACEORDER","APPLY","GRANDTOTAL","WRITEMSGFORPRINTINGSTAFF",
        "ADD_ADDRESS"];
        $myCartLabels = getCodesMsg($request->language_id, $codes);
        
        //Product Details Labels        
        $codes = ["FIRST_TO_REVIEW","ENTER_AREA","CONTACT_SELLER","REPORT_ITEM","MORE","LESS","AVAILABILITY",
        "ENTERPOSTALCODE","CHECK","ADD_TO_CART","GO_TO_CART","BUY_NOW","SOLD_BY","CONTACT","FOLLOW",
        "ADD_REVIEW","REVIEWS","FOLLOWERS","OUTOFSTOCK","EMAILPLACEHOLDER","NOTIFYME","DELIVERYBY",
        "QUANTITY","TOTAL_PRICE","GITFMESSAGEHINT","PRINT","ADD_BOTH_TO_CART","SIZE","SELECT_ATTRIBUTE",
        "PREVIEW","BY","CHOOSEFILE","NOFILECHOOSEN","FILECHOOSEN","CUSTOMIZE","PRODUCTDETAILS"];
        $productDetailsLabels = getCodesMsg($request->language_id, $codes);

        //Checkout Labels        
        $codes = ["ADDNEWADDRESS","VIEWONMAP","WRITEHERE","BILLINGADDASDELIVERY"];
        $checkOutLabels = getCodesMsg($request->language_id, $codes);        
        
        //Order Confirmation Labels        
        $codes = ["CONTINUE","RETURNHOME","VIEW","SUCCESS","SUCCESSENQUIRY","ORDERCONFIRM","DESCRIPTION1",
        "DESCRIPTION2","TRANSACTIONID"
        ];
        $orderConLabels = getCodesMsg($request->language_id, $codes);

        //Payment Methods Labels        
        $codes = ["USE_YOUR","ENTER_CVV","CVV_INFO","NAME_ON_CARD","CARD_NUMBER","EXPIRY_MM","EXPIRY_YYYY",
        "ADD_CARD","CARD_INFO","NB_OPTION","GITF_CARD","CANCEL","GIFT_CARD_BALANCE","PLACE_ORDER"];
        $paymentMethodsLabels = getCodesMsg($request->language_id, $codes);

        //Track Details Labels        
        $codes = ["TRACKITEM","SHIPTO","SHIPBY"];
        $trackDetailsLabels = getCodesMsg($request->language_id, $codes);

        //Save Cards Labels        
        $codes = ["TITLE","EDIT","REMOVE"];
        $saveCardsLabels = getCodesMsg($request->language_id, $codes);
        
        //Help Center Labels        
        $codes = ["HELP_CENTER_TITLE","DETAILS","CALLUS_TEXT","OR","EMAILUS"];
        $helpCenterLabels = getCodesMsg($request->language_id, $codes);  
        
        //Select Photo Labels
        $codes = ["SELECTPHOTOS_TITLE","CAMERA","FACEBOOK","INSTAGRAM","GOOGLE_PHOTOS",
        "PHOTO","PHOTOS"];
        $selectPhotosLabels = getCodesMsg($request->language_id, $codes);      
        
        //Search Labels
        $codes = ["RECENT_TITLE","CLEAR_HISTORY","EMPTY_DESC","EMPTY_TITLE"];
        $searchLabels = getCodesMsg($request->language_id, $codes);              

        // $codes = ["addressType1","addressType2"];
        // $addressType = getCodesMsg($request->language_id, $codes);

        $codes = ["addressType1","addressType2"];
        $addressType = getCodesMsg($request->language_id, $codes);      
        $addressType_arr = [array('id'=>"1", "name" => $addressType['addressType1']),
        array('id'=>"2", "name" => $addressType['addressType2'])];

        $codes = ["MALE","FEMALE","OTHER"];
        $gender = getCodesMsg($request->language_id, $codes);      
        $gender_arr = [array('id'=>"Male", "name" => $gender['MALE']),
        array('id'=>"Female", "name" => $gender['FEMALE']),
        array('id'=>"Other", "name" => $gender['OTHER'])];

        $codes = ["MALE","FEMALE","BOTH"];
        $gender = getCodesMsg($request->language_id, $codes);
        $enquiryGender = [array('id'=>"Male", "name" => $gender['MALE']),
        array('id'=>"Female", "name" => $gender['FEMALE']),
        array('id'=>"Both", "name" => $gender['BOTH'])];

        $codes = ["EVENTLISTTITLE"];
        $eventList = getCodesMsg($request->language_id, $codes);
                       
        //My Enquiry Labels        
        $codes = ["enquiryListTitle","eventTitle","planTitle","dateTitle","advPaymentTitle",
        "agreedAmtTitle","PAYNOW"];
        $myEnquiry = getCodesMsg($request->language_id, $codes);
        
        $result['status'] = $responseLabels["OK"];
        $result['statusCode'] = '200';                
        $result['errorCodes'] = $errorCodesLabels;
        $result['messageCode'] = $messageLabels;
        $result['ChangePassword'] = $changePassLabels;
        $result['eventEnquiry'] = $eventEnquiryLabels;
        $result['general'] = $generalLabels;
        $result['selectLanguage'] = $selectLangaugeLabels;
        $result['signin'] = $signinLabels;
        $result['signup'] = $signupLabels;
        $result['forgetPassword'] = $forgotPassLabels;
        $result['productList'] = $productListLabels;
        $result['myProfile'] = $myProfileLabels;
        $result['address'] = $addressLabels;
        $result['home'] = $homeLabels;
        $result['MyOrders'] = $myOrderLabels;
        $result['eventsAndGallery'] = $eventGalleryLabels;
        $result['eventsAndOccasions'] = $eventOccasionLabels;
        $result['myCart'] = $myCartLabels;
        $result['productDetails'] = $productDetailsLabels;
        $result['Checkout'] = $checkOutLabels;
        $result['orderConfirmation'] = $orderConLabels;
        $result['getPaymentMethods'] = $paymentMethodsLabels;
        $result['trackDetails'] = $trackDetailsLabels;
        $result['savedCard'] = $saveCardsLabels;
        $result['helpCenter'] = $helpCenterLabels;
        $result['dropdown']['addressType'] = $addressType_arr;
        $result['dropdown']['gender'] = $gender_arr;
        $result['dropdown']['enquiryGender'] = $enquiryGender;
        $result['myEnquiry'] = $myEnquiry;      
        $result['selectPhotos'] = $selectPhotosLabels;
        $result['search'] = $searchLabels;
        $result['eventList'] = $eventList;
        return response()->json($result); 
    }
    
}
