<?php

return [
    /*
     * Telegram Bot API Token
     */
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
     * Telegram Bot Name
     */
    'bot_name' => env('TELEGRAM_BOT_NAME', 'TelegramBot'),

    /*
     * Webhook URL
     */
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),

    /*
     * Allowed User IDs (comma-separated)
     * Leave empty to allow all users
     */
    'allowed_users' => explode(',', env('TELEGRAM_ALLOWED_USERS', '')),

    /*
     * Enable debug mode
     */
    'debug' => env('TELEGRAM_DEBUG', false),

    /*
     * API Base URL
     */
    'api_base_url' => 'https://api.telegram.org',

    /*
     * Async Request Handler
     */
    'async_request_handler' => null,
];
