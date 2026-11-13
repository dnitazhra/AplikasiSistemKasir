<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * Tampilkan halaman riwayat transaksi lunas khusus hari ini.
     */
    public function index(Request $request): View
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $search = trim((string) $request->query('search', ''));
        $paymentMethod = $request->query('payment_method');

        // Query pesanan lunas hari ini
        $baseQuery = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('payment_status', 'paid');

        // Statistik Ringkasan Hari Ini
        $totalPaidOrders = (clone $baseQuery)->count();
        $totalRevenue = (clone $baseQuery)->sum('total_amount');
        $totalItemsSold = (int) OrderDetail::whereHas('order', function ($query) use ($todayStart, $todayEnd) {
            $query->whereBetween('created_at', [$todayStart, $todayEnd])
                ->where('payment_status', 'paid');
        })->sum('quantity');

        // Query tabel riwayat pesanan hari ini
        $ordersQuery = (clone $baseQuery)
            ->with(['user', 'cafeTable', 'orderDetails.menu']);

        if ($search !== '') {
            $ordersQuery->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            });
        }

        if ($paymentMethod && in_array($paymentMethod, ['cash', 'qris'], true)) {
            $ordersQuery->where('payment_method', $paymentMethod);
        }

        $orders = $ordersQuery->latest()->paginate(15)->withQueryString();

        return view('history.index', compact(
            'orders',
            'totalPaidOrders',
            'totalItemsSold',
            'totalRevenue',
            'search',
            'paymentMethod'
        ));
    }
}
