@extends('layouts.app')

@section('header_title')
    Folio: <span class="font-mono">{{ $reservation->booking_number }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Folio Header / Details -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Guest Info Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Guest Information</h3>
            <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $reservation->guest->name }}</p>
            <p class="text-xs text-slate-400 font-mono mt-1">Guest Code: {{ $reservation->guest->guest_code }}</p>
            <div class="mt-4 space-y-2 text-sm">
                <p><strong>Phone:</strong> {{ $reservation->guest->phone }}</p>
                <p><strong>Email:</strong> {{ $reservation->guest->email ?? 'N/A' }}</p>
                <p><strong>Identity:</strong> {{ $reservation->guest->identity_type }} - {{ $reservation->guest->identity_number }}</p>
                <p class="text-xs text-slate-400"><strong>Address:</strong> {{ $reservation->guest->address ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Stay Info Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Stay Information</h3>
            <div class="space-y-2 text-sm">
                <p><strong>Check-in:</strong> {{ $reservation->checkin_date->format('M d, Y') }}</p>
                <p><strong>Check-out:</strong> {{ $reservation->checkout_date->format('M d, Y') }}</p>
                <p><strong>Nights:</strong> {{ $reservation->nights_count }} Nights</p>
                <p><strong>Total Guests:</strong> {{ $reservation->total_guest }} Persons</p>
                <p><strong>Status:</strong> 
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold 
                        @if($reservation->status === 'checkin') bg-emerald-500/10 text-emerald-500
                        @elseif($reservation->status === 'checkout') bg-slate-500/10 text-slate-400
                        @elseif($reservation->status === 'cancelled') bg-red-500/10 text-red-500
                        @else bg-amber-500/10 text-amber-500 @endif">
                        {{ ucfirst($reservation->status) }}
                    </span>
                </p>
                <div class="border-t border-slate-100 dark:border-slate-800 pt-2 mt-2">
                    <p class="font-bold text-slate-900 dark:text-white">Allocated Room(s):</p>
                    @foreach($reservation->reservationRooms as $resRoom)
                        <p class="text-xs mt-1">
                            <strong>Room {{ $resRoom->room->room_number }}</strong> (Floor {{ $resRoom->room->floor }} - {{ $resRoom->room->roomType->name }})
                        </p>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Summary / Invoice Draft Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Financial Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Total Folio Billed:</span>
                        <span class="font-medium">Rp {{ number_format($reservation->grand_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-500">
                        <span>Total Paid:</span>
                        <span class="font-medium">Rp {{ number_format($reservation->payments_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 dark:border-slate-800 pt-2 text-base font-bold">
                        <span>Balance Due:</span>
                        <span class="{{ $reservation->balance_due > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                            Rp {{ number_format($reservation->balance_due, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2">
                @if($reservation->status === 'checkout' || $reservation->invoices->isNotEmpty())
                    <a href="{{ route('reservations.invoice', $reservation->id) }}" target="_blank"
                       class="w-full py-2 bg-slate-900 dark:bg-slate-700 text-white font-semibold text-xs rounded-xl text-center hover:bg-slate-800 transition-colors">
                        Print Invoice
                    </a>
                @else
                    <a href="{{ route('reservations.invoice', $reservation->id) }}" target="_blank"
                       class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-xs rounded-xl text-center hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Draft Invoice
                    </a>
                @endif
                <a href="{{ route('reservations.registration-form', $reservation->id) }}" target="_blank"
                   class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl text-center transition-colors shadow-sm">
                    Print Registration Form
                </a>
            </div>
        </div>
    </div>

    <!-- Booking Actions Panel -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Reservation Actions</h3>
        
        @if($reservation->status === 'pending')
            <div class="flex flex-col gap-4">
                <div class="flex flex-wrap gap-4">
                    <details class="group">
                        <summary class="inline-block px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-sm transition-colors cursor-pointer list-none">
                            Check-in Guest
                        </summary>
                        <div class="mt-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700 w-full max-w-2xl">
                            <h4 class="font-bold text-sm mb-4">Process Check-in</h4>
                            <form action="{{ route('reservations.checkin', $reservation->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">Guarantee Type</label>
                                        <select name="guarantee_type" required class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                                            <option value="ktp">Hold ID Card (KTP)</option>
                                            <option value="deposit">Cash/Card Deposit</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">Deposit Amount (Rp)</label>
                                        <input type="number" name="deposit_amount" min="0" value="0" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                                        <p class="text-[10px] text-slate-400 mt-1">If KTP, leave as 0.</p>
                                    </div>
                                </div>
                                
                                <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mt-4">
                                    <h5 class="text-xs font-semibold mb-2 text-slate-500 uppercase">Upfront Room Payment (Total: Rp {{ number_format($reservation->room_charges_total * 1.1) }} incl. 10% tax)</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">Payment Method</label>
                                            <select name="payment_method" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                                                <option value="">Pay Later / No Upfront Payment</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Transfer Bank">Transfer Bank</option>
                                                <option value="QRIS">QRIS</option>
                                                <option value="Credit Card">Credit Card</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">Amount Paid</label>
                                            <input type="number" name="amount_paid" value="{{ $reservation->room_charges_total * 1.1 }}" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors">
                                        Confirm & Check-in
                                    </button>
                                </div>
                            </form>
                        </div>
                    </details>
                    
                    <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" class="inline mt-1">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl text-sm transition-colors">
                        Cancel Booking
                    </button>
                </form>
            </div>
        @elseif($reservation->status === 'checkin')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Extend stay form -->
                <div>
                    <h4 class="font-bold text-sm mb-2">Extend Guest Stay</h4>
                    <form action="{{ route('reservations.extend', $reservation->id) }}" method="POST" class="flex items-end space-x-3">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">New Checkout Date</label>
                            <input type="date" name="new_checkout_date" required min="{{ $reservation->checkout_date->addDay()->format('Y-m-d') }}"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none">
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-slate-900 dark:bg-slate-700 text-white font-semibold rounded-xl text-xs hover:bg-slate-800">
                            Extend
                        </button>
                    </form>
                </div>

                <!-- Checkout / Inspection Workflow -->
                <div>
                    <h4 class="font-bold text-sm mb-2">Checkout & Settlement</h4>
                    
                    @if($reservation->inspection_status === 'none')
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 space-y-3">
                            <div class="p-3 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 rounded-xl text-xs">
                                <strong>Inspection Needed:</strong> You must request a room inspection from Housekeeping before proceeding to checkout.
                            </div>
                            <form action="{{ route('reservations.request-inspection', $reservation->id) }}" method="POST" class="flex justify-end">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs">
                                    Request HK Inspection
                                </button>
                            </form>
                        </div>
                    @elseif($reservation->inspection_status === 'requested')
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 space-y-3">
                            <div class="p-3 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl text-xs">
                                <strong>Inspection Requested:</strong> Waiting for Housekeeping to complete the inspection. Please refresh this page once done.
                            </div>
                            <div class="flex justify-end">
                                <button type="button" disabled class="px-4 py-2 bg-slate-300 text-slate-500 font-bold rounded-xl text-xs cursor-not-allowed">
                                    Waiting for HK...
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Final Checkout Form -->
                        <form action="{{ route('reservations.checkout.process', $reservation->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Method</label>
                                    <select name="payment_method" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer Bank">Transfer Bank</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="Credit Card">Credit Card</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Amount Paid (Rp)</label>
                                    <input type="number" name="amount_paid" required min="0" step="1" value="{{ max(0, $reservation->balance_due) }}"
                                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-400 mb-1 uppercase tracking-wider">Reference No (e.g. Bank Trf Code)</label>
                                <input type="text" name="reference_number" placeholder="Optional"
                                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
                            </div>
                            
                            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs space-y-1">
                                <p><strong>Inspection Completed:</strong> Room is {{ count($reservation->inspections) > 0 ? $reservation->inspections->last()->room_condition : 'checked' }}.</p>
                                @if(count($reservation->inspections) > 0 && $reservation->inspections->last()->additional_charge > 0)
                                    <p>Damage charge of Rp {{ number_format($reservation->inspections->last()->additional_charge) }} was added to the folio.</p>
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs">
                                    @if($reservation->balance_due > 0)
                                        Record Payment & Finalize Checkout
                                    @else
                                        Finalize Checkout
                                    @endif
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm text-slate-400">Checkout is complete or booking is cancelled. No further actions can be performed.</p>
        @endif
    </div>

    <!-- Itemized Billing Ledger (Folio) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Guest Folio Details</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Service / Charge Category</th>
                        <th class="py-3 font-semibold">Description</th>
                        <th class="py-3 font-semibold text-right">Rates/Prices</th>
                        <th class="py-3 font-semibold text-center">Qty/Nights</th>
                        <th class="py-3 font-semibold text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    <!-- 1. Room rent charges -->
                    @foreach($reservation->reservationRooms as $resRoom)
                        <tr>
                            <td class="py-4 font-semibold">Room Charge</td>
                            <td class="py-4">Room {{ $resRoom->room->room_number }} ({{ $resRoom->room->roomType->name }})</td>
                            <td class="py-4 text-right">Rp {{ number_format($resRoom->room_rate) }}</td>
                            <td class="py-4 text-center">{{ $reservation->nights_count }} Night(s)</td>
                            <td class="py-4 text-right font-medium">Rp {{ number_format($resRoom->room_rate * $reservation->nights_count) }}</td>
                        </tr>
                        @if($resRoom->extra_bed_qty > 0)
                            <tr>
                                <td class="py-4 font-semibold">Extra Bed Add-on</td>
                                <td class="py-4">Extra Single Bed</td>
                                <td class="py-4 text-right">Rp {{ number_format($resRoom->extra_bed_price) }}</td>
                                <td class="py-4 text-center">{{ $resRoom->extra_bed_qty }} Bed(s) x {{ $reservation->nights_count }} Night(s)</td>
                                <td class="py-4 text-right font-medium">Rp {{ number_format($resRoom->extra_bed_price * $reservation->nights_count * $resRoom->extra_bed_qty) }}</td>
                            </tr>
                        @endif
                    @endforeach

                    <!-- 2. Food & Beverage Room Service -->
                    @foreach($reservation->fnbOrders as $order)
                        @foreach($order->details as $detail)
                            <tr>
                                <td class="py-4 font-semibold text-emerald-500">Food & Beverage</td>
                                <td class="py-4">F&B Room Service: {{ $detail->menu->name }} (Order #{{ $order->id }})</td>
                                <td class="py-4 text-right">Rp {{ number_format($detail->price) }}</td>
                                <td class="py-4 text-center">x{{ $detail->qty }}</td>
                                <td class="py-4 text-right font-medium">Rp {{ number_format($detail->subtotal) }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                    <!-- 3. Laundry service -->
                    @foreach($reservation->laundryRequests as $req)
                        @foreach($req->items as $item)
                            <tr>
                                <td class="py-4 font-semibold text-sky-500">Laundry Services</td>
                                <td class="py-4">Laundry Charge: {{ $item->item_name }} (Request #{{ $req->id }})</td>
                                <td class="py-4 text-right">Rp {{ number_format($item->price) }}</td>
                                <td class="py-4 text-center">x{{ $item->qty }}</td>
                                <td class="py-4 text-right font-medium">Rp {{ number_format($item->qty * $item->price) }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                    <!-- 4. Additional Charges / Inspection damages -->
                    @foreach($reservation->additionalCharges as $charge)
                        <tr>
                            <td class="py-4 font-semibold text-purple-500">Additional Charges</td>
                            <td class="py-4">{{ $charge->description }} (Type: {{ ucfirst($charge->charge_type) }})</td>
                            <td class="py-4 text-right">Rp {{ number_format($charge->amount) }}</td>
                            <td class="py-4 text-center">1</td>
                            <td class="py-4 text-right font-medium">Rp {{ number_format($charge->amount) }}</td>
                        </tr>
                    @endforeach

                    <!-- Calculations Summary -->
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <td colspan="4" class="py-4 font-bold text-right text-slate-500 dark:text-slate-400">Total Billed:</td>
                        <td class="py-4 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($reservation->grand_total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments Ledger -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Payment Receipts Log</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs uppercase text-slate-400">
                        <th class="py-3 font-semibold">Payment ID</th>
                        <th class="py-3 font-semibold">Date & Time</th>
                        <th class="py-3 font-semibold">Method</th>
                        <th class="py-3 font-semibold">Ref Number</th>
                        <th class="py-3 font-semibold text-right">Amount Settled</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($reservation->payments as $payment)
                        <tr>
                            <td class="py-4 font-mono">PAY-{{ sprintf('%05d', $payment->id) }}</td>
                            <td class="py-4">{{ $payment->payment_date->format('M d, Y H:i') }}</td>
                            <td class="py-4">{{ $payment->payment_method }}</td>
                            <td class="py-4 font-mono text-slate-400">{{ $payment->reference_number ?? 'N/A' }}</td>
                            <td class="py-4 text-right font-bold text-emerald-500">Rp {{ number_format($payment->amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400">No payment receipts registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
