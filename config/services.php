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

    // dashboardv2 URLs — base URL is used to resolve user profile pics,
    // make_payment_url is the netwostore "Pay Now" endpoint.
    // Pay Now format: {make_payment_url}?uid={base64(email)}&type=custom&id={request_id}
    'dashboardv2' => [
        'base_url'         => env('DASHBOARDV2_URL', 'https://dashboard.gowologlobal.com'),
        'make_payment_url' => env('MAKE_PAYMENT_URL', 'https://netwostore.gowologlobal.com/gowolo-make-payment'),
    ],

];
