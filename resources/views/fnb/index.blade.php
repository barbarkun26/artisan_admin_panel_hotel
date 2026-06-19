@extends('layouts.app')

@section('header_title', 'Food & Beverage Orders')

@section('content')
<div class="space-y-6">
    <!-- Orders Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Order ID</th>
                        <th class="py-3 font-semibold">Room No</th>
                        <th class="py-3 font-semibold">Guest</th>
                        <th class="py-3 font-semibold">Ordered Date</th>
                        <th class="py-3 font-semibold">Menu Details (Qty x Price)</th>
                        <th class="py-3 font-semibold text-right">Total Amount</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                        <th class="py-3 font-semibold text-right">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="py-4 font-mono font-medium text-slate-900 dark:text-white">FNB-{{ sprintf('%05d', $ord->id) }}</td>
                            <td class="py-4 font-bold">Room {{ $ord->room->room_number }}</td>
                            <td class="py-4">{{ $ord->reservation->guest->name }}</td>
                            <td class="py-4 text-xs">{{ $ord->order_date->format('M d, Y H:i') }}</td>
                            <td class="py-4 text-xs">
                                <ul class="list-disc pl-4 space-y-0.5">
                                    @foreach($ord->details as $det)
                                        <li>{{ $det->menu->name }} ({{ $det->qty }}x Rp {{ number_format($det->price) }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($ord->total_amount) }}</td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                                    @if($ord->status === 'pending') bg-amber-500/10 text-amber-500
                                    @elseif(in_array($ord->status, ['process', 'processing'])) bg-sky-500/10 text-sky-500
                                    @elseif($ord->status === 'waiting') bg-purple-500/10 text-purple-500
                                    @else bg-emerald-500/10 text-emerald-500 @endif">
                                    @if($ord->status === 'process' || $ord->status === 'processing')
                                        Processing
                                    @elseif($ord->status === 'delivered' || $ord->status === 'completed')
                                        Delivered
                                    @else
                                        {{ ucfirst($ord->status) }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <form action="{{ route('fnb.status', $ord->id) }}" method="POST" class="flex items-center justify-end space-x-2">
                                    @csrf
                                    <select name="status" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs">
                                        <option value="pending" {{ in_array($ord->status, ['pending']) ? 'selected' : '' }}>Pending</option>
                                        <option value="process" {{ in_array($ord->status, ['process', 'processing']) ? 'selected' : '' }}>Process</option>
                                        <option value="waiting" {{ $ord->status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                                        <option value="delivered" {{ in_array($ord->status, ['delivered', 'completed']) ? 'selected' : '' }}>Delivered</option>
                                    </select>
                                    <button type="submit" class="px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white rounded-lg text-xs font-semibold hover:bg-slate-800">
                                        Apply
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">No Food & Beverage orders registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
