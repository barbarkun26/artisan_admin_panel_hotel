@extends('layouts.app')

@section('header_title', 'Room Inspection Check')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
    <!-- Readonly reservation context -->
    <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-xl text-sm">
        <p class="font-bold text-base text-slate-900 dark:text-white mb-2">Inspection Context</p>
        <div class="grid grid-cols-2 gap-4">
            <p><strong>Room Number:</strong> Room {{ $room->room_number }} ({{ $room->roomType->name }})</p>
            <p><strong>Booking Number:</strong> {{ $reservation->booking_number }}</p>
            <p><strong>Guest Name:</strong> {{ $reservation->guest->name }}</p>
            <p><strong>Checkout Date:</strong> {{ $reservation->checkout_date->format('M d, Y') }}</p>
        </div>
    </div>

    <form action="{{ route('inspections.store', ['reservation' => $reservation->id, 'room' => $room->id]) }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="room_condition" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Overall Room Condition</label>
                <select name="room_condition" id="room_condition" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                    <option value="Clean & Neat">Clean & Neat</option>
                    <option value="Dirty / Needs Cleaning">Dirty / Needs Cleaning</option>
                    <option value="Damaged / Items Broken">Damaged / Items Broken</option>
                </select>
            </div>

            <div>
                <label for="additional_charge" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Damage / Item Loss Penalty Charge (Rp)</label>
                <input type="number" name="additional_charge" id="additional_charge" min="0" step="1" value="0"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
            </div>

            <div class="md:col-span-2">
                <label for="damages" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Damage Details (if any)</label>
                <textarea name="damages" id="damages" rows="2" placeholder="e.g. Broken wall lamp, stained towels..."
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="md:col-span-2">
                <label for="missing_items" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Missing Items (if any)</label>
                <textarea name="missing_items" id="missing_items" rows="2" placeholder="e.g. Missing TV remote, mini bar items consumed..."
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Additional Notes</label>
                <textarea name="notes" id="notes" rows="2" placeholder="Remarks or inspection feedback"
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500"></textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-4 border-t border-slate-100 dark:border-slate-800 pt-6">
            <a href="{{ redirect()->getUrlGenerator()->previous() }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-sm shadow-sm">
                Submit Inspection Report
            </button>
        </div>
    </form>
</div>
@endsection
