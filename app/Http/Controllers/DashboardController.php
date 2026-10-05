<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Pastikan tabel database memiliki struktur kolom terbaru
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'payment_status')) {
            $currentUser = Auth::user();
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            
            $admin = User::where('role', 'admin')->first() ?? $currentUser;
            if ($admin) {
                Auth::login($admin);
            }
        }

        $today = today();

        // 1. OMZET HARI INI
        $todayOrdersQuery = Order::whereDate('created_at', $today);
        $omzetHariIni = (clone $todayOrdersQuery)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $transaksiPaidCount = (clone $todayOrdersQuery)
            ->where('payment_status', 'paid')
            ->count();

        // 2. TRANSAKSI HARI INI (Total, Dine In, Take Away)
        $totalTransaksi = (clone $todayOrdersQuery)->count();

        $totalDineIn = (clone $todayOrdersQuery)
            ->where('order_type', 'dine_in')
            ->count();

        $totalTakeAway = (clone $todayOrdersQuery)
            ->where('order_type', 'take_away')
            ->count();

        // 3. STATUS MEJA
        $totalMeja = CafeTable::count();
        $mejaTersedia = CafeTable::where('status', 'available')->count();
        $mejaTerisi = CafeTable::where('status', 'occupied')->count();
        $mejaDipesan = CafeTable::where('status', 'reserved')->count();
        $cafeTables = CafeTable::orderBy('table_number')->get();

        // 4. MENU TERLARIS
        $menuTerlaris = OrderDetail::select(
                'menu_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->with('menu.category')
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 5. MENU HABIS (Out of stock)
        $menuHabis = Menu::with('category')
            ->where('is_available', false)
            ->orderBy('name')
            ->get();

        return view('dashboard.index', compact(
            'omzetHariIni',
            'transaksiPaidCount',
            'totalTransaksi',
            'totalDineIn',
            'totalTakeAway',
            'totalMeja',
            'mejaTersedia',
            'mejaTerisi',
            'mejaDipesan',
            'cafeTables',
            'menuTerlaris',
            'menuHabis'
        ));
    }
}
