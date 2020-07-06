<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe'   => [
        'model'  => \App\Models\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id'     => env('FACEBOOK_ID'),
        'client_secret' => env('FACEBOOK_SECRET'),
        'redirect'      => env('FACEBOOK_REDIRECT'),
    ],
    'google'   => [
        'client_id'     => env('GOOGLE_ID'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT'),
    ],
    'linkedin' => [
        'client_id'     => env('LINKEDIN_ID'),
        'client_secret' => env('LINKEDIN_SECRET'),
        'redirect'      => env('LINKEDIN_REDIRECT'),
    ],
    'eMoney'   => [
        'url'         => "https://api.emoney.ge/v1/Service.svc/json",
        'key'         => "5HZHDF0TZGCDU2QG5B2G0PG4A3JECKQ0",
        'merchant_id' => "1071",
    ],
    'sms'      => [
        'aerialink' => [
            'url'    => 'https://apix.aerialink.net/v4/messages/',
            'key'    => 'c973fa4a5ff719a653f0f7013965a0b7',
            'secret' => '73cfa7638f4db259cf3f49a8424570a9',
        ],
    ],
];
