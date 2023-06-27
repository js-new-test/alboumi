<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = 'user_device';

    protected $fillable = ['user_id', 'user_device_type_id', 'device_id', 'fcm_token'];
}
