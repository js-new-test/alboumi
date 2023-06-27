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
use Illuminate\Support\Str;
use Exception;
use Mail;
use Socialite;
use App\Traits\ReuseFunctionTrait;

class HomePageAPIController extends Controller
{
    use ReuseFunctionTrait;

    public function homePageComponent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
              'statusCode' => 300,
              'message' => $validator->errors(),
            ], 300);
        }
        //Localization
        $codes = ['OURPRINTSERVICES', 'OURCOLLECTION', 'BAHRIANPHOTOGRAPHER', 'OK', 'SUCCESS','LANGUAGENOTFOUND'];
        $homePageAPILabels = getCodesMsg($request->language_id, $codes);

        $lang_id = $request->language_id;
        $baseUrl = $this->getBaseUrl();

        //Localization
        $codes = ["MASTERCARTIDREQ","MASTERCARTIDNUM"];
        $ordersLabels = getCodesMsg($lang_id, $codes);

        // $msg = [
        //     'cart_master_id.required' => $ordersLabels["MASTERCARTIDREQ"],
        //     'cart_master_id.numeric' => $ordersLabels["MASTERCARTIDNUM"],
        // ];

        // $validator = Validator::make($request->all(), [
        //     'cart_master_id' => 'required|numeric',
        // ],$msg);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 300,
                'message' => $validator->errors(),
            ], 300);
        }

        $language_found = isLanguageExists($lang_id);
        if($language_found == 'false')
        {
            $defaultLang = $this->getDefaultLanguage();
            $codes = ['LANGUAGENOTFOUND'];
            $homePageAPILabels = getCodesMsg($defaultLang, $codes);

            $result['statusCode'] = "300";
            $result['message'] = $homePageAPILabels['LANGUAGENOTFOUND'];
            return response()->json($result,300);
        }

        //Set notification flag based on notification records
        if(Auth::guard('api')->check())
        {
            $customer_id = Auth::guard('api')->user()->token()->user_id;
            $notification = \App\Models\Notifications::where('user_id', $customer_id)
            ->where('read_flag', 0)->count();
            if($notification >= 1)
            {
                $notificationFlag = "1";
            }
            else
            {
                $notificationFlag = "0";
            }
        }
        else
        {
            $notificationFlag = "0";
        }


        //Banner component
        $banner_component = [];
        $banner_data = [];
        $i = 0;
        $banners = \App\Models\Banners::where('language_id', $lang_id)
        ->where('status', 1)->where('is_deleted', 0)->get();
        $banner_component['componentId'] = "banner";
        $banner_component['sequenceId'] = "1";
        $banner_component['isActive'] = "1";
        $banner_component['imageHeight'] = "300";
        $banner_component['imageWidth'] = "590";
        if($banners)
        {
            foreach ($banners as $banner) {
                $banner_data[$i]['id'] = (String) $banner->id;
                $banner_data[$i]['name'] = $banner->text;
                $banner_data[$i]['status'] = (String) $banner->status;
                $banner_data[$i]['image'] = $baseUrl."/public/assets/images/banners/desktop/".$banner->image;
                $banner_data[$i]['type'] = "1";
                $banner_data[$i]['navigationFlag'] = "1";
                // $banner_data[$i]['navigationFlag'] = "0";
                $banner_data[$i]['query'] = $baseUrl."/api/v1/getProductList?language_id=".$lang_id."&category_id=".$banner->category_id;
                $i++;
            }
            $banner_component['bannerData']['list'] = $banner_data;
        }

        //Banner component over

        //fourComponent component
        $four_component = [];
        $four_data = [];
        $k = 0;
        $four_component['componentId'] = "fourComponent";
        $four_component['sequenceId'] = "2";
        $is_active = $this->homePageCompIsActiveDeactive(2);
        $four_component['isActive'] = (String) $is_active;
        $our_services = \App\Models\Services::where('language_id', $lang_id)->where('status', 1)
        ->whereNull('deleted_at')->get();
        if($our_services)
        {
            foreach ($our_services as $our_service) {
                $four_data[$k]['id'] = (String) $our_service->id;
                $four_data[$k]['image'] = $baseUrl."/public/assets/images/services/".$our_service->service_image;
                $four_data[$k]['type'] = "1";
                $four_data[$k]['navigationFlag'] = "1";
                // $four_data[$k]['navigationFlag'] = "0";
                // $four_data[$k]['query'] = $our_service->link;
                $four_data[$k]['query'] = $baseUrl."/api/v1/getProductList?language_id=".$lang_id."&category_id=".$our_service->category_id;
                $four_data[$k]['title'] = $our_service->service_name;
                $k++;
            }
            $four_component['fourComponentData']['title'] = $homePageAPILabels["OURPRINTSERVICES"];
            $four_component['fourComponentData']['list'] = $four_data;
        }
        //fourComponent component over

        //singleBanner
        $singlebanner_component = [];
        $singlebanner_data = [];
        $j = 0;
        $singlebanner_component['componentId'] = "singleBanner";
        $singlebanner_component['sequenceId'] = "3";
        $is_active = $this->homePageMobileAppIsActiveDeactive(1);
        $singlebanner_component['isActive'] = (String) $is_active;
        $singlebanner_component['imageHeight'] = "546";
        $singlebanner_component['imageWidth'] = "1077";
        $home_page_mobile_app = \App\Models\HomePageMobileApp::where('id', 1)->first();
        $singlebanner_data['id'] = (String) $home_page_mobile_app->id;
        $singlebanner_data['image'] = $baseUrl."/public/assets/images/general-settings/mobile-app/".$home_page_mobile_app->image;
        $singlebanner_data['type'] = "1";
        // $singlebanner_data['navigationFlag'] = "1";
        $singlebanner_data['navigationFlag'] = "0";
        $singlebanner_data['query'] = $baseUrl."/api/v1/getPackageslist?language_id=".$lang_id."&event_id=".$home_page_mobile_app->event_id;
        $singlebanner_component['singleBannerData']['list'][] = $singlebanner_data;
        //singleBanner over

        //SingleImage 1
        // $singleimage_component = [];
        // $singleimage_data = [];
        // $k = 0;
        // $singleimage_component['componentId'] = "SingleImage";
        // $singleimage_component['sequenceId'] = "4";
        // $is_active = $this->homePageCompIsActiveDeactive(3);
        // $singleimage_component['isActive'] = (String) $is_active;
        // $singleimage_component['imageHeight'] = "546";
        // $singleimage_component['imageWidth'] = "1077";
        // $home_page_content = \App\Models\HomePageContent::where('language_id', $lang_id)->where('is_deleted', 0)->first();
        // if($home_page_content)
        // {
        //     $singleimage_data['id'] = (String) $home_page_content->id;
        //     $singleimage_data['image'] = $baseUrl."/public/assets/images/home-page-content/mobile/".$home_page_content->mobile_image_1;
        //     $singleimage_data['title'] = $home_page_content->image_text_1;
        //     $singleimage_data['type'] = "1";
        //     $singleimage_data['navigationFlag'] = "1";
        //     // $singleimage_data['navigationFlag'] = "0";
        //     // $singleimage_data['query'] = $home_page_content->link;
        //     $singleimage_data['query'] = $baseUrl."/api/v1/getProductList?language_id=".$lang_id."&category_id=".$home_page_content->category_id_1;
        //     $singleimage_component['SingleImageData']['title'] = $home_page_content->title;
        //     $singleimage_component['SingleImageData']['list'][] = $singleimage_data;
        // }
        // else
        // {
        //     $singleimage_component['SingleImageData']['title'] = "";
        //     $singleimage_component['SingleImageData']['list'] = [];
        // }
        //SingleImage 1 over

        //SingleImage 2
        // $singleimage_component_2 = [];
        // $singleimage_data_2 = [];
        // $k = 0;
        // $singleimage_component_2['componentId'] = "SingleImage";
        // $singleimage_component_2['sequenceId'] = "5";
        // $is_active = $this->homePageCompIsActiveDeactive(3);
        // $singleimage_component_2['isActive'] = (String) $is_active;
        // $singleimage_component_2['imageHeight'] = "546";
        // $singleimage_component_2['imageWidth'] = "1077";
        // $home_page_content = \App\Models\HomePageContent::where('language_id', $lang_id)->where('is_deleted', 0)->first();
        // if($home_page_content)
        // {
        //     $singleimage_data_2['id'] = (String) $home_page_content->id;
        //     $singleimage_data_2['image'] = $baseUrl."/public/assets/images/home-page-content/mobile/".$home_page_content->mobile_image_2;
        //     $singleimage_data_2['title'] = $home_page_content->image_text_2;
        //     $singleimage_data_2['type'] = "1";
        //     $singleimage_data_2['navigationFlag'] = "1";
        //     // $singleimage_data_2['query'] = $home_page_content->link;
        //     // $singleimage_data_2['navigationFlag'] = "0";
        //     $singleimage_data_2['query'] = $baseUrl."/api/v1/getProductList?language_id=".$lang_id."&category_id=".$home_page_content->category_id_2;
        //     $singleimage_component_2['SingleImageData']['list'][] = $singleimage_data_2;
        // }
        // else
        // {
        //     $singleimage_component_2['SingleImageData']['list'] = [];
        // }
        //SingleImage 2 over

        // singleBanner
        $singleBanner_component = [];
        $singleBanner_data = [];
        $l = 0;
        $singleBanner_component['componentId'] = "singleBanner";
        $singleBanner_component['sequenceId'] = "6";
        $is_active = $this->homePageMobileAppIsActiveDeactive(2);
        $singleBanner_component['isActive'] = (String) $is_active;
        $singleBanner_component['imageHeight'] = "546";
        $singleBanner_component['imageWidth'] = "1077";
        $home_page_mobile_app = \App\Models\HomePageMobileApp::where('id', 2)->first();
        $singleBanner_data['id'] = (String) $home_page_mobile_app->id;
        $singleBanner_data['image'] = $baseUrl."/public/assets/images/general-settings/mobile-app/".$home_page_mobile_app->image;

        $singleBanner_data['type'] = "8";
        $singleBanner_data['navigationFlag'] = "1";
        //$singleBanner_data['navigationFlag'] = "0";
        $singleBanner_data['query'] = $baseUrl."/api/v1/getEventList?language_id=".$lang_id;
        $singleBanner_component['singleBannerData']['list'][] = $singleBanner_data;
        // singleBanner over

        //fourImageComponent component
        $fourImage_component = [];
        $fourImage_data = [];
        $m = 0;
        $fourImage_component['componentId'] = "fourImageComponent";
        $fourImage_component['sequenceId'] = "7";
        $is_active = $this->homePageCompIsActiveDeactive(5);
        $fourImage_component['isActive'] = (String) $is_active;
        $our_collections = \App\Models\Collection::where('language_id', $lang_id)
        ->where('status', 1)->whereNull('deleted_at')->get();
        if($our_collections)
        {
            foreach ($our_collections as $our_collection) {
                $fourImage_data[$m]['id'] = (String) $our_collection->id;
                $fourImage_data[$m]['image'] = $baseUrl."/public/assets/images/collections/".$our_collection->collection_image;
                $fourImage_data[$m]['type'] = "1";
                $fourImage_data[$m]['navigationFlag'] = "1";
                // $fourImage_data[$m]['query'] = $our_collection->collection_link;
                // $fourImage_data[$m]['navigationFlag'] = "0";
                $fourImage_data[$m]['query'] = $baseUrl."/api/v1/getProductList?language_id=".$lang_id."&category_id=".$our_collection->category_id;
                $fourImage_data[$m]['title'] = $our_collection->collection_title;
                $m++;
            }
            $fourImage_component['fourImageComponentData']['title'] = $homePageAPILabels["OURCOLLECTION"];
            $fourImage_component['fourImageComponentData']['list'] = $fourImage_data;
        }
        //fourImageComponent component over

        //portfolio
        $portfolio_component = [];
        $portfolio_data = [];
        $n = 0;
        $portfolio_component['componentId'] = "portfolio";
        $portfolio_component['sequenceId'] = "8";
        $is_active = $this->homePageCompIsActiveDeactive(6);
        $portfolio_component['isActive'] = (String) $is_active;
        $home_page_photographers = \App\Models\HomePagePhotographer::select('home_page_photographer.id as hpp_id',
        'home_page_photographer.big_image','home_page_photographer.small_image','photographers.id',
        'photographer_details.name','home_page_photographer.photographer_id as pgrapher_id')
        ->join('photographers', 'photographers.id', '=', 'home_page_photographer.photographer_id')
        ->join('photographer_details', 'photographer_details.photographer_id', '=', 'photographers.id');
        if($lang_id)
        {
            $home_page_photographers->where(function($query) use($lang_id){
                return $query->where('photographer_details.language_id', $lang_id)
                ->where('photographers.status', 1)
                ->where('home_page_photographer.is_deleted', 0)
                ->whereNull('photographers.deleted_at');
            });
        }
        else
        {
            $defaultLang = $this->getDefaultLanguage();
            $home_page_photographers->where(function($query) use($defaultLang){
                return $query->where('photographer_details.language_id', $defaultLang)
                ->where('photographers.status', 1)
                ->where('home_page_photographer.is_deleted', 0)
                ->whereNull('photographers.deleted_at');
            });
        }
        $home_page_photographers = $home_page_photographers->get();
        if($home_page_photographers)
        {
            $counter = 0;
            foreach ($home_page_photographers as $home_page_photographer) {
                $counter++;
                $portfolio_data[$n]['id'] = (String) $home_page_photographer->id;
                //commented  By Nivedia 03-09-2021//
                // if($counter % 3 == 0)
                // {
                //     $portfolio_data[$n]['image'] = $baseUrl."/public/assets/images/home-page-photographer/bigimage/".$home_page_photographer->big_image;
                // }
                // else
                // {
                //     $portfolio_data[$n]['image'] = $baseUrl."/public/assets/images/home-page-photographer/smallimage/".$home_page_photographer->small_image;
                // }
                //End commented  By Nivedia 03-09-2021//
                // By Nivedia 03-09-2021//
                $portfolio_data[$n]['image'] = $baseUrl."/public/assets/images/home-page-photographer/smallimage/".$home_page_photographer->small_image;
                $portfolio_data[$n]['bigimage'] = $baseUrl."/public/assets/images/home-page-photographer/bigimage/".$home_page_photographer->big_image;
                // End By Nivedia 03-09-2021//
                $portfolio_data[$n]['type'] = "4";
                $portfolio_data[$n]['navigationFlag'] = "1";
                // $portfolio_data[$n]['navigationFlag'] = "0";
                $portfolio_data[$n]['isShowName'] = "1";
                $portfolio_data[$n]['title'] = $home_page_photographer->name;
                // $portfolio_data[$n]['query'] = $baseUrl."/bahrain-photographer/".$home_page_photographer->id;
                $portfolio_data[$n]['query'] = $baseUrl."/api/v1/getPhotographerProfile?language_id=".$lang_id."&profile_id=".$home_page_photographer->pgrapher_id;
                $n++;
            }
            $portfolio_component['portfolioData']['title'] = $homePageAPILabels["BAHRIANPHOTOGRAPHER"];
            $portfolio_component['portfolioData']['list'] = $portfolio_data;
        }
        //portfolio over

        //fourComponent component
        $four_component_2 = [];
        $singleimage_data = [];
        $singleimage_data_2 = [];
        $k = 0;
        $four_component_2['componentId'] = "fourComponent";
        $four_component_2['sequenceId'] = "4";
        $is_active = $this->homePageCompIsActiveDeactive(3);
        $four_component_2['isActive'] = (String) $is_active;
        $home_page_content = \App\Models\HomePageContent::where('language_id', $lang_id)->where('is_deleted', 0)->first();
        if($home_page_content)
        {
            $singleimage_data['id'] = (String) $home_page_content->id;
            $singleimage_data['image'] = $baseUrl."/public/assets/images/home-page-content/mobile/".$home_page_content->mobile_image_1;
            $singleimage_data['title'] = $home_page_content->image_text_1;
            $singleimage_data['type'] = "1";
            $singleimage_data['navigationFlag'] = "1";
            $singleimage_data['query'] = $baseUrl."/api/v1/getProductList?language_id=".$lang_id."&category_id=".$home_page_content->category_id_1;

            $singleimage_data_2['id'] = (String) $home_page_content->id;
            $singleimage_data_2['image'] = $baseUrl."/public/assets/images/home-page-content/mobile/".$home_page_content->mobile_image_2;
            $singleimage_data_2['title'] = $home_page_content->image_text_2;
            $singleimage_data_2['type'] = "1";
            $singleimage_data_2['navigationFlag'] = "1";
            $singleimage_data_2['query'] = $baseUrl."/api/v1/getProductList?language_id=".$lang_id."&category_id=".$home_page_content->category_id_2;

            $four_component_2['fourComponentData']['title'] = "Gift Ideas & Cards";
            $four_component_2['fourComponentData']['list'][] = $singleimage_data;
            $four_component_2['fourComponentData']['list'][] = $singleimage_data_2;
        }
        else
        {
            $four_component_2['fourComponentData']['title'] = "Gift Ideas & Cards";
            $four_component_2['fourComponentData']['list'] = [];
        }
        //fourComponent component over

        //Get Cart Items Count
        $cartCount = \App\Models\Cart::where('cart_master_id', $request->cart_master_id)->count();

        $result['status'] = $homePageAPILabels["OK"];
        $result['statusCode'] = "200";
        $result['message'] = $homePageAPILabels["SUCCESS"];
        $result['cartCount'] = ($cartCount > 0) ? (string) $cartCount : "0";
        $result['wishlistCount'] = "0";
        $result['notificationFlag'] = $notificationFlag;
        $result['component'][] = $banner_component;
        $result['component'][] = $four_component;
        $result['component'][] = $singlebanner_component;
        // $result['component'][] = $singleimage_component;
        // $result['component'][] = $singleimage_component_2;
        $result['component'][] = $singleBanner_component;
        $result['component'][] = $fourImage_component;
        $result['component'][] = $portfolio_component;
        $result['component'][] = $four_component_2;
        return response()->json($result);
    }

    public function homePageCompIsActiveDeactive($id)
    {
        $home_page_component = \App\Models\HomePageComponent::where('id', $id)->first();
        return $home_page_component->is_active;
    }

    public function homePageMobileAppIsActiveDeactive($id)
    {
        $home_page_component = \App\Models\HomePageMobileApp::where('id', $id)->first();
        return $home_page_component->is_active;
    }
}
