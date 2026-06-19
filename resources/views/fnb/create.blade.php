@extends('layouts.app')

@section('header_title', 'Place F&B Order')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <form action="{{ route('fnb.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Room context selection -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Select Occupied Room</label>
            <select name="reservation_id" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                <option value="">-- Choose room --</option>
                @foreach($reservations as $res)
                    @foreach($res->reservationRooms as $resRoom)
                        <option value="{{ $res->id }}">Room {{ $resRoom->room->room_number }} (Guest: {{ $res->guest->name }} - Booking: {{ $res->booking_number }})</option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <!-- Payment Selection -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Payment Method</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Payment Type</label>
                    <select name="payment_type" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                        <option value="billed_to_room">Bill to Room (Pay at Checkout)</option>
                        <option value="on_the_spot">Pay On The Spot (Direct Payment)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Payment Method (if On The Spot)</label>
                    <select name="payment_method" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                        <option value="">-- Select Method --</option>
                        <option value="Cash">Cash</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Credit Card">Credit Card</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Menu Catalog -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6">Menu Catalog Selection</h3>

            <div class="space-y-8">
                @foreach($categories as $category)
                    <div>
                        <h4 class="font-bold text-base text-amber-500 border-b border-slate-100 dark:border-slate-800 pb-2 mb-4">
                            {{ $category->name }}
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php $idx = 0; @endphp
                            @foreach($menus->where('category_id', $category->id) as $menu)
                                <div class="border border-slate-100 dark:border-slate-800/80 rounded-xl p-4 bg-slate-50 dark:bg-slate-800/40 flex justify-between items-center">
                                    <div class="flex-1 pr-4">
                                        <span class="block font-semibold text-slate-900 dark:text-white">{{ $menu->name }}</span>
                                        <span class="block text-xs text-slate-400 mt-1">{{ $menu->description ?? 'No description.' }}</span>
                                        <span class="block text-xs font-bold text-amber-500 mt-1">Rp {{ number_format($menu->price) }}</span>
                                    </div>
                                    <div class="w-20 shrink-0">
                                        <label class="block text-[10px] text-slate-400 mb-0.5 text-center">QTY</label>
                                        <!-- Keep input names array indices continuous across menus -->
                                        <input type="hidden" name="items[{{ $menu->id }}][menu_id]" value="{{ $menu->id }}">
                                        <input type="number" name="items[{{ $menu->id }}][qty]" min="0" value="0"
                                               class="w-full px-2 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-center focus:outline-none">
                                    </div>
                                </div>
                            @endphp
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('fnb.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-sm shadow-sm">
                Place Room Service Order
            </button>
        </div>
    </form>
</div>
@endsection
