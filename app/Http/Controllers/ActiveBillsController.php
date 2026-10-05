<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActiveBillsController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');

        $query = Order::with(['user', 'cafeTable', 'voucher', 'orderDetails.menu', 'orderDetails.variants'])
            ->whereDate('created_at', today());

        if ($statusFilter === 'unpaid') {
            $query->where('payment_status', 'pending');
        } elseif ($statusFilter === 'active_kitchen') {
            $query->whereIn('kitchen_status', ['pending', 'cooking', 'ready']);
        } elseif ($statusFilter === 'dine_in') {
            $query->where('order_type', 'dine_in');
        } elseif ($statusFilter === 'take_away') {
            $query->where('order_type', 'take_away');
        }

        $orders = $query->latest()->get();
        $tables = CafeTable::with(['orders' => function ($q) {
            $q->whereDate('created_at', today())->latest();
        }])->get();

        $stats = [
            'total_active' => Order::whereDate('created_at', today())->count(),
            'pending_payment' => Order::whereDate('created_at', today())->where('payment_status', 'pending')->count(),
            'in_kitchen' => Order::whereDate('created_at', today())->whereIn('kitchen_status', ['pending', 'cooking', 'ready'])->count(),
            'served_today' => Order::whereDate('created_at', today())->where('kitchen_status', 'served')->count(),
        ];

        $storeSettings = Setting::pluck('value', 'key_name')->toArray();

        return view('bills.index', compact('orders', 'tables', 'stats', 'statusFilter', 'storeSettings'));
    }

    public function payBill(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:cash,qris'],
            'cash_given' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order->update([
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'paid',
        ]);

        if ($order->kitchen_status === 'served' && $order->cafe_table_id) {
            $table = CafeTable::find($order->cafe_table_id);
            if ($table) {
                $table->update(['status' => 'available']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan order #' . $order->order_number . ' telah berhasil dilunasi!',
        ]);
    }
}
