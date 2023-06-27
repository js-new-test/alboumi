<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageFeatures extends Model
{
    protected $table = 'package_features';

    protected $fillable = ['package_id', 'feature_id', 'package_value'];
}
