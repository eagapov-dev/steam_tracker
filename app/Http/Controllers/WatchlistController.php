<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Watchlist;
use App\Services\SteamApiService;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function __construct(private SteamApiService $steamApi) {}

    public function index(Request $request)
    {
        $watchlistItems = $request->user()
            ->watchlistItems()
            ->with('game')
            ->latest()
            ->paginate(20);

        return view('watchlist.index', compact('watchlistItems'));
    }

    public function store(int $steamAppId, Request $request)
    {
        $user = $request->user();

        if (! $user->canAddGame()) {
            $limit = $user->gameLimit();

            return back()->with('error', "You've reached your limit of {$limit} games. Upgrade your plan to track more.");
        }

        $game = Game::where('steam_app_id', $steamAppId)->first();

        if (! $game) {
            $game = $this->steamApi->syncGameData($steamAppId);
        }

        if (! $game) {
            return back()->with('error', 'Game not found on Steam.');
        }

        $existing = Watchlist::where('user_id', $user->id)->where('game_id', $game->id)->first();
        if ($existing) {
            return back()->with('info', 'Game is already in your watchlist.');
        }

        Watchlist::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'notify_any_discount' => true,
        ]);

        return back()->with('success', "{$game->name} added to your watchlist!");
    }

    public function destroy(int $steamAppId, Request $request)
    {
        $user = $request->user();
        $game = Game::where('steam_app_id', $steamAppId)->firstOrFail();

        Watchlist::where('user_id', $user->id)->where('game_id', $game->id)->delete();

        return back()->with('success', "{$game->name} removed from your watchlist.");
    }

    public function update(int $steamAppId, Request $request)
    {
        $validated = $request->validate([
            'target_price' => 'nullable|numeric|min:0',
            'notify_any_discount' => 'boolean',
        ]);

        $user = $request->user();
        $game = Game::where('steam_app_id', $steamAppId)->firstOrFail();

        $watchlistItem = Watchlist::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->firstOrFail();

        $watchlistItem->update($validated);

        return back()->with('success', 'Alert settings updated.');
    }
}
