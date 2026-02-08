<?php

return [
    'store_url' => env('STEAM_API_STORE_URL', 'https://store.steampowered.com/api'),

    'cache_search_ttl' => 3600, // 1 hour

    'request_delay_ms' => 1500, // delay between API requests to avoid rate limiting
];
