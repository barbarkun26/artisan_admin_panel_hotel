@extends('layouts.app')

@section('header_title', 'Food & Beverage Reports')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('fnb.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white font-bold rounded-xl text-sm hover:bg-slate-800 transition-colors">
                    Filter Reports
                </button>
            </div>
        </form>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Metric 1: Total Orders -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total F&B Orders</h3>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-2">{{ $totalOrders }}</p>
                <span class="text-xs text-slate-400 block mt-1">Orders placed in selected range</span>
            </div>
            <div class="p-3 bg-amber-500/10 text-amber-500 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
        </div>

        <!-- Metric 2: Total Revenue -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider font-bold text-amber-500">Delivered Revenue</h3>
                <p class="text-3xl font-extrabold text-amber-500 mt-2">Rp {{ number_format($revenue, 2) }}</p>
                <span class="text-xs text-slate-400 block mt-1">Excludes pending or in-process orders</span>
            </div>
            <div class="p-3 bg-emerald-500/10 text-emerald-500 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Metric 3: Average Order Value -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Average Order Value</h3>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-2">Rp {{ number_format($avgOrderValue, 2) }}</p>
                <span class="text-xs text-slate-400 block mt-1">Average spent per room service order</span>
            </div>
            <div class="p-3 bg-sky-500/10 text-sky-500 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Status Breakdown & Popular Menus -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Status Counts -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Order Status Distribution</h3>
            <div class="space-y-4">
                @php
                    $statuses = [
                        'pending' => ['name' => 'Pending', 'color' => 'bg-amber-500'],
                        'process' => ['name' => 'Cooking (Process)', 'color' => 'bg-sky-500'],
                        'processing' => ['name' => 'Processing (Legacy)', 'color' => 'bg-slate-400'],
                        'waiting' => ['name' => 'Waiting Delivery', 'color' => 'bg-purple-500'],
                        'delivered' => ['name' => 'Delivered', 'color' => 'bg-emerald-500'],
                        'completed' => ['name' => 'Delivered (Legacy)', 'color' => 'bg-slate-500'],
                    ];
                @endphp
                @foreach($statuses as $key => $meta)
                    @php
                        $count = isset($statusCounts[$key]) ? $statusCounts[$key]->count : 0;
                        $totalAmt = isset($statusCounts[$key]) ? $statusCounts[$key]->total : 0;
                        $percentage = $totalOrders > 0 ? ($count / $totalOrders) * 100 : 0;
                    @endphp
                    @if($count > 0 || in_array($key, ['pending', 'process', 'waiting', 'delivered']))
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-600 dark:text-slate-300">{{ $meta['name'] }} ({{ $count }} order{{ $count !== 1 ? 's' : '' }})</span>
                                <span class="text-slate-900 dark:text-white">Rp {{ number_format($totalAmt) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                                <div class="{{ $meta['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Popular Menu Items -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm lg:col-span-2 space-y-4">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Top Selling Menu Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                            <th class="py-2.5 font-semibold">Menu Name</th>
                            <th class="py-2.5 font-semibold">Category</th>
                            <th class="py-2.5 font-semibold text-center">Portions Sold</th>
                            <th class="py-2.5 font-semibold text-right">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($popularMenus as $pop)
                            <tr>
                                <td class="py-3 font-semibold text-slate-900 dark:text-white">{{ $pop->menu_name }}</td>
                                <td class="py-3 text-slate-500">{{ $pop->category_name }}</td>
                                <td class="py-3 text-center font-bold text-slate-800 dark:text-slate-200">{{ $pop->total_qty }}x</td>
                                <td class="py-3 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($pop->total_revenue) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">No items sold during this date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sales Order Log -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">F&B Detailed Orders Log</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Order ID</th>
                        <th class="py-3 font-semibold">Room No</th>
                        <th class="py-3 font-semibold">Guest</th>
                        <th class="py-3 font-semibold">Date</th>
                        <th class="py-3 font-semibold">Ordered Items (Qty)</th>
                        <th class="py-3 font-semibold text-right">Total Amount</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="py-4 font-mono font-medium text-slate-900 dark:text-white">FNB-{{ sprintf('%05d', $ord->id) }}</td>
                            <td class="py-4 font-bold text-slate-800 dark:text-slate-200">Room {{ $ord->room->room_number }}</td>
                            <td class="py-4 text-slate-600 dark:text-slate-300">{{ $ord->reservation->guest->name }}</td>
                            <td class="py-4 text-xs text-slate-400">{{ $ord->order_date->format('M d, Y H:i') }}</td>
                            <td class="py-4 text-xs">
                                <ul class="list-disc pl-4 space-y-0.5 text-slate-500">
                                    @foreach($ord->details as $det)
                                        <li>{{ $det->menu->name }} ({{ $det->qty }}x)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($ord->total_amount) }}</td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                                    @if($ord->status === 'pending') bg-amber-500/10 text-amber-500
                                    @elseif(in_array($ord->status, ['process', 'processing'])) bg-sky-500/10 text-sky-500
                                    @elseif($ord->status === 'waiting') bg-purple-500/10 text-purple-500
                                    @else bg-emerald-500/10 text-emerald-500 @endif">
                                    @if($ord->status === 'process' || $ord->status === 'processing')
                                        Processing
                                    @elseif($ord->status === 'delivered' || $ord->status === 'completed')
                                        Delivered
                                    @else
                                        {{ ucfirst($ord->status) }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No Food & Beverage orders found in this range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
