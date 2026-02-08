<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Tracked Games</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900">
                        {{ $stats['total_tracked'] }}
                        @if($stats['game_limit'])
                            <span class="text-sm font-normal text-gray-500">/ {{ $stats['game_limit'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Active Deals</div>
                    <div class="mt-1 text-3xl font-bold text-green-600">{{ $stats['active_deals'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Your Plan</div>
                    <div class="mt-1 text-3xl font-bold text-indigo-600">{{ $stats['plan']['name'] }}</div>
                    @if(auth()->user()->plan === 'free')
                        <a href="{{ route('pricing') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Upgrade &rarr;</a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Watchlist -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900">Recently Tracked</h3>
                        <a href="{{ route('watchlist.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
                    </div>

                    @if($watchlistItems->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($watchlistItems as $item)
                                <a href="{{ route('games.show', $item->game->steam_app_id) }}" class="flex items-center gap-3 p-4 hover:bg-gray-50">
                                    @if($item->game->header_image_url)
                                        <img src="{{ $item->game->header_image_url }}" alt="" class="w-16 h-10 object-cover rounded">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->game->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        @if($item->game->current_price)
                                            <span class="font-semibold">${{ number_format($item->game->current_price, 2) }}</span>
                                        @elseif($item->game->is_free)
                                            <span class="text-green-600 font-semibold">Free</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            <p>No games tracked yet.</p>
                            <a href="{{ route('games.search') }}" class="text-indigo-600 hover:text-indigo-800 text-sm mt-2 inline-block">Search games &rarr;</a>
                        </div>
                    @endif
                </div>

                <!-- Price Drops -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Current Deals</h3>
                    </div>

                    @if($recentPriceDrops->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($recentPriceDrops as $game)
                                <a href="{{ route('games.show', $game->steam_app_id) }}" class="flex items-center gap-3 p-4 hover:bg-gray-50">
                                    @if($game->header_image_url)
                                        <img src="{{ $game->header_image_url }}" alt="" class="w-16 h-10 object-cover rounded">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $game->name }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold">${{ number_format($game->current_price, 2) }}</span>
                                        <span class="px-1.5 py-0.5 bg-green-500 text-white rounded text-xs font-bold">
                                            -{{ $game->current_discount_percent }}%
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            <p>No active deals on your tracked games.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
