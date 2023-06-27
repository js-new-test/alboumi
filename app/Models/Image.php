<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    public $table = 'images';
    // public $primaryKey = 'id';

    protected $fillable = ['name'];

    /**
     * Get Path prefix to Images folder
     *
     * @return mixed
     */
    public static function getPathPrefix() {
        return Storage::disk()->getDriver()->getAdapter()->getPathPrefix();
    }

    public static function make($file)  {

    }
}
