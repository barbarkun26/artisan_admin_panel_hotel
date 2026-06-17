@extends('layouts.app')

@section('header_title', 'Front Office Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('reservations.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold rounded-xl transition-all shadow-sm">
            + New Booking
        </a>
        <a href="{{ route('guests.create') }}" class="px-4 py-2.5 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 font-semibold rounded-xl transition-all">
            + Add Guest Profile
        </a>
        <a href="{{ route('laundry.create') }}" class="px-4 py-2.5 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 font-semibold rounded-xl transition-all">
            New Laundry Request
        </a>
        <a href="{{ route('fnb.create') }}" class="px-4 py-2.5 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 font-semibold rounded-xl transition-all">
            New F&B Order
        </a>
    </div>

    <!-- Quick Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Occupancy -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Hotel Occupancy</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $occupancyRate }}%</p>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span>{{ $occupiedCount }} of {{ $roomsCount }} Rooms Occupied</span>
            </div>
        </div>

        <!-- Card 2: Arrivals Today -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Arrivals Today</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $arrivalsCount }} Guests</p>
            <div class="mt-4 flex items-center text-xs text-amber-500">
                <span>Awaiting Check-in</span>
            </div>
        </div>

        <!-- Card 3: Departures Today -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Departures Today</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $departuresCount }} Rooms</p>
            <div class="mt-4 flex items-center text-xs text-sky-500">
                <span>Due Out / Settlement</span>
            </div>
        </div>
    </div>

    <!-- Room Status Matrix & Recent Bookings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Room Status Summary -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm lg:col-span-1">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Room Status Counts</h3>
            <div class="grid grid-cols-2 gap-4">
                @foreach($roomStatusCounts as $status)
                    @if($status->rooms_count > 0)
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <div>
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 mr-2">
                                    {{ $status->code }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $status->name }}</span>
                            </div>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $status->rooms_count }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Recent Bookings</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                            <th class="py-3 font-semibold">Booking ID</th>
                            <th class="py-3 font-semibold">Guest</th>
                            <th class="py-3 font-semibold">Dates</th>
                            <th class="py-3 font-semibold">Status</th>
                            <th class="py-3 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                        @forelse($recentReservations as $res)
                            <tr>
                                <td class="py-4 font-mono font-medium">{{ $res->booking_number }}</td>
                                <td class="py-4">{{ $res->guest->name }}</td>
                                <td class="py-4 text-xs">
                                    {{ $res->checkin_date->format('M d, Y') }} - {{ $res->checkout_date->format('M d, Y') }}
                                </td>
                                <td class="py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium 
                                        @if($res->status === 'checkin') bg-emerald-500/10 text-emerald-500
                                        @elseif($res->status === 'checkout') bg-slate-500/10 text-slate-400
                                        @elseif($res->status === 'cancelled') bg-red-500/10 text-red-500
                                        @else bg-amber-500/10 text-amber-500 @endif">
                                        {{ ucfirst($res->status) }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <a href="{{ route('reservations.show', $res->id) }}" class="text-amber-500 hover:text-amber-600 font-medium">
                                        Open Folio
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400">No reservations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
