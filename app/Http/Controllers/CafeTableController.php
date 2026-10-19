<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CafeTableController extends Controller
{
    public function index(): View
    {
        $tables = CafeTable::with(['currentOrder.user'])->orderBy('table_number')->get();

        $stats = [
            'total' => $tables->count(),
            'available' => $tables->where('status', 'available')->count(),
            'occupied' => $tables->where('status', 'occupied')->count(),
            'reserved' => $tables->where('status', 'reserved')->count(),
        ];

        $groupedTables = $tables->groupBy(function ($table) {
            return strtoupper(trim($table->area ?: 'INDOOR'));
        });

        return view('tables.index', compact('tables', 'stats', 'groupedTables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50', 'unique:cafe_tables,table_number'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'area' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:available,occupied,reserved'],
        ]);

        $validated['area'] = strtoupper(trim($validated['area'] ?? 'INDOOR'));

        CafeTable::create($validated);

        return redirect()->route('tables.index')->with('success', 'Meja baru berhasil ditambahkan!');
    }

    public function update(Request $request, CafeTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50', 'unique:cafe_tables,table_number,'.$table->id],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'area' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:available,occupied,reserved'],
        ]);

        $validated['area'] = strtoupper(trim($validated['area'] ?? 'INDOOR'));

        $table->update($validated);

        return redirect()->route('tables.index')->with('success', 'Data meja berhasil diperbarui!');
    }

    public function destroy(CafeTable $table): RedirectResponse
    {
        $table->delete();

        return redirect()->route('tables.index')->with('success', 'Meja berhasil dihapus!');
    }

    public function updateStatus(Request $request, CafeTable $table): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:available,occupied,reserved'],
        ]);

        $table->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status meja berhasil diubah.',
        ]);
    }
}
