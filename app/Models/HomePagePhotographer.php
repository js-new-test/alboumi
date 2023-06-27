<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePagePhotographer extends Model
{
    use HasFactory;

    public $table = 'home_page_photographer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_id',
        'name',
        'image',
        'status',
        'is_deleted'     
    ];
}
