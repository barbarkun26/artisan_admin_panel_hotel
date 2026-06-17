<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Checkout;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\RoomStatus;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Process checkout settlement and record payment.
     */
    public function checkout(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:Cash,Transfer Bank,QRIS,Credit Card',
            'amount_paid' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $reservation) {
            // 1. Record the payment
            if ($request->amount_paid > 0) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'payment_date' => Carbon::now(),
                    'payment_method' => $request->payment_method,
                    'amount' => $request->amount_paid,
                    'reference_number' => $request->reference_number,
                ]);
            }

            // Reload reservation relation totals
            $reservation->load('payments');

            // 2. Finalize check-out if fully paid
            // Allow checkout even with minor rounding discrepancies
            if ($reservation->balance_due <= 10.00) {
                $reservation->update(['status' => 'checkout']);

                Checkout::create([
                    'reservation_id' => $reservation->id,
                    'actual_checkout' => Carbon::now(),
                    'checked_out_by' => Auth::id(),
                ]);

                // Create official Invoice
                $invoiceCount = Invoice::count() + 1;
                $invoiceNumber = 'INV-' . Carbon::now()->format('Ymd') . '-' . sprintf('%04d', $invoiceCount);
                
                $subtotal = $reservation->grand_total;
                $tax = $subtotal * 0.10; // 10% tax
                $grandTotal = $subtotal + $tax;

                Invoice::create([
                    'reservation_id' => $reservation->id,
                    'invoice_number' => $invoiceNumber,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'grand_total' => $grandTotal,
                ]);

                // Change Room Status to VD (Vacant Dirty)
                $vdStatus = RoomStatus::where('code', 'VD')->first();
                if ($vdStatus) {
                    foreach ($reservation->reservationRooms as $resRoom) {
                        $resRoom->room->update(['current_status_id' => $vdStatus->id]);
                    }
                }

                ActivityLog::log(
                    Auth::id(),
                    'Front Office',
                    'Checkout',
                    "Finalized checkout for reservation {$reservation->booking_number}. Invoice {$invoiceNumber} generated. Room status set to Vacant Dirty."
                );
            } else {
                ActivityLog::log(
                    Auth::id(),
                    'Front Office',
                    'Payment',
                    "Recorded payment of Rp " . number_format($request->amount_paid) . " for reservation {$reservation->booking_number}. Remaining balance: Rp " . number_format($reservation->balance_due)
                );
            }
        });

        if ($reservation->fresh()->status === 'checkout') {
            return redirect()->route('reservations.show', $reservation->id)->with('success', 'Checkout finalized and Invoice generated.');
        }

        return redirect()->route('reservations.show', $reservation->id)->with('success', 'Payment recorded successfully.');
    }

    /**
     * View/Print Invoice.
     */
    public function printInvoice(Reservation $reservation): View
    {
        $reservation->load([
            'guest',
            'reservationRooms.room.roomType',
            'payments',
            'invoice',
            'fnbOrders.details.menu',
            'laundryRequests.items',
            'additionalCharges'
        ]);

        if (!$reservation->invoice) {
            // Create temporary invoice values if not checked out yet (draft invoice)
            $subtotal = $reservation->grand_total;
            $tax = $subtotal * 0.10;
            $grandTotal = $subtotal + $tax;

            $draftInvoice = (object)[
                'invoice_number' => 'DRAFT-' . $reservation->booking_number,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'created_at' => Carbon::now(),
            ];
            $invoice = $draftInvoice;
        } else {
            $invoice = $reservation->invoice;
        }

        return view('payments.invoice', compact('reservation', 'invoice'));
    }
}
