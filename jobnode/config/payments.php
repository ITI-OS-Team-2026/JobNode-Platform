<?php

return [
    // Unlock price (server-controlled)
    'unlock_candidate_price' => env('KASHIER_UNLOCK_PRICE', 100.00),
    'unlock_candidate_currency' => env('KASHIER_UNLOCK_CURRENCY', 'EGP'),

    // Allowed providers
    'allowed_providers' => explode(',', env('PAYMENTS_ALLOWED_PROVIDERS', 'kashier')),

    // Kashier settings
    'kashier' => [
        'merchant_id' => env('KASHIER_MERCHANT_ID'),
        'api_key' => env('KASHIER_API_KEY'),
        'secret_key' => env('KASHIER_SECRET_KEY'),
        'mode' => env('KASHIER_MODE', 'test'),
        'base_url' => env('KASHIER_BASE_URL', 'https://test-api.kashier.io/v3'),
    ],

    // Public base URL used for Kashier redirect/webhook URLs.
    // Must be publicly reachable (not localhost).
    // In dev: set KASHIER_REDIRECT_BASE_URL to your ngrok HTTPS URL.
    // In prod: leave unset and it defaults to APP_URL.
    'redirect_base_url' => rtrim(env('KASHIER_REDIRECT_BASE_URL', env('APP_URL', 'http://localhost')), '/'),
];
