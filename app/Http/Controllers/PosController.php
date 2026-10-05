<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount(['menus' => function ($q) {
            $q->where('is_available', true);
        }])->orderBy('name')->get();

        $menus = Menu::with(['category', 'variants.options'])
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        $tables = CafeTable::orderBy('table_number')->get();

        return view('pos.index', compact('categories', 'menus', 'tables'));
    }

    public function storeOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_type' => ['required', 'in:dine_in,take_away'],
            'cafe_table_id' => ['nullable', 'exists:cafe_tables,id'],
            'payment_method' => ['required', 'in:cash,qris'],
            'payment_status' => ['nullable', 'in:pending,paid,failed'],
            'cash_given' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
            'items.*.variants' => ['nullable', 'array'],
            'items.*.variants.*.name' => ['required_with:items.*.variants', 'string'],
            'items.*.variants.*.additional_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validated['order_type'] === 'dine_in' && empty($validated['cafe_table_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor meja wajib dipilih untuk pesanan Dine In.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0.00;
            $itemsToProcess = [];

            // Calculate exact subtotal server-side to prevent tampering
            foreach ($validated['items'] as $itemData) {
                $menu = Menu::findOrFail($itemData['menu_id']);
                $unitPrice = (float) $menu->price;
                $variantsTotal = 0.00;

                if (!empty($itemData['variants'])) {
                    foreach ($itemData['variants'] as $v) {
                        $variantsTotal += (float) ($v['additional_price'] ?? 0);
                    }
                }

                $totalUnitPrice = $unitPrice + $variantsTotal;
                $lineSubtotal = $totalUnitPrice * (int) $itemData['quantity'];
                $subtotal += $lineSubtotal;

                $itemsToProcess[] = [
                    'menu' => $menu,
                    'quantity' => (int) $itemData['quantity'],
                    'unit_price' => $totalUnitPrice,
                    'subtotal' => $lineSubtotal,
                    'notes' => $itemData['notes'] ?? null,
                    'variants' => $itemData['variants'] ?? [],
                ];
            }

            $totalAmount = $subtotal;
            $userId = Auth::id() ?? 1;

            $paymentStatus = $validated['payment_status'] ?? 'paid';

            // Create Order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $userId,
                'cafe_table_id' => $validated['order_type'] === 'dine_in' ? $validated['cafe_table_id'] : null,
                'voucher_id' => null,
                'order_type' => $validated['order_type'],
                'subtotal' => $subtotal,
                'discount_amount' => 0.00,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'kitchen_status' => 'pending',
            ]);

            // Create Order Details and Variants
            foreach ($itemsToProcess as $proc) {
                $detail = OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $proc['menu']->id,
                    'quantity' => $proc['quantity'],
                    'unit_price' => $proc['unit_price'],
                    'subtotal' => $proc['subtotal'],
                    'notes' => $proc['notes'],
                ]);

                if (!empty($proc['variants'])) {
                    foreach ($proc['variants'] as $variant) {
                        OrderDetailVariant::create([
                            'order_detail_id' => $detail->id,
                            'variant_option_name' => $variant['name'],
                            'additional_price' => (float) ($variant['additional_price'] ?? 0),
                        ]);
                    }
                }
            }

            // Update table status if dine_in
            if ($validated['order_type'] === 'dine_in' && !empty($validated['cafe_table_id'])) {
                $table = CafeTable::find($validated['cafe_table_id']);
                if ($table) {
                    $table->update(['status' => 'occupied']);
                }
            }

            DB::commit();

            // Load order relations for receipt response
            $order->load(['user', 'cafeTable', 'orderDetails.menu', 'orderDetails.variants']);

            $cashGiven = (float) ($validated['cash_given'] ?? 0);
            $change = max(0, $cashGiven - $totalAmount);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'order' => $order,
                'receipt' => [
                    'order_number' => $order->order_number,
                    'date' => $order->created_at->format('d/m/Y H:i'),
                    'cashier' => $order->user ? $order->user->name : 'Kasir',
                    'order_type' => $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away',
                    'table_number' => $order->cafeTable ? $order->cafeTable->table_number : '-',
                    'subtotal' => $order->subtotal,
                    'formatted_subtotal' => 'Rp ' . number_format($order->subtotal, 0, ',', '.'),
                    'total_amount' => $order->total_amount,
                    'formatted_total' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    'payment_method' => strtoupper($order->payment_method),
                    'payment_status' => strtoupper($order->payment_status),
                    'cash_given' => $cashGiven > 0 ? $cashGiven : null,
                    'formatted_cash_given' => $cashGiven > 0 ? 'Rp ' . number_format($cashGiven, 0, ',', '.') : null,
                    'change' => $cashGiven > 0 ? $change : null,
                    'formatted_change' => $cashGiven > 0 ? 'Rp ' . number_format($change, 0, ',', '.') : null,
                    'items' => $order->orderDetails->map(function ($d) {
                        return [
                            'name' => $d->menu->name,
                            'quantity' => $d->quantity,
                            'unit_price' => $d->unit_price,
                            'subtotal' => $d->subtotal,
                            'formatted_subtotal' => 'Rp ' . number_format($d->subtotal, 0, ',', '.'),
                            'notes' => $d->notes,
                            'variants' => $d->variants->pluck('variant_option_name')->toArray(),
                        ];
                    }),
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }
}

