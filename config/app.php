<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,

        /*
         *included by Ajay 08/10/2020
         *included for Excel operation
        */
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Yajra\DataTables\DataTablesServiceProvider::class,
        /*
         Included by Pallavi 24 Nov 2020
         Included for social login
        */
        Laravel\Socialite\SocialiteServiceProvider::class,
        /*
            Included by Pallavi 2 Dec 2020
            Included for getting browser details of client
        */
       Jenssegers\Agent\AgentServiceProvider::class,
        /*
            Included by Pallavi 28 Dec 2020
            Included for resizing image before uploading to server
        */
       Intervention\Image\ImageServiceProvider::class,
       Laravel\Passport\PassportServiceProvider::class,
        /*
            Included by Pallavi 30 March 2021
            Included for generating order pdf
        */
        Barryvdh\DomPDF\ServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'DataTables' => Yajra\DataTables\Facades\DataTables::class,
        'Socialite' => Laravel\Socialite\Facades\Socialite::class,
        'Agent' => Jenssegers\Agent\Facades\Agent::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,

    ],

    /*
     |----------------------------------------------------------------------------
     |  administrator slugs
     |----------------------------------------------------------------------------
     |
     | To get the slugs of the admins which are being used in the system
     |
     |
     |
     */

    'admin_slug' => [
        'admin',
        'super_admin'
    ],
    /*
      |--------------------------------------------------------------------------
      | Application Default User Types slugs
      |--------------------------------------------------------------------------
      |
      | Here you may specify the default User Types for your application, which
      | will be used by the App. We have Added user type slugs to database. We can
      | match any change in slug in database here instead of changing through out Codebase
      |
     */
    'role_type' => [
        'admin' => 'Admin',
        'photographer' => 'Photographer'
    ],
    /*
     |
     | View composers are callbacks or class methods that are called when a view is rendered
     */
    'arrWhoCanCheck' => ['admin','photographer'],

    /*
      |--------------------------------------------------------------------------
      | Application Default Image Paths
      |--------------------------------------------------------------------------
      |
      | Here you may specify the default Image Paths for your application, which
      | will be used by the App. This path is within '\storage\app\' directory
      |
     */
    'image_paths' => [
        'PRODUCT' => 'image' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR,
        'CATEGORY'          => 'image' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR,
        'BANNER'          => 'image' . DIRECTORY_SEPARATOR . 'banner' . DIRECTORY_SEPARATOR,
        'STORE'          => 'image' . DIRECTORY_SEPARATOR . 'store' . DIRECTORY_SEPARATOR,
        'BRAND'          => 'image' . DIRECTORY_SEPARATOR . 'brand' . DIRECTORY_SEPARATOR,
        'TDS'          => 'image' . DIRECTORY_SEPARATOR . 'tds' . DIRECTORY_SEPARATOR,
        'DEAL'          => 'image' . DIRECTORY_SEPARATOR . 'deal' . DIRECTORY_SEPARATOR,
        'CAMPAIGN'          => 'image' . DIRECTORY_SEPARATOR . 'campaign' . DIRECTORY_SEPARATOR,
        'BIGDEAL'      => 'image' . DIRECTORY_SEPARATOR . 'bigdeal' . DIRECTORY_SEPARATOR

    ],

    'banner_image' => [
        'width' => 1000,
        'height' => 1000,
    ],

    //additional services image
    'service_image' => [
        'width' => 280,
        'height' => 280,
    ],
    //additional services sample
    'service_sample_image' => [
        'width' => 320,
        'height' => 320,
    ],

    'seller_image' => [
        'width' => 286,
        'height' => 286,
    ],

    'brand_image' => [
        'width' => 350,
        'height' => 350,
    ],

    'attribute_image' => [
        'width' => 24,
        'height' => 24,
    ],

    // bahrain photographers
    'photographer_profile_pic' => [
        'width' => 288,
        'height' => 288,
    ],

    'photographer_cover_pic' => [
        'width' => 1351,
        'height' => 293,
    ],

    'collection_image' => [
        'width' => 288,
        'height' => 344,
    ],

    'how_it_works_image' => [
        'width' => 280,
        'height' => 184,
    ],

    'category_image' => [
        'width' => 290,
        'height' => 290,
    ],

    'home_page_content' => [
        'width' => 387,
        'height' => 535,
    ],

    'home_page_content_mobile' => [
        'width' => 200,
        'height' => 200,
    ],

    'home_page_photographer' => [
        'width' => 1000,
        'height' => 1000,
    ],

    'megamenu_small_image' => [
        'width' => 256,
        'height' => 392,
    ],

    'megamenu_big_image' => [
        'width' => 704,
        'height' => 392,
    ],

    'products' => [
        'width' => 500,
        'height' => 500,
    ],

    'language_image' => [
        'width' => 50,
        'height' => 50,
    ],

    /* Constant for banner and mobile banner of CMS :Nivedita(11-01-2021)*/
    'cms_banner_image' => [
        'width' => 1366,
        'height' => 400,
    ],
    'cms_mobile_banner_image' => [
        'width' => 360,
        'height' => 400,
    ],
    /* Constant end for banner and mobile banner of CMS :Nivedita(11-01-2021)*/
    /* Constant for banner and mobile banner of Category :Nivedita(11-01-2021)*/
    'category_banner_image' => [
        'width' => 1353,
        'height' => 358,
    ],
    'category_mobile_banner_image' => [
        'width' => 361,
        'height' => 195,
    ],
    /* Constant end for banner and mobile banner of Category :Nivedita(11-01-2021)*/
    /* Constant for small and big image of home page photographer  :Nivedita(13-01-2021)*/
    'home_page_photographer_bigimg' => [
        'width' => 600,
        'height' => 288,
    ],
    'home_page_photographer_smallimg' => [
        'width' => 288,
        'height' => 288,
    ],
    /* Constant end for small and big image of home page photographer  :Nivedita(13-01-2021)*/
    'megamenu_icon_image' => [
        'width' => 200,
        'height' => 200
    ],

    'home_page_mobile_app_image' => [
        'width' => 343,
        'height' => 200,
    ],

    'banner_image_desktop' => [
        'width' => 1315,
        'height' => 515,
    ],

    'banner_image_mobile' => [
        'width' => 360,
        'height' => 592,
    ],

    'how_it_works_banner' => [
        'width' => 1224,
        'height' => 275,
    ],

    'event_image' => [
        'width' => 288,
        'height' => 288,
    ],

    'event_banner_image' => [
        'width' => 1366,
        'height' => 400,
    ],
    'event_mobile_banner_image' => [
        'width' => 360,
        'height' => 400,
    ],
    'event_banner_for_app' => [
        'width' => 375,
        'height' => 200,
    ],
    /* Constant for portfolio image :Nivedita(20-01-2021)*/
    'portfolio_image' => [
        'width' => 392,
    ],
    /* Constant for size attribute*/
    'sizeAttribute' => [
        'size' => 1,
    ],
    // Photo book image
    'photobook_image' => [
        'width' => 584,
        'height' => 288,
    ],

    "invoicePdfPath" => 'public/order-invoices',
    'photoBookCatId' => 12,
    'photoBookCatSlug' => 'photo-books',
    'MULTIPLE_IMAGE_COUNT' => 10,
    'copyProduct' => 1,                     // 1: yes 0:no

    // added by Pallavi (July 30, 2021) - print files pdf
    'imageHeight' => 700,
    'imageWidth' => 700,

    //Credimax Constant
    'CREDIMAX_MERCHANT_ID' => 'E01616951',
    'CREDIMAX_BASIC_AUTH' => 'bWVyY2hhbnQuRTAxNjE2OTUxOjkxOTczMGUxYzMxODM2NGM0MTMzYmM3YzI2NmQxNmZm',
    'CREDIMAX_ACTION' => env('CREDIMAX_ACTION', 'AUTHORIZE'), // AUTHORIZE (for test) OR PURCHASE (for live)

    /*'aramex' => [
        'URL' => 'https://ws.dev.aramex.net/shippingapi.v2/shipping/service_1_0.svc',
        'AccountCountryCode' => 'BH',
        'AccountEntity' => 'BAH',
        'AccountNumber' => '20000068',
        'AccountPin' => '543543',
        'UserName' => 'reem@reem.com',
        'Password' => '123456789',
        'Version' => '1.0',
    ],*/

    // LIVE
    'aramex' => [
        'URL' => 'http://ws.aramex.net/shippingapi/shipping/service_1_0.svc',
        'AccountCountryCode' => 'BH',
        'AccountEntity' => 'BAH',
        'AccountNumber' => '161395',
        'AccountPin' => '332432',
        'UserName' => 'kkgandhi@ashrafs.com.bh',
        'Password' => '@Sh19130',
        'Version' => '1.0',
    ],

    'FROM_EMAIL_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    'TIMEZONE_DIFF' => 331,
];
