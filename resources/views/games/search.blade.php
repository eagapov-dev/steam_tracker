<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Search Steam Games</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Livewire Search -->
                <div class="mb-8">
                    @livewire('game-search')
                </div>

                <!-- Full Search Form -->
                <form action="{{ route('games.search') }}" method="GET" class="mb-8">
                    <div class="flex gap-4">
                        <input type="text" name="q" value="{{ $query }}" placeholder="Search for a game..."
                            class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                            Search
                        </button>
                    </div>
                </form>

                @if($query && count($results) === 0)
                    <p class="text-gray-500 text-center py-8">No games found for "{{ $query }}".</p>
                @endif

                @if(count($results) > 0)
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($results as $game)
                            <a href="{{ route('games.show', $game['steam_app_id']) }}"
                               class="block bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow border border-gray-200">
                                @if($game['header_image_url'])
                                    <img src="{{ $game['header_image_url'] }}" alt="{{ $game['name'] }}" class="w-full h-32 object-cover">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $game['name'] }}</h3>
                                    <div class="mt-2 flex items-center justify-between">
                                        @if($game['is_free'])
                                            <span class="text-green-600 font-bold">Free to Play</span>
                                        @elseif($game['price'])
                                            <span class="text-lg font-bold text-gray-900">${{ number_format($game['price'], 2) }}</span>
                                        @else
                                            <span class="text-gray-500">Price N/A</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if(!$query)
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <p class="text-lg">Search for Steam games to start tracking prices</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
