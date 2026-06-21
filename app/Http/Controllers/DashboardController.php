<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FnbOrder;
use App\Models\LaundryRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomStatus;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard.
     */
    public function adminDashboard(): View
    {
        $today = Carbon::today();

        // Calculate financial summaries
        $roomRevenue = DB::table('reservation_rooms')
            ->join('reservations', 'reservation_rooms.reservation_id', '=', 'reservations.id')
            ->where('reservations.status', '!=', 'cancelled')
            ->select(DB::raw('SUM(room_rate + (extra_bed_qty * extra_bed_price)) as total'))
            ->value('total') ?: 0;

        $fnbRevenue = FnbOrder::whereIn('status', ['completed', 'delivered'])->sum('total_amount');
        $laundryRevenue = LaundryRequest::where('status', 'completed')->sum('total_amount');
        $additionalCharges = DB::table('additional_charges')->sum('amount');

        $totalRevenue = $roomRevenue + $fnbRevenue + $laundryRevenue + $additionalCharges;

        $activityLogs = ActivityLog::with('user')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $roomsCount = Room::count();
        $occupiedCount = Room::whereHas('status', function ($query) {
            $query->whereIn('code', ['O', 'OC', 'OD', 'Comp', 'HU']);
        })->count();
        $occupancyRate = $roomsCount > 0 ? round(($occupiedCount / $roomsCount) * 100) : 0;

        return view('dashboard.admin', compact(
            'roomRevenue',
            'fnbRevenue',
            'laundryRevenue',
            'additionalCharges',
            'totalRevenue',
            'activityLogs',
            'roomsCount',
            'occupiedCount',
            'occupancyRate'
        ));
    }

    /**
     * Front Office Dashboard.
     */
    public function foDashboard(): View
    {
        $today = Carbon::today();

        $roomsCount = Room::count();

        // Occupied room status codes
        $occupiedCount = Room::whereHas('status', function ($query) {
            $query->whereIn('code', ['O', 'OC', 'OD', 'Comp', 'HU']);
        })->count();

        $occupancyRate = $roomsCount > 0 ? round(($occupiedCount / $roomsCount) * 100) : 0;

        $arrivalsCount = Reservation::whereDate('checkin_date', $today)
            ->whereIn('status', ['pending', 'checkin'])
            ->count();

        $departuresCount = Reservation::whereDate('checkout_date', $today)
            ->whereIn('status', ['checkin', 'checkout'])
            ->count();

        // Count rooms by status
        $roomStatusCounts = RoomStatus::withCount('rooms')->get();

        // Get recent reservations
        $recentReservations = Reservation::with('guest', 'reservationRooms.room')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Get all rooms for the dashboard grid
        $allRooms = Room::with(['status', 'roomType', 'reservationRooms' => function ($query) {
            $query->whereHas('reservation', function ($q) {
                $q->whereIn('status', ['checkin']);
            })->with('reservation.guest');
        }])->orderBy('floor')->orderBy('room_number')->get();

        return view('dashboard.fo', compact(
            'roomsCount',
            'occupiedCount',
            'occupancyRate',
            'arrivalsCount',
            'departuresCount',
            'roomStatusCounts',
            'recentReservations',
            'allRooms'
        ));
    }

    /**
     * Housekeeping Dashboard.
     */
    public function hkDashboard(): View
    {
        // Counts for housekeeping dashboard
        $dirtyRoomsCount = Room::whereHas('status', function ($query) {
            $query->whereIn('code', ['VD', 'OD']);
        })->count();

        $vacantRoomsCount = Room::whereHas('status', function ($query) {
            $query->whereIn('code', ['V', 'VC', 'VCI', 'VD']);
        })->count();

        $occupiedRoomsCount = Room::whereHas('status', function ($query) {
            $query->whereIn('code', ['O', 'OC', 'OD', 'Comp', 'HU']);
        })->count();

        $pendingLaundryCount = LaundryRequest::whereIn('status', ['pending', 'processing'])->count();

        // Get rooms list with their status for HK tracking
        $rooms = Room::with('status', 'roomType')
            ->orderBy('room_number')
            ->get();

        $allStatuses = RoomStatus::orderBy('name')->get();

        // Get reservations that requested checkout inspection
        $urgentInspections = Reservation::with('reservationRooms.room', 'guest')
            ->where('inspection_status', 'requested')
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('dashboard.hk', compact(
            'dirtyRoomsCount',
            'vacantRoomsCount',
            'occupiedRoomsCount',
            'pendingLaundryCount',
            'rooms',
            'allStatuses',
            'urgentInspections'
        ));
    }

    /**
     * F&B Dashboard.
     */
    public function fnbDashboard(): View
    {
        $today = Carbon::today();

        $ordersToday = FnbOrder::whereDate('order_date', $today)->count();
        $revenueToday = FnbOrder::whereDate('order_date', $today)
            ->whereIn('status', ['completed', 'delivered'])
            ->sum('total_amount');

        $pendingOrders = FnbOrder::with('reservation.guest', 'room', 'details.menu')
            ->whereIn('status', ['pending', 'process', 'processing', 'waiting'])
            ->orderBy('order_date', 'asc')
            ->get();

        return view('dashboard.fnb', compact(
            'ordersToday',
            'revenueToday',
            'pendingOrders'
        ));
    }

    /**
     * Update Room Status directly (Housekeeping action).
     */
    public function updateRoomStatus(Request $request, Room $room): RedirectResponse
    {
        $request->validate([
            'status_id' => 'required|exists:room_statuses,id',
        ]);

        $oldStatus = $room->status->name;
        $room->update(['current_status_id' => $request->status_id]);
        $room->load('status');

        ActivityLog::log(
            Auth::id(),
            'Housekeeping',
            'Room Status',
            "Updated Room {$room->room_number} status from {$oldStatus} to {$room->status->name}."
        );

        return back()->with('success', "Room {$room->room_number} status updated to {$room->status->name}.");
    }
}
