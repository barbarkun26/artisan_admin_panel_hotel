@extends('layouts.app')

@section('header_title', 'Food & Beverage Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Quick Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Orders Today -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Orders Today</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $ordersToday }} Orders</p>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span>All F&B room orders placed today</span>
            </div>
        </div>

        <!-- Card 2: F&B Revenue Today -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">F&B Revenue Today</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">Rp {{ number_format($revenueToday, 2) }}</p>
            <div class="mt-4 flex items-center text-xs text-emerald-500">
                <span>From completed order settlements</span>
            </div>
        </div>
    </div>

    <!-- Active Orders Queue -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Pending & Processing Orders</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($pendingOrders as $order)
                <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-5 bg-slate-50 dark:bg-slate-800/40 flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-lg text-slate-900 dark:text-white">Room {{ $order->room->room_number }}</h4>
                                <p class="text-xs text-slate-400">Guest: {{ $order->reservation->guest->name }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                                @if($order->status === 'pending') bg-amber-500/10 text-amber-500
                                @elseif($order->status === 'processing') bg-sky-500/10 text-sky-500
                                @else bg-emerald-500/10 text-emerald-500 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Items List -->
                        <div class="border-t border-b border-slate-200/50 dark:border-slate-700/50 py-3 mb-4">
                            <p class="text-xs text-slate-400 uppercase tracking-wider mb-2">Order Items</p>
                            <ul class="space-y-1.5 text-sm">
                                @foreach($order->details as $detail)
                                    <li class="flex justify-between">
                                        <span>{{ $detail->menu->name }} <span class="text-slate-400">x{{ $detail->qty }}</span></span>
                                        <span class="font-medium">Rp {{ number_format($detail->subtotal) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Total & Date -->
                        <div class="flex justify-between text-sm mb-4">
                            <span class="text-slate-400">Ordered: {{ $order->order_date->diffForHumans() }}</span>
                            <span class="font-bold text-slate-900 dark:text-white">Total: Rp {{ number_format($order->total_amount) }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <form action="{{ route('fnb.status', $order->id) }}" method="POST" class="flex gap-2 border-t border-slate-200/50 dark:border-slate-700/50 pt-4 mt-2">
                        @csrf
                        @if($order->status === 'pending')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="w-full py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                Start Cooking (Process)
                            </button>
                        @elseif($order->status === 'processing')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="w-full py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                Mark as Delivered (Complete)
                            </button>
                        @endif
                    </form>
                </div>
            @empty
                <div class="md:col-span-2 text-center py-12 text-slate-400">
                    No pending room service orders at the moment.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
