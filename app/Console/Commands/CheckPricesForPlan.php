<?php

namespace App\Console\Commands;

use App\Jobs\CheckGamePrices;
use App\Models\Game;
use Illuminate\Console\Command;

class CheckPricesForPlan extends Command
{
    protected $signature = 'price:check {plan : The subscription plan (free, starter, pro, enterprise)}';

    protected $description = 'Check prices for games watched by users on a specific plan';

    public function handle(): void
    {
        $plan = $this->argument('plan');

        if (! in_array($plan, ['free', 'starter', 'pro', 'enterprise'])) {
            $this->error('Invalid plan. Use: free, starter, pro, enterprise');

            return;
        }

        $gameIds = Game::whereHas('watchlistEntries', function ($query) use ($plan) {
            $query->whereHas('user', function ($q) use ($plan) {
                $q->where('plan', $plan);
            });
        })->pluck('id')->toArray();

        if (empty($gameIds)) {
            $this->info("No games to check for plan: {$plan}");

            return;
        }

        // Split into batches of 10 to avoid rate limits
        $batches = array_chunk($gameIds, 10);

        foreach ($batches as $index => $batch) {
            CheckGamePrices::dispatch($batch)->delay(now()->addSeconds($index * 20));
        }

        $this->info('Dispatched price checks for '.count($gameIds)." games on plan: {$plan}");
    }
}
