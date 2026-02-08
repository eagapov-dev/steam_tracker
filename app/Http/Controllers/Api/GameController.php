<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\PriceHistory;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function watchlist(Request $request)
    {
        $items = $request->user()
            ->watchlistItems()
            ->with('game')
            ->get()
            ->map(function ($item) {
                return [
                    'steam_app_id' => $item->game->steam_app_id,
                    'name' => $item->game->name,
                    'current_price' => $item->game->current_price,
                    'current_discount' => $item->game->current_discount_percent,
                    'target_price' => $item->target_price,
                    'tracked_since' => $item->created_at,
                ];
            });

        return response()->json(['data' => $items]);
    }

    public function prices(int $steamAppId, Request $request)
    {
        $game = Game::where('steam_app_id', $steamAppId)->firstOrFail();

        $days = $request->input('days', 30);
        $days = min($days, 3650);

        $history = PriceHistory::where('game_id', $game->id)
            ->where('recorded_at', '>=', now()->subDays($days))
            ->orderBy('recorded_at')
            ->get(['price', 'discount_percent', 'recorded_at']);

        return response()->json([
            'game' => [
                'steam_app_id' => $game->steam_app_id,
                'name' => $game->name,
                'current_price' => $game->current_price,
                'current_discount' => $game->current_discount_percent,
            ],
            'price_history' => $history,
        ]);
    }
}
