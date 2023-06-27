<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterDetails extends Model
{
    protected $table = 'footer_details';

    protected $fillable = ['language_id','about_us','contact_email','contact_number'];
}
