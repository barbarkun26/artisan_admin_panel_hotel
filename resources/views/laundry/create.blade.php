@extends('layouts.app')

@section('header_title', 'New Laundry Request')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
    <form action="{{ route('laundry.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Booking context selection -->
        <div>
            <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Select Occupied Room</label>
            <select name="reservation_id" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-amber-500">
                <option value="">-- Choose room --</option>
                @foreach($reservations as $res)
                    @foreach($res->reservationRooms as $resRoom)
                        <option value="{{ $res->id }}">Room {{ $resRoom->room->room_number }} (Guest: {{ $res->guest->name }} - Booking: {{ $res->booking_number }})</option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <!-- Payment Selection -->
        <div>
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Payment Option</h3>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">Payment Type</label>
                        <select name="payment_type" required class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                            <option value="billed_to_room">Bill to Room (Pay at Checkout)</option>
                            <option value="on_the_spot">Pay On The Spot (Direct Payment)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">Payment Method (If On The Spot)</label>
                        <select name="payment_method" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                            <option value="">-- Select Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Items Table -->
        <div>
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Laundry Items List</h3>
            <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse" id="laundry-items-table">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <th class="p-3">Item Name</th>
                            <th class="p-3 w-28">Quantity</th>
                            <th class="p-3 w-36">Unit Price (Rp)</th>
                            <th class="p-3 w-20 text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <tr class="item-row">
                            <td class="p-3">
                                <input type="text" name="items[0][name]" required placeholder="e.g. T-Shirt, Jeans..." 
                                       class="w-full px-3 py-1.5 bg-transparent border border-transparent hover:border-slate-200 dark:hover:border-slate-700 focus:border-amber-500 rounded-lg text-sm focus:outline-none">
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[0][qty]" required min="1" value="1"
                                       class="w-full px-3 py-1.5 bg-transparent border border-transparent hover:border-slate-200 dark:hover:border-slate-700 focus:border-amber-500 rounded-lg text-sm focus:outline-none text-center">
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[0][price]" required min="0" step="1" value="15000"
                                       class="w-full px-3 py-1.5 bg-transparent border border-transparent hover:border-slate-200 dark:hover:border-slate-700 focus:border-amber-500 rounded-lg text-sm focus:outline-none text-right">
                            </td>
                            <td class="p-3 text-center">
                                <button type="button" class="text-red-500 hover:text-red-600 remove-row">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-3 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <button type="button" id="add-item-row" class="text-xs text-amber-500 font-semibold hover:underline">
                        + Add Item Row
                    </button>
                    <span class="text-xs text-slate-400">Standard rate examples: Shirt = Rp 15.000, Trousers = Rp 20.000, Dress = Rp 25.000</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 border-t border-slate-100 dark:border-slate-800 pt-6">
            <a href="{{ route('laundry.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-sm shadow-sm">
                Submit Laundry Request
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.querySelector('#laundry-items-table tbody');
        const addRowBtn = document.getElementById('add-item-row');
        let rowIdx = 1;

        // Add dynamic row
        addRowBtn.addEventListener('click', function() {
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.innerHTML = `
                <td class="p-3">
                    <input type="text" name="items[${rowIdx}][name]" required placeholder="e.g. T-Shirt, Jeans..." 
                           class="w-full px-3 py-1.5 bg-transparent border border-transparent hover:border-slate-200 dark:hover:border-slate-700 focus:border-amber-500 rounded-lg text-sm focus:outline-none">
                </td>
                <td class="p-3">
                    <input type="number" name="items[${rowIdx}][qty]" required min="1" value="1"
                           class="w-full px-3 py-1.5 bg-transparent border border-transparent hover:border-slate-200 dark:hover:border-slate-700 focus:border-amber-500 rounded-lg text-sm focus:outline-none text-center">
                </td>
                <td class="p-3">
                    <input type="number" name="items[${rowIdx}][price]" required min="0" step="1" value="15000"
                           class="w-full px-3 py-1.5 bg-transparent border border-transparent hover:border-slate-200 dark:hover:border-slate-700 focus:border-amber-500 rounded-lg text-sm focus:outline-none text-right">
                </td>
                <td class="p-3 text-center">
                    <button type="button" class="text-red-500 hover:text-red-600 remove-row">
                        Remove
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
            rowIdx++;
            bindRemoveButtons();
        });

        function bindRemoveButtons() {
            document.querySelectorAll('.remove-row').forEach(btn => {
                btn.onclick = function() {
                    const rowCount = document.querySelectorAll('.item-row').length;
                    if (rowCount > 1) {
                        btn.closest('tr').remove();
                    } else {
                        alert('At least one item is required.');
                    }
                }
            });
        }

        // Initialize bind
        bindRemoveButtons();
    });
</script>
@endsection
