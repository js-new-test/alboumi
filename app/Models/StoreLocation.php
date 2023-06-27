<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocation extends Model
{
    use HasFactory;

    protected $table = 'store_location';

    protected $fillable = ['language_id','title','address_1','address_2','phone','map_url','is_deleted'];
}
