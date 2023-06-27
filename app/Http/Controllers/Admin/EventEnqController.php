<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalLanguage;
use App\Models\EventEnq;
use App\Models\AdditionalService;
use App\Models\EventEnqUploadedImages;
use Auth;
use Validator;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Traits\CommonTrait;
use Storage;
use Aws\S3\S3Client;
use Intervention\Image\ImageManagerStatic as Image;
use Mail;

class EventEnqController extends Controller
{
    use CommonTrait;
    public function getEventEnquiries()
    {
        $id = Auth::guard('admin')->user()->id;
        $languages = GlobalLanguage::getAllLanguages();
        $baseUrl = $this->getBaseUrl();
        $otherLanguages = $this->getNonDefaultLanguage()->pluck('id')->toArray();
        $users = \App\Models\User::select('users.id', 'users.firstname','users.lastname')->leftJoin('role_user','role_user.admin_id','=','users.id')
        ->leftJoin('roles','roles.id','=','role_user.role_id')
        ->where(['users.is_active' => '1', 'users.is_verify' => '1', 'users.is_deleted' => 0,
        'roles.id' => '2', 'roles.is_deleted' => 0])->get();
        $user_role = getUserRole($id);
        return view('admin.eventEnquiry.index',compact('languages','baseUrl','otherLanguages',
        'users','user_role'));
    }

    public function getEventEnqList(Request $request)
    {
        // dd($request->all());
        $id = Auth::guard('admin')->user()->id;
        $user_role = getUserRole($id);

        $timezone = \App\Models\UserTimezone::where('user_id', $id)->pluck('zone')->first();

        DB::statement(DB::raw('set @rownum=0'));
        DB::enableQueryLog();
        $eventEnq = EventEnq::select('event_enquiry.id','event_enquiry.full_name','event_enquiry.status','payment_status','free_photo_download',
                            DB::raw('@rownum  := @rownum  + 1 AS rownum'),'event_date','price_per_photo','total_amount','advance_payment',
                            DB::raw("date_format(event_enquiry.created_at,'%Y-%m-%d %h:%i:%s') as a_created_at"),
                            'e.event_name','p.package_name','additional_pkg_ids','event_enquiry.photographer_id','users.firstname', 'users.lastname')
                            ->leftjoin('events as e','e.id','=','event_enquiry.event_id')
                            ->leftjoin('packages as p','p.id','=','event_enquiry.package_id')
                            ->leftjoin('users','users.id','=','event_enquiry.photographer_id')
                            ->where('event_enquiry.language_id', $request['lang_id'])
                            ->orderBy('event_enquiry.created_at','desc');
                            if($user_role->role_title == "Photographer")
                            {
                                $eventEnq = $eventEnq->where('event_enquiry.photographer_id', $id);
                            }
                            if(!empty($request['eventId'])) {
                                $eventEnq = $eventEnq->where('event_enquiry.event_id',$request['eventId']);
                            }
                            if(!empty($request['packageId'])) {
                                $eventEnq = $eventEnq->where('event_enquiry.package_id',$request['packageId']);
                            }
                            if(isset($request['status']) && $request['status'] != "") {
                                $eventEnq = $eventEnq->where('event_enquiry.status',$request['status']);
                            }
                            if(isset($request['payment']) && $request['payment'] != "all" && $request['payment'] != "") {
                                $eventEnq = $eventEnq->where('event_enquiry.payment_status',$request['payment']);
                            }
                            if($request['startDate'] != "" || $request['endDate'] != "") {
                                $eventEnq = $eventEnq->whereBetween(DB::raw('DATE(event_date)'),[$request['startDate'], $request['endDate']]);
                            }
                            if($request['photographer'] == "assigned" && $request['photographer'] != "all") {
                                $eventEnq = $eventEnq->where('event_enquiry.photographer_id','!=', 0);
                            }
                            if($request['photographer'] == "not_assigned" && $request['photographer'] != "all") {
                                $eventEnq = $eventEnq->where('event_enquiry.photographer_id', 0);
                            }
        $eventEnq = $eventEnq->get();
        // dd(DB::getQueryLog());
        foreach($eventEnq as $event)
        {
            $additionalPackageDetails = [];
            $additional_pkgs = explode(",", $event->additional_pkg_ids);
            foreach($additional_pkgs as $additionalPkgId)
            {
                $temp = AdditionalService::find($additionalPkgId);
                if(!empty($temp))
                {
                    $additionalPackageDetails[] = $temp->name;
                }
            }
            $event['additional_package_name'] = implode(",",$additionalPackageDetails);
        }
        
        return Datatables::of($eventEnq)->editColumn('user_zone', function () use($timezone){
            return $timezone;
        })->make(true);
    }

    public function addAllocPhotographer(Request $request)
    {
        $eventEnquiry = \App\Models\EventEnq::where('id', $request->event_enq_id)->first();
        if(!empty($eventEnquiry))
        {
            $eventEnquiry->photographer_id = $request->photographer_id;
            $eventEnquiry->save();
            $result['status'] = 'true';
            $result['msg'] = config('message.EventEnquiery.PhotograperAllocated');
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = config('message.500.SomeThingWrong');
            return $result;
        }
    }

    public function changeEventEnqStatus(Request $request)
    {
        $eventEnquiry = EventEnq::where('id', $request->event_enq_id)->first();
        if(!empty($eventEnquiry))
        {
            $eventEnquiry->status = $request->enqStatusId;
            $eventEnquiry->save();
            
            // Send email start           
            $temp_arr = [];
            $new_user = $this->getEmailTemp();
            foreach($new_user as $code )
            {
                if($code->code == 'EVNTSTTUS')
                {
                    array_push($temp_arr, $code);
                }
            }

            if(is_array($temp_arr))
            {
                $value = $temp_arr[0]['value'];
            }

            $baseUrl = $this->getBaseUrl();
            $id_encoded = rtrim(strtr(base64_encode($eventEnquiry->id), '+/', '-_'), '=');
            $link = $baseUrl."/eventEnq/payment/".$id_encoded;
            $replace_data = array(
                '{{customer}}' => $eventEnquiry->full_name,
                '{{advance_payment}}' => $eventEnquiry->advance_payment,                
                '{{link}}' => $link,
            );
            $html_value = $this->replaceHtmlContent($replace_data,$value);
            $data = [
                'html' => $html_value,
            ];
            $subject = $temp_arr[0]['subject'];
            $email = $eventEnquiry->email;
            Mail::send('admin.emails.event-enquiry', $data, function ($message) use ($email,$subject) {
                $message->from('testmail.magneto@gmail.com', 'Alboumi');
                $message->to($email)->subject($subject);
            });
            // Send email over

            $result['status'] = 'true';
            $result['msg'] = config('message.EventEnquiery.EnqStatusChanged');
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = config('message.500.SomeThingWrong');
            return $result;
        }
    }

    public function getPhotos($enqId)
    {
        $baseUrl = $this->getBaseUrl();
        return view('admin/eventEnquiry/enqPhotos/enqPhotosList',compact('baseUrl','enqId'));
    }

    public function getPhotosList()
    {
        $photos = EventEnqUploadedImages::where('event_enq_id',$_GET['enqId'])->whereNull('deleted_at')->get();
        $i = 0;
        $photosList = [];
        foreach($photos as $photo)
        {
            $photosList[$i]['id'] = $photo['id'];
            $photosList[$i++]['image'] = $this->getS3ImagePath($photo['event_enq_id'],$photo['photos']);
        }
        return Datatables::of($photosList)->make(true);
    }

    public function photosAddView($enqId)
    {
        $baseUrl = $this->getBaseUrl();
        return view('admin/eventEnquiry/enqPhotos/addPhotos',compact('baseUrl','enqId'));
    }

    public function addPhotos(Request $request)
    {
        $waterMarkUrl = public_path('/assets/images/watermarkLogo.png');
            
        if($request->hasFile('upload_imgs'))
        {
            foreach($request->file('upload_imgs') as $file)
            {
                $imageName = $request->enqId.'/'.$file->getClientOriginalName();
                $image = $file;
                $t = Storage::disk('s3')->put($imageName, file_get_contents($image), 'public');

                $img = Image::make($image->getRealPath());
                $img->insert($waterMarkUrl, 'center', 0, 0);
                $img->save(public_path('assets/images/'.$file->getClientOriginalName()));

                $watermarkImg = public_path('assets/images/'.$file->getClientOriginalName());
                $watermarkImgPath = $request->enqId.'/watermark/'.$file->getClientOriginalName();
                Storage::disk('s3')->put($watermarkImgPath, file_get_contents($watermarkImg), 'public');

                if(file_exists($watermarkImg))
                {
                    unlink($watermarkImg);
                }

                //check if free download flag is 1 in event enq table
                $freeDownloadFlag = EventEnq::select('free_photo_download')
                                            ->where('id',$request->enqId)
                                            ->first();

                $enqPhoto = new EventEnqUploadedImages;
                $enqPhoto->event_enq_id = $request->enqId;
                $enqPhoto->photos = $file->getClientOriginalName();

                if($freeDownloadFlag->free_photo_download)
                    $enqPhoto->flag_purchased = 1;
                
                $enqPhoto->save();
            }
        }

        $notification = array(
            'message' => 'Photos uploaded successfully!',
            'alert-type' => 'success'
        );
        return redirect('admin/eventEnq/photos/getPhotos/'.$request->enqId)->with($notification);
    }

    public function deletePhoto(Request $request)
    {
        if(!empty($request->photo_id))
        {
            foreach($request->photo_id as $photoId)
            {
                $photo = EventEnqUploadedImages::where('id', $photoId)->first();
                $photo->deleted_at = Carbon::now();
                $photo->save();
                Storage::disk('s3')->delete($request->eventEnqId.'/'.$photo->photos);
                Storage::disk('s3')->delete($request->eventEnqId.'/watermark/'.$photo->photos);
            }
            $result['status'] = 'true';
            $result['msg'] = "Photo Deleted Successfully!";
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = "Please select at least one photo!!";
            return $result;
        }
    }

    public function viewEnqDetails($enqId)
    {
        $eventEnq = EventEnq::select('event_enquiry.id','event_enquiry.full_name','event_enquiry.status','payment_status',
                            'event_date','event_enquiry.email','event_time','p.price as pkgPrice',
                            'e.event_name','p.package_name','additional_pkg_ids','event_enquiry.photographer_id')
                            ->leftjoin('events as e','e.id','=','event_enquiry.event_id')
                            ->leftjoin('packages as p','p.id','=','event_enquiry.package_id')
                            ->leftjoin('users','users.id','=','event_enquiry.photographer_id')
                            ->where('event_enquiry.id', $enqId)
                            ->get();

        foreach($eventEnq as $event)
        {
            $i = 0;
            $additionalPackageDetails = [];
            $additional_pkgs = explode(",", $event->additional_pkg_ids);
            foreach($additional_pkgs as $additionalPkgId)
            {
                $temp = AdditionalService::find($additionalPkgId);
                if(!empty($temp))
                {
                    $additionalPackageDetails['name'][] = $temp->name;
                    $additionalPackageDetails['price'][] = $temp->price;
                }
            }
            if(!empty($additionalPackageDetails))
            {
                $event['additional_package_name'] = implode(",",$additionalPackageDetails['name']);
                $event['additional_package_price'] = $additionalPackageDetails['price'];
            }
            
            //To get logged in user timezone
            $parent_id = Auth::guard('admin')->user()->id;
            $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
            $EventDate=convertTimeToTz($event['event_date'],$timezone);
            $event['event_date']=$EventDate;
        }
        $defaultLanguageData = $this->getDefaultLanguage(null);
        $defaultLanguageId = $defaultLanguageData->id;

        $currenyIdFromLang = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id',$defaultLanguageId)->first();

        $conversionRate = getCurrencyRates($currenyIdFromLang->currency_id);
        $decimalNumber=$currenyIdFromLang->decimal_number;
        $decimalSeparator=$currenyIdFromLang->decimal_separator;
        $thousandSeparator=$currenyIdFromLang->thousand_separator;
        return view('admin/eventEnquiry/view',compact('event','conversionRate','decimalNumber','thousandSeparator','decimalSeparator'));
    }

    public function updatePhotoPrice(Request $request)
    {
        $eventEnquiry = EventEnq::where('id', $request->event_enq_id)->first();
        if(!empty($eventEnquiry))
        {
            $eventEnquiry->total_amount = $request->total_amt;
            $eventEnquiry->advance_payment = $request->advance_payment;
            $eventEnquiry->price_per_photo = $request->price_per_photo;
            $eventEnquiry->free_photo_download = $request->free_photo_download;
            $eventEnquiry->save();

            // set purchased flag = 1 if free download checkbox is checked
            if($request->free_photo_download == 1)
            {
                //get all photos Id related to this enq
                $photIds = EventEnqUploadedImages::select('id')
                                                ->where('event_enq_id',$request->event_enq_id)
                                                ->whereNull('deleted_at')
                                                ->get();
                // dd($photIds);
                foreach($photIds as $photoId)
                {
                    EventEnqUploadedImages::where('id',$photoId->id)
                                            ->update(['flag_purchased'=> 1]);
                }
            }
            $result['status'] = 'true';
            $result['msg'] = 'Photo Price Updated Successfully !!';
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            $result['msg'] = config('message.500.SomeThingWrong');
            return $result;
        }
    }

    /** Developed by : Jignesh **/
    //Note : This function creates because of method collision in CommonTrait and ReuseFunctionTrait
    public function getEmailTemp($language_id = null)
    {
        if($language_id == null || $language_id == '' || !isset($language_id))
        {
            $lang = \App\Models\GlobalLanguage::where('is_default', 1)->first();
            if($lang)
            {
                $selected_lang = $lang->id;
            }
        }
        else
        {
            $lang = \App\Models\GlobalLanguage::where('id', $language_id)->first();
            if($lang)
            {
                $selected_lang = $lang->id;
            }
        }

        $email_template = \App\Models\EmailTemplate::select('email_template.code','email_template.title','email_template_details.value','email_template_details.email_template_id','email_template_details.language_id','email_template_details.subject')
        ->leftJoin('email_template_details','email_template_details.email_template_id','=','email_template.id')
        ->where('email_template_details.language_id',$selected_lang)
        ->get();
        return $email_template;
    }

    /** Developed by : Jignesh **/
    //Note : This function creates because of method collision in CommonTrait and ReuseFunctionTrait
    public function replaceHtmlContent($data,$html_value)
    {
        $html = $html_value;
        foreach ($data as $key => $value) {
            $html = str_replace($key, $value, $html);
        }
        return $html;
    }
}
?>
