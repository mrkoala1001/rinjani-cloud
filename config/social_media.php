<?php

return [
    /*
     * Facebook Graph API Version
     */
    'api_version' => env('FACEBOOK_API_VERSION', 'v18.0'),

    /*
     * Facebook Page Configuration
     */
    'page' => [
        'access_token' => env('FACEBOOK_PAGE_ACCESS_TOKEN'),
        'id' => env('FACEBOOK_PAGE_ID'),
    ],

    /*
     * Instagram Business Account Configuration
     */
    'instagram' => [
        'business_account_id' => env('INSTAGRAM_BUSINESS_ACCOUNT_ID'),
    ],

    /*
     * Auto-post to social media after blog creation
     */
    'auto_post' => env('SOCIAL_MEDIA_AUTO_POST', true),
];
