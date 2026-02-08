<?php

namespace App\Providers;

use App\Services\SteamApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SteamApiService::class);
    }

    public function boot(): void
    {
        //
    }
}
