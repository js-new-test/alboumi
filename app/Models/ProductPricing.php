<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class ProductPricing extends Model
{
    protected $table = 'product_pricing';

    // protected $fillable = ['language_id', 'image', 'text', 'status'];
    public static function getProductVariants($prodId,$langId)
    {
        // Size attribute group id
        $sizeAttribute = config('app.sizeAttribute.size');

        $productVariants = ProductPricing::select('product_pricing.attribute_ids', 'product_pricing.id', 'product_pricing.attribute_group_ids')
                            ->where('product_id',$prodId)
                            ->whereNull('deleted_at')
                            ->orderByRaw("product_pricing.is_default DESC, product_pricing.id ASC")
                            ->get();

        $result = [];
        $i = -1;
        foreach ($productVariants as $variants)
        {
            if(!empty($variants->attribute_ids))
            {
                $i += 1;
                $option_id = $variants->id;
                $pGroups = explode(',',$variants->attribute_group_ids);

                // finding size attribute
                $flagSize = false;
                $sizeKey = "";
                foreach ($pGroups as $k => $groupId) {
                    if($sizeAttribute == $groupId)
                    {
                        $flagSize = true;
                        $sizeKey = $k;
                    }
                }

                $pVariants = explode(',',$variants->attribute_ids);
                // dd($variants);
                foreach($pVariants as $vk => $variant)
                {
                    if($flagSize && $vk != $sizeKey) continue;

                    $var = Attribute::select('attribute.id as attribute_id','ad.display_name')
                                        ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
                                        ->where('attribute.id',$variant)
                                        ->where('ad.language_id',$langId)
                                        ->whereNull('attribute.deleted_at')
                                        ->whereNull('ad.deleted_at')
                                        ->get()->toArray();
                    if(empty($var))
                    {
                        // dd('in if');
                        // dd($variant);
                        $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
                        $defaultLanguageId = $defaultLanguageData['id'];

                        DB::enableQueryLog();
                        $var = Attribute::select('attribute.id as attribute_id','ad.display_name')
                                        ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
                                        ->where('attribute.id',$variant)
                                        ->where('ad.language_id',$defaultLanguageId)
                                        ->whereNull('attribute.deleted_at')
                                        ->whereNull('ad.deleted_at')
                                        ->get()->toArray();
                                        // dd(DB::getQueryLog());
                    }

                    if(!empty($var))
                    {
                        foreach($var as $v)
                        {
                            $result[$i]['option_id'] = $option_id;
                            if(empty($result[$i]['displayName']))
                            {
                                $result[$i]['displayName'] = $v['display_name'];
                                $result[$i]['ids'] = $v['attribute_id'];
                            }
                            else
                            {
                                $result[$i]['displayName'] .= ", " . $v['display_name'];
                                $result[$i]['ids'] .= "," . $v['attribute_id'];
                            }

                        }
                    }
                }
            }
        }        //dd($result);
        return $result;

    }
    // To get variations of options
    public static function getOptionVariations($optionId,$langId)
    {
      $i = 0;
      $var= Attribute::select('attribute.id as attribute_id','ad.display_name')
      ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
      ->where('attribute.id',$optionId)
      ->where('ad.language_id',$langId)
      ->whereNull('attribute.deleted_at')
      ->whereNull('ad.deleted_at')
      ->first();
      if(empty($var))
      {
          $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
          $defaultLanguageId = $defaultLanguageData['id'];
                    $var= Attribute::select('attribute.id as attribute_id','ad.display_name')
                                    ->join('attribute_details as ad','ad.attribute_id','=','attribute.id')
                                    ->where('attribute.id',$optionId)
                                    ->where('ad.language_id',$defaultLanguageId)
                                    ->whereNull('attribute.deleted_at')
                                    ->whereNull('ad.deleted_at')
                                    ->first();
                }//dd($var);
        if(!empty($var) && isset($var['display_name']))
        return $var['display_name'];
        else
        return '';
    }
}
