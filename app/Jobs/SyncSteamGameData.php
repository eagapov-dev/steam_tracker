<?php

namespace App\Jobs;

use App\Services\SteamApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSteamGameData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $steamAppId
    ) {}

    public function handle(SteamApiService $steamApi): void
    {
        $steamApi->syncGameData($this->steamAppId);
    }
}
