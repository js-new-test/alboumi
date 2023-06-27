<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\GlobalLanguage;
use App\Models\WorldLanguage;
use App\Models\Locale;
use App\Models\Product;
use Exception;
use Auth;
use Mail;
use Socialite;
use Agent;
use Illuminate\Support\Facades\Session;
use App\Traits\ReuseFunctionTrait;
use DB;
use URL;
use Revolution\Google\Photos\Facades\Photos;
class SocialPhotosController extends Controller
{
    public function redirectgp($maxUpload,$type)
    {
        $config = [
            'client_id'     => config('services.extra.google.client_id'),
            'client_secret' => config('services.extra.google.client_secret'),
            'redirect'      => config('services.extra.google.redirect'),
        ];

        $provider = Socialite::buildProvider(
                                    \Laravel\Socialite\Two\GoogleProvider::class,
                                    $config
                                );
        Session::put('selectionType', $type);
        Session::put('maxUpload', $maxUpload);
        return $provider->scopes(config('services.extra.google.scopes'))
                        ->with([
                            'access_type'     => config('services.extra.google.access_type'),
                            'approval_prompt' => config('services.extra.google.approval_prompt'),
                            // 'state' => 'asd'
                        ])->redirect();
    }

    public function redirectfb($maxUpload,$type)
    {
        $config = [
            'client_id'     => config('services.extra.facebook.client_id'),
            'client_secret' => config('services.extra.facebook.client_secret'),
            'redirect'      => config('services.extra.facebook.redirect'),
        ];
        Session::put('selectionType', $type);
        Session::put('maxUpload', $maxUpload);
        $provider = Socialite::buildProvider(
                                    \Laravel\Socialite\Two\FacebookProvider::class,
                                    $config
                                );

        return $provider->scopes(['user_photos'])->redirect();
    }

    public function redirectig($maxUpload,$type)
    {
        $config = [
            'client_id'     => config('services.extra.instagram.client_id'),
            'client_secret' => config('services.extra.instagram.client_secret'),
            'redirect'      => config('services.extra.instagram.redirect'),
        ];
        Session::put('selectionType', $type);
        Session::put('maxUpload', $maxUpload);
        // return $provider->redirect();
        $url = 'https://api.instagram.com/oauth/authorize?client_id='.$config['client_id'].'&redirect_uri='.$config['redirect'].'&scope=user_profile,user_media&response_type=code';
        return redirect()->away($url);
    }

    public function callbackgp(){
        if (!request()->has('code')) {
            return redirect('/');
        }
        $state = request()->input('state');
        // print_r($state);die;
        /**
         * @var \Laravel\Socialite\Two\User $user
         */
        $config = [
            'client_id'     => config('services.extra.google.client_id'),
            'client_secret' => config('services.extra.google.client_secret'),
            'redirect'      => config('services.extra.google.redirect'),
        ];

        $provider = Socialite::buildProvider(
            \Laravel\Socialite\Two\GoogleProvider::class,
            $config
        );
        $user = $provider->user();
        // $user = Socialite::driver('google')->user();
        $return = [];
        // echo "<pre>";
        // print_r($user);die;

        $optParams = [];
        $token = [
            'access_token'  => $user->token,
            'refresh_token' => $user->refreshToken,
            'expires_in'    => $user->expiresIn,
            'created'       => time(),
            // 'created'       => $user->updated_at->getTimestamp(),
        ];
        try {
            $media_object = Photos::setAccessToken($token)->search($optParams);
            $return[] = ['album'=>'All','photos'=>$media_object];
            $listAlbums = Photos::setAccessToken($token)->listAlbums($optParams);
            // print_r($listAlbums->albums);
            if ($listAlbums && isset($listAlbums->albums)) {
                foreach ($listAlbums->albums as $key => $value) {
                    // print_r($value);
                    // echo "<b>".$value->title."</b>";
                    // echo "<br>";
                    $listAlbumsData = Photos::setAccessToken($token)->search(['albumId'=>$value->id]);
                    // print_r($listAlbumsData);
                    $return[] = ['album'=>$value->title,'photos'=>$listAlbumsData];
                }
            }

            // echo "<pre>";
            // print_r($return);die;
            // $media_object = Photos::setAccessToken($token)->search($optParams);
            $selectionType = Session::get('selectionType');
            Session::forget('selectionType');
            $maxUpload = Session::get('maxUpload');
            Session::forget('maxUpload');
            return view('frontend.social-images.google',['data'=>$return,'selectionType'=>$selectionType,'platform'=>'Google Photos','maxUpload'=>$maxUpload]);
        } catch (Exception $e) {
            // print_r($e->getMessage());die;
            return view('frontend.social-images.social-fail',['platform'=>'Google Photos']);
        }
    }

    public function callbackfb(){
        if (!request()->has('code')) {
            return redirect('/');
        }

        /**
         * @var \Laravel\Socialite\Two\User $user
         */

        $config = [
            'client_id'     => config('services.extra.facebook.client_id'),
            'client_secret' => config('services.extra.facebook.client_secret'),
            'redirect'      => config('services.extra.facebook.redirect'),
        ];

        $provider = Socialite::buildProvider(
            \Laravel\Socialite\Two\FacebookProvider::class,
            $config
        );
        $user = $provider->user();
        // $user = Socialite::driver('facebook')->user();
        $return = [];
        $accesstoken = $user->token;
        // print_r($accesstoken);

        $fb = new \Facebook\Facebook([
            'app_id' => config('services.extra.facebook.client_id'),
            'app_secret' => config('services.extra.facebook.client_secret'),
            'default_graph_version' => 'v10.0',
        ]);

        $response = $fb->get('/me?fields=albums{photos,name}', $accesstoken);
        $albums = $response->getDecodedBody();
        if(isset($albums['albums'])){
            foreach ($albums['albums']['data'] as $key => $value) {
              if(isset($value['photos'])){
                $albumphotos = [];
                foreach ($value['photos']['data'] as $key2 => $value2) {
                    $photoId = $value2['id'];
                    $albumpic = $fb->get('/'.$photoId.'?fields=images', $accesstoken);
                    $albumpic = $albumpic->getDecodedBody();
                    $albumphotos[] = $albumpic['images'][0];
                }

                $return[] = ['album'=>$value['name'],'photos'=>$albumphotos];
              }
            }
        }
        // echo "<pre>";
        // print_r($return);die;
        $selectionType = Session::get('selectionType');
        Session::forget('selectionType');
        $maxUpload = Session::get('maxUpload');
        Session::forget('maxUpload');
        return view('frontend.social-images.facebook',['data'=>$return,'selectionType'=>$selectionType,'platform'=>'Facebook','maxUpload'=>$maxUpload]);
    }


    public function callbackig(){
        if (!request()->has('code')) {
            return redirect('/');
        }
        $input = request()->all();

        /**
         * @var \Laravel\Socialite\Two\User $user
         */

        $config = [
            'client_id'     => config('services.extra.instagram.client_id'),
            'client_secret' => config('services.extra.instagram.client_secret'),
            'redirect'      => config('services.extra.instagram.redirect'),
        ];

        $url = 'client_id='.$config['client_id'].'&client_secret='.$config['client_secret'].'&grant_type=authorization_code&redirect_uri='.$config['redirect'].'&code='.$input['code'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.instagram.com/oauth/access_token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response,true);
        // print_r($response);
        $data = $this->getIGMedia($response['user_id'],$response['access_token']);

        // echo "<pre>";
        // print_r($data);die;
        $selectionType = Session::get('selectionType');
        Session::forget('selectionType');
        $maxUpload = Session::get('maxUpload');
        Session::forget('maxUpload');
        return view('frontend.social-images.instagram',['data'=>$data,'selectionType'=>$selectionType,'platform'=>'Instagram','maxUpload'=>$maxUpload]);
    }

    public function getIGMedia($userId,$accessToken){
        $url = 'https://graph.instagram.com/v10.0/'.$userId.'?fields=username,media{media_url}&access_token='.$accessToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response,true);
        // print_r($response);
        return $response;
    }
}
