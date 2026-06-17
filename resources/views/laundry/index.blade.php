@extends('layouts.app')

@section('header_title', 'Laundry Operations')

@section('content')
<div class="space-y-6">
    <!-- Laundry Requests List -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Request ID</th>
                        <th class="py-3 font-semibold">Room No</th>
                        <th class="py-3 font-semibold">Guest</th>
                        <th class="py-3 font-semibold">Requested Date</th>
                        <th class="py-3 font-semibold">Items (Qty x Price)</th>
                        <th class="py-3 font-semibold text-right">Total Fee</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                        <th class="py-3 font-semibold text-right">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($requests as $req)
                        <tr>
                            <td class="py-4 font-mono font-medium text-slate-900 dark:text-white">LAU-{{ sprintf('%05d', $req->id) }}</td>
                            <td class="py-4 font-bold">Room {{ $req->room->room_number }}</td>
                            <td class="py-4">{{ $req->reservation->guest->name }}</td>
                            <td class="py-4 text-xs">{{ $req->request_date->format('M d, Y H:i') }}</td>
                            <td class="py-4 text-xs">
                                <ul class="list-disc pl-4 space-y-0.5">
                                    @foreach($req->items as $item)
                                        <li>{{ $item->item_name }} ({{ $item->qty }}x Rp {{ number_format($item->price) }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($req->total_amount) }}</td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                                    @if($req->status === 'pending') bg-amber-500/10 text-amber-500
                                    @elseif($req->status === 'processing') bg-sky-500/10 text-sky-500
                                    @else bg-emerald-500/10 text-emerald-500 @endif">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                @if($req->status !== 'completed')
                                    <form action="{{ route('laundry.status', $req->id) }}" method="POST" class="flex items-center justify-end space-x-2">
                                        @csrf
                                        <select name="status" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs">
                                            @if($req->status === 'pending')
                                                <option value="processing">Start Processing</option>
                                            @endif
                                            @if(in_array($req->status, ['pending', 'processing']))
                                                <option value="completed">Mark Completed</option>
                                            @endif
                                        </select>
                                        <button type="submit" class="px-2 py-1 bg-slate-900 dark:bg-slate-700 text-white rounded-lg text-xs font-semibold hover:bg-slate-800">
                                            Apply
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">No laundry requests registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
