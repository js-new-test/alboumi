<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateDetails extends Model
{
    protected $table = 'email_template_details';

    protected $fillable = ['email_template_id','language_id','value','deleted_at'];
}
