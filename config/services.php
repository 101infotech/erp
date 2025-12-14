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

    /*
    |--------------------------------------------------------------------------
    | Jibble API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Jibble time tracking and attendance management API.
    | Used by the HRM module for employee and timesheet synchronization.
    |
    */

    'jibble' => [
        'client_id' => env('JIBBLE_CLIENT_ID'),
        'client_secret' => env('JIBBLE_CLIENT_SECRET'),
        'workspace_id' => env('JIBBLE_WORKSPACE_ID'),
        'base_url' => env('JIBBLE_BASE_URL', 'https://time-attendance.prod.jibble.io'),
        'people_select' => env('JIBBLE_PEOPLE_SELECT', 'id,fullName,email,department'),
        'default_company_id' => env('JIBBLE_DEFAULT_COMPANY_ID'),
    ],

];
