<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FnbCategory;
use App\Models\FnbMenu;
use App\Models\FnbOrder;
use App\Models\FnbOrderDetail;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FnbController extends Controller
{
    /**
     * List F&B orders.
     */
    public function index(Request $request): View
    {
        $query = FnbOrder::with('reservation.guest', 'room', 'details.menu');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15);

        return view('fnb.index', compact('orders'));
    }

    /**
     * Show ordering form.
     */
    public function create(): View
    {
        $reservations = Reservation::where('status', 'checkin')
            ->with('guest', 'reservationRooms.room')
            ->get();

        $menus = FnbMenu::where('active', true)->with('category')->get();
        $categories = FnbCategory::all();

        return view('fnb.create', compact('reservations', 'menus', 'categories'));
    }

    /**
     * Store F&B order.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:fnb_menus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $resRoom = $reservation->reservationRooms()->first();
        if (!$resRoom) {
            return back()->with('error', 'No room allocated to this reservation.');
        }

        DB::transaction(function () use ($request, $reservation, $resRoom) {
            $order = FnbOrder::create([
                'reservation_id' => $reservation->id,
                'room_id' => $resRoom->room_id,
                'order_date' => Carbon::now(),
                'status' => 'pending',
                'total_amount' => 0.00,
            ]);

            $total = 0.00;
            foreach ($request->items as $item) {
                if ($item['qty'] <= 0) continue;
                
                $menu = FnbMenu::findOrFail($item['menu_id']);
                $subtotal = $item['qty'] * $menu->price;
                $total += $subtotal;

                FnbOrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'qty' => $item['qty'],
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $total]);

            ActivityLog::log(
                Auth::id(),
                'Front Office',
                'F&B Order',
                "Placed F&B room service order for room {$resRoom->room->room_number} (booking {$reservation->booking_number}). Total: Rp " . number_format($total)
            );
        });

        return redirect()->route('fnb.index')->with('success', 'Food & Beverage order created successfully.');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, FnbOrder $fnbOrder): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed',
        ]);

        $oldStatus = $fnbOrder->status;
        $fnbOrder->update(['status' => $request->status]);

        ActivityLog::log(
            Auth::id(),
            'Food & Beverage',
            'Order Status',
            "Updated F&B order #{$fnbOrder->id} status from {$oldStatus} to {$request->status}."
        );

        return back()->with('success', 'Order status updated.');
    }
}
