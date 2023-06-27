<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'events';

    protected $fillable = ['event_name','event_image','is_active'];

    public static function getEvents($langId)
    {
        $events = Events::select('event_name','event_image','id','event_desc')
                        ->where('language_id',$langId)
                        ->where('is_active',1)
                        ->whereNull('deleted_at')
                        ->orderBy('sort_order')
                        ->get();

        if(count($events) == 0 )
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];

            $events = Events::select('event_name','event_image','id')
                        ->with(['children' => function($query) use($defaultLanguageId)
                        {
                            $query->where('language_id', '=', $defaultLanguageId);
                        }
                        ])
                        ->where('language_id',$defaultLanguageId)
                        ->where('is_active',1)
                        ->whereNull('deleted_at')
                        ->orderBy('sort_order')
                        ->get();
        }

        return $events;
    }

    public function children()
    {
        return $this
                    ->hasMany('App\Models\Package',"event_id")
                    ->select(
                        "id",
                        "package_name",
                        'price',
                        'discounted_price',
                        "event_id",
                        'sort_order'
                    )
                    ->where('is_active',1)
                    ->whereNull('deleted_at')
                    ->orderBy('sort_order');

    }

    public function eventFeatures()
    {
        return $this
                    ->hasMany('App\Models\EventFeatures',"event_id")
                    ->select(
                        "id",
                        "feature_name",
                        "event_id",
                    );

    }

    public function submitEnquiryEnq($request,$langId,$customerId)
    {
        $eventEnq = new EventEnq;
        $eventEnq->event_id = $request->event_id;
        $eventEnq->package_id = $request->package_id;
        $eventEnq->customer_id = $customerId;
        $eventEnq->language_id = $langId;
        $eventEnq->full_name = $request->full_name;
        $eventEnq->email = $request->email;
        $eventEnq->event_date = $request->event_date;
        $eventEnq->event_time = $request->event_time;
        $eventEnq->photographer_count = $request->photographer_count;
        $eventEnq->photographer_gender = $request->photographer_gender;
        $eventEnq->videographer_count = $request->videographer_count;
        $eventEnq->videographer_gender = $request->videographer_gender;
        $eventEnq->additional_pkg_ids = $request->additional_pkg_ids;
        if($eventEnq->save())
            return true;
        else
            return false;
    }

    public function createEventsData($arrEvents,$menuData,$baseUrl,$cmsPages,$langId)
    {
        $categoryTabData = $level2 = [];
        $j = 0;
        $categoryTabData['title'] = "Event & Occasions";
        $categoryTabData['categoryId'] = "";

        if($menuData['icon_image'] != null)
            $categoryTabData['backgroundImage'] = $baseUrl.'/public/assets/images/megamenu/icon/'.$menuData['icon_image'];
        else
            $categoryTabData['backgroundImage'] = $baseUrl.'/public/assets/frontend/img/more/8Camera.jpg';

        $categoryTabData['query'] = "";
        $categoryTabData['type'] = "";

        foreach($arrEvents as $ek => $eventData)
        {
            $subtitle[] = $eventData->event_name;
            $level2[$j]['id'] = "".$eventData->id."";
            $level2[$j]['title'] = $eventData->event_name;
            $level2[$j]['query'] = $baseUrl."/api/v1/getPackageslist?language_id=".$langId.'&event_id='.$eventData->id;
            $level2[$j]['type'] = "3";
            $level2[$j++]['navigationFlag'] = "1";
        }
        if(!empty($level2))
        {
            $categoryTabData['navigationFlag'] = "0";
            $categoryTabData['subTitle'] = implode(',',$subtitle);
        }
        else
            $categoryTabData['navigationFlag'] = "1";

        $categoryTabData['level2'] = $level2;
        return $categoryTabData;
    }

    public static function getEventList($langId)
    {
        $events = Events::select('event_name','event_image','id')
                        ->where('language_id',$langId)
                        ->where('is_active',1)
                        ->whereNull('deleted_at')
                        ->orderBy('sort_order')
                        ->get();
        return $events;
    }
}
