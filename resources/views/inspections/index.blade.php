@extends('layouts.app')

@section('header_title', 'Room Inspection History')

@section('content')
<div class="space-y-6">
    <!-- Inspections Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Inspection Date</th>
                        <th class="py-3 font-semibold">Room Number</th>
                        <th class="py-3 font-semibold">Booking ID</th>
                        <th class="py-3 font-semibold">Condition</th>
                        <th class="py-3 font-semibold">Inspector</th>
                        <th class="py-3 font-semibold text-right">Penalty Charge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($inspections as $ins)
                        <tr>
                            <td class="py-4">{{ $ins->inspection_date->format('M d, Y H:i') }}</td>
                            <td class="py-4 font-bold text-slate-900 dark:text-white">Room {{ $ins->room->room_number }}</td>
                            <td class="py-4 font-mono font-medium">{{ $ins->reservation->booking_number }}</td>
                            <td class="py-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium 
                                    @if($ins->room_condition === 'Clean & Neat') bg-emerald-500/10 text-emerald-500
                                    @else bg-red-500/10 text-red-500 @endif">
                                    {{ $ins->room_condition }}
                                </span>
                            </td>
                            <td class="py-4 text-xs">{{ $ins->inspector->name }}</td>
                            <td class="py-4 text-right font-bold text-slate-900 dark:text-white">
                                Rp {{ number_format($ins->additional_charge) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No inspections logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $inspections->links() }}
        </div>
    </div>
</div>
@endsection
