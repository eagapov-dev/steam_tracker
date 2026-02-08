<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Landing
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Pricing (public)
Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('pricing');

// Games (public search + details)
Route::get('/games/search', [GameController::class, 'search'])->name('games.search');
Route::get('/games/{steamAppId}', [GameController::class, 'show'])->name('games.show');
Route::get('/games/{steamAppId}/price-history', [GameController::class, 'priceHistoryJson'])->name('games.price-history');

// Webhooks (no auth, verified by signature)
Route::post('/webhooks/lemonsqueezy', [WebhookController::class, 'handleLemonSqueezy'])
    ->name('webhooks.lemonsqueezy');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Watchlist
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist/{steamAppId}', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::delete('/watchlist/{steamAppId}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');
    Route::patch('/watchlist/{steamAppId}', [WatchlistController::class, 'update'])->name('watchlist.update');

    // Subscription
    Route::get('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/portal', [SubscriptionController::class, 'portal'])->name('subscription.portal');

    // Admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/games', [AdminDashboardController::class, 'games'])->name('games');
    });
});

require __DIR__.'/auth.php';
