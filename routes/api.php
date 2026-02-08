<?php

use App\Http\Controllers\Api\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API v1 (Enterprise plan only)
Route::middleware(['auth:sanctum', 'subscribed:enterprise'])
    ->prefix('v1')
    ->group(function () {
        Route::get('/watchlist', [GameController::class, 'watchlist']);
        Route::get('/games/{steamAppId}/prices', [GameController::class, 'prices']);
    });
