@extends('layouts.app')

@section('title', 'Daftar Menu')

@section('page_title', 'Daftar Menu')
@section('page_subtitle', 'Kelola harga, ketersediaan, dan varian menu')

@section('header_actions')
    <a href="{{ route('menus.create') }}"
       class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-sm transition-all duration-200 active:scale-95">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Tambah Menu</span>
    </a>
@endsection

@section('content')

{{-- ─────────────────────────────────────────────── --}}
{{-- FILTER & PENCARIAN                              --}}
{{-- ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(61,35,29,0.08)] border border-[#EADBCE] p-5 mb-6">
    <form method="GET" action="{{ route('menus.index') }}" id="filter-form">
        <div class="flex flex-col sm:flex-row gap-3 items-end">

            {{-- Cari Menu --}}
            <div class="flex-1 min-w-0">
                <label class="block text-xs font-semibold text-[#3D231D] mb-1.5 tracking-wide">Cari Menu</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#C88A42]">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="Nama atau kode menu"
                           class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] placeholder-[#B9A99A] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all">
                </div>
            </div>

            {{-- Kategori --}}
            <div class="w-full sm:w-52">
                <label class="block text-xs font-semibold text-[#3D231D] mb-1.5 tracking-wide">Kategori</label>
                <div class="relative">
                    <select name="category_id"
                            class="w-full appearance-none px-4 py-2.5 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all pr-9">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#C88A42]">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Ketersediaan --}}
            <div class="w-full sm:w-44">
                <label class="block text-xs font-semibold text-[#3D231D] mb-1.5 tracking-wide">Ketersediaan</label>
                <div class="relative">
                    <select name="available"
                            class="w-full appearance-none px-4 py-2.5 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all pr-9">
                        <option value="">Semua</option>
                        <option value="1" {{ request('available') === '1' ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ request('available') === '0' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#C88A42]">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all duration-200 active:scale-95">
                    <i class="fa-solid fa-filter text-xs"></i>
                    Terapkan
                </button>
                <a href="{{ route('menus.index') }}"
                   class="inline-flex items-center gap-2 bg-white hover:bg-[#FAF6F0] border border-[#EADBCE] hover:border-[#D9A05B] text-[#3D231D] px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95">
                    <i class="fa-solid fa-rotate-left text-xs text-[#C88A42]"></i>
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ─────────────────────────────────────────────── --}}
{{-- INFO ROW: Jumlah hasil + summary                --}}
{{-- ─────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-[#8A7A75]">
        Menampilkan
        <span class="font-semibold text-[#3D231D]">{{ $menus->total() }}</span>
        produk
        @if($search || $categoryId)
            <span class="text-[#C88A42]">(difilter)</span>
        @endif
    </p>
    <div class="flex items-center gap-2 text-xs text-[#8A7A75]">
        <span class="flex items-center gap-1">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Tersedia
        </span>
        <span class="flex items-center gap-1 ml-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-400"></span> Habis
        </span>
    </div>
</div>

{{-- ─────────────────────────────────────────────── --}}
{{-- GRID KATALOG MENU                               --}}
{{-- ─────────────────────────────────────────────── --}}
@if($menus->isEmpty())
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-16 text-center">
        <div class="w-16 h-16 bg-[#FAF6F0] rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-book-open text-[#D9A05B] text-2xl"></i>
        </div>
        <p class="text-[#3D231D] font-semibold text-base mb-1">Belum ada menu</p>
        <p class="text-[#8A7A75] text-sm mb-5">Tambahkan produk pertama ke katalog kafe.</p>
        <a href="{{ route('menus.create') }}"
           class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Menu
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
        @foreach($menus as $menu)
        @php
            $price      = (float) $menu->price;
            $modal      = round($price * 0.65, 0);   // estimasi 65% COGS
            $margin     = $price - $modal;
            $isFav      = $menu->orderDetails()->sum('quantity') >= 10;
            $codePrefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $menu->category?->name ?? 'PRD'), 0, 3));
            $codeSuffix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $menu->name), 0, 3));
            $code       = $codePrefix . '-' . $codeSuffix;
        @endphp

        <div class="bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(61,35,29,0.08)] border border-[#EADBCE] overflow-hidden flex flex-col transition-all duration-200 hover:shadow-[0_6px_20px_-4px_rgba(61,35,29,0.14)] hover:-translate-y-0.5 group">

            {{-- ── BADGE ROW (TOP) ── --}}
            <div class="flex items-center gap-1.5 px-4 pt-3.5 pb-0 flex-wrap">
                {{-- Kode produk --}}
                <span class="text-[10px] font-bold tracking-widest uppercase px-2 py-0.5 rounded-full bg-[#FAF6F0] text-[#8A7A75] border border-[#EADBCE]">
                    {{ $code }}
                </span>
                {{-- Favorit --}}
                @if($isFav)
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-[#3D231D] text-[#D9A05B]">
                    <i class="fa-solid fa-star text-[8px] mr-0.5"></i>Favorit
                </span>
                @endif
                {{-- Ketersediaan --}}
                @if($menu->is_available)
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 ml-auto">
                    <i class="fa-solid fa-circle text-[7px] mr-0.5 text-emerald-500"></i>Tersedia
                </span>
                @else
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200 ml-auto">
                    <i class="fa-solid fa-circle text-[7px] mr-0.5 text-rose-400"></i>Habis
                </span>
                @endif
            </div>

            {{-- ── GAMBAR PRODUK ── --}}
            <div class="mx-4 mt-3 rounded-xl overflow-hidden bg-[#FAF6F0] border border-[#EADBCE] aspect-[4/3] flex items-center justify-center relative">
                @if($menu->image && file_exists(public_path($menu->image)))
                    <img src="{{ asset($menu->image) }}"
                         alt="{{ $menu->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    {{-- Placeholder ilustratif --}}
                    <div class="flex flex-col items-center gap-2 text-[#D9A05B]/60">
                        <i class="fa-solid fa-mug-hot text-4xl"></i>
                        <span class="text-[10px] font-medium text-[#B9A99A] uppercase tracking-wider">No Image</span>
                    </div>
                @endif
                {{-- Overlay toggle availability --}}
                <button type="button"
                        onclick="toggleAvail({{ $menu->id }}, this)"
                        title="{{ $menu->is_available ? 'Tandai Habis' : 'Tandai Tersedia' }}"
                        class="absolute top-2 right-2 w-7 h-7 rounded-lg {{ $menu->is_available ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-rose-400 hover:bg-rose-500' }} text-white flex items-center justify-center shadow transition-all duration-200 text-xs opacity-0 group-hover:opacity-100">
                    <i class="fa-solid {{ $menu->is_available ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                </button>
            </div>

            {{-- ── INFO PRODUK ── --}}
            <div class="px-4 pt-3 pb-1">
                <p class="text-[10px] font-bold tracking-widest uppercase text-[#C88A42] mb-0.5">
                    {{ $menu->category?->name ?? 'Tanpa Kategori' }}
                </p>
                <h3 class="text-[15px] font-bold text-[#2D2422] leading-tight mb-1">{{ $menu->name }}</h3>
                <p class="text-lg font-extrabold text-[#3D231D]">
                    Rp {{ number_format($price, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-[#8A7A75] mt-0.5">
                    Modal Rp {{ number_format($modal, 0, ',', '.') }}
                    <span class="text-[#C88A42]">·</span>
                    Margin Rp {{ number_format($margin, 0, ',', '.') }}
                </p>
            </div>

            {{-- ── TAG VARIAN ── --}}
            @if($menu->variants->count() > 0)
            <div class="px-4 pt-2 flex flex-wrap gap-1.5">
                @foreach($menu->variants as $variant)
                <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-[#FDF6ED] text-[#C88A42] border border-[#EADBCE]">
                    {{ $variant->name }}
                </span>
                @endforeach
            </div>
            @else
            <div class="px-4 pt-2">
                <span class="text-[10px] text-[#B9A99A] italic">Tidak ada varian</span>
            </div>
            @endif

            {{-- ── TOMBOL AKSI ── --}}
            <div class="mt-auto px-4 pb-4 pt-3 border-t border-[#F5EDE3] mt-3">
                {{-- Baris 1: Ubah & Hapus --}}
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('menus.edit', $menu) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 bg-[#FAF6F0] hover:bg-[#3D231D] hover:text-white border border-[#EADBCE] hover:border-[#3D231D] text-[#3D231D] px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200">
                        <i class="fa-solid fa-pen text-[10px]"></i>
                        Ubah
                    </a>

                    <form method="POST" action="{{ route('menus.destroy', $menu) }}"
                          onsubmit="return confirm('Hapus menu \'{{ addslashes($menu->name) }}\'? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 bg-white hover:bg-rose-50 border border-[#EADBCE] hover:border-rose-200 text-rose-500 hover:text-rose-700 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200">
                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                            Hapus
                        </button>
                    </form>
                </div>

                {{-- Baris 2: Kelola Varian --}}
                <a href="{{ route('menus.edit', $menu) }}#variants-section"
                   class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#D9A05B] to-[#C88A42] hover:brightness-105 text-white px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shadow-sm">
                    <i class="fa-solid fa-sliders text-[10px]"></i>
                    Kelola Varian
                    @if($menu->variants->count() > 0)
                        <span class="ml-auto bg-white/25 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $menu->variants->count() }}</span>
                    @endif
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- PAGINATION                                      --}}
    {{-- ─────────────────────────────────────────────── --}}
    @if($menus->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $menus->links() }}
    </div>
    @endif
@endif

@endsection

@push('scripts')
<script>
    // Toggle availability via AJAX (dari tombol overlay gambar)
    async function toggleAvail(menuId, btn) {
        btn.disabled = true;
        try {
            const res = await fetch(`/menus/${menuId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            if (data.success) {
                // Reload halaman untuk refleksi perubahan status
                window.location.reload();
            }
        } catch (e) {
            btn.disabled = false;
        }
    }
</script>
@endpush
