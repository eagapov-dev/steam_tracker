<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Watchlist</h2>
            <a href="{{ route('games.search') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                + Add Game
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($watchlistItems->count() === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Your watchlist is empty</h3>
                    <p class="text-gray-500 mb-6">Search for games and add them to your watchlist to track price changes.</p>
                    <a href="{{ route('games.search') }}" class="inline-flex px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                        Search Games
                    </a>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200">
                        <p class="text-sm text-gray-600">
                            Tracking {{ $watchlistItems->total() }} game(s)
                            @if(auth()->user()->gameLimit())
                                / {{ auth()->user()->gameLimit() }} max
                            @endif
                        </p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($watchlistItems as $item)
                            <div class="p-4 flex items-center gap-4 hover:bg-gray-50">
                                @if($item->game->header_image_url)
                                    <img src="{{ $item->game->header_image_url }}" alt="{{ $item->game->name }}" class="w-24 h-14 object-cover rounded">
                                @endif

                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('games.show', $item->game->steam_app_id) }}" class="font-medium text-gray-900 hover:text-indigo-600 truncate block">
                                        {{ $item->game->name }}
                                    </a>
                                    <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                                        @if($item->target_price)
                                            <span>Target: ${{ number_format($item->target_price, 2) }}</span>
                                        @endif
                                        @if($item->notify_any_discount)
                                            <span class="text-green-600">Any discount alert</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right">
                                    @if($item->game->is_free)
                                        <span class="text-green-600 font-bold">Free</span>
                                    @elseif($item->game->current_price)
                                        <span class="text-lg font-bold text-gray-900">${{ number_format($item->game->current_price, 2) }}</span>
                                        @if($item->game->current_discount_percent > 0)
                                            <span class="ml-1 px-1.5 py-0.5 bg-green-500 text-white rounded text-xs font-bold">
                                                -{{ $item->game->current_discount_percent }}%
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <form action="{{ route('watchlist.destroy', $item->game->steam_app_id) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Remove">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6">
                    {{ $watchlistItems->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
