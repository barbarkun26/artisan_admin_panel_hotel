<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\LaundryItem;
use App\Models\LaundryRequest;
use App\Models\Payment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaundryController extends Controller
{
    /**
     * List laundry requests.
     */
    public function index(Request $request): View
    {
        $query = LaundryRequest::with('reservation.guest', 'room', 'items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('id', 'desc')->paginate(15);

        return view('laundry.index', compact('requests'));
    }

    /**
     * Show create laundry request form.
     */
    public function create(): View
    {
        // Only checked-in reservations can request laundry
        $reservations = Reservation::where('status', 'checkin')
            ->with('guest', 'reservationRooms.room')
            ->get();

        return view('laundry.create', compact('reservations'));
    }

    /**
     * Store new laundry request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_type' => 'required|in:on_the_spot,billed_to_room',
            'payment_method' => 'required_if:payment_type,on_the_spot|in:Cash,Transfer Bank,QRIS,Credit Card',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        // Get the active room linked to this reservation
        $resRoom = $reservation->reservationRooms()->first();
        if (! $resRoom) {
            return back()->with('error', 'No room allocated to this reservation.');
        }

        DB::transaction(function () use ($request, $reservation, $resRoom) {
            $laundryRequest = LaundryRequest::create([
                'reservation_id' => $reservation->id,
                'room_id' => $resRoom->room_id,
                'request_date' => Carbon::now(),
                'status' => 'pending',
                'total_amount' => 0.00,
                'payment_type' => $request->payment_type,
            ]);

            $total = 0.00;
            foreach ($request->items as $item) {
                $itemSubtotal = $item['qty'] * $item['price'];
                $total += $itemSubtotal;

                LaundryItem::create([
                    'laundry_request_id' => $laundryRequest->id,
                    'item_name' => $item['name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                ]);
            }

            $laundryRequest->update(['total_amount' => $total]);

            // If on the spot, record a payment and an invoice immediately
            if ($request->payment_type === 'on_the_spot') {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'payment_date' => Carbon::now(),
                    'payment_method' => $request->payment_method ?? 'Cash',
                    'amount' => $total,
                    'reference_number' => 'Laundry On the Spot',
                ]);

                $invoiceCount = Invoice::count() + 1;
                $invoiceNumber = 'INV-LND-'.Carbon::now()->format('Ymd').'-'.sprintf('%04d', $invoiceCount);

                Invoice::create([
                    'reservation_id' => $reservation->id,
                    'invoice_number' => $invoiceNumber,
                    'invoice_type' => 'addon_laundry',
                    'subtotal' => $total,
                    'tax' => 0, // Assuming tax is inclusive
                    'grand_total' => $total,
                ]);
            }

            ActivityLog::log(
                Auth::id(),
                'Front Office',
                'Laundry Request',
                "Created laundry request for room {$resRoom->room->room_number} (booking {$reservation->booking_number}). Total: Rp ".number_format($total)." ({$request->payment_type})"
            );
        });

        if (Auth::user()->hasRole('Front Office')) {
            return redirect()->route('fo.dashboard')->with('success', 'Laundry request created.');
        }

        return redirect()->route('laundry.index')->with('success', 'Laundry request created.');
    }

    /**
     * Update laundry status.
     */
    public function updateStatus(Request $request, LaundryRequest $laundryRequest): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed',
        ]);

        $oldStatus = $laundryRequest->status;
        $laundryRequest->update(['status' => $request->status]);

        ActivityLog::log(
            Auth::id(),
            'Housekeeping',
            'Laundry Status',
            "Updated laundry request #{$laundryRequest->id} status from {$oldStatus} to {$request->status}."
        );

        return back()->with('success', 'Laundry request status updated.');
    }
}
