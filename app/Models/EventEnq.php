<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEnq extends Model
{
    protected $table = 'event_enquiry';

    protected $fillable = ['event_id','package_id','customer_id','language_id','full_name','email','event_date',
                            'event_time','photographer_count','photographer_gender','videographer_count','videographer_gender',
                        'additional_pkg_ids','status','payment_status','deleted_at'];

    public function getEventEnquiriesByCustId($custId)
    {
        $eventEnqs = EventEnq::select('event_enquiry.id','event_enquiry.created_at as enqDate','e.event_name as eventName',
                                'p.package_name as packageName','status','advance_payment','payment_status','total_amount')
                                ->leftJoin('events as e',function ($join) {
                                    $join->on('e.id','=','event_enquiry.event_id');
                                    $join->where('e.is_active','=',1);
                                    $join->whereNull('e.deleted_at');
                                })
                                ->leftJoin('packages as p',function ($join) {
                                    $join->on('p.id','=','event_enquiry.package_id');
                                    $join->where('p.is_active','=',1);
                                    $join->whereNull('p.deleted_at');
                                })
                                ->where('customer_id',$custId)
                                ->orderBy('event_enquiry.created_at','desc')
                                ->get();
        return $eventEnqs;
    }
}
