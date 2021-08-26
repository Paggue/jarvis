<?php

return [
    'prefix'     => 'api',
    'middleware' => ['api'],

    'trello' => [
        'key' => env('TRELLO_KEY'),

        'token' => env('TRELLO_TOKEN'),

        'production' => env('TRELLO_PRODUCTION', false),
    ],

    'pipefy' => [
        'user_token' => env('PIPEFY_USER_TOKEN'),

        'token' => env('PIPEFY_TOKEN'),

        'production' => env('PIPEFY_PRODUCTION', false),
    ],

    'comtele' => [
        'url' => env('COMTELE_URL'),

        'token' => env('COMTELE_TOKEN'),

        'production' => env('COMTELE_PRODUCTION', false),
    ],

    'starkbank' => [
        'project_id'  => env('STARKBANK_PROJECT_ID'),
        'environment' => env('STARKBANK_ENVIRONMENT'),
        'token'       => env('STARKBANK_TOKEN'),
    ],

    'pagseguro' => [
        'base_url'         => env('PAGSEGURO_URL'),
        'token'            => env('PAGSEGURO_TOKEN'),
        'email'            => env('PAGSEGURO_EMAIL'),
        'webhook_base_url' => env('PAGSEGURO_WEBHOOK_BASE_URL'),
    ],

    'pix' => [
        'pix_key'       => env('PIX_KEY'),
        'merchant_name' => env('PIX_MERCHANT_NAME'),
        'merchant_city' => env('PIX_MERCHANT_CITY'),
    ],

    'metabase_secret_key' => env('METABASE_SECRET_KEY'),

    'maps_api_key' => env('MAPS_API_KEY'),

    'cnpja_api_key' => env('CNPJA_API_KEY'),

    'firebase' => [
        'token' => env('FIREBASE_TOKEN'),
    ],
];
