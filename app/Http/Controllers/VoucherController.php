<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = Voucher::withCount('orders')->latest()->get();

        $stats = [
            'total' => $vouchers->count(),
            'active' => $vouchers->where('is_active', true)->count(),
            'total_redeemed' => $vouchers->sum('orders_count'),
        ];

        return view('vouchers.index', compact('vouchers', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:vouchers,code'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Voucher::create([
            'code' => strtoupper(trim($validated['code'])),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_order_amount' => $validated['min_order_amount'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('vouchers.index')->with('success', 'Kode voucher promo baru berhasil ditambahkan!');
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:vouchers,code,' . $voucher->id],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $voucher->update([
            'code' => strtoupper(trim($validated['code'])),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_order_amount' => $validated['min_order_amount'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('vouchers.index')->with('success', 'Data voucher promo berhasil diperbarui!');
    }

    public function toggle(Voucher $voucher): JsonResponse
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $voucher->is_active,
            'message' => $voucher->is_active ? 'Voucher telah diaktifkan' : 'Voucher telah dinonaktifkan',
        ]);
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();
        return redirect()->route('vouchers.index')->with('success', 'Voucher promo berhasil dihapus!');
    }
}
