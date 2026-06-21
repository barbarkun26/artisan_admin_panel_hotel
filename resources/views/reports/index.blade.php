@extends('layouts.app')

@section('header_title', 'Financial & Operational Reports')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('admin.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white font-semibold rounded-xl text-sm hover:bg-slate-800 transition-colors">
                    Filter Reports
                </button>
            </div>
        </form>
    </div>

    <!-- Revenue Summary Panel -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6">Financial Summary (Revenue)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100 dark:divide-slate-800">
            <div class="pt-4 md:pt-0 md:pl-0 flex flex-col justify-center">
                <span class="text-xs text-slate-400 uppercase tracking-wider">Room Revenue</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($roomRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center">
                <span class="text-xs text-slate-400 uppercase tracking-wider">F&B Revenue</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($fnbRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center">
                <span class="text-xs text-slate-400 uppercase tracking-wider">Laundry Revenue</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($laundryRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center">
                <span class="text-xs text-slate-400 uppercase tracking-wider">Additional Charges</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($additionalRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center">
                <span class="text-xs text-slate-400 uppercase tracking-wider text-amber-500 font-bold">Total Net Revenue</span>
                <span class="text-2xl font-bold text-amber-500 mt-1">Rp {{ number_format($totalRevenue, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Revenue by Room Type & Operational Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Room Type Revenue Breakdown -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Revenue by Room Category</h3>
            <div class="space-y-4">
                @foreach($roomTypeRevenues as $typeName => $revenue)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium">{{ $typeName }}</span>
                            <span class="font-bold">Rp {{ number_format($revenue) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $roomRevTotal > 0 ? ($revenue / $roomRevTotal) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium">Extra Bed Charges</span>
                        <span class="font-bold">Rp {{ number_format($extraBedRevenue) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                        <div class="bg-amber-400 h-2 rounded-full" style="width: {{ $roomRevTotal > 0 ? ($extraBedRevenue / $roomRevTotal) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Occupancy & Operations Stats -->
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

            <div class="border-t border-slate-100 dark:border-slate-800 pt-4">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Service Summaries</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <span class="text-xs text-slate-400 block">F&B Orders Placed</span>
                        <span class="text-base font-bold">
                            {{ $fnbReport->sum('count') }} Orders (Rp {{ number_format($fnbRevTotal) }})
                        </span>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <span class="text-xs text-slate-400 block">Laundry Jobs Handled</span>
                        <span class="text-base font-bold">
                            {{ $laundryReport->sum('count') }} Jobs (Rp {{ number_format($laundryRevTotal) }})
                        </span>
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
                            <td class="py-3 text-center font-bold text-amber-500">{{ $row->count }} Booking(s)</td>
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
