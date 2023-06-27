<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Agent;
use Auth;
use DB;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\CmsPages;
use App\Models\GlobalCurrency;
use App\Models\Currency;
use App\Models\PhotoBooks;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;

class PhotoBookController extends Controller
{
    use ReuseFunctionTrait;

    public function getPhotoBookList()
    {
        $langId = $_GET['language_id'];
        $codes = ['CREATEPHOTOBOOK','OK','SUCCESS','FROM','SELECTBOOK'];
        $bookLabels = getCodesMsg($langId, $codes);

        // Toget currency code and decimal data
        $currenyCode =getCurrSymBasedOnLangId($langId);
        $get_curr = GlobalLanguage::select('currency_id','decimal_number','decimal_separator','thousand_separator')->where('id', $langId)->first();
        $conversionRate = getCurrencyRates($get_curr->currency_id);
        $decimalNumber=$get_curr->decimal_number;
        $decimalSeparator=$get_curr->decimal_separator;
        $thousandSeparator=$get_curr->thousand_separator;

        $cmsPageDetails = CmsPages::select('cms_pages.id','cd.title','cd.description','cms_pages.banner_image','cms_pages.mobile_banner_image','cd.seo_title','cd.seo_description','cd.seo_keyword')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug','photo-book')
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('language_id',$langId)
                                    ->first();


        $baseUrl = $this->getBaseUrl();

        if($cmsPageDetails == null)
        {
            $cmsPageDetails = CmsPages::select('cms_pages.id','title','description','banner_image','mobile_banner_image','seo_title','seo_description','seo_keyword')
                                    ->join('cms_details as cd','cd.cms_id','=','cms_pages.id')
                                    ->where('slug','photo-book')
                                    ->whereNull('cd.deleted_at')
                                    ->whereNull('cms_pages.deleted_at')
                                    ->where('language_id',$defaultLanguageId)
                                    ->first();
        }
        $booksData= PhotoBooks::getBooks($langId);
        $baseUrl = $this->getBaseUrl();
        $i = 0;
        // book list array //
        $books_arrList = [];
        foreach ($booksData as $data) {
          $books_arrList[$i]['id'] = "".$data['id']."";
          $books_arrList[$i]['image'] = $baseUrl."/public/assets/images/books/".$data['image'];
          $books_arrList[$i]['navigationFlag'] = '1';
          $books_arrList[$i]['type'] = "2";
          $books_arrList[$i]['description'] = "".$data['description']."";
          $books_arrList[$i]['query'] = $baseUrl."/api/v1/getProductDetails?language_id=".$langId.'&product_id='.$data['link'];
          $books_arrList[$i]['title'] = "".$data['title']."";
          $books_arrList[$i++]['price'] = $bookLabels['FROM']." ".$currenyCode." ".number_format($data['price'] * $conversionRate, $decimalNumber, $decimalSeparator, $thousandSeparator);
        }
        //book Componanat
            $bookData=[];
            $bookData['title']=$bookLabels['SELECTBOOK'];
            $bookData['list']=$books_arrList;

            // Photographer Details photographerProfileData
            $bannerdata_arr = [];
            $bannerdata_arr['id']=$cmsPageDetails->id;
            $bannerdata_arr['title']=$bookLabels['CREATEPHOTOBOOK'];
            $bannerdata_arr['list'][]=array("image"=>$baseUrl."/public/assets/images/cms/mobile_banner/".$cmsPageDetails->mobile_banner_image);


            // To get component one

            $compnant_one=[];
            $compnant_one['componentId']='banner';
            $compnant_one['sequenceId']='1';
            $compnant_one['isActive']='1';
            $compnant_one['imageWidth']='357';
            $compnant_one['imageHeight']='200';
            $compnant_one['bannerData']=$bannerdata_arr;

            // To get component two

            $compnant_two=[];
            $compnant_two['componentId']='bookComponent';
            $compnant_two['sequenceId']='2';
            $compnant_two['isActive']='1';
            $compnant_two['bookComponentData']=$bookData;

            //portfolio over
            $result['status'] = $bookLabels['OK'];
            $result['statusCode'] = "200";
            $result['message'] = $bookLabels['SUCCESS'];
            $result['component'][] = $compnant_one;
            $result['component'][] = $compnant_two;

            return response()->json($result);

        }
}
