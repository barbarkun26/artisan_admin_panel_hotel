<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
                color: #000;
                padding: 0;
            }
        }
    </style>
</head>
<body class="bg-white p-8">
    <!-- Print toolbar -->
    <div class="no-print max-w-4xl mx-auto mb-8 bg-slate-100 p-4 rounded-xl flex justify-between items-center">
        <span class="text-sm font-medium text-slate-600">Print Preview Mode</span>
        <button onclick="window.print()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-xs transition-colors">
            Print Invoice
        </button>
    </div>

    <!-- Invoice Layout Container -->
    <div class="max-w-4xl mx-auto border border-slate-200 p-8 rounded-lg shadow-sm">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-wider">ARTISAN HOTEL</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Jl. Sudirman No. 123, Jakarta, Indonesia<br>
                    Phone: (021) 555-0199 | Email: reservation@artisan.com
                </p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-slate-800 uppercase">Official Invoice</h2>
                <p class="text-xs text-slate-400 font-mono mt-1">No: {{ $invoice->invoice_number }}</p>
                <p class="text-xs text-slate-500 mt-1">Date: {{ $invoice->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <!-- Customer & Stay Info -->
        <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
            <div>
                <h3 class="font-bold text-slate-800 uppercase text-xs tracking-wider mb-2 border-b border-slate-100 pb-1">Billed To</h3>
                <p class="font-semibold text-base">{{ $reservation->guest->name }}</p>
                <p class="text-xs text-slate-500 font-mono mt-0.5">Guest Code: {{ $reservation->guest->guest_code }}</p>
                <p class="mt-1">Phone: {{ $reservation->guest->phone }}</p>
                <p>Identity: {{ $reservation->guest->identity_type }} - {{ $reservation->guest->identity_number }}</p>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 uppercase text-xs tracking-wider mb-2 border-b border-slate-100 pb-1">Stay Particulars</h3>
                <p><strong>Booking ID:</strong> <span class="font-mono">{{ $reservation->booking_number }}</span></p>
                <p><strong>Rooms Allocated:</strong> 
                    @foreach($reservation->reservationRooms as $resRoom)
                        Room {{ $resRoom->room->room_number }} ({{ $resRoom->room->roomType->name }})
                    @endforeach
                </p>
                <p><strong>Arrival:</strong> {{ $reservation->checkin_date->format('M d, Y') }}</p>
                <p><strong>Departure:</strong> {{ $reservation->checkout_date->format('M d, Y') }}</p>
                <p><strong>Duration:</strong> {{ $reservation->nights_count }} Nights</p>
            </div>
        </div>

        <!-- Folio Items Table -->
        <table class="w-full text-left border-collapse mb-8 text-sm">
            <thead>
                <tr class="bg-slate-50 text-xs uppercase font-bold text-slate-600 border-b border-slate-200">
                    <th class="p-3">Charge Description</th>
                    <th class="p-3 text-right">Unit Rate</th>
                    <th class="p-3 text-center">Qty/Nights</th>
                    <th class="p-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <!-- Room rent -->
                @foreach($reservation->reservationRooms as $resRoom)
                    <tr>
                        <td class="p-3 font-medium">Room Charge - Room {{ $resRoom->room->room_number }} ({{ $resRoom->room->roomType->name }})</td>
                        <td class="p-3 text-right">Rp {{ number_format($resRoom->room_rate) }}</td>
                        <td class="p-3 text-center">{{ $reservation->nights_count }} Nights</td>
                        <td class="p-3 text-right">Rp {{ number_format($resRoom->room_rate * $reservation->nights_count) }}</td>
                    </tr>
                    @if($resRoom->extra_bed_qty > 0)
                        <tr>
                            <td class="p-3 font-medium">Extra Bed Add-on</td>
                            <td class="p-3 text-right">Rp {{ number_format($resRoom->extra_bed_price) }}</td>
                            <td class="p-3 text-center">{{ $resRoom->extra_bed_qty }} Bed(s) x {{ $reservation->nights_count }} Nights</td>
                            <td class="p-3 text-right">Rp {{ number_format($resRoom->extra_bed_price * $reservation->nights_count * $resRoom->extra_bed_qty) }}</td>
                        </tr>
                    @endif
                @endforeach

                <!-- F&B Room Service -->
                @foreach($reservation->fnbOrders as $order)
                    @foreach($order->details as $detail)
                        <tr>
                            <td class="p-3">Room Service: {{ $detail->menu->name }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($detail->price) }}</td>
                            <td class="p-3 text-center">x{{ $detail->qty }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($detail->subtotal) }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <!-- Laundry service -->
                @foreach($reservation->laundryRequests as $req)
                    @foreach($req->items as $item)
                        <tr>
                            <td class="p-3">Laundry Charge: {{ $item->item_name }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($item->price) }}</td>
                            <td class="p-3 text-center">x{{ $item->qty }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($item->qty * $item->price) }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <!-- Additional charges -->
                @foreach($reservation->additionalCharges as $charge)
                    <tr>
                        <td class="p-3 font-medium text-slate-800">{{ $charge->description }}</td>
                        <td class="p-3 text-right">Rp {{ number_format($charge->amount) }}</td>
                        <td class="p-3 text-center">1</td>
                        <td class="p-3 text-right">Rp {{ number_format($charge->amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Calculations & Payments Block -->
        <div class="flex justify-between items-start border-t border-slate-200 pt-6">
            <!-- Payments received -->
            <div class="w-1/2 text-xs text-slate-500">
                <h4 class="font-bold text-slate-700 uppercase tracking-wider mb-2">Settlement Receipts Log</h4>
                <ul class="space-y-1">
                    @forelse($reservation->payments as $pay)
                        <li>{{ $pay->payment_date->format('M d, Y') }} - {{ $pay->payment_method }}: <span class="font-semibold text-black">Rp {{ number_format($pay->amount) }}</span></li>
                    @empty
                        <li>No payments posted.</li>
                    @endforelse
                </ul>
            </div>
            
            <!-- Calculations -->
            <div class="w-1/3 text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-500">Subtotal:</span>
                    <span class="font-semibold">Rp {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tax (10%):</span>
                    <span class="font-semibold">Rp {{ number_format($invoice->tax, 2) }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold">
                    <span>Grand Total:</span>
                    <span>Rp {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs text-emerald-600">
                    <span>Payments Applied:</span>
                    <span>- Rp {{ number_format($reservation->payments_total, 2) }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-1 text-xs font-semibold">
                    <span>Balance Due:</span>
                    <span>Rp {{ number_format(max(0, $invoice->grand_total - $reservation->payments_total), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Signatures Section -->
        <div class="grid grid-cols-2 gap-8 mt-16 pt-8 border-t border-slate-100 text-sm text-center">
            <div class="flex flex-col justify-between h-24">
                <span>Guest's Signature</span>
                <span class="border-t border-slate-300 w-48 mx-auto pt-1 text-xs text-slate-400">Authorized Sign</span>
            </div>
            <div class="flex flex-col justify-between h-24">
                <span>Receptionist Cashier</span>
                <span class="border-t border-slate-300 w-48 mx-auto pt-1 text-xs text-slate-400">Artisan Staff Sign</span>
            </div>
        </div>
    </div>

    <!-- Print immediately -->
    <script>
        window.onload = function() {
            // Uncomment to trigger print immediately upon loading in print mode
            // window.print();
        }
    </script>
</body>
</html>
