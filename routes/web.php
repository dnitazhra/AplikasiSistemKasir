<?php

use App\Http\Controllers\ActiveBillsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CafeTableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Root & Admin Redirects (Redirect default to dashboard)
Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/admin', function () {
    return redirect()->route('dashboard');
});

// Protected Application Routes
Route::middleware('auth')->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Terminal POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/order', [PosController::class, 'storeOrder'])->name('pos.order');

    // Quick stock toggle action from Dashboard "Menu Habis" widget
    Route::post('/menus/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menus.toggle');

    // 3. Bill Aktif
    Route::get('/bills', [ActiveBillsController::class, 'index'])->name('bills.index');
    Route::post('/bills/{order}/pay', [ActiveBillsController::class, 'payBill'])->name('bills.pay');

    // 4. Dapur & Bar
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::get('/kitchen/orders-json', [KitchenController::class, 'ordersJson'])->name('kitchen.orders-json');
    Route::patch('/kitchen/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.update-status');

    // 5. Daftar Menu (Kelola Katalog)
    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
    Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
    Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

    // 6. Kategori Menu
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 7. Denah & Meja Kafe
    Route::get('/tables', [CafeTableController::class, 'index'])->name('tables.index');
    Route::post('/tables', [CafeTableController::class, 'store'])->name('tables.store');
    Route::put('/tables/{table}', [CafeTableController::class, 'update'])->name('tables.update');
    Route::delete('/tables/{table}', [CafeTableController::class, 'destroy'])->name('tables.destroy');
    Route::post('/tables/{table}/status', [CafeTableController::class, 'updateStatus'])->name('tables.update-status');

    // 8. Kelola Karyawan & Role
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // 9. Voucher & Promo Diskon
    Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::put('/vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update');
    Route::post('/vouchers/{voucher}/toggle', [VoucherController::class, 'toggle'])->name('vouchers.toggle');
    Route::delete('/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');

    // 10. Laporan Penjualan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // 11. Pengaturan Kafe & Profil
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/store', [SettingController::class, 'updateStore'])->name('settings.store');
    Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
});
