@extends('layouts.app')

@section('header_title', 'Housekeeping Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Quick Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Card 1: Dirty Rooms -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dirty Rooms</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $dirtyRoomsCount }} Rooms</p>
            <div class="mt-4 flex items-center text-xs text-red-500">
                <span>Needs cleaning attention</span>
            </div>
        </div>

        <!-- Card 2: Vacant Rooms -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Vacant Rooms</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $vacantRoomsCount }} Rooms</p>
            <div class="mt-4 flex items-center text-xs text-emerald-500">
                <span>Ready or cleaning in progress</span>
            </div>
        </div>

        <!-- Card 3: Occupied Rooms -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Occupied Rooms</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $occupiedRoomsCount }} Rooms</p>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span>Guests currently staying</span>
            </div>
        </div>

        <!-- Card 4: Laundry Queue -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Laundry</h3>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $pendingLaundryCount }} Requests</p>
            <div class="mt-4 flex items-center text-xs text-amber-500">
                <span><a href="{{ route('laundry.index') }}" class="underline hover:text-amber-600">Open Laundry List</a></span>
            </div>
        </div>
    </div>

    <!-- Rooms List & Cleaning Panel -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Rooms Status & Cleaning Logs</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Room No</th>
                        <th class="py-3 font-semibold">Type</th>
                        <th class="py-3 font-semibold">Floor</th>
                        <th class="py-3 font-semibold">Current Status</th>
                        <th class="py-3 font-semibold text-center">Checkout Inspection</th>
                        <th class="py-3 font-semibold">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @foreach($rooms as $room)
                        <tr>
                            <td class="py-4 font-bold text-slate-900 dark:text-white">{{ $room->room_number }}</td>
                            <td class="py-4 text-xs">{{ $room->roomType->name }}</td>
                            <td class="py-4">{{ $room->floor }}</td>
                            <td class="py-4">
                                <span class="px-2 py-0.5 rounded text-xs font-bold 
                                    @if(in_array($room->status->code, ['VD', 'OD'])) bg-red-500/10 text-red-500
                                    @elseif(in_array($room->status->code, ['VC', 'VCI'])) bg-emerald-500/10 text-emerald-500
                                    @elseif($room->status->code === 'O') bg-blue-500/10 text-blue-500
                                    @else bg-amber-500/10 text-amber-500 @endif">
                                    {{ $room->status->code }} - {{ $room->status->name }}
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                @php
                                    $activeRes = $room->reservationRooms()
                                        ->whereHas('reservation', function($q) {
                                            $q->where('status', 'checkin');
                                        })
                                        ->first()?->reservation;
                                @endphp
                                @if($activeRes)
                                    <a href="{{ route('inspections.create', ['reservation' => $activeRes->id, 'room' => $room->id]) }}" 
                                       class="inline-flex px-3 py-1 bg-amber-500 hover:bg-amber-600 text-slate-950 font-medium rounded-lg text-xs transition-colors">
                                        Inspect Room
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">N/A (Vacant)</span>
                                @endif
                            </td>
                            <td class="py-4">
                                <form action="{{ route('rooms.status', $room->id) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    <select name="status_id" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs focus:outline-none focus:border-amber-500">
                                        @foreach($allStatuses as $st)
                                            <option value="{{ $st->id }}" {{ $room->current_status_id == $st->id ? 'selected' : '' }}>
                                                {{ $st->code }} ({{ $st->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white rounded-lg text-xs font-semibold hover:bg-slate-800">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
