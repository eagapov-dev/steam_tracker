<?php

return [
    'free' => [
        'name' => 'Free',
        'price' => 0,
        'game_limit' => 3,
        'check_interval_hours' => 24,
        'email_notifications' => false,
        'telegram_notifications' => false,
        'price_history_days' => 7,
        'api_access' => false,
        'webhooks' => false,
    ],
    'starter' => [
        'name' => 'Starter',
        'price' => 3,
        'game_limit' => 10,
        'check_interval_hours' => 12,
        'email_notifications' => true,
        'telegram_notifications' => false,
        'price_history_days' => 30,
        'api_access' => false,
        'webhooks' => false,
    ],
    'pro' => [
        'name' => 'Pro',
        'price' => 9,
        'game_limit' => 50,
        'check_interval_hours' => 6,
        'email_notifications' => true,
        'telegram_notifications' => true,
        'price_history_days' => 365,
        'api_access' => false,
        'webhooks' => false,
    ],
    'enterprise' => [
        'name' => 'Enterprise',
        'price' => 29,
        'game_limit' => null, // unlimited
        'check_interval_hours' => 1,
        'email_notifications' => true,
        'telegram_notifications' => true,
        'price_history_days' => null, // unlimited
        'api_access' => true,
        'webhooks' => true,
    ],
];
