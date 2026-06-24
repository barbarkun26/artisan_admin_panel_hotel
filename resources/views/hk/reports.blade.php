@extends('layouts.app')

@section('header_title', 'Housekeeping Reports')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('hk.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
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

    <!-- Operations Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Service Summaries -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Service Summaries</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <span class="text-xs text-slate-400 block">Room Inspections Completed</span>
                    <span class="text-xl font-bold">{{ $inspectionsCount }} Inspections</span>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <span class="text-xs text-slate-400 block">Laundry Jobs Handled</span>
                    <span class="text-xl font-bold">
                        {{ $laundryReport->sum('count') }} Jobs
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Room Status Summary -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Current Room Status (Out of {{ $totalRooms }})</h3>
            <div class="grid grid-cols-2 gap-4">
                @foreach($roomStatuses as $status)
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <span class="text-xs text-slate-400 block font-semibold" style="color: {{ $status->color_code }}">{{ $status->name }} ({{ $status->code }})</span>
                        <span class="text-xl font-bold">{{ $status->rooms_count }} Rooms</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
