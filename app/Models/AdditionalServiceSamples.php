<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalServiceSamples extends Model
{
    use HasFactory;

    protected $table = 'additional_service_samples';

    protected $fillable = ['addi_serv_id', 'image'];

}
