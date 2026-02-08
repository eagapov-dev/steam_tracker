<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Users</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Paid Users</div>
                    <div class="text-2xl font-bold text-green-600">{{ number_format($stats['paid_users']) }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Tracked Games</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_games']) }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Notifications Today</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['notifications_today']) }}</div>
                </div>
            </div>

            <!-- Users by Plan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Users by Plan</h3>
                    <div class="space-y-3">
                        @foreach($stats['users_by_plan'] as $plan => $count)
                            <div class="flex items-center justify-between">
                                <span class="capitalize text-gray-700">{{ $plan }}</span>
                                <span class="font-semibold">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-900">Recent Users</h3>
                        <a href="{{ route('admin.users') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
                    </div>
                    <div class="space-y-3">
                        @foreach($recentUsers as $user)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                    <span class="text-gray-500 ml-2">{{ $user->email }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $user->plan }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-8 flex gap-4">
                <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-white shadow-sm rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm">
                    Manage Users
                </a>
                <a href="{{ route('admin.games') }}" class="px-4 py-2 bg-white shadow-sm rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm">
                    Manage Games
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
