<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function products(Request $request): View
    {
        $search     = $request->query('search', '');
        $categoryId = $request->query('category_id', '');
        $available  = $request->query('available', '');

        $query = Menu::with('category');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId !== '') {
            $query->where('category_id', (int) $categoryId);
        }

        if ($available !== '') {
            $query->where('is_available', $available === '1');
        }

        $menus      = $query->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $totalMenus     = Menu::count();
        $totalAvailable = Menu::where('is_available', true)->count();
        $totalHabis     = Menu::where('is_available', false)->count();

        return view('kitchen.products', compact(
            'menus', 'categories',
            'totalMenus', 'totalAvailable', 'totalHabis',
            'search', 'categoryId', 'available'
        ));
    }

    public function toggleProduct(Menu $menu): JsonResponse
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        return response()->json([
            'success'      => true,
            'is_available' => $menu->is_available,
            'message'      => $menu->is_available ? 'Menu diaktifkan (Tersedia)' : 'Menu ditandai Habis',
        ]);
    }

    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'active');

        $query = Order::with(['user', 'cafeTable', 'orderDetails.menu', 'orderDetails.variants'])
            ->whereDate('created_at', today());

        if ($statusFilter === 'active') {
            $query->whereIn('kitchen_status', ['pending', 'cooking', 'ready']);
        } elseif (in_array($statusFilter, ['pending', 'cooking', 'ready', 'served'])) {
            $query->where('kitchen_status', $statusFilter);
        }

        $orders = $query->orderBy('created_at', 'asc')->get();

        $counts = [
            'pending' => Order::whereDate('created_at', today())->where('kitchen_status', 'pending')->count(),
            'cooking' => Order::whereDate('created_at', today())->where('kitchen_status', 'cooking')->count(),
            'ready' => Order::whereDate('created_at', today())->where('kitchen_status', 'ready')->count(),
            'served' => Order::whereDate('created_at', today())->where('kitchen_status', 'served')->count(),
        ];

        return view('kitchen.index', compact('orders', 'counts', 'statusFilter'));
    }

    public function ordersJson(): JsonResponse
    {
        $orders = Order::with(['user', 'cafeTable', 'orderDetails.menu', 'orderDetails.variants'])
            ->whereDate('created_at', today())
            ->whereIn('kitchen_status', ['pending', 'cooking', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order_type' => $order->order_type,
                    'table_number' => $order->cafeTable ? $order->cafeTable->table_number : null,
                    'cashier_name' => $order->user ? $order->user->name : '-',
                    'created_at_time' => $order->created_at->format('H:i:s'),
                    'elapsed_minutes' => (int) $order->created_at->diffInMinutes(now()),
                    'kitchen_status' => $order->kitchen_status,
                    'payment_status' => $order->payment_status,
                    'items' => $order->orderDetails->map(function ($detail) {
                        return [
                            'name' => $detail->menu->name,
                            'quantity' => $detail->quantity,
                            'notes' => $detail->notes,
                            'variants' => $detail->variants->pluck('variant_option_name')->toArray(),
                        ];
                    }),
                ];
            });

        $counts = [
            'pending' => Order::whereDate('created_at', today())->where('kitchen_status', 'pending')->count(),
            'cooking' => Order::whereDate('created_at', today())->where('kitchen_status', 'cooking')->count(),
            'ready' => Order::whereDate('created_at', today())->where('kitchen_status', 'ready')->count(),
        ];

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'counts' => $counts,
            'server_time' => now()->format('H:i:s'),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'kitchen_status' => ['required', 'in:pending,cooking,ready,served'],
        ]);

        $order->update([
            'kitchen_status' => $validated['kitchen_status'],
        ]);

        // If served and paid, check if table can be released
        if ($validated['kitchen_status'] === 'served' && $order->cafe_table_id && $order->payment_status === 'paid') {
            // Check if there are other pending/active orders on this table
            $otherActiveOrders = Order::where('cafe_table_id', $order->cafe_table_id)
                ->where('id', '!=', $order->id)
                ->whereIn('kitchen_status', ['pending', 'cooking', 'ready'])
                ->exists();

            if (! $otherActiveOrders) {
                $table = CafeTable::find($order->cafe_table_id);
                if ($table && $table->status === 'occupied') {
                    $table->update(['status' => 'available']);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diubah menjadi '.ucfirst($validated['kitchen_status']),
            'new_status' => $validated['kitchen_status'],
        ]);
    }
}
