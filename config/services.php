<?php

return [
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'instagram' => [
        'handle' => env('INSTAGRAM_HANDLE', 'alishe_nails'),
        'access_token' => env('INSTAGRAM_ACCESS_TOKEN'),
        'business_account_id' => env('INSTAGRAM_BUSINESS_ACCOUNT_ID'),
    ],

    'whatsapp' => [
        'number' => env('WHATSAPP_NUMBER', '+92 3412126680'),
    ],

    'payment' => [
        'bank_name' => env('BANK_NAME', 'Meezan Bank'),
        'bank_account_title' => env('BANK_ACCOUNT_TITLE', 'Alishe Nails'),
        'bank_account_number' => env('BANK_ACCOUNT_NUMBER', '01020304050607'),
        'bank_iban' => env('BANK_IBAN', 'PK36MEZN0001020304050607'),
        'jazzcash_account_title' => env('JAZZCASH_ACCOUNT_TITLE', 'Alishe Nails'),
        'jazzcash_number' => env('JAZZCASH_NUMBER', '03412126680'),
        'easypaisa_account_title' => env('EASYPAISA_ACCOUNT_TITLE', 'Alishe Nails'),
        'easypaisa_number' => env('EASYPAISA_NUMBER', '03412126680'),
    ],

    // NOTE: admin authentication now uses real hashed-password Admin
    // accounts (see App\Models\Admin + AdminSeeder) via the 'admin' Auth
    // guard, not a flat .env password. This key only supplies where new-order
    // notifications are sent.
    'admin' => [
        'notification_email' => env('ADMIN_NOTIFICATION_EMAIL', 'owner@alishenails.com'),
    ],
];
