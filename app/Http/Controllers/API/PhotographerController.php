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
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use App\Traits\ReuseFunctionTrait;
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;
use App\Models\Photographers;
use App\Models\Product;

class PhotographerController extends Controller
{
    use ReuseFunctionTrait;

    public function getPhotographerProfile()
    {
        // $validator = Validator::make($request->all(), [
        //     'language_id' => 'required|numeric',
        //     'profile_id'  => 'required|numeric',
        // ]);
        //
        // if ($validator->fails()) {
        //     return response()->json([
        //       'statusCode' => 300,
        //       'message' => $validator->errors(),
        //     ]);
        // }
        $lang_id = $_GET['language_id'];
        $profile_id=$_GET['profile_id'];
        $codes = ['PHOTOGRAPHERNOTFOUND','PORTFOLIO','OK','SUCCESS'];
        $photographerLabels = getCodesMsg($lang_id, $codes);
        $defaultLanguageData = \App\Models\GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();        ;
        $defaultLanguageId = $defaultLanguageData['id'];
        $photographerDetials = \App\Models\Photographers::getPhotographerById($profile_id,$lang_id);
        if(empty($photographerDetials)){
          $photographerDetials = \App\Models\Photographers::getPhotographerById($profile_id,$defaultLanguageId);
        }
        $portfolioCount=\App\Models\PhotographerPortfolio::getPortfolioCount($profile_id);
        $portfolioArr=\App\Models\PhotographerPortfolio::getPortfolioByPhotographer($profile_id);
        $baseUrl = $this->getBaseUrl();
            if(empty($photographerDetials))
            {
                $result['statusCode'] = '300';
                $result['message'] = $photographerLabels['PHOTOGRAPHERNOTFOUND'];
                return response()->json($result);
            }
            $i = 0;
            // Portfolio details array  portfolioData//
            $photographer_portfolio_arr = [];
            foreach ($portfolioArr as $portfolio) {
                $product=Product::where('id',$portfolio['product_id'])->where('status','Active')->whereNull('deleted_at')->first();
                $photographer_portfolio_arr[$i]['id'] = "".$portfolio['product_id']."";
                $photographer_portfolio_arr[$i]['image'] = $baseUrl."/public/assets/images/photographers/portfolio/".$portfolio['image'];
                $photographer_portfolio_arr[$i]['isShowName'] = '0';
                $photographer_portfolio_arr[$i]['title'] = "";
                $photographer_portfolio_arr[$i]['type'] = '2';
                if(!empty($product))
                $photographer_portfolio_arr[$i]['navigationFlag'] = '1';
                else
                $photographer_portfolio_arr[$i]['navigationFlag'] = '0';
                $photographer_portfolio_arr[$i++]['query'] = $baseUrl."/api/v1/getProductDetails?language_id=".$lang_id.'&product_id='.$portfolio['product_id'].'&photographer_id='.$photographerDetials->id;

            }
            //portfolio companent
            $portfolioData=[];
            $portfolioData['title']=$photographerLabels['PORTFOLIO'].'('.$portfolioCount.')';
            $portfolioData['list']=$photographer_portfolio_arr;

            // Photographer Details photographerProfileData
            $photographer_details_arr = [];
            $photographer_details_arr['backImage']=$baseUrl."/public/assets/images/photographers/".$photographerDetials->cover_photo;
            $photographer_details_arr['profileImage']=$baseUrl."/public/assets/images/photographers/".$photographerDetials->profile_pic;
            $photographer_details_arr['name']=$photographerDetials->name;
            $photographer_details_arr['description']=$photographerDetials->about;
            $photographer_details_arr['location']=$photographerDetials->location;
            $photographer_details_arr['website']=$photographerDetials->web;
            $photographer_details_arr['experienceDetail']=$photographerDetials->experience;

            // To get component one

            $compnant_one=[];
            $compnant_one['componentId']='photographerProfile';
            $compnant_one['sequenceId']='1';
            $compnant_one['isActive']='1';
            $compnant_one['imageHeight']="300";
            $compnant_one['imageWidth']="590";
            $compnant_one['photographerProfileData']=$photographer_details_arr;

            // To get component one

            $compnant_two=[];
            $compnant_two['componentId']='portfolio';
            $compnant_two['sequenceId']='2';
            $compnant_two['isActive']='1';
            $compnant_two['portfolioData']=$portfolioData;

            //portfolio over
            $result['status'] = $photographerLabels['OK'];
            $result['statusCode'] = "200";
            $result['message'] = $photographerLabels['SUCCESS'];
            $result['component'][] = $compnant_one;
            $result['component'][] = $compnant_two;

            return response()->json($result);

    }

    public function getPhotographersList()
    {
        $langId = $_GET['language_id'];
        $photographersList = Photographers::getPhotographers($langId);
        $j = 0;
        $baseUrl = $this->getBaseUrl();
        $photographerNotAvailable = ['NOPHOTOGRAPHERFOUND'];
        $noPhotographerAvail = getCodesMsg($_GET['language_id'], $photographerNotAvailable);

        $photo['componentId'] = 'componentPhotographerList';

        foreach($photographersList as $ek => $photoData)
        {
            $photoList[$j]['id'] = "".$photoData['id']."";
            $photoList[$j]['name'] = $photoData['name'];
            $photoList[$j]['image'] = $baseUrl.'/public/assets/images/photographers/'.$photoData['profile_pic'];
            $photoList[$j]['query'] = $baseUrl."/api/v1/getPhotographerProfile?language_id=".$langId.'&profile_id='.$photoData['id'];
            $photoList[$j]['type'] = "5";
            $photoList[$j++]['navigationFlag'] = "1";
        }

        $photo['componentPhotographerListData'] = $photoList;

        if(!empty($photo['componentPhotographerListData']))
        {
            $result['status'] = "OK";
            $result['statusCode'] = 200;
            $result['message'] = "Success";
            $result['component'][] = $photo;
        }
        else
        {
            $result['status'] = "OK";
            $result['statusCode'] = 300;
            $result['message'] = $noPhotographerAvail['NOPRODAVAILABLE'];
            $result['component'] = [];
        }
        return response()->json($result);

    }
}
