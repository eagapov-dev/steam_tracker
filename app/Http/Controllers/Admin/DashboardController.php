<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Watchlist;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'paid_users' => User::where('plan', '!=', 'free')->count(),
            'total_games' => Game::count(),
            'total_watchlist_items' => Watchlist::count(),
            'notifications_today' => NotificationLog::where('sent_at', '>=', now()->startOfDay())->count(),
            'users_by_plan' => User::selectRaw('plan, count(*) as count')->groupBy('plan')->pluck('count', 'plan'),
        ];

        $recentUsers = User::latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    public function users()
    {
        $users = User::withCount('watchlistItems')
            ->latest()
            ->paginate(25);

        return view('admin.users', compact('users'));
    }

    public function games()
    {
        $games = Game::withCount('watchlistEntries')
            ->orderByDesc('watchlist_entries_count')
            ->paginate(25);

        return view('admin.games', compact('games'));
    }
}
