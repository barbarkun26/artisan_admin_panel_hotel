<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Checkin;
use App\Models\Checkout;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    /**
     * List reservations.
     */
    public function index(Request $request): View
    {
        $query = Reservation::with('guest', 'reservationRooms.room.roomType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('guest', function($qGuest) use ($search) {
                      $qGuest->where('name', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->orderBy('id', 'desc')->paginate(15);

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show create booking form.
     */
    public function create(Request $request): View
    {
        $guests = Guest::orderBy('name')->get();
        $rooms = [];
        $checkin = $request->checkin ?? Carbon::today()->format('Y-m-d');
        $checkout = $request->checkout ?? Carbon::tomorrow()->format('Y-m-d');
        
        // Find available rooms
        if ($request->filled(['checkin', 'checkout'])) {
            $bookedRoomIds = DB::table('reservation_rooms')
                ->join('reservations', 'reservation_rooms.reservation_id', '=', 'reservations.id')
                ->where('reservations.status', '!=', 'cancelled')
                ->where('reservations.checkout_date', '>', $checkin)
                ->where('reservations.checkin_date', '<', $checkout)
                ->pluck('reservation_rooms.room_id');

            $rooms = Room::whereNotIn('id', $bookedRoomIds)
                ->with('roomType', 'status')
                ->orderBy('room_number')
                ->get();
        }

        return view('reservations.create', compact('guests', 'rooms', 'checkin', 'checkout'));
    }

    /**
     * Store new reservation.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
            'room_id' => 'required|exists:rooms,id',
            'total_guest' => 'required|integer|min:1',
            'extra_bed_qty' => 'integer|min:0|max:2',
        ]);

        $room = Room::findOrFail($request->room_id);
        
        // Verify room availability one more time to avoid race condition
        $isBooked = DB::table('reservation_rooms')
            ->join('reservations', 'reservation_rooms.reservation_id', '=', 'reservations.id')
            ->where('reservations.status', '!=', 'cancelled')
            ->where('reservation_rooms.room_id', $room->id)
            ->where('reservations.checkout_date', '>', $request->checkin_date)
            ->where('reservations.checkin_date', '<', $request->checkout_date)
            ->exists();

        if ($isBooked) {
            return back()->withErrors(['room_id' => 'This room has already been booked for the selected dates.']);
        }

        // Generate Booking Number: AH-YYYYMMDD-ROOMNO-RANDOM
        $dateStr = Carbon::parse($request->checkin_date)->format('Ymd');
        $randomNum = sprintf('%05d', rand(1, 99999));
        $bookingNumber = "AH-{$dateStr}-{$room->room_number}-{$randomNum}";

        DB::transaction(function () use ($request, $room, $bookingNumber) {
            $reservation = Reservation::create([
                'booking_number' => $bookingNumber,
                'guest_id' => $request->guest_id,
                'reservation_date' => Carbon::now(),
                'checkin_date' => $request->checkin_date,
                'checkout_date' => $request->checkout_date,
                'total_guest' => $request->total_guest,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Room rates
            $rate = $room->roomType->base_price;
            $extraBedPrice = $request->extra_bed_qty > 0 ? 150000.00 : 0.00; // Charge Rp 150.000 for extra bed

            ReservationRoom::create([
                'reservation_id' => $reservation->id,
                'room_id' => $room->id,
                'room_rate' => $rate,
                'extra_bed_qty' => $request->extra_bed_qty ?? 0,
                'extra_bed_price' => $extraBedPrice,
            ]);

            // Update room expected arrival status in room_statuses
            // We can optionally keep it, but changing actual status when checkin happens is the main requirement.

            ActivityLog::log(
                Auth::id(),
                'Reservation',
                'Create',
                "Created reservation {$bookingNumber} for room {$room->room_number}."
            );
        });

        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully.');
    }

    /**
     * Show guest folio / ledger details.
     */
    public function show(Reservation $reservation): View
    {
        $reservation->load([
            'guest',
            'reservationRooms.room.roomType',
            'payments',
            'invoice',
            'fnbOrders.details.menu',
            'laundryRequests.items',
            'additionalCharges',
            'inspections.inspector'
        ]);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Process actual Check-in.
     */
    public function checkin(Reservation $reservation): RedirectResponse
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be checked in.');
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'checkin']);

            Checkin::create([
                'reservation_id' => $reservation->id,
                'actual_checkin' => Carbon::now(),
                'checked_in_by' => Auth::id(),
            ]);

            // Update rooms status to Occupied (O)
            $occupiedStatus = DB::table('room_statuses')->where('code', 'O')->first();
            if ($occupiedStatus) {
                foreach ($reservation->reservationRooms as $resRoom) {
                    $resRoom->room->update(['current_status_id' => $occupiedStatus->id]);
                }
            }

            ActivityLog::log(
                Auth::id(),
                'Checkin',
                'Process',
                "Checked in reservation {$reservation->booking_number}."
            );
        });

        return back()->with('success', 'Check-in processed successfully. Room status is now Occupied.');
    }

    /**
     * Extend Guest Stay.
     */
    public function extend(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'new_checkout_date' => 'required|date|after:' . $reservation->checkout_date->format('Y-m-d'),
        ]);

        // Verify room availability for the extension
        foreach ($reservation->reservationRooms as $resRoom) {
            $isBooked = DB::table('reservation_rooms')
                ->join('reservations', 'reservation_rooms.reservation_id', '=', 'reservations.id')
                ->where('reservations.status', '!=', 'cancelled')
                ->where('reservations.id', '!=', $reservation->id)
                ->where('reservation_rooms.room_id', $resRoom->room_id)
                ->where('reservations.checkout_date', '>', $reservation->checkout_date->format('Y-m-d'))
                ->where('reservations.checkin_date', '<', $request->new_checkout_date)
                ->exists();

            if ($isBooked) {
                return back()->with('error', "Room {$resRoom->room->room_number} is not available for extension.");
            }
        }

        $oldDate = $reservation->checkout_date->format('Y-m-d');
        $reservation->update(['checkout_date' => $request->new_checkout_date]);

        ActivityLog::log(
            Auth::id(),
            'Reservation',
            'Extend',
            "Extended reservation {$reservation->booking_number} from {$oldDate} to {$request->new_checkout_date}."
        );

        return back()->with('success', 'Stay extended successfully.');
    }

    /**
     * Cancel Reservation.
     */
    public function cancel(Reservation $reservation): RedirectResponse
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be cancelled.');
        }

        $reservation->update(['status' => 'cancelled']);

        ActivityLog::log(
            Auth::id(),
            'Reservation',
            'Cancel',
            "Cancelled reservation {$reservation->booking_number}."
        );

        return back()->with('success', 'Reservation cancelled.');
    }
}
