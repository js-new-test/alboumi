<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalService extends Model
{
    use HasFactory;

    protected $table = 'additional_service';

    protected $fillable = ['name', 'image', 'text', 'status', 'is_deleted'];

    public function addServSamples()
    {
        return $this
                    ->hasMany('App\Models\AdditionalServiceSamples',"addi_serv_id")
                    ->select(
                        "id",
                        "image",
                        'addi_serv_id'
                    );
                    
    }
    public function addServRequirements()
    {
        return $this
                    ->hasMany('App\Models\AdditionalServiceRequirement',"addi_serv_id")
                    ->select(
                        "id",
                        "requirements",
                        'value',
                        'addi_serv_id',
                    );
                    
    }
}
