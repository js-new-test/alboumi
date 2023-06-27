<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocaleDetails extends Model
{
    use HasFactory;
    
    protected $table = 'locale_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['locale_id', 'language_id', 'value'];
}
