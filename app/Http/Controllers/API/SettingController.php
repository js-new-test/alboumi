<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\FooterDetails;
use App\Models\GlobalLanguage;
use Config;

class SettingController extends Controller
{
    public function getHelpCenterData(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'language_id' => 'required|numeric',
      ]);
      if ($validator->fails()) {
          return response()->json([
            'statusCode' => 300,
            'message' => $validator->errors(),
          ]);
      }
      $lang_id = $request->language_id;
      $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
      $defaultLanguageId = $defaultLanguageData['id'];
      $footerData = FooterDetails::select('contact_email','contact_number','whatsapp_number')
                                ->where('language_id',$lang_id)
                                ->first();
      if(empty($footerData))
      $footerData = FooterDetails::select('contact_email','contact_number','whatsapp_number')
                                ->where('language_id',$defaultLanguageId)
                                ->first();
      $result['status'] = "OK";
      $result['statusCode'] = 200;
      $result['message'] = "Success";
      $result['email'] = $footerData['contact_email'];
      $result['phone'] = $footerData['contact_number'];
      $result['WhatsApp'] = $footerData['whatsapp_number'];
      $result['helpCenterImage']=$this->getBaseUrl().'/public/assets/frontend/img/HelpCenter.png';
      return response()->json($result);
    }
}
