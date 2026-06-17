@extends('layouts.app')

@section('header_title', 'Create New Booking')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Date Availability Search Checker -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Check Availability</h3>
        <form action="{{ route('reservations.create') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Check-in Date</label>
                <input type="date" name="checkin" value="{{ $checkin }}" min="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Check-out Date</label>
                <input type="date" name="checkout" value="{{ $checkout }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white font-semibold rounded-xl text-sm hover:bg-slate-800 transition-colors">
                    Check Rooms
                </button>
            </div>
        </form>
    </div>

    <!-- Booking Details Form (shown only if checkin & checkout are defined) -->
    @if(request()->filled(['checkin', 'checkout']))
        <form action="{{ route('reservations.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="checkin_date" value="{{ $checkin }}">
            <input type="hidden" name="checkout_date" value="{{ $checkout }}">

            <!-- Guest Allocation -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Allocate Guest</h3>
                    <a href="{{ route('guests.create') }}" class="text-xs text-amber-500 hover:underline">
                        + Register New Guest
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Select Guest</label>
                        <select name="guest_id" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                            <option value="">-- Choose guest --</option>
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}">{{ $guest->name }} ({{ $guest->guest_code }} - {{ $guest->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Total Guests Count</label>
                        <input type="number" name="total_guest" required min="1" value="1"
                               class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <!-- Available Rooms Grid -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Select Available Room</h3>
                
                @if(count($rooms) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($rooms as $room)
                            <label class="border border-slate-200 dark:border-slate-800 rounded-xl p-4 flex items-start space-x-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <input type="radio" name="room_id" required value="{{ $room->id }}" class="mt-1 text-amber-500 focus:ring-amber-500">
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="block font-bold text-slate-900 dark:text-white text-base">Room {{ $room->room_number }}</span>
                                            <span class="block text-xs text-slate-400">{{ $room->roomType->name }}</span>
                                        </div>
                                        <span class="font-bold text-amber-500 text-sm">Rp {{ number_format($room->roomType->base_price) }}/night</span>
                                    </div>
                                    <div class="mt-2 text-xs text-slate-500 border-t border-slate-100 dark:border-slate-800 pt-2">
                                        <span class="block"><strong>Capacity:</strong> {{ $room->roomType->capacity }} Persons</span>
                                        <span class="block"><strong>Facilities:</strong> {{ $room->roomType->description }}</span>
                                        @if($room->roomType->breakfast_included)
                                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                Breakfast Included
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        No rooms are available for the selected dates. Try changing check-in/out dates.
                    </div>
                @endif
            </div>

            <!-- Extra Add-ons -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Extra Bed Add-ons</h3>
                <div class="flex items-center space-x-6">
                    <div class="flex-1">
                        <p class="text-sm text-slate-500">Need an extra single bed? Charge is <strong>Rp 150.000</strong> per night.</p>
                    </div>
                    <div class="w-32">
                        <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Quantity</label>
                        <select name="extra_bed_qty" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                            <option value="0">None</option>
                            <option value="1">1 Bed</option>
                            <option value="2">2 Beds</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Booking -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all">
                    Create Reservation
                </button>
            </div>
        </form>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center text-slate-500">
            Please pick Check-in and Check-out dates above and click "Check Rooms" to search room availability.
        </div>
    @endif
</div>
@endsection
