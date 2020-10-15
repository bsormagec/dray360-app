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
        Str::snake(Company::TCOMPANIES_DEMO) => [
            'username' => env('RIP_CMS_TCOMPANIES_DEMO_USERNAME'),
            'password' => env('RIP_CMS_TCOMPANIES_DEMO_PASSWORD'),
            'token' => env('RIP_CMS_TCOMPANIES_DEMO_TOKEN'),
        ],
        Str::snake(Company::IXT_ONBOARDING) => [
            'username' => env('RIP_CMS_IXT_ONBOARDING_USERNAME'),
            'password' => env('RIP_CMS_IXT_ONBOARDING_PASSWORD'),
            'token' => env('RIP_CMS_IXT_ONBOARDING_TOKEN'),
        ],
        Str::snake(Company::IXT) => [
            'username' => env('RIP_CMS_IXT_USERNAME'),
            'password' => env('RIP_CMS_IXT_PASSWORD'),
            'token' => env('RIP_CMS_IXT_TOKEN'),
        ],
    ],

    'search-address' => [
        'url' => env('SEARCH_ADDRESS_URL', 'https://b9da68xgc1.execute-api.us-east-2.amazonaws.com/api/1.0/dev/address-search'),
        'api_key' => env('SEARCH_ADDRESS_API_KEY'),
    ],

    'sns-topics' => [
        'status' => env('SNS_STATUS_TOPIC', 'arn:aws:sns:us-east-2:781066913506:dray360-status-topic-dev'),
    ],

];
