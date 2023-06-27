<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEnqUploadedImages extends Model
{
    protected $table = 'event_enq_uploaded_images';

    protected $fillable = ['event_enq_id','photos','deleted_at'];

    public function getEventGalleryListingByCustId($custId)
    {
        // dd($custId);
        $eventsGallery = EventEnqUploadedImages::select('event_enq_uploaded_images.created_at as date','e.event_name as title','ee.id','flag_purchased as isPayable')
                                            ->join('event_enquiry as ee','ee.id','event_enq_uploaded_images.event_enq_id')
                                            ->join('events as e',function ($join) {
                                                $join->on('e.id','=','ee.event_id');
                                                $join->where('e.is_active','=',1);
                                                $join->whereNull('e.deleted_at');
                                            })
                                            ->where('ee.customer_id',$custId)
                                            ->whereNull('ee.deleted_at')
                                            ->whereNull('event_enq_uploaded_images.deleted_at')
                                            ->groupBy('event_enq_id')
                                            ->orderBy('ee.created_at','desc')
                                            ->get();

        $i = 0;
        $eventGallery = [];                                        
        foreach($eventsGallery as $gallery)
        {
            $allPhotos = EventEnqUploadedImages::select('flag_purchased')
                                                ->where('event_enq_id',$gallery->id)
                                                ->whereNull('deleted_at')
                                                ->get();

            $eventGallery[$i]['id'] = "".$gallery->id."";
            $eventGallery[$i]['title'] = $gallery->title;
            foreach($allPhotos as $photo)
            {
                $j = $k = 0;
                if($photo['flag_purchased'] == 0)
                {
                    $j++;
                }
                else
                {
                    $k++;
                }
            }

            if($j > 0)
                $eventGallery[$i]['isPayable'] = "1";
            if($k > 0)
                $eventGallery[$i]['isPayable'] = "0";
            $eventGallery[$i++]['date'] = date('d M Y',strtotime($gallery->date));
        }
        return $eventGallery;
    }
}
