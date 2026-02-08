<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pricing Plans</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Choose Your Plan</h2>
                <p class="mt-4 text-lg text-gray-600">Start free, upgrade as you grow your watchlist.</p>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                @foreach($plans as $key => $plan)
                    <div class="bg-white rounded-xl shadow-sm border {{ $key === 'pro' ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-gray-200' }} p-6 flex flex-col">
                        @if($key === 'pro')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 self-start mb-4">Most Popular</span>
                        @endif

                        <h3 class="text-xl font-bold text-gray-900">{{ $plan['name'] }}</h3>

                        <div class="mt-4">
                            <span class="text-4xl font-bold text-gray-900">${{ $plan['price'] }}</span>
                            @if($plan['price'] > 0)
                                <span class="text-gray-500">/mo</span>
                            @endif
                        </div>

                        <ul class="mt-6 space-y-3 flex-grow">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                {{ $plan['game_limit'] ? $plan['game_limit'] . ' games' : 'Unlimited games' }}
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                Check every {{ $plan['check_interval_hours'] }}h
                            </li>
                            <li class="flex items-center text-sm {{ $plan['email_notifications'] ? 'text-gray-600' : 'text-gray-400' }}">
                                @if($plan['email_notifications'])
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                @endif
                                Email notifications
                            </li>
                            <li class="flex items-center text-sm {{ $plan['telegram_notifications'] ? 'text-gray-600' : 'text-gray-400' }}">
                                @if($plan['telegram_notifications'])
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                @endif
                                Telegram notifications
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                {{ $plan['price_history_days'] ? $plan['price_history_days'] . ' days history' : 'Full history' }}
                            </li>
                            @if($plan['api_access'])
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                API access + Webhooks
                            </li>
                            @endif
                        </ul>

                        <div class="mt-6">
                            @if($key === 'free')
                                <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                                    Get Started
                                </a>
                            @else
                                @auth
                                    <a href="{{ route('subscription.checkout', $key) }}" class="block w-full text-center px-4 py-2 {{ $key === 'pro' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-900 text-white hover:bg-gray-800' }} rounded-lg font-medium">
                                        Subscribe
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 {{ $key === 'pro' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-900 text-white hover:bg-gray-800' }} rounded-lg font-medium">
                                        Get Started
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
