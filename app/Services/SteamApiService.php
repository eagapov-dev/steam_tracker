<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteamApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('steam.store_url');
    }

    public function searchGames(string $query): array
    {
        $cacheKey = 'steam_search_' . md5($query);

        return Cache::remember($cacheKey, config('steam.cache_search_ttl'), function () use ($query) {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/storesearch/", [
                    'term' => $query,
                    'l' => 'english',
                    'cc' => 'us',
                ]);

                if (!$response->successful()) {
                    return [];
                }

                $data = $response->json();

                return collect($data['items'] ?? [])->map(function ($item) {
                    return [
                        'steam_app_id' => $item['id'],
                        'name' => $item['name'],
                        'header_image_url' => $item['tiny_image'] ?? null,
                        'price' => isset($item['price']) ? $item['price']['final'] / 100 : null,
                        'is_free' => ($item['price']['final'] ?? 0) === 0,
                    ];
                })->toArray();
            } catch (\Exception $e) {
                Log::error('Steam search failed', ['error' => $e->getMessage(), 'query' => $query]);
                return [];
            }
        });
    }

    public function getAppDetails(int $steamAppId): ?array
    {
        $cacheKey = "steam_app_{$steamAppId}";

        return Cache::remember($cacheKey, 300, function () use ($steamAppId) {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/appdetails", [
                    'appids' => $steamAppId,
                    'cc' => 'us',
                ]);

                if (!$response->successful()) {
                    return null;
                }

                $data = $response->json();

                if (!isset($data[$steamAppId]['success']) || !$data[$steamAppId]['success']) {
                    return null;
                }

                $appData = $data[$steamAppId]['data'];

                return [
                    'steam_app_id' => $steamAppId,
                    'name' => $appData['name'],
                    'header_image_url' => $appData['header_image'] ?? null,
                    'is_free' => $appData['is_free'] ?? false,
                    'price' => isset($appData['price_overview'])
                        ? $appData['price_overview']['final'] / 100
                        : null,
                    'discount_percent' => $appData['price_overview']['discount_percent'] ?? 0,
                    'short_description' => $appData['short_description'] ?? '',
                    'genres' => collect($appData['genres'] ?? [])->pluck('description')->toArray(),
                    'release_date' => $appData['release_date']['date'] ?? null,
                    'developers' => $appData['developers'] ?? [],
                    'publishers' => $appData['publishers'] ?? [],
                ];
            } catch (\Exception $e) {
                Log::error('Steam app details failed', ['error' => $e->getMessage(), 'app_id' => $steamAppId]);
                return null;
            }
        });
    }

    public function getAppPrice(int $steamAppId): ?array
    {
        $details = $this->getAppDetails($steamAppId);

        if (!$details) {
            return null;
        }

        return [
            'price' => $details['price'],
            'discount_percent' => $details['discount_percent'],
            'is_free' => $details['is_free'],
        ];
    }

    public function syncGameData(int $steamAppId): ?Game
    {
        $details = $this->getAppDetails($steamAppId);

        if (!$details) {
            return null;
        }

        return Game::updateOrCreate(
            ['steam_app_id' => $steamAppId],
            [
                'name' => $details['name'],
                'header_image_url' => $details['header_image_url'],
                'current_price' => $details['price'],
                'current_discount_percent' => $details['discount_percent'],
                'is_free' => $details['is_free'],
                'price_last_checked_at' => now(),
            ]
        );
    }
}
