<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $game->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="md:flex">
                    @if($game->header_image_url)
                        <div class="md:w-1/3">
                            <img src="{{ $game->header_image_url }}" alt="{{ $game->name }}" class="w-full h-64 object-cover">
                        </div>
                    @endif
                    <div class="p-6 md:w-2/3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $game->name }}</h1>

                        @if($details)
                            <p class="mt-2 text-gray-600">{{ $details['short_description'] ?? '' }}</p>

                            @if(!empty($details['genres']))
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($details['genres'] as $genre)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">{{ $genre }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($details['developers']))
                                <p class="mt-2 text-sm text-gray-500">Developer: {{ implode(', ', $details['developers']) }}</p>
                            @endif
                        @endif

                        <div class="mt-4 flex items-center gap-4">
                            @if($game->is_free)
                                <span class="text-2xl font-bold text-green-600">Free to Play</span>
                            @elseif($game->current_price)
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($game->current_price, 2) }}</span>
                                @if($game->current_discount_percent > 0)
                                    <span class="px-2 py-1 bg-green-500 text-white rounded font-bold">
                                        -{{ $game->current_discount_percent }}%
                                    </span>
                                @endif
                            @endif
                        </div>

                        <div class="mt-6">
                            @auth
                                @if($isTracked)
                                    <div class="flex items-center gap-4">
                                        <span class="text-green-600 font-medium">Tracking this game</span>
                                        <form action="{{ route('watchlist.destroy', $game->steam_app_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remove from watchlist</button>
                                        </form>
                                    </div>

                                    <form action="{{ route('watchlist.update', $game->steam_app_id) }}" method="POST" class="mt-4 flex items-end gap-4">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Price ($)</label>
                                            <input type="number" name="target_price" step="0.01" min="0"
                                                value="{{ $watchlistEntry->target_price }}"
                                                placeholder="e.g. 9.99"
                                                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-32">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="hidden" name="notify_any_discount" value="0">
                                            <input type="checkbox" name="notify_any_discount" value="1"
                                                {{ $watchlistEntry->notify_any_discount ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <label class="text-sm text-gray-600">Notify on any discount</label>
                                        </div>
                                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm">
                                            Update Alert
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('watchlist.store', $game->steam_app_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                                            Add to Watchlist
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="inline-flex px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                                    Log in to Track
                                </a>
                            @endauth
                        </div>

                        <div class="mt-4">
                            <a href="https://store.steampowered.com/app/{{ $game->steam_app_id }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                View on Steam &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($priceHistory->count() > 0)
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Price History</h3>
                    <canvas id="priceChart" height="100"></canvas>
                </div>

                @push('scripts')
                <script>
                    const ctx = document.getElementById('priceChart').getContext('2d');
                    const priceData = @json($priceHistory);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: priceData.map(p => new Date(p.recorded_at).toLocaleDateString()),
                            datasets: [{
                                label: 'Price ($)',
                                data: priceData.map(p => parseFloat(p.price)),
                                borderColor: 'rgb(79, 70, 229)',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                fill: true,
                                tension: 0.3,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => '$' + value.toFixed(2)
                                    }
                                }
                            }
                        }
                    });
                </script>
                @endpush
            @endif
        </div>
    </div>
</x-app-layout>
