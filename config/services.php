<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id' => '4680181785338990',
        'client_secret' => '7e4545578f9872f253dc316ab66c9ee2',
        'redirect' => PHP_SAPI === 'cli' ? false : url('/').'/auth/facebook/callback',
    ],
    //Old Credentials
    // 'google' => [
    //     'client_id' => '316927230092-970kuili1ngtogj9c5f6bdse6euotd5v.apps.googleusercontent.com',
    //     'client_secret' => 'mdRHmkH3oOXGjOZGc8kZpEaZ',
    //     'redirect' => PHP_SAPI === 'cli' ? false : url('/').'/auth/google/callback',
    // ],
    // New credential with alboumi account By Nivedita 22-04-2021
    'google' => [
        'client_id' => '375243362688-s5uvv420q0kv3aom8jbd34vst6nbsreu.apps.googleusercontent.com',
        'client_secret' => '2QJfHBm7R2PYYLiDgEQviwaT',
        'redirect' => PHP_SAPI === 'cli' ? false : url('/').'/auth/google/callback',
    ],
    'recaptcha' => [
        'key' => '6LedYKYbAAAAACBAr-TcWixIhU7UTHHoTJAYAuLd',
        'secret' => '6LedYKYbAAAAAL3FIp-jBzp_48HSY9I4Xc1SFBPw',
    ],
    'extra' => [
        'facebook' => [
            'client_id' => '4680181785338990',
            'client_secret' => '7e4545578f9872f253dc316ab66c9ee2',
            'redirect' => PHP_SAPI === 'cli' ? false : url('/').'/auth/facebook/photo_callback',
        ],
        // 'google' => [
        //     'client_id' => '390174302304-b6qlah170r9rh2m3ltqmondrsqi5qa8v.apps.googleusercontent.com',
        //     'client_secret' => 'PF5hnIJv9gEN3zre-uxPbD7W',
        //     'redirect' => PHP_SAPI === 'cli' ? false : url('/').'/auth/google/photo_callback',
        //     'scopes' => [\Google_Service_PhotosLibrary::PHOTOSLIBRARY],
        //     'access_type' => 'offline',
        //     'approval_prompt' => 'force',
        //     'prompt' => 'consent',
        // ],
        // New credential with alboumi account By Nivedita 22-04-2021
        'google' => [
            'client_id' => '375243362688-s5uvv420q0kv3aom8jbd34vst6nbsreu.apps.googleusercontent.com',
            'client_secret' => '2QJfHBm7R2PYYLiDgEQviwaT',
            'redirect' => PHP_SAPI === 'cli' ? false : url('/').'/auth/google/photo_callback',
            //'redirect' => "https://alboumi.com/auth/google/photo_callback",
            // 'scopes' => [\Google_Service_PhotosLibrary::PHOTOSLIBRARY],
            'scopes' => ['https://www.googleapis.com/auth/photoslibrary.readonly'],
            'access_type' => 'offline',
            'approval_prompt' => 'force',
            'prompt' => 'consent',
        ],
        'instagram' => [
            'client_id' => '286751573213811',
            'client_secret' => 'e3e662673eb77e1b0ae469dc7f0e7895',
            'redirect' => PHP_SAPI === 'cli' ? false : 'https://alboumi.com/auth/instagram/photo_callback',
            // 'redirect' => PHP_SAPI === 'cli' ? false : url('/').'/auth/instagram/photo_callback',
        ],
    ],

];
// account used
// email : magnetoapps12@gmail.com
// Password : magneto123!@#
