<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerPasswordReset extends Model
{
    use HasFactory;

    protected $table = 'customer_password_reset';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'email',        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [        
        'token',    
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public static function savePasswordResetData($request)
    {
        $forgot_password = new \App\Models\CustomerPasswordReset;
        $forgot_password->email = $request->email;
        $forgot_password->token = Str::random(60);
        $forgot_password->save();
        return $forgot_password;
    }
}
