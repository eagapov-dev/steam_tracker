<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subscription</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Subscription</h3>

                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">{{ ucfirst($user->plan) }}</span>
                            <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->subscription_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($user->subscription_status ?? 'free') }}
                            </span>
                        </div>
                    </div>

                    @if($user->subscription_ends_at)
                        <p class="text-sm text-gray-600">
                            @if($user->subscription_status === 'cancelled')
                                Access until: {{ $user->subscription_ends_at->format('M d, Y') }}
                            @else
                                Next billing date: {{ $user->subscription_ends_at->format('M d, Y') }}
                            @endif
                        </p>
                    @endif

                    @php $planConfig = $user->planConfig(); @endphp

                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Game limit:</span>
                            <span class="ml-1 font-medium">{{ $planConfig['game_limit'] ?? 'Unlimited' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Check interval:</span>
                            <span class="ml-1 font-medium">Every {{ $planConfig['check_interval_hours'] }}h</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Email alerts:</span>
                            <span class="ml-1 font-medium">{{ $planConfig['email_notifications'] ? 'Yes' : 'No' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Telegram alerts:</span>
                            <span class="ml-1 font-medium">{{ $planConfig['telegram_notifications'] ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>

                @if($user->plan !== 'enterprise')
                    <div class="mt-6">
                        <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                            Upgrade Plan
                        </a>
                    </div>
                @endif

                <!-- Telegram Connection -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Telegram Notifications</h4>
                    @if($user->telegram_chat_id)
                        <p class="text-sm text-green-600">Connected (Chat ID: {{ $user->telegram_chat_id }})</p>
                    @else
                        <p class="text-sm text-gray-600 mb-2">
                            To connect Telegram, send <code class="bg-gray-100 px-1 py-0.5 rounded">/start</code> to our Telegram bot, then enter your Chat ID below.
                        </p>
                        <form action="{{ route('profile.update') }}" method="POST" class="flex gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="telegram_chat_id" placeholder="Your Telegram Chat ID"
                                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm">
                                Connect
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
