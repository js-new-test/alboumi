<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterGenerator extends Model
{
    public $table = 'footer_generation';

    protected $fillable = [
        'language_id',
        'footer_group',
        'sort_order',
        'is_deleted',       
    ];

    public static function getFooterData($language_id) 
    {
        $footerData = FooterGenerator::
                select(
                    "id",
                    "footer_group",
                    'sort_order as MainOrder'
                )
                ->with("children")
                ->where('language_id', $language_id)
                ->where('is_deleted',0)
                ->orderBy('sort_order')
                ->get();

        if(count($footerData) == 0)
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];
            $footerData = FooterGenerator::
                select(
                    "id",
                    "footer_group",
                    'sort_order as MainOrder'
                )
                ->with("children")
                ->where('language_id', $defaultLanguageId)
                ->where('is_deleted',0)
                ->orderBy('sort_order')
                ->get();
        }
        return $footerData;
    }

    public function children()
    {
        return $this
                    ->hasMany('App\Models\FooterLinkSection',"footer_gen_id")
                    ->select(
                        "id",
                        "name",
                        "footer_gen_id",
                        'sort_order as SubOrder',
                        'link'
                    )
                    ->orderBy('sort_order','asc');
    }
}
