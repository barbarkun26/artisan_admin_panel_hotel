@extends('layouts.app')

@section('header_title', 'Front Office Reports')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('fo.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white font-semibold rounded-xl text-sm hover:bg-slate-800 transition-colors">
                    Filter Reports
                </button>
            </div>
        </form>
    </div>

    <!-- Revenue by Room Type & Operational Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Room Type Revenue Breakdown -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Revenue by Room Category</h3>
            <div class="space-y-4">
                @php $roomRevTotal = array_sum($roomTypeRevenues); @endphp
                @foreach($roomTypeRevenues as $typeName => $revenue)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium">{{ $typeName }}</span>
                            <span class="font-bold">Rp {{ number_format($revenue) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $roomRevTotal > 0 ? ($revenue / $roomRevTotal) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Occupancy Stats -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Occupancy Summary</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <span class="text-xs text-slate-400 block">Total Rooms Available</span>
                        <span class="text-xl font-bold">{{ $totalRooms }} Rooms</span>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <span class="text-xs text-slate-400 block">Active Occupied Rooms</span>
                        <span class="text-xl font-bold">{{ $occupiedRooms }} Rooms ({{ $occupancyRate }}%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Reservation Count Chart Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Daily Reservation Volumes</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Date</th>
                        <th class="py-3 font-semibold text-center">Reservations Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($reservationsReport as $row)
                        <tr>
                            <td class="py-3 font-medium">{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                            <td class="py-3 text-center font-bold text-indigo-500">{{ $row->count }} Booking(s)</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-4 text-center text-slate-400">No reservations placed in this date range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
