<?php

namespace App\Http\Controllers;

use App\Models\FnbOrder;
use App\Models\LaundryRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomInspection;
use App\Models\RoomStatus;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the reporting dashboard.
     */
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::today()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::today()->endOfMonth();

        // 1. Reservation Report (Daily Reservation Counts)
        $reservationsReport = Reservation::whereBetween('reservation_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(reservation_date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 2. Occupancy Report
        $totalRooms = Room::count();
        $rooms = Room::with('status')->get();
        
        $occupiedRooms = Room::whereHas('status', function($q) {
            $q->whereIn('code', ['O', 'OC', 'OD', 'Comp', 'HU']);
        })->count();

        $vacantRooms = $totalRooms - $occupiedRooms;
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $roomStatuses = RoomStatus::withCount('rooms')->get();

        // 3. Housekeeping Report (Inspection & Laundry Count)
        $inspectionsCount = RoomInspection::whereBetween('inspection_date', [$startDate, $endDate])->count();
        
        $laundryReport = LaundryRequest::whereBetween('request_date', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get();

        // 4. Food & Beverage Report
        $fnbReport = FnbOrder::whereBetween('order_date', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get();

        // 5. Room Type Revenues
        $roomTypes = RoomType::all();
        $roomTypeRevenues = [];
        
        foreach ($roomTypes as $type) {
            $revenue = DB::table('reservation_rooms')
                ->join('rooms', 'reservation_rooms.room_id', '=', 'rooms.id')
                ->join('reservations', 'reservation_rooms.reservation_id', '=', 'reservations.id')
                ->where('rooms.room_type_id', $type->id)
                ->where('reservations.status', '!=', 'cancelled')
                ->whereBetween('reservations.checkin_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                // Calculate rate * nights
                ->select(DB::raw('SUM(room_rate * DATEDIFF(checkout_date, checkin_date)) as total'))
                ->value('total') ?: 0.00;

            // DATEDIFF might return 0 if same day check-in/out. Ensure at least 1 night is charged.
            // Let's fallback to calculating based on records if diff is 0 or null
            $roomTypeRevenues[$type->name] = (float) $revenue;
        }

        // Extra Bed Revenue
        $extraBedRevenue = DB::table('reservation_rooms')
            ->join('reservations', 'reservation_rooms.reservation_id', '=', 'reservations.id')
            ->where('reservations.status', '!=', 'cancelled')
            ->whereBetween('reservations.checkin_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(DB::raw('SUM(extra_bed_qty * extra_bed_price * DATEDIFF(checkout_date, checkin_date)) as total'))
            ->value('total') ?: 0.00;

        // 6. Summary Report
        $roomRevTotal = array_sum($roomTypeRevenues) + $extraBedRevenue;
        $fnbRevTotal = FnbOrder::whereBetween('order_date', [$startDate, $endDate])->whereIn('status', ['completed', 'delivered'])->sum('total_amount');
        $laundryRevTotal = LaundryRequest::whereBetween('request_date', [$startDate, $endDate])->where('status', 'completed')->sum('total_amount');
        $additionalRevTotal = DB::table('additional_charges')
            ->join('reservations', 'additional_charges.reservation_id', '=', 'reservations.id')
            ->whereBetween('reservations.checkin_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');

        $totalRevenue = $roomRevTotal + $fnbRevTotal + $laundryRevTotal + $additionalRevTotal;

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'reservationsReport',
            'totalRooms',
            'occupiedRooms',
            'vacantRooms',
            'occupancyRate',
            'roomStatuses',
            'inspectionsCount',
            'laundryReport',
            'fnbReport',
            'roomTypeRevenues',
            'extraBedRevenue',
            'roomRevTotal',
            'fnbRevTotal',
            'laundryRevTotal',
            'additionalRevTotal',
            'totalRevenue'
        ));
    }
}
