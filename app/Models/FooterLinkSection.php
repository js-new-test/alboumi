<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterLinkSection extends Model
{
    use HasFactory;

    public $table = 'footer_link_section';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'footer_gen_id',
        'name',
        'link',
        'sort_order',       
    ];

    public function parent()
    {
        return $this->belongsTo('App\Models\FooterGenerator',"footer_gen_id","id");
    }
}
