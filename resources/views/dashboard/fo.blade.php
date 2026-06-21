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

    <!-- All Rooms Status Cards -->
    <div class="mt-8">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Room Status Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @foreach($allRooms as $room)
                @php
                    $isOccupied = in_array($room->status->code, ['O', 'OC', 'OD', 'Comp', 'HU']);
                    $activeReservation = $isOccupied && $room->reservationRooms->isNotEmpty() 
                        ? $room->reservationRooms->first()->reservation 
                        : null;
                @endphp
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col relative overflow-hidden">
                    <!-- Status Indicator Bar -->
                    <div class="absolute top-0 left-0 right-0 h-1 
                        @if(in_array($room->status->code, ['V', 'VC'])) bg-emerald-500
                        @elseif(in_array($room->status->code, ['VD', 'OD', 'OOO', 'OOS'])) bg-red-500
                        @elseif($isOccupied) bg-amber-500
                        @else bg-slate-500 @endif"></div>

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 dark:text-white">{{ $room->room_number }}</h4>
                            <p class="text-xs text-slate-500">{{ $room->roomType->name }} (Floor {{ $room->floor }})</p>
                        </div>
                        <span class="px-2.5 py-1 rounded text-xs font-bold
                            @if(in_array($room->status->code, ['V', 'VC'])) bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                            @elseif(in_array($room->status->code, ['VD', 'OD', 'OOO', 'OOS'])) bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400
                            @elseif($isOccupied) bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400
                            @else bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 @endif">
                            {{ $room->status->name }} ({{ $room->status->code }})
                        </span>
                    </div>

                    <div class="flex-1">
                        <!-- Facilities -->
                        <div class="mb-4">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold mb-1.5">Facilities</p>
                            <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span> Capacity: {{ $room->roomType->capacity }} Person(s)</li>
                                <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full {{ $room->roomType->breakfast_included ? 'bg-emerald-400' : 'bg-rose-400' }} mr-2"></span> {{ $room->roomType->breakfast_included ? 'Breakfast Included' : 'No Breakfast' }}</li>
                                <li class="text-slate-400 italic line-clamp-1 mt-1" title="{{ $room->roomType->description }}">{{ Str::limit($room->roomType->description, 50) }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Add Breakfast Button -->
                    @if($activeReservation && !$room->roomType->breakfast_included)
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <a href="{{ route('fnb.create', ['reservation_id' => $activeReservation->id, 'breakfast' => 1]) }}" class="block w-full py-2 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-500/30 text-center text-xs font-bold rounded-lg transition-colors">
                                + Add Breakfast Package
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
