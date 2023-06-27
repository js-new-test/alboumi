<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photographers extends Model
{
    protected $table = 'photographers';

    protected $fillable = ['profile_pic','cover_photo','web','status','deleted_at'];


    public static function getPhotographerById($photgrapherID,$langId) {
      $photographerDetials = \App\Models\Photographers::select('photographers.id','profile_pic','cover_photo','web','name','about','location','experience','seo_title','seo_description','seo_keyword')
      ->join('photographer_details as PD', 'photographers.id', '=', 'PD.photographer_id')
      ->where('PD.language_id', $langId)
      ->where('photographers.id', $photgrapherID)
      ->where('photographers.status', 1)
      ->whereNull('photographers.deleted_at')
      ->first();
      return $photographerDetials;
    }

    public static function getPhotographers($langId)
    {
        $photographer = Photographers::select('photographers.id','profile_pic')
                                ->where('photographers.status',1)
                                ->whereNull('photographers.deleted_at')
                                ->get();

        $allPhotoIds = array_column($photographer->toArray(),'id');
        $photoArr = $resultArr = [];
        $defaultLangPhotographers = [];
        if(count($photographer) != 0)
        {
            $defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
            $defaultLanguageId = $defaultLanguageData['id'];

            $photographers = Photographers::select('photographers.id','profile_pic','pd.name')
                                ->join('photographer_details as pd','pd.photographer_id','=','photographers.id')
                                ->where('photographers.status',1)
                                ->where('pd.language_id',$langId)
                                ->whereNull('photographers.deleted_at')
                                ->whereNull('pd.deleted_at')
                                ->get()->toArray();

            $photoArr[] = $photographers;

            $selectedLangPhotoIds = array_column($photographers,'id');

            $notInSelectLangPhotoIds = array_diff($allPhotoIds,$selectedLangPhotoIds);

            if(!empty($notInSelectLangPhotoIds))
            {
                foreach($notInSelectLangPhotoIds as $defaultPhotoId)
                {
                    $defaultLangPhotographers[] = Photographers::select('photographers.id','profile_pic','pd.name')
                                                ->join('photographer_details as pd','pd.photographer_id','=','photographers.id')
                                                ->where('photographers.status',1)
                                                ->where('pd.language_id',$defaultLanguageId)
                                                ->where('photographers.id',$defaultPhotoId)
                                                ->whereNull('photographers.deleted_at')
                                                ->whereNull('pd.deleted_at')
                                                ->get()->toArray();
                }
            }
        }
        
        $allPhotographers = array_merge($photoArr,$defaultLangPhotographers);
        if(!empty($allPhotographers))
        {
            foreach($allPhotographers as $photographer)
            {
                foreach($photographer as $photo)
                {
                    if(!empty($photo))
                        $resultArr[] = $photo;
                }
            }
        }
        return $resultArr;

    }

    public function createPhotographerData($arrPhotographers,$menuData,$baseUrl,$cmsPages,$langId)
    {
        $categoryTabData = $level2 = [];
        $j = 0;
        $categoryTabData['title'] = "Bahrain Photographers";
        $categoryTabData['categoryId'] = "";

        if($menuData['icon_image'] != null)
            $categoryTabData['backgroundImage'] = $baseUrl.'/public/assets/images/megamenu/icon/'.$menuData['icon_image'];
        else
            $categoryTabData['backgroundImage'] = $baseUrl.'/public/assets/frontend/img/more/8Camera.jpg';

        $categoryTabData['query'] = "";
        $categoryTabData['type'] = "";

        foreach($arrPhotographers as $ek => $photoData)
        {
            $subtitle[] = $photoData['name'];
            $level2[$j]['id'] = "".$photoData['id']."";
            $level2[$j]['title'] = $photoData['name'];
            $level2[$j]['query'] = $baseUrl."/api/v1/getPhotographerProfile?language_id=".$langId.'&profile_id='.$photoData['id'];
            $level2[$j]['type'] = "4";
            $level2[$j++]['navigationFlag'] = "1";
        }
        
        if(!empty($level2))
        {
            $categoryTabData['navigationFlag'] = "0";
            $categoryTabData['subTitle'] = implode(',',$subtitle);
        }
        else
            $categoryTabData['navigationFlag'] = "1";

        $categoryTabData['level2'] = $level2;
        return $categoryTabData;
    }
}
