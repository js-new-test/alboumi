<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';

    protected $fillable = ['user_id','notification_type','order_id','order_number','read_flag'];

    public $timestamps = false;
}
