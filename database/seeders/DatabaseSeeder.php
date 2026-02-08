<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\PriceHistory;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@steam.test',
            'password' => Hash::make('password'),
            'plan' => 'enterprise',
            'subscription_status' => 'active',
            'is_admin' => true,
        ]);

        // Regular free user
        $freeUser = User::create([
            'name' => 'Free User',
            'email' => 'free@steam.test',
            'password' => Hash::make('password'),
            'plan' => 'free',
            'is_admin' => false,
        ]);

        // Sample games
        $games = [
            ['steam_app_id' => 730, 'name' => 'Counter-Strike 2', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/730/header.jpg', 'current_price' => 0, 'current_discount_percent' => 0, 'is_free' => true],
            ['steam_app_id' => 570, 'name' => 'Dota 2', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/570/header.jpg', 'current_price' => 0, 'current_discount_percent' => 0, 'is_free' => true],
            ['steam_app_id' => 1091500, 'name' => 'Cyberpunk 2077', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/1091500/header.jpg', 'current_price' => 29.99, 'current_discount_percent' => 50, 'is_free' => false],
            ['steam_app_id' => 1245620, 'name' => 'Elden Ring', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/1245620/header.jpg', 'current_price' => 39.99, 'current_discount_percent' => 33, 'is_free' => false],
            ['steam_app_id' => 292030, 'name' => 'The Witcher 3: Wild Hunt', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/292030/header.jpg', 'current_price' => 7.99, 'current_discount_percent' => 80, 'is_free' => false],
            ['steam_app_id' => 1174180, 'name' => 'Red Dead Redemption 2', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/1174180/header.jpg', 'current_price' => 40.19, 'current_discount_percent' => 33, 'is_free' => false],
            ['steam_app_id' => 413150, 'name' => 'Stardew Valley', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/413150/header.jpg', 'current_price' => 14.99, 'current_discount_percent' => 0, 'is_free' => false],
            ['steam_app_id' => 1086940, 'name' => 'Baldur\'s Gate 3', 'header_image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/1086940/header.jpg', 'current_price' => 59.99, 'current_discount_percent' => 0, 'is_free' => false],
        ];

        foreach ($games as $gameData) {
            $game = Game::create(array_merge($gameData, ['price_last_checked_at' => now()]));

            // Generate price history for the last 30 days
            $basePrice = $gameData['is_free'] ? 0 : ($gameData['current_price'] / (1 - $gameData['current_discount_percent'] / 100));
            if ($gameData['current_discount_percent'] == 0 && ! $gameData['is_free']) {
                $basePrice = $gameData['current_price'];
            }

            for ($day = 30; $day >= 0; $day--) {
                $discount = 0;
                // Simulate a sale period
                if ($day <= 5 && $gameData['current_discount_percent'] > 0) {
                    $discount = $gameData['current_discount_percent'];
                } elseif ($day >= 15 && $day <= 18 && ! $gameData['is_free']) {
                    $discount = rand(10, 30);
                }

                $price = $gameData['is_free'] ? 0 : round($basePrice * (1 - $discount / 100), 2);

                PriceHistory::create([
                    'game_id' => $game->id,
                    'price' => $price,
                    'discount_percent' => $discount,
                    'recorded_at' => now()->subDays($day),
                ]);
            }
        }

        // Add some games to admin's watchlist
        $allGames = Game::all();
        foreach ($allGames as $game) {
            Watchlist::create([
                'user_id' => $admin->id,
                'game_id' => $game->id,
                'target_price' => $game->is_free ? null : round($game->current_price * 0.7, 2),
                'notify_any_discount' => true,
            ]);
        }

        // Add 3 games to free user's watchlist (at the limit)
        foreach ($allGames->take(3) as $game) {
            Watchlist::create([
                'user_id' => $freeUser->id,
                'game_id' => $game->id,
                'notify_any_discount' => true,
            ]);
        }
    }
}
