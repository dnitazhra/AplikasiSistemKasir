<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Produk — d'nale caffe</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        sidebar: '#3D231D',
                        sidebarHover: '#4A2E2B',
                        sidebarActive: '#5A3830',
                        accent: '#D9A05B',
                        accentHover: '#C88A42',
                        cream: '#FAF6F0',
                        border: '#EADBCE',
                        muted: '#8A7A75',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #F1ECE4; }
        ::-webkit-scrollbar-thumb { background: #D9A05B; border-radius: 9999px; }
        #sidebar::-webkit-scrollbar-track { background: #2A1713; }
        #sidebar::-webkit-scrollbar-thumb { background: #4A2E2B; }
    </style>
</head>
<body class="bg-[#FAF6F0] text-[#2D2422] antialiased h-screen overflow-hidden flex">

{{-- ══════════════════════════════════════════════════════ --}}
{{-- SIDEBAR KIRI                                          --}}
{{-- ══════════════════════════════════════════════════════ --}}
<aside id="sidebar" class="w-72 h-screen bg-[#3D231D] flex flex-col flex-shrink-0 shadow-2xl">

    {{-- ── Brand ── --}}
    <div class="p-6 border-b border-[#4A2E2B] flex-shrink-0">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#C88A42] to-[#D9A05B] flex items-center justify-center text-white shadow-lg flex-shrink-0">
                <i class="fa-solid fa-mug-hot text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-white">
                    d'nale <span class="text-[#D9A05B] font-light">caffe</span>
                </h1>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs text-[#D9A05B]/80 font-medium tracking-wider uppercase">POS Kasir</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Nav ── --}}
    <nav class="p-4 flex-1 overflow-y-auto">
        <p class="px-3 py-1.5 text-[11px] font-semibold tracking-wider text-[#D9A05B]/70 uppercase mb-1">Menu</p>
        <div class="space-y-1">

        {{-- Daftar Produk --}}
        <a href="{{ route('kitchen.products') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('kitchen.products') ? 'bg-[#D9A05B] text-white shadow-md font-semibold' : 'text-[#FAF6F0]/80 hover:bg-[#4A2E2B] hover:text-white' }}">
            <i class="fa-solid fa-book-open text-base w-5 text-center {{ request()->routeIs('kitchen.products') ? 'text-white' : 'text-[#D9A05B]' }}"></i>
            <span>Daftar Produk</span>
        </a>

        {{-- Order List --}}
        <a href="{{ route('kitchen.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('kitchen.index') ? 'bg-[#D9A05B] text-white shadow-md font-semibold' : 'text-[#FAF6F0]/80 hover:bg-[#4A2E2B] hover:text-white' }}">
            <i class="fa-solid fa-list-check text-base w-5 text-center {{ request()->routeIs('kitchen.index') ? 'text-white' : 'text-[#D9A05B]' }}"></i>
            <span>Order List</span>
        </a>

        </div>
    </nav>

    {{-- ── User Footer ── --}}
    <div class="p-4 border-t border-[#4A2E2B] bg-[#2A1713]/80 flex-shrink-0">
        <div class="flex items-center justify-between mb-3 px-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#C88A42] to-[#D9A05B] border border-[#D9A05B]/40 flex items-center justify-center text-white font-extrabold text-sm shadow-md flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'B', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-white leading-tight truncate w-32" title="{{ auth()->user()->name ?? 'Barista' }}">
                        {{ auth()->user()->name ?? 'Barista' }}
                    </p>
                    <span class="inline-block text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full mt-1 bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        {{ auth()->user()->role ?? 'barista' }}
                    </span>
                </div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold rounded-xl text-white bg-rose-700/80 hover:bg-rose-600 transition-all duration-200 shadow-sm hover:shadow active:scale-95 cursor-pointer">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout dari Sistem</span>
            </button>
        </form>
    </div>
</aside>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- KONTEN UTAMA                                          --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">

    {{-- ── Topbar ── --}}
    <header class="bg-white border-b border-[#EADBCE] px-6 py-4 flex items-center justify-between flex-shrink-0 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-[#3D231D]">Daftar Produk</h2>
            <p class="text-xs text-[#8A7A75] mt-0.5">Katalog cepat dan terkendali ketersediaan stok menu di sisi kasir.</p>
        </div>
        <a href="{{ route('pos.index') }}"
           class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Buka Terminal Pesanan</span>
        </a>
    </header>

    {{-- ── Main scroll area ── --}}
    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

        {{-- ─── RINGKASAN STOK ─── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm px-5 py-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-utensils text-[#C88A42] text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#8A7A75]">Total Menu</p>
                    <p class="text-2xl font-black text-[#3D231D] leading-none mt-0.5">{{ $totalMenus }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm px-5 py-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#8A7A75]">Menu Tersedia</p>
                    <p class="text-2xl font-black text-emerald-600 leading-none mt-0.5">{{ $totalAvailable }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm px-5 py-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark text-rose-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#8A7A75]">Stok Habis</p>
                    <p class="text-2xl font-black text-rose-500 leading-none mt-0.5">{{ $totalHabis }}</p>
                </div>
            </div>
        </div>

        {{-- ─── FILTER & PENCARIAN ─── --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm px-5 py-4">
            <form method="GET" action="{{ route('kitchen.products') }}" id="filter-form">
                <div class="flex flex-col sm:flex-row gap-3 items-end">

                    {{-- Search --}}
                    <div class="flex-1 min-w-0">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8A7A75] text-sm"></i>
                            <input type="text" name="search" id="input-search"
                                   value="{{ $search }}"
                                   placeholder="Cari nama produk / kode produk..."
                                   autocomplete="off"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] placeholder-[#B9A99A] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all">
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div class="w-full sm:w-48">
                        <div class="relative">
                            <select name="category_id"
                                    onchange="document.getElementById('filter-form').submit()"
                                    class="w-full appearance-none px-4 py-2.5 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all pr-9 cursor-pointer">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $categoryId !== '' && (int)$categoryId === $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#C88A42] text-xs"></i>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="w-full sm:w-40">
                        <div class="relative">
                            <select name="available"
                                    onchange="document.getElementById('filter-form').submit()"
                                    class="w-full appearance-none px-4 py-2.5 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all pr-9 cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="1" {{ $available === '1' ? 'selected' : '' }}>Tersedia</option>
                                <option value="0" {{ $available === '0' ? 'selected' : '' }}>Habis</option>
                            </select>
                            <i class="fa-solid fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#C88A42] text-xs"></i>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex gap-2 flex-shrink-0">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all active:scale-95">
                            <i class="fa-solid fa-filter text-xs"></i> Filter
                        </button>
                        <a href="{{ route('kitchen.products') }}"
                           class="inline-flex items-center gap-2 bg-white hover:bg-[#FAF6F0] border border-[#EADBCE] text-[#3D231D] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all active:scale-95">
                            <i class="fa-solid fa-rotate-left text-xs text-[#C88A42]"></i> Reset
                        </a>
                    </div>
                </div>

                {{-- Badge filter aktif --}}
                @if($search !== '' || $categoryId !== '' || $available !== '')
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-[#F5EDE3] flex-wrap">
                    <span class="text-xs text-[#8A7A75]">Filter aktif:</span>
                    @if($categoryId !== '')
                        @php $activeCat = $categories->firstWhere('id', (int)$categoryId); @endphp
                        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-[#FDF6ED] text-[#C88A42] border border-[#EADBCE] px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-tags text-[9px]"></i> {{ $activeCat?->name ?? '-' }}
                        </span>
                    @endif
                    @if($search !== '')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-[#FDF6ED] text-[#C88A42] border border-[#EADBCE] px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-magnifying-glass text-[9px]"></i> "{{ $search }}"
                        </span>
                    @endif
                    @if($available === '1')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-circle text-[8px]"></i> Tersedia
                        </span>
                    @elseif($available === '0')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-200 px-2.5 py-0.5 rounded-full">
                            <i class="fa-solid fa-circle text-[8px]"></i> Habis
                        </span>
                    @endif
                </div>
                @endif
            </form>
        </div>

        {{-- ─── TABEL PRODUK ─── --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm overflow-hidden">

            {{-- Table Header strip --}}
            <div class="px-5 py-3.5 border-b border-[#EADBCE] flex items-center justify-between bg-[#FAF6F0]/60">
                <p class="text-xs text-[#8A7A75]">
                    Menampilkan <span class="font-bold text-[#3D231D]">{{ $menus->count() }}</span> produk
                    @if($search !== '' || $categoryId !== '' || $available !== '')
                        <span class="text-[#C88A42]">(difilter)</span>
                    @endif
                </p>
                <span class="text-xs text-[#8A7A75]">Klik <span class="font-semibold text-[#C88A42]">toggle stok</span> untuk ubah ketersediaan</span>
            </div>

            @if($menus->isEmpty())
                <div class="py-16 text-center">
                    <i class="fa-solid fa-mug-saucer text-4xl text-[#D9A05B]/40 mb-3"></i>
                    <p class="text-sm font-semibold text-[#3D231D]">Tidak ada produk ditemukan</p>
                    <p class="text-xs text-[#8A7A75] mt-1">Coba ubah filter atau kata kunci pencarian</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#FAF6F0] border-b border-[#EADBCE]">
                                <th class="text-left px-5 py-3 text-[11px] font-bold tracking-widest uppercase text-[#3D231D]">Produk</th>
                                <th class="text-left px-4 py-3 text-[11px] font-bold tracking-widest uppercase text-[#3D231D] hidden sm:table-cell">Kode</th>
                                <th class="text-left px-4 py-3 text-[11px] font-bold tracking-widest uppercase text-[#3D231D] hidden md:table-cell">Kategori</th>
                                <th class="text-right px-4 py-3 text-[11px] font-bold tracking-widest uppercase text-[#3D231D]">Harga Jual</th>
                                <th class="text-center px-4 py-3 text-[11px] font-bold tracking-widest uppercase text-[#3D231D]">Status Stok</th>
                                <th class="text-center px-4 py-3 text-[11px] font-bold tracking-widest uppercase text-[#3D231D]">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5EDE3]">
                            @foreach($menus as $menu)
                            @php
                                $catSlug    = $menu->category?->slug ?? '';
                                $catIcons   = ['coffee' => 'fa-mug-hot', 'non-coffee' => 'fa-glass-water', 'dessert' => 'fa-cake-candles', 'main-course' => 'fa-bowl-food', 'snacks' => 'fa-cookie-bite'];
                                $catIcon    = $catIcons[$catSlug] ?? 'fa-utensils';
                                $codePrefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $menu->category?->name ?? 'PRD'), 0, 3));
                                $codeSuffix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $menu->name), 0, 3));
                                $code       = $codePrefix . '-' . $codeSuffix;
                            @endphp
                            <tr class="hover:bg-[#FAF6F0]/60 transition-colors duration-100" id="row-{{ $menu->id }}">

                                {{-- Produk --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid {{ $catIcon }} text-sm text-[#C88A42]"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-[#2D2422] text-sm leading-tight">{{ $menu->name }}</p>
                                            @if($menu->category)
                                                <p class="text-[11px] text-[#8A7A75] sm:hidden mt-0.5">{{ $menu->category->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Kode --}}
                                <td class="px-4 py-3.5 hidden sm:table-cell">
                                    <span class="font-mono text-[11px] font-bold text-[#8A7A75] bg-[#FAF6F0] border border-[#EADBCE] px-2 py-0.5 rounded-lg tracking-widest">
                                        {{ $code }}
                                    </span>
                                </td>

                                {{-- Kategori --}}
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#C88A42] bg-[#FDF6ED] border border-[#EADBCE] px-2.5 py-1 rounded-lg">
                                        <i class="fa-solid {{ $catIcon }} text-[10px]"></i>
                                        {{ $menu->category?->name ?? '—' }}
                                    </span>
                                </td>

                                {{-- Harga --}}
                                <td class="px-4 py-3.5 text-right">
                                    <span class="font-bold text-[#3D231D] text-sm">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Status Stok --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span id="badge-{{ $menu->id }}"
                                          class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full
                                                 {{ $menu->is_available
                                                     ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                     : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full inline-block {{ $menu->is_available ? 'bg-emerald-500' : 'bg-rose-400' }}"></span>
                                        {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </td>

                                {{-- Aksi Cepat --}}
                                <td class="px-4 py-3.5 text-center">
                                    <button type="button"
                                            id="btn-toggle-{{ $menu->id }}"
                                            onclick="toggleStock({{ $menu->id }}, this)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all active:scale-95
                                                   {{ $menu->is_available
                                                       ? 'bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600'
                                                       : 'bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700' }}">
                                        <i class="fa-solid {{ $menu->is_available ? 'fa-eye-slash' : 'fa-eye' }} text-[10px]"></i>
                                        <span id="btn-label-{{ $menu->id }}">
                                            {{ $menu->is_available ? 'Tandai Habis' : 'Aktifkan' }}
                                        </span>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Table footer --}}
                <div class="px-5 py-3 border-t border-[#EADBCE] bg-[#FAF6F0]/50 flex items-center justify-between">
                    <p class="text-xs text-[#8A7A75]">
                        <span class="font-semibold text-emerald-600">{{ $menus->where('is_available', true)->count() }}</span> tersedia
                        · <span class="font-semibold text-rose-500">{{ $menus->where('is_available', false)->count() }}</span> habis
                    </p>
                    <p class="text-xs text-[#8A7A75]">
                        Update terakhir: <span class="font-medium text-[#3D231D]">{{ now()->format('H:i:s') }}</span>
                    </p>
                </div>
            @endif
        </div>

    </div>{{-- /main scroll --}}
</div>{{-- /konten utama --}}

<script>
    async function toggleStock(menuId, btn) {
        btn.disabled = true;
        const label = document.getElementById('btn-label-' + menuId);
        const badge = document.getElementById('badge-' + menuId);
        const prevLabel = label.textContent.trim();
        label.textContent = '...';

        try {
            const res = await fetch(`/kitchen/products/${menuId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            const data = await res.json();

            if (data.success) {
                const isAvailable = data.is_available;

                // Update badge status
                badge.className = `inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full ${
                    isAvailable
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                        : 'bg-rose-50 text-rose-600 border border-rose-200'
                }`;
                badge.innerHTML = `
                    <span class="w-1.5 h-1.5 rounded-full inline-block ${isAvailable ? 'bg-emerald-500' : 'bg-rose-400'}"></span>
                    ${isAvailable ? 'Tersedia' : 'Habis'}
                `;

                // Update tombol toggle
                btn.className = `inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all active:scale-95 ${
                    isAvailable
                        ? 'bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600'
                        : 'bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700'
                }`;
                btn.querySelector('i').className = `fa-solid ${isAvailable ? 'fa-eye-slash' : 'fa-eye'} text-[10px]`;
                label.textContent = isAvailable ? 'Tandai Habis' : 'Aktifkan';

                // Flash row
                const row = document.getElementById('row-' + menuId);
                row.classList.add('bg-[#FDF6ED]');
                setTimeout(() => row.classList.remove('bg-[#FDF6ED]'), 800);

            } else {
                label.textContent = prevLabel;
                alert(data.message || 'Gagal mengubah status.');
            }
        } catch (e) {
            label.textContent = prevLabel;
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
        }
    }
</script>

</body>
</html>
