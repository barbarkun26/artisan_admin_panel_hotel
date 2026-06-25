@extends('layouts.app')

@section('header_title', 'Financial & Operational Reports')

@section('content')
<div class="space-y-6">
    <!-- Header Title with Print Button (Screen Only) -->
    <div class="flex justify-between items-center no-print">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Financial & Operational Reports</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Audit reports for hotel operations and finances.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Print Report</span>
        </button>
    </div>

    <!-- Print-Only Audit Header -->
    <div class="print-header hidden">
        <div style="border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 25px;">
            <h1 style="font-size: 24px; font-weight: bold; margin: 0; color: #000; text-transform: uppercase; letter-spacing: 1px;">Artisan Hotel HMS</h1>
            <p style="font-size: 14px; margin: 5px 0 0 0; color: #475569; font-weight: 500;">Financial & Operational Audit Report</p>
            <div style="margin-top: 15px; display: grid; grid-template-cols: 2fr 1fr; gap: 10px; font-size: 12px; color: #64748b;">
                <div><strong>Audit Period:</strong> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</div>
                <div style="text-align: right;"><strong>Printed By:</strong> {{ Auth::user()->name }}</div>
                <div><strong>Generated On:</strong> {{ now()->format('M d, Y H:i:s') }}</div>
                <div style="text-align: right;"><strong>Status:</strong> Offical Audit Copy</div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter (Screen Only) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm no-print">
        <form action="{{ route('admin.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-indigo-500 text-slate-800 dark:text-slate-100">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 dark:bg-slate-750 text-white font-semibold rounded-xl text-sm hover:bg-slate-800 transition-colors">
                    Filter Reports
                </button>
            </div>
        </form>
    </div>

    <!-- Revenue Summary Panel -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm print-card">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6 print-sub-title">Financial Summary (Revenue)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100 dark:divide-slate-800 print-grid">
            <div class="pt-4 md:pt-0 md:pl-0 flex flex-col justify-center print-col">
                <span class="text-xs text-slate-400 uppercase tracking-wider">Room Revenue</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($roomRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center print-col">
                <span class="text-xs text-slate-400 uppercase tracking-wider">F&B Revenue</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($fnbRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center print-col">
                <span class="text-xs text-slate-400 uppercase tracking-wider">Laundry Revenue</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($laundryRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center print-col">
                <span class="text-xs text-slate-400 uppercase tracking-wider">Additional Charges</span>
                <span class="text-xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($additionalRevTotal, 2) }}</span>
            </div>
            <div class="pt-4 md:pt-0 md:pl-6 flex flex-col justify-center print-col">
                <span class="text-xs text-slate-400 uppercase tracking-wider text-indigo-500 font-bold print-text-dark">Total Net Revenue</span>
                <span class="text-2xl font-bold text-indigo-500 mt-1 print-text-dark">Rp {{ number_format($totalRevenue, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Revenue by Room Type & Operational Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print-grid">
        <!-- Room Type Revenue Breakdown -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm print-card">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 print-sub-title">Revenue by Room Category</h3>
            <div class="space-y-4">
                @foreach($roomTypeRevenues as $typeName => $revenue)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-slate-700 dark:text-slate-300 print-text-dark">{{ $typeName }}</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100 print-text-dark">Rp {{ number_format($revenue) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 no-print">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $roomRevTotal > 0 ? ($revenue / $roomRevTotal) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-700 dark:text-slate-300 print-text-dark">Extra Bed Charges</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 print-text-dark">Rp {{ number_format($extraBedRevenue) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 no-print">
                        <div class="bg-indigo-400 h-2 rounded-full" style="width: {{ $roomRevTotal > 0 ? ($extraBedRevenue / $roomRevTotal) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Occupancy & Operations Stats -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6 print-card">
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 print-sub-title">Occupancy Summary</h3>
                <div class="grid grid-cols-2 gap-4 print-grid-2">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl print-col-bordered">
                        <span class="text-xs text-slate-400 block">Total Rooms Available</span>
                        <span class="text-xl font-bold text-slate-800 dark:text-slate-200 print-text-dark">{{ $totalRooms }} Rooms</span>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl print-col-bordered">
                        <span class="text-xs text-slate-400 block">Active Occupied Rooms</span>
                        <span class="text-xl font-bold text-slate-800 dark:text-slate-200 print-text-dark">{{ $occupiedRooms }} Rooms ({{ $occupancyRate }}%)</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800 pt-4 print-divider">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 print-sub-title">Service Summaries</h3>
                <div class="grid grid-cols-2 gap-4 print-grid-2">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl print-col-bordered">
                        <span class="text-xs text-slate-400 block">F&B Orders Placed</span>
                        <span class="text-base font-bold text-slate-800 dark:text-slate-200 print-text-dark">
                            {{ $fnbReport->sum('count') }} Orders (Rp {{ number_format($fnbRevTotal) }})
                        </span>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl print-col-bordered">
                        <span class="text-xs text-slate-400 block">Laundry Jobs Handled</span>
                        <span class="text-base font-bold text-slate-800 dark:text-slate-200 print-text-dark">
                            {{ $laundryReport->sum('count') }} Jobs (Rp {{ number_format($laundryRevTotal) }})
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Reservation Count Chart Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm print-card">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 print-sub-title">Daily Reservation Volumes</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400 print-tr-header">
                        <th class="py-3 font-semibold">Date</th>
                        <th class="py-3 font-semibold text-center">Reservations Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($reservationsReport as $row)
                        <tr>
                            <td class="py-3 font-medium text-slate-700 dark:text-slate-300 print-text-dark">{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                            <td class="py-3 text-center font-bold text-indigo-500 print-text-dark">{{ $row->count }} Booking(s)</td>
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

@section('scripts')
<style>
    @media print {
        /* Hide layout and navigation */
        aside, header, .no-print, form, #theme-toggle {
            display: none !important;
        }
        
        /* Make content page-width */
        body, main, div.space-y-6 {
            background: white !important;
            color: #000 !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }

        .min-h-full {
            display: block !important;
        }

        /* Show the custom print header */
        .print-header {
            display: block !important;
        }

        /* Border adjustments for printed page structure */
        .print-card {
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            margin-bottom: 20px !important;
            border-radius: 12px !important;
            padding: 15px !important;
            page-break-inside: avoid;
        }

        .print-grid {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            gap: 10px !important;
        }

        .print-grid-2 {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 15px !important;
        }

        .print-col {
            border: none !important;
            padding: 0 10px !important;
            margin: 0 !important;
            flex: 1 !important;
        }

        .print-col-bordered {
            border: 1px solid #cbd5e1 !important;
            padding: 10px !important;
            border-radius: 8px !important;
            background: #f8fafc !important;
        }

        .print-divider {
            border-top: 1px solid #cbd5e1 !important;
            margin-top: 15px !important;
            padding-top: 15px !important;
        }

        .print-tr-header {
            border-bottom: 2px solid #94a3b8 !important;
        }

        /* Override text colors to ensure perfect contrast in print */
        .print-text-dark {
            color: #0f172a !important;
        }

        .print-sub-title {
            color: #475569 !important;
            font-size: 11px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding-bottom: 5px !important;
            margin-bottom: 15px !important;
        }

        /* Prevent large page overflow issues */
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
@endsection
