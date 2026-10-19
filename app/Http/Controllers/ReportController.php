<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan penjualan dan analitik transaksi.
     */
    public function index(Request $request): View
    {
        $preset = $request->query('preset', 'today');
        $now = Carbon::now();

        // Tentukan rentang tanggal berdasarkan filter atau preset
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->query('end_date'))->endOfDay();
            $preset = 'custom';
        } else {
            switch ($preset) {
                case 'yesterday':
                    $startDate = Carbon::yesterday()->startOfDay();
                    $endDate = Carbon::yesterday()->endOfDay();
                    break;
                case '7_days':
                    $startDate = $now->copy()->subDays(6)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'this_month':
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    break;
                case 'all_time':
                    $startDate = Carbon::create(2020, 1, 1);
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'today':
                default:
                    $preset = 'today';
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
            }
        }

        $paymentMethod = $request->query('payment_method');
        $paymentStatus = $request->query('payment_status', 'paid');
        $orderType = $request->query('order_type');

        // Query dasar order sesuai rentang tanggal
        $baseOrdersQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

        if ($paymentMethod && in_array($paymentMethod, ['cash', 'qris'], true)) {
            $baseOrdersQuery->where('payment_method', $paymentMethod);
        }

        if ($paymentStatus && in_array($paymentStatus, ['paid', 'pending', 'failed'], true)) {
            $baseOrdersQuery->where('payment_status', $paymentStatus);
        }

        if ($orderType && in_array($orderType, ['dine_in', 'take_away'], true)) {
            $baseOrdersQuery->where('order_type', $orderType);
        }

        // 1. Metrik Ringkasan
        $paidOrdersQuery = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid');

        $totalRevenue = (clone $paidOrdersQuery)->sum('total_amount');
        $totalPaidOrders = (clone $paidOrdersQuery)->count();
        $totalDiscountGiven = (clone $paidOrdersQuery)->sum('discount_amount');

        $averageOrderValue = $totalPaidOrders > 0 ? ($totalRevenue / $totalPaidOrders) : 0;

        // Breakdown Metode Pembayaran (Paid)
        $cashRevenue = (clone $paidOrdersQuery)->where('payment_method', 'cash')->sum('total_amount');
        $cashOrdersCount = (clone $paidOrdersQuery)->where('payment_method', 'cash')->count();

        $qrisRevenue = (clone $paidOrdersQuery)->where('payment_method', 'qris')->sum('total_amount');
        $qrisOrdersCount = (clone $paidOrdersQuery)->where('payment_method', 'qris')->count();

        // Breakdown Tipe Pesanan
        $dineInCount = (clone $paidOrdersQuery)->where('order_type', 'dine_in')->count();
        $takeAwayCount = (clone $paidOrdersQuery)->where('order_type', 'take_away')->count();

        // 2. Menu Terlaris dalam Periode Ini
        $topMenus = OrderDetail::select(
            'menu_id',
            DB::raw('SUM(quantity) as total_sold'),
            DB::raw('SUM(subtotal) as total_sales')
        )
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('payment_status', 'paid');
            })
            ->with(['menu.category'])
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 3. Daftar Transaksi (Orders)
        $isPrint = $request->boolean('print', false);
        $ordersListQuery = (clone $baseOrdersQuery)
            ->with(['user', 'cafeTable', 'voucher', 'orderDetails.menu'])
            ->latest();

        $orders = $isPrint ? $ordersListQuery->get() : $ordersListQuery->paginate(15)->withQueryString();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'preset',
            'paymentMethod',
            'paymentStatus',
            'orderType',
            'totalRevenue',
            'totalPaidOrders',
            'totalDiscountGiven',
            'averageOrderValue',
            'cashRevenue',
            'cashOrdersCount',
            'qrisRevenue',
            'qrisOrdersCount',
            'dineInCount',
            'takeAwayCount',
            'topMenus',
            'orders',
            'isPrint'
        ));
    }
}
