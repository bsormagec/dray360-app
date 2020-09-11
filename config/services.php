<?php

use App\Models\Company;
use Illuminate\Support\Str;

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
        Str::snake(Company::CUSHING) => [
            'username' => env('RIP_CMS_CUSHING_USERNAME'),
            'password' => env('RIP_CMS_CUSHING_PASSWORD'),
            'token' => env('RIP_CMS_CUSHING_TOKEN'),
        ],
        Str::snake(Company::TCOMPANIES_DEV) => [
            'username' => env('RIP_CMS_TCOMPANIES_DEV_USERNAME'),
            'password' => env('RIP_CMS_TCOMPANIES_DEV_PASSWORD'),
            'token' => env('RIP_CMS_TCOMPANIES_DEV_TOKEN'),
        ],
    ],

    'search-address' => [
        'url' => env('SEARCH_ADDRESS_URL', 'https://i0mgwmnrb1.execute-api.us-east-2.amazonaws.com/default/ocr-address-search-dev'),
        'api_key' => env('SEARCH_ADDRESS_API_KEY'),
    ],

    'sns-topics' => [
        'send-to-tms' => env('SNS_SEND_TO_TMS_TOPIC', 'arn:aws:sns:us-east-2:228274877766:ocr-status-update-gateway-dev'),
    ],

];
