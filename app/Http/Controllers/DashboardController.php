<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $watchlistItems = $user->watchlistItems()
            ->with('game')
            ->latest()
            ->limit(5)
            ->get();

        $recentPriceDrops = $user->watchlist()
            ->where('current_discount_percent', '>', 0)
            ->orderByDesc('current_discount_percent')
            ->limit(5)
            ->get();

        $stats = [
            'total_tracked' => $user->watchlistItems()->count(),
            'game_limit' => $user->gameLimit(),
            'active_deals' => $user->watchlist()->where('current_discount_percent', '>', 0)->count(),
            'plan' => $user->planConfig(),
        ];

        return view('dashboard', compact('watchlistItems', 'recentPriceDrops', 'stats'));
    }
}
