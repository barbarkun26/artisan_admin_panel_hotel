@extends('layouts.app')

@section('header_title', 'Reservations List')

@section('content')
<div class="space-y-6">
    <!-- Filters and Search -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('reservations.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1 flex gap-4">
                <input type="text" name="search" placeholder="Search booking number, guest name, phone..." value="{{ request('search') }}"
                       class="w-full max-w-md px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                
                <select name="status" class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="checkin" {{ request('status') === 'checkin' ? 'selected' : '' }}>Checked In</option>
                    <option value="checkout" {{ request('status') === 'checkout' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white rounded-xl text-sm font-semibold hover:bg-slate-800">
                    Apply Filter
                </button>
                <a href="{{ route('reservations.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-200 dark:hover:bg-slate-700">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Booking ID</th>
                        <th class="py-3 font-semibold">Guest</th>
                        <th class="py-3 font-semibold">Room No</th>
                        <th class="py-3 font-semibold">Stay Dates</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold">Created At</th>
                        <th class="py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($reservations as $res)
                        <tr>
                            <td class="py-4 font-mono font-medium text-slate-900 dark:text-white">{{ $res->booking_number }}</td>
                            <td class="py-4">
                                <span class="block font-medium">{{ $res->guest->name }}</span>
                                <span class="text-xs text-slate-400">{{ $res->guest->phone }}</span>
                            </td>
                            <td class="py-4">
                                @foreach($res->reservationRooms as $resRoom)
                                    <span class="block font-bold">Room {{ $resRoom->room->room_number }}</span>
                                    <span class="text-xs text-slate-400">{{ $resRoom->room->roomType->name }}</span>
                                @endforeach
                            </td>
                            <td class="py-4">
                                <span class="block font-medium text-xs">{{ $res->checkin_date->format('M d, Y') }} - {{ $res->checkout_date->format('M d, Y') }}</span>
                                <span class="text-xs text-slate-400">{{ $res->nights_count }} Nights</span>
                            </td>
                            <td class="py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold 
                                    @if($res->status === 'checkin') bg-emerald-500/10 text-emerald-500
                                    @elseif($res->status === 'checkout') bg-slate-500/10 text-slate-400
                                    @elseif($res->status === 'cancelled') bg-red-500/10 text-red-500
                                    @else bg-indigo-500/10 text-indigo-500 @endif">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                            <td class="py-4 text-xs text-slate-400">{{ $res->created_at->format('M d, Y H:i') }}</td>
                            <td class="py-4 text-right">
                                <a href="{{ route('reservations.show', $res->id) }}" class="px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-slate-950 text-xs font-semibold rounded-lg transition-colors">
                                    Open Folio
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No reservations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $reservations->links() }}
        </div>
    </div>
</div>
@endsection
