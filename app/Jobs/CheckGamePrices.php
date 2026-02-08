<?php

namespace App\Jobs;

use App\Models\Game;
use App\Models\PriceHistory;
use App\Models\Watchlist;
use App\Services\SteamApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckGamePrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $gameIds
    ) {}

    public function handle(SteamApiService $steamApi): void
    {
        $games = Game::whereIn('id', $this->gameIds)->get();

        foreach ($games as $game) {
            try {
                $priceData = $steamApi->getAppPrice($game->steam_app_id);

                if (!$priceData) {
                    continue;
                }

                $oldPrice = $game->current_price;
                $newPrice = $priceData['price'];

                $game->update([
                    'current_price' => $newPrice,
                    'current_discount_percent' => $priceData['discount_percent'],
                    'is_free' => $priceData['is_free'],
                    'price_last_checked_at' => now(),
                ]);

                PriceHistory::create([
                    'game_id' => $game->id,
                    'price' => $newPrice ?? 0,
                    'discount_percent' => $priceData['discount_percent'],
                    'recorded_at' => now(),
                ]);

                // Detect price drop and trigger notifications
                if ($oldPrice !== null && $newPrice !== null && $newPrice < $oldPrice) {
                    $this->notifyWatchers($game, $oldPrice, $newPrice, $priceData['discount_percent']);
                }

                // Rate limit: sleep between requests
                usleep(config('steam.request_delay_ms', 1500) * 1000);
            } catch (\Exception $e) {
                Log::error("Failed to check price for game {$game->steam_app_id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function notifyWatchers(Game $game, float $oldPrice, float $newPrice, int $discountPercent): void
    {
        $watchlistEntries = Watchlist::where('game_id', $game->id)
            ->with('user')
            ->get();

        foreach ($watchlistEntries as $entry) {
            $shouldNotify = false;

            if ($entry->notify_any_discount && $discountPercent > 0) {
                $shouldNotify = true;
            }

            if ($entry->target_price !== null && $newPrice <= $entry->target_price) {
                $shouldNotify = true;
            }

            if ($shouldNotify) {
                SendPriceDropNotification::dispatch($entry->user, $game, $oldPrice, $newPrice, $discountPercent);

                $entry->update(['notified_at' => now()]);
            }
        }
    }
}
