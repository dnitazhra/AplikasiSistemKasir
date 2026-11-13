<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettlementController extends Controller
{
    /**
     * Tampilkan halaman Tutup Kasir (Settlement Shift).
     */
    public function index(Request $request): View
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        // Query pesanan lunas hari ini
        $paidOrdersQuery = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('payment_status', 'paid');

        // Statistik Utama Hari Ini
        $totalRevenue = (clone $paidOrdersQuery)->sum('total_amount');
        $totalPaidOrders = (clone $paidOrdersQuery)->count();
        $averageOrderValue = $totalPaidOrders > 0 ? ($totalRevenue / $totalPaidOrders) : 0;

        // Total Produk Terjual Hari Ini
        $totalItemsSold = (int) OrderDetail::whereHas('order', function ($query) use ($todayStart, $todayEnd) {
            $query->whereBetween('created_at', [$todayStart, $todayEnd])
                ->where('payment_status', 'paid');
        })->sum('quantity');

        // Rincian Metode Pembayaran
        $cashOrders = (clone $paidOrdersQuery)->where('payment_method', 'cash');
        $cashTotal = (clone $cashOrders)->sum('total_amount');
        $cashCount = (clone $cashOrders)->count();

        $qrisOrders = (clone $paidOrdersQuery)->where('payment_method', 'qris');
        $qrisTotal = (clone $qrisOrders)->sum('total_amount');
        $qrisCount = (clone $qrisOrders)->count();

        $cardOrders = (clone $paidOrdersQuery)->whereIn('payment_method', ['card', 'debit', 'credit']);
        $cardTotal = (clone $cardOrders)->sum('total_amount');
        $cardCount = (clone $cardOrders)->count();

        $transferOrders = (clone $paidOrdersQuery)->where('payment_method', 'transfer');
        $transferTotal = (clone $transferOrders)->sum('total_amount');
        $transferCount = (clone $transferOrders)->count();

        // Rincian Menu yang Berhasil Terjual Hari Ini
        $soldMenus = OrderDetail::select(
            'menu_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_sales'),
            DB::raw('AVG(unit_price) as avg_price')
        )
            ->whereHas('order', function ($query) use ($todayStart, $todayEnd) {
                $query->whereBetween('created_at', [$todayStart, $todayEnd])
                    ->where('payment_status', 'paid');
            })
            ->with(['menu.category'])
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->get();

        // Informasi Toko
        $storeName = Setting::get('store_name', "d'nale caffe");
        $storeAddress = Setting::get('store_address', 'Jl. Merdeka No. 12, Bandung') ?: 'Jl. Merdeka No. 12, Bandung';
        $storePhone = Setting::get('store_phone', '0812-3456-7890') ?: '0812-3456-7890';

        $isPrint = $request->boolean('print', false);

        return view('settlement.index', compact(
            'totalRevenue',
            'totalPaidOrders',
            'totalItemsSold',
            'averageOrderValue',
            'cashTotal',
            'cashCount',
            'qrisTotal',
            'qrisCount',
            'cardTotal',
            'cardCount',
            'transferTotal',
            'transferCount',
            'soldMenus',
            'storeName',
            'storeAddress',
            'storePhone',
            'isPrint'
        ));
    }
}
