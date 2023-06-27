<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFeatures extends Model
{
    use HasFactory;

    protected $table = 'event_features';

    protected $fillable = ['event_id', 'feature_name'];
}
