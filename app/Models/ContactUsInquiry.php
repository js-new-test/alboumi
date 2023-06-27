<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUsInquiry extends Model
{
	public $table = 'contactus_inquiry';

    public function contactUsReply()
    {
    	return $this->hasOne('App\Models\ContactUsReply', 'contactus_inquiry_id', 'id');
    }

    public static function saveContactUsInquiry($request)
    {
        $contact_us_inquiry = new \App\Models\ContactUsInquiry;
        $contact_us_inquiry->name = $request->fullname;
        $contact_us_inquiry->email = $request->email;
        $contact_us_inquiry->message = $request->text_message;
        $contact_us_inquiry->save();
        return $contact_us_inquiry;        
    }
}
