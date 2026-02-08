<?php

return [
    'api_key' => env('LEMONSQUEEZY_API_KEY'),
    'store_id' => env('LEMONSQUEEZY_STORE_ID'),
    'webhook_secret' => env('LEMONSQUEEZY_WEBHOOK_SECRET'),

    'variant_ids' => [
        'starter' => env('LEMONSQUEEZY_STARTER_VARIANT_ID'),
        'pro' => env('LEMONSQUEEZY_PRO_VARIANT_ID'),
        'enterprise' => env('LEMONSQUEEZY_ENTERPRISE_VARIANT_ID'),
    ],
];
