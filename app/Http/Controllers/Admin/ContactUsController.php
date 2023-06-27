<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Models\ContactUsInquiry;
use App\Models\ContactUsReply;
use Mail;
use App\Traits\ReuseFunctionTrait;

class ContactUsController extends Controller
{
	use ReuseFunctionTrait;

	public function getContactUs()
	{
		$baseUrl = $this->getBaseUrl();
		return view('admin.contactUs.contactUs', compact('baseUrl'));
	}

	public function getContactUsData(Request $request)
	{
		$id = Auth::guard('admin')->user()->id;
        $user_role = getUserRole($id);

        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();
		
		$contactUsInquiry = ContactUsInquiry::select(DB::raw("date_format(created_at,'%Y-%m-%d %h:%i:%s') as a_created_at"),'name','email','message','ip_address','id')
												->where('is_deleted',0)
												->orderBy('created_at','desc');
												if ($request['startDate'] != "" || $request['endDate'] != "") 
												{
													$contactUsInquiry = $contactUsInquiry->whereBetween(DB::raw('DATE(created_at)'),[$request['startDate'], $request['endDate']]);
												}
												$contactUsInquiry = $contactUsInquiry->get();

		return DataTables::of($contactUsInquiry)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
	}

	public function getContactUsInquiry(Request $request)
	{
		if ($request['activity'] == "contactUsReplyModal") {
			$contactUsInquiry = ContactUsInquiry::find($request['inquiryId']);
		}else if ($request['activity'] == "contactUsMessageModal") {
			$contactUsInquiry = ContactUsInquiry::with('contactUsReply')->find($request['inquiryId']);
		}
		return $contactUsInquiry;
	}

	public function postContactUsReply(Request $request)
	{
		$contactUsReply = new ContactUsReply;
		$contactUsReply->contactus_inquiry_id = $request['inquiryId'];
		$contactUsReply->reply = $request['replyMessage'];
		$contactUsReply->created_by = Auth::guard('admin')->user()->id;

		if ($contactUsReply->save()) {
			
			$contactUsInquiry = ContactUsInquiry::find($request['inquiryId']);
			$contactUsInquiry->is_replied = 1;
			$contactUsInquiry->save();

			// Send email start                                  
			$email = $contactUsInquiry->email;
            $temp_arr = []; 
            $cont_us_reply = $this->getEmailTemp();
			$adminResponse = $contactUsReply->reply;
            foreach($cont_us_reply as $code )
            {            
                if($code->code == 'CONTUSREPLY')
                {
                    array_push($temp_arr, $code);
                }
            }         

            if(is_array($temp_arr))
            {
                $value = $temp_arr[0]['value'];
            }            

            $replace_data = array(
                '{{name}}' => $contactUsInquiry->name,  
				'{{response}}' => $adminResponse              
            );
			// dd($replace_data);
            $html_value = $this->replaceHtmlContent($replace_data,$value);            
            $data = [                
                'html' => $html_value,                
            ]; 
            $subject = $temp_arr[0]['subject'];
            Mail::send('admin.emails.contact-us-reply', $data, function ($message) use ($email,$subject) {                
                $message->from(config('app.FROM_EMAIL_ADDRESS'), 'Alboumi');
                $message->to($email)->subject($subject);
            });
            // Send email over

			return array(
	            'success' => true,
	            'message' => trans('Sent Successfully!')
	        );
		}

		return array(
	            'success' => false,
	            'message' => trans('messages.success.product.otherInfo_add')
	        );
	}

	public function postDeleteInquiry(Request $request)
	{
		$contactUsInquiry = ContactUsInquiry::find($request['inquiryIdForDelete']);
		$contactUsInquiry->is_deleted = 1;
		if ($contactUsInquiry->save()) {
			return array(
	            'success' => true,
	            'message' => "Inquiry deleted Successfully"
	        );
		}
		
	}

	public function uploadCKeditorContactUsImage(Request $request)
    {
        $folder_name = 'ckeditor-contact-us-image';
        uploadCKeditorImage($request, $folder_name);
    }
}
