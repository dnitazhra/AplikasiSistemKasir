<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PosController;
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

    // ==============================================================
    // RESTRIKSI AKSES / PROTECTED REDIRECTS UNTUK MENU NONAKTIF
    // (Pengguna hanya berinteraksi di Dashboard dan Terminal POS)
    // ==============================================================
    // 3. Bill Aktif
    Route::get('/bills', [App\Http\Controllers\ActiveBillsController::class, 'index'])->name('bills.index');
    Route::post('/bills/{order}/pay', [App\Http\Controllers\ActiveBillsController::class, 'payBill'])->name('bills.pay');

    // 4. Dapur & Bar
    Route::get('/kitchen', [App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.index');
    Route::get('/kitchen/orders-json', [App\Http\Controllers\KitchenController::class, 'ordersJson'])->name('kitchen.orders-json');
    Route::patch('/kitchen/{order}/status', [App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('kitchen.update-status');

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

    Route::any('/tables/{any?}', function () {
        return redirect()->route('dashboard');
    })->where('any', '.*')->name('tables.index');

    Route::any('/vouchers/{any?}', function () {
        return redirect()->route('dashboard');
    })->where('any', '.*')->name('vouchers.index');

    Route::any('/users/{any?}', function () {
        return redirect()->route('dashboard');
    })->where('any', '.*')->name('users.index');

    Route::any('/settings/{any?}', function () {
        return redirect()->route('dashboard');
    })->where('any', '.*')->name('settings.index');
});
