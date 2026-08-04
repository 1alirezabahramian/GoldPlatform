<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'kimia' => [
        'base_url' => env('KIMIA_BASE_URL'),
        'username' => env('KIMIA_USERNAME'),
        'password' => env('KIMIA_PASSWORD'),
        'timeout' => env('KIMIA_TIMEOUT', 30),
        'read_only' => env('KIMIA_READ_ONLY', true),
        'read_retries' => env('KIMIA_READ_RETRIES', 2),
        'retry_delay_ms' => env('KIMIA_RETRY_DELAY_MS', 250),
        'timeout_profiles' => [
            '/api/account' => env('KIMIA_TIMEOUT_ACCOUNT', 15),
            '/api/product' => env('KIMIA_TIMEOUT_PRODUCT', 20),
            '/api/barcode' => env('KIMIA_TIMEOUT_BARCODE', 20),
            '/api/voucher/balance' => env('KIMIA_TIMEOUT_BALANCE', 20),
            '/api/voucher/transactions' => env('KIMIA_TIMEOUT_VOUCHER', 30),
        ],
    ],

    'smsir' => [
        'base_url' => env('SMSIR_BASE_URL'),
        'api_key' => env('SMSIR_API_KEY'),
        'templates' => [
            'login' => env('SMSIR_TEMPLATE_LOGIN'),
        ],
    ],

];
