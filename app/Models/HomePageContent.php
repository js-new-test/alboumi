<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageContent extends Model
{
    use HasFactory;

    public $table = 'home_page_content';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_id',
        'title',
        'description',
        'link',
        'image_text_1',
        'image_1',
        'image_text_2',
        'image_2',
        'is_deleted',      
    ];
}
