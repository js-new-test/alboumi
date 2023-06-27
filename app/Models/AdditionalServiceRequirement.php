<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalServiceRequirement extends Model
{
    use HasFactory;

    protected $table = 'additional_service_requirements';

    protected $fillable = ['addi_serv_id', 'value'];

}
