<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTimezone extends Model
{
    use HasFactory;

    protected $table = 'customer_timezone';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['timezone'];

}
