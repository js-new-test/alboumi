<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempCartImages extends Model
{
    use HasFactory;

    protected $table = 'temp_cart_images';
    public $timestamps = false;
}
