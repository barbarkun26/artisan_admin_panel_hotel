<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AdditionalCharge;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomInspection;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InspectionController extends Controller
{
    /**
     * Show inspection list or form.
     */
    public function index(): View
    {
        $inspections = RoomInspection::with('reservation.guest', 'room', 'inspector')
            ->orderBy('id', 'desc')
            ->paginate(15);
            
        return view('inspections.index', compact('inspections'));
    }

    /**
     * Show form to inspect room for a check-in reservation.
     */
    public function create(Reservation $reservation, Room $room): View
    {
        return view('inspections.create', compact('reservation', 'room'));
    }

    /**
     * Store inspection report.
     */
    public function store(Request $request, Reservation $reservation, Room $room): RedirectResponse
    {
        $request->validate([
            'room_condition' => 'required|string|max:255',
            'missing_items' => 'nullable|string',
            'damages' => 'nullable|string',
            'additional_charge' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $reservation, $room) {
            $chargeAmount = $request->additional_charge ?? 0.00;

            // 1. Create Room Inspection record
            RoomInspection::create([
                'reservation_id' => $reservation->id,
                'room_id' => $room->id,
                'inspected_by' => Auth::id(),
                'inspection_date' => Carbon::now(),
                'room_condition' => $request->room_condition,
                'missing_items' => $request->missing_items,
                'damages' => $request->damages,
                'additional_charge' => $chargeAmount,
                'notes' => $request->notes,
            ]);

            // 2. If additional charge > 0, create AdditionalCharge entry
            if ($chargeAmount > 0) {
                $descriptionParts = [];
                if ($request->filled('damages')) {
                    $descriptionParts[] = "Damages: " . $request->damages;
                }
                if ($request->filled('missing_items')) {
                    $descriptionParts[] = "Missing Items: " . $request->missing_items;
                }
                $description = implode('; ', $descriptionParts) ?: 'HK Inspection Additional Charge';

                AdditionalCharge::create([
                    'reservation_id' => $reservation->id,
                    'charge_type' => 'damages',
                    'description' => $description,
                    'amount' => $chargeAmount,
                ]);
            }

            // Record log
            ActivityLog::log(
                Auth::id(),
                'Housekeeping',
                'Inspection',
                "Inspected room {$room->room_number} for booking {$reservation->booking_number}. Condition: {$request->room_condition}. Charge: Rp " . number_format($chargeAmount)
            );
        });

        // Redirect back to housekeeping dashboard or reservations index
        if (Auth::user()->hasRole('Housekeeping')) {
            return redirect('/hk/dashboard')->with('success', 'Room inspection report submitted.');
        }

        return redirect()->route('reservations.show', $reservation->id)->with('success', 'Room inspection report submitted.');
    }
}
