<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SSLCommerz
    |--------------------------------------------------------------------------
    */

    'sslcommerz' => [

        /*
        |--------------------------------------------------------------------------
        | Sandbox Mode
        |--------------------------------------------------------------------------
        */

        'sandbox' => env(
            'SSLCOMMERZ_SANDBOX',
            true
        ),

        /*
        |--------------------------------------------------------------------------
        | Store Credentials
        |--------------------------------------------------------------------------
        */

        'store_id' => env(
            'SSLCOMMERZ_STORE_ID'
        ),

        'store_password' => env(
            'SSLCOMMERZ_STORE_PASSWORD'
        ),

        /*
        |--------------------------------------------------------------------------
        | Callback URLs
        |--------------------------------------------------------------------------
        */

        'success_url' => env(
            'SSLCOMMERZ_SUCCESS_URL'
        ),

        'fail_url' => env(
            'SSLCOMMERZ_FAIL_URL'
        ),

        'cancel_url' => env(
            'SSLCOMMERZ_CANCEL_URL'
        ),

        'ipn_url' => env(
            'SSLCOMMERZ_IPN_URL'
        ),

        /*
        |--------------------------------------------------------------------------
        | Payment Initiation URL
        |--------------------------------------------------------------------------
        */

        'init_url' => env(
            'SSLCOMMERZ_SANDBOX',
            true
        )
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php',

        /*
        |--------------------------------------------------------------------------
        | Validation URL
        |--------------------------------------------------------------------------
        */

        'validation_url' => env(
            'SSLCOMMERZ_SANDBOX',
            true
        )
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php',

        /*
        |--------------------------------------------------------------------------
        | Transaction Query URL
        |--------------------------------------------------------------------------
        */

        'transaction_query_url' => env(
            'SSLCOMMERZ_SANDBOX',
            true
        )
            ? 'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php',
    ],

];
