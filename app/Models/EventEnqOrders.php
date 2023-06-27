<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class EventEnqOrders extends Model
{
    protected $table = 'event_enquiry_orders';

    protected $fillable = ['event_enq_id ','order_id','payment_id','payment_type','amount','result','payment_status'];
}
?>