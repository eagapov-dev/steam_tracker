<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\PriceHistory;
use App\Services\SteamApiService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __construct(private SteamApiService $steamApi) {}

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $results = $this->steamApi->searchGames($query);
        }

        if ($request->wantsJson()) {
            return response()->json($results);
        }

        return view('games.search', compact('results', 'query'));
    }

    public function show(int $steamAppId, Request $request)
    {
        $game = Game::where('steam_app_id', $steamAppId)->first();

        if (! $game) {
            $game = $this->steamApi->syncGameData($steamAppId);
        }

        if (! $game) {
            abort(404, 'Game not found on Steam.');
        }

        $details = $this->steamApi->getAppDetails($steamAppId);

        $user = $request->user();
        $isTracked = false;
        $watchlistEntry = null;
        $historyDays = 7;

        if ($user) {
            $watchlistEntry = $user->watchlistItems()->where('game_id', $game->id)->first();
            $isTracked = $watchlistEntry !== null;
            $historyDays = $user->priceHistoryDays() ?? 3650; // ~10 years for unlimited
        }

        $priceHistory = PriceHistory::where('game_id', $game->id)
            ->where('recorded_at', '>=', now()->subDays($historyDays))
            ->orderBy('recorded_at')
            ->get();

        return view('games.show', compact('game', 'details', 'isTracked', 'watchlistEntry', 'priceHistory'));
    }

    public function priceHistoryJson(int $steamAppId, Request $request)
    {
        $game = Game::where('steam_app_id', $steamAppId)->firstOrFail();

        $user = $request->user();
        $historyDays = 7;
        if ($user) {
            $historyDays = $user->priceHistoryDays() ?? 3650;
        }

        $history = PriceHistory::where('game_id', $game->id)
            ->where('recorded_at', '>=', now()->subDays($historyDays))
            ->orderBy('recorded_at')
            ->get(['price', 'discount_percent', 'recorded_at']);

        return response()->json($history);
    }
}
