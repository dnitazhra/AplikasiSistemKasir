<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\MenuVariantOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $query = Menu::with(['category', 'variants.options']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $menus = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('menus.index', compact('menus', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('menus.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['required', 'string'],
            'variants.*.options' => ['required', 'array'],
            'variants.*.options.*.name' => ['required', 'string'],
            'variants.*.options.*.additional_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadDir = public_path('uploads/menus');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $imagePath = 'uploads/menus/' . $fileName;
        }

        $menu = Menu::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image' => $imagePath,
            'is_available' => $request->boolean('is_available', true),
        ]);

        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $v) {
                if (empty($v['name'])) continue;
                $variant = MenuVariant::create([
                    'menu_id' => $menu->id,
                    'name' => $v['name'],
                ]);

                if (!empty($v['options'])) {
                    foreach ($v['options'] as $opt) {
                        if (empty($opt['name'])) continue;
                        MenuVariantOption::create([
                            'menu_variant_id' => $variant->id,
                            'name' => $opt['name'],
                            'additional_price' => (float) ($opt['additional_price'] ?? 0),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu): View
    {
        $categories = Category::orderBy('name')->get();
        $menu->load('variants.options');
        return view('menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['required', 'string'],
            'variants.*.options' => ['required', 'array'],
            'variants.*.options.*.name' => ['required', 'string'],
            'variants.*.options.*.additional_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadDir = public_path('uploads/menus');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $imagePath = 'uploads/menus/' . $fileName;
        }

        $menu->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image' => $imagePath,
            'is_available' => $request->boolean('is_available'),
        ]);

        // Sync variants
        if ($request->has('variants')) {
            $menu->variants()->delete();
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    if (empty($v['name'])) continue;
                    $variant = MenuVariant::create([
                        'menu_id' => $menu->id,
                        'name' => $v['name'],
                    ]);

                    if (!empty($v['options'])) {
                        foreach ($v['options'] as $opt) {
                            if (empty($opt['name'])) continue;
                            MenuVariantOption::create([
                                'menu_variant_id' => $variant->id,
                                'name' => $opt['name'],
                                'additional_price' => (float) ($opt['additional_price'] ?? 0),
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('menus.index')->with('success', 'Data menu berhasil diperbarui!');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus!');
    }

    public function toggleAvailability(Menu $menu): JsonResponse
    {
        $menu->update(['is_available' => !$menu->is_available]);

        return response()->json([
            'success' => true,
            'is_available' => $menu->is_available,
            'message' => $menu->is_available ? 'Menu diaktifkan (Tersedia)' : 'Menu ditandai Habis',
        ]);
    }
}
