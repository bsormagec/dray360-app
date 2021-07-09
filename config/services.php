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
        'region' => env('AWS_SES_REGION', 'us-east-1'),
    ],

    'ripcms' => [
        'url' => 'https://www.ripcms.com/',
    ],

    'blackfly' => [
        'url' => env('BLACKFLY_URL'),
    ],

    'compcare' => [
        'identity_url' => env('COMPCARE_IDENTITY_URL'),
        'entities_url' => env('COMPCARE_ENTITIES_URL'),
        'orders_url' => env('COMPCARE_ORDERS_URL'),
    ],

    'search-address' => [
        'url' => env('SEARCH_ADDRESS_URL', 'https://b9da68xgc1.execute-api.us-east-2.amazonaws.com/api/1.0/dev/address-search'),
        'api_key' => env('SEARCH_ADDRESS_API_KEY'),
    ],

    'sns-topics' => [
        'status' => env('SNS_STATUS_TOPIC', 'arn:aws:sns:us-east-2:781066913506:dray360-status-topic-dev'),
    ],

    'dray360-api' => [
        'url' => env('DRAY360_API_URL', ''),
        'api_key' => env('DRAY360_API_KEY', ''),
    ]

];
