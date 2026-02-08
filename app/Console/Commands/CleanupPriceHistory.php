<?php

namespace App\Console\Commands;

use App\Models\PriceHistory;
use Illuminate\Console\Command;

class CleanupPriceHistory extends Command
{
    protected $signature = 'price:cleanup-history';

    protected $description = 'Clean up old price history data based on plan limits';

    public function handle(): void
    {
        // Remove price history older than 30 days for games only watched by free users
        // This keeps data for higher plans while cleaning up free tier data
        $deleted = PriceHistory::where('recorded_at', '<', now()->subDays(30))
            ->whereHas('game', function ($query) {
                $query->whereDoesntHave('watchlistEntries', function ($q) {
                    $q->whereHas('user', function ($userQuery) {
                        $userQuery->where('plan', '!=', 'free');
                    });
                });
            })
            ->delete();

        $this->info("Cleaned up {$deleted} old price history records.");
    }
}
