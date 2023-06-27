<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';

    protected $fillable = ['language_id','service_name','service_image','short_desc','link','sort_order','status','deleted_at'];
}
