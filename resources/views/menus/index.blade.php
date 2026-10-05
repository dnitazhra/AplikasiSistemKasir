@extends('layouts.app')

@section('title', 'Katalog Menu')
@section('page_title', 'Katalog & Manajemen Menu')
@section('page_subtitle', 'Kelola Produk, Varian, Harga, dan Ketersediaan Stok')

@section('content')
<div class="space-y-6">

    <!-- TOP HEADER BAR -->
    <div class="bg-white rounded-2xl border border-[#EADBCE] p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Search and Filter Form -->
        <form action="{{ route('menus.index') }}" method="GET" class="flex items-center gap-2.5 w-full sm:w-auto flex-1">
            <div class="relative flex-1 sm:max-w-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#8A7A75]"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama menu..." 
                       class="w-full pl-9 pr-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <select name="category_id" onchange="this.form.submit()" class="bg-[#FAF6F0] border border-[#EADBCE] rounded-xl px-3 py-2 text-xs font-semibold text-[#3D231D] outline-none">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            @if($search || $categoryId)
                <a href="{{ route('menus.index') }}" class="p-2 text-[#8A7A75] hover:text-[#3D231D]" title="Reset Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <!-- Add Menu Button -->
        <a href="{{ route('menus.create') }}" class="w-full sm:w-auto px-4 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs flex items-center justify-center gap-2 transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Menu Baru</span>
        </a>
    </div>

    <!-- MENUS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($menus as $menu)
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm hover:shadow-md transition-all p-4 flex flex-col justify-between group">
                <div>
                    <!-- Category Badge & Stock Pill -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-[#FAF6F0] text-[#8A7A75] border border-[#EADBCE]">
                            {{ $menu->category->name ?? 'Menu' }}
                        </span>
                        
                        <!-- Availability Toggle Switch -->
                        <button type="button" onclick="toggleMenuAvailability({{ $menu->id }}, this)" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold cursor-pointer transition-colors
                                {{ $menu->is_available ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $menu->is_available ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            <span>{{ $menu->is_available ? 'Tersedia' : 'Habis' }}</span>
                        </button>
                    </div>

                    <!-- Visual Thumbnail / Icon -->
                    <div class="w-full h-32 rounded-xl bg-[#FAF6F0] border border-[#EADBCE]/50 flex items-center justify-center text-[#D9A05B] mb-3 group-hover:scale-102 transition-transform overflow-hidden">
                        @if($menu->image && file_exists(public_path($menu->image)))
                            <img src="{{ asset($menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                        @elseif(str_contains(strtolower($menu->name), 'croissant') || str_contains(strtolower($menu->name), 'roll'))
                            <i class="fa-solid fa-bread-slice text-4xl text-amber-600"></i>
                        @elseif(str_contains(strtolower($menu->name), 'fries') || str_contains(strtolower($menu->name), 'nasi') || str_contains(strtolower($menu->name), 'spaghetti'))
                            <i class="fa-solid fa-bowl-food text-4xl text-amber-700"></i>
                        @elseif(str_contains(strtolower($menu->name), 'tea') || str_contains(strtolower($menu->name), 'matcha'))
                            <i class="fa-solid fa-leaf text-4xl text-emerald-600"></i>
                        @else
                            <i class="fa-solid fa-mug-hot text-4xl text-[#8A5A44]"></i>
                        @endif
                    </div>

                    <h4 class="text-sm font-bold text-[#3D231D] leading-tight">{{ $menu->name }}</h4>
                    <p class="text-xs text-[#8A7A75] mt-1 line-clamp-2 leading-relaxed">
                        {{ $menu->description ?? 'Deskripsi produk khas d\'nale caffe.' }}
                    </p>

                    <!-- Variants Summary -->
                    @if($menu->variants->isNotEmpty())
                        <div class="mt-2.5 pt-2 border-t border-[#EADBCE]/50">
                            <span class="text-[10px] font-bold text-[#C88A42] uppercase tracking-wider block mb-1">Pilihan Variasi:</span>
                            <div class="flex flex-wrap gap-1">
                                @foreach($menu->variants as $v)
                                    <span class="text-[10px] bg-[#FAF6F0] border border-[#EADBCE] text-[#3D231D] px-2 py-0.5 rounded-md font-medium">
                                        {{ $v->name }} ({{ $v->options->count() }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Price & Edit Actions -->
                <div class="mt-4 pt-3 border-t border-[#EADBCE] flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-[#8A7A75] block">Harga:</span>
                        <span class="text-sm font-extrabold text-[#3D231D]">{{ $menu->formatted_price }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('menus.edit', $menu) }}" class="p-2 text-[#8A7A75] hover:text-[#3D231D] hover:bg-[#FAF6F0] rounded-lg transition-colors" title="Edit Menu">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </a>

                        <form action="{{ route('menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Menu">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-[#EADBCE] p-12 text-center text-[#8A7A75]">
                <i class="fa-solid fa-utensils text-3xl mb-2 text-[#D9A05B]"></i>
                <p class="text-xs font-semibold">Belum ada menu yang terdaftar atau cocok dengan pencarian.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $menus->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleMenuAvailability(menuId, btn) {
        btn.disabled = true;
        fetch(`/menus/${menuId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => {
            btn.disabled = false;
            alert('Gagal mengubah status ketersediaan.');
        });
    }
</script>
@endpush
