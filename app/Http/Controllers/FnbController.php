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
        $items = array_filter($request->input('items', []), function ($item) {
            return isset($item['qty']) && (int)$item['qty'] > 0;
        });
        $request->merge(['items' => $items]);

        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:fnb_menus,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_type' => 'required|in:on_the_spot,billed_to_room',
            'payment_method' => 'required_if:payment_type,on_the_spot|in:Cash,Transfer Bank,QRIS,Credit Card',
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
                'payment_type' => $request->payment_type,
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

            // If on the spot, record a payment and an invoice immediately
            if ($request->payment_type === 'on_the_spot') {
                \App\Models\Payment::create([
                    'reservation_id' => $reservation->id,
                    'payment_date' => Carbon::now(),
                    'payment_method' => $request->payment_method ?? 'Cash',
                    'amount' => $total,
                    'reference_number' => 'F&B On the Spot',
                ]);

                $invoiceCount = \App\Models\Invoice::count() + 1;
                $invoiceNumber = 'INV-FNB-' . Carbon::now()->format('Ymd') . '-' . sprintf('%04d', $invoiceCount);

                \App\Models\Invoice::create([
                    'reservation_id' => $reservation->id,
                    'invoice_number' => $invoiceNumber,
                    'invoice_type' => 'addon_fnb',
                    'subtotal' => $total,
                    'tax' => 0, // Assuming tax is inclusive or handled differently
                    'grand_total' => $total,
                ]);
            }

            ActivityLog::log(
                Auth::id(),
                'Front Office',
                'F&B Order',
                "Placed F&B room service order for room {$resRoom->room->room_number} (booking {$reservation->booking_number}). Total: Rp " . number_format($total) . " ({$request->payment_type})"
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
            'status' => 'required|in:pending,process,processing,waiting,delivered,completed',
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

    /**
     * Display F&B specific reports.
     */
    public function reports(Request $request): View
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::today()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::today()->endOfMonth();

        // Ensure date search covers full end date day
        $endDateForQuery = $endDate->copy()->endOfDay();

        // 1. Order status metrics
        $statusCounts = FnbOrder::whereBetween('order_date', [$startDate, $endDateForQuery])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Total orders in range
        $totalOrders = FnbOrder::whereBetween('order_date', [$startDate, $endDateForQuery])->count();

        // Total revenue (delivered or completed status)
        $revenue = FnbOrder::whereBetween('order_date', [$startDate, $endDateForQuery])
            ->whereIn('status', ['delivered', 'completed'])
            ->sum('total_amount');

        // Average order value
        $avgOrderValue = $totalOrders > 0 ? $revenue / $totalOrders : 0;

        // 2. Popular menu items in date range
        $popularMenus = DB::table('fnb_order_details')
            ->join('fnb_orders', 'fnb_order_details.order_id', '=', 'fnb_orders.id')
            ->join('fnb_menus', 'fnb_order_details.menu_id', '=', 'fnb_menus.id')
            ->join('fnb_categories', 'fnb_menus.category_id', '=', 'fnb_categories.id')
            ->whereBetween('fnb_orders.order_date', [$startDate, $endDateForQuery])
            ->select(
                'fnb_menus.name as menu_name',
                'fnb_categories.name as category_name',
                DB::raw('SUM(fnb_order_details.qty) as total_qty'),
                DB::raw('SUM(fnb_order_details.subtotal) as total_revenue')
            )
            ->groupBy('fnb_menus.id', 'fnb_menus.name', 'fnb_categories.name')
            ->orderBy('total_qty', 'desc')
            ->get();

        // 3. Detailed order list in range
        $orders = FnbOrder::with('reservation.guest', 'room', 'details.menu')
            ->whereBetween('order_date', [$startDate, $endDateForQuery])
            ->orderBy('order_date', 'desc')
            ->paginate(15);

        return view('fnb.reports', compact(
            'startDate',
            'endDate',
            'statusCounts',
            'totalOrders',
            'revenue',
            'avgOrderValue',
            'popularMenus',
            'orders'
        ));
    }
}
