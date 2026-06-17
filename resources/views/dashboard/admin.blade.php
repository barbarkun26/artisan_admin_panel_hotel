@extends('layouts.app')

@section('header_title', 'Administrator Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Quick Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Card 1: Total Revenue -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Revenue</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">Rp {{ number_format($totalRevenue, 2) }}</p>
            <div class="mt-4 flex items-center text-xs text-emerald-500">
                <span>All sources integrated</span>
            </div>
        </div>

        <!-- Card 2: Occupancy Rate -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Occupancy Rate</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $occupancyRate }}%</p>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span>{{ $occupiedCount }} of {{ $roomsCount }} Rooms Occupied</span>
            </div>
        </div>

        <!-- Card 3: Room Revenue -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Room Revenue</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">Rp {{ number_format($roomRevenue, 2) }}</p>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span>Including Extra Beds</span>
            </div>
        </div>

        <!-- Card 4: F&B + Laundry -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">F&B + Laundry</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">Rp {{ number_format($fnbRevenue + $laundryRevenue, 2) }}</p>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span>FnB: {{ number_format($fnbRevenue) }} | Laundry: {{ number_format($laundryRevenue) }}</span>
            </div>
        </div>
    </div>

    <!-- Additional Charges & Activity log -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Breakdown Chart panel -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm lg:col-span-1">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Revenue Breakdown</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Rooms</span>
                        <span class="font-medium">Rp {{ number_format($roomRevenue) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($roomRevenue / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Food & Beverage</span>
                        <span class="font-medium">Rp {{ number_format($fnbRevenue) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($fnbRevenue / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Laundry</span>
                        <span class="font-medium">Rp {{ number_format($laundryRevenue) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-sky-500 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($laundryRevenue / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Additional Charges</span>
                        <span class="font-medium">Rp {{ number_format($additionalCharges) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($additionalCharges / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity logs -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Audit Activity Logs</h3>
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($activityLogs as $log)
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200 dark:bg-slate-800" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500 font-semibold text-xs">
                                            {{ substr($log->module, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-slate-800 dark:text-slate-200">
                                                {{ $log->description }}
                                            </p>
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-slate-400">
                                            <time datetime="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400 text-center py-4">No system activities recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
