<div class="relative" x-data="{ open: @entangle('showResults') }" @click.away="open = false">
    <input
        type="text"
        wire:model.live.debounce.300ms="query"
        placeholder="Quick search for a game..."
        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        @focus="if(query.length >= 2) open = true"
    >

    @if($showResults && count($results) > 0)
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-80 overflow-y-auto" x-show="open">
            @foreach($results as $game)
                <a href="{{ route('games.show', $game['steam_app_id']) }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                    @if($game['header_image_url'])
                        <img src="{{ $game['header_image_url'] }}" alt="" class="w-12 h-8 object-cover rounded flex-shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $game['name'] }}</p>
                    </div>
                    <div class="flex-shrink-0 text-sm">
                        @if($game['is_free'])
                            <span class="text-green-600 font-medium">Free</span>
                        @elseif($game['price'])
                            <span class="font-medium">${{ number_format($game['price'], 2) }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
