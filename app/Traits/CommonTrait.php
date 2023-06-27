<?php

namespace App\Traits;

use Response;
use App\Models\GlobalLanguage;
use Aws\S3\S3Client;

trait CommonTrait
{
	/*
		get default language and piece of default language based on parameter value
	*/
	public function getDefaultLanguage($part)
	{
		$defaultLanguage = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
		if ($part != null) {
			$defaultLanguage = $defaultLanguage['language']["$part"];
		}
		return $defaultLanguage;
	}

	/*
		get non default language list
	*/
	public function getNonDefaultLanguage()
	{
		$nonDefaultlanguage = GlobalLanguage::with('language')->where('is_default',0)->where('status',1)->where('is_deleted',0)->get();
		return $nonDefaultlanguage;
	}

	// Get lang details using code from URL
	public function getLanguageDetailsByCode($code)
	{
		$languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName')
                                ->join('world_languages as wl','wl.id','=','language_id')
                                ->where('alpha2',$code)
                                ->where('status',1)
                                ->where('is_deleted',0)
								->first();
		if(!empty($languageData))
			return $languageData;
		else
			return false;
	}
	/** Developed By Nivedita (29-jan-2021) **/
	/** function to laguage session and set laguage sessions **/
	function setSessionforLang($defaultLanguageId)
	{
	    if(session('language_id')==''){
	      $languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName')
	                              ->join('world_languages as wl','wl.id','=','language_id')
	                              ->where('is_default',1)
	                              ->where('status',1)
	                              ->where('is_deleted',0)
	                              ->first();
	      Session::put('language_id',$defaultLanguageId);
	      Session::put('language_code',$languageData['Code']);
	      Session::put('language_name',$languageData['langName']);
	      Session::put('default_lang_id',$defaultLanguageId);
	    }
	    else{
	      $languageData = GlobalLanguage::select('global_language.id','alpha2 as Code','langEn as langName')
	                                 ->join('world_languages as wl','wl.id','=','language_id')
	                                ->where('global_language.id',Session::get('language_id'))
	                                 ->where('status',1)
	                                 ->where('is_deleted',0)
	                                 ->first();
	         Session::put('language_code',$languageData['Code']);
	         Session::put('language_name',$languageData['langName']);
	         Session::put('default_lang_id',$languageData['id']);
	    }
	    return true;
	}


    // added by Pallavi
    public function getS3ImagePath($folderName,$imageName)
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_REGION'),
            'credentials' => [
                'key' => env('AWS_KEY'),
                'secret' => env('AWS_SECRET'),
            ]
        ]);

        $plain_url = $s3->getObjectUrl(env('AWS_BUCKET'), $folderName.'/'.$imageName);
        return $plain_url;
    }
}
