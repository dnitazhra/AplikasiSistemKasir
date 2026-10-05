@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan Statistik & Operasional Harian')

@section('header_actions')
    <button type="button" 
            onclick="refreshDashboard(this)" 
            id="btn-refresh-dashboard"
            class="inline-flex items-center gap-2 bg-[#FAF6F0] hover:bg-white border border-[#EADBCE] hover:border-[#D9A05B] text-[#3D231D] px-4 py-2 rounded-xl text-xs font-semibold shadow-xs hover:shadow-sm transition-all duration-200 cursor-pointer group active:scale-95"
            title="Muat Ulang Data Dashboard">
        <!-- Lucide Rotate / Refresh Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#C88A42] transition-transform duration-500 group-hover:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
            <path d="M3 3v5h5"/>
            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
            <path d="M16 16h5v5"/>
        </svg>
        <span>Muat Ulang</span>
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- ============================================================ -->
    <!-- BARIS 1: 1. OMZET HARI INI & 2. TRANSAKSI HARI INI (GRID 2) -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <!-- 1. OMZET HARI INI -->
        <div class="bg-white rounded-2xl p-6 border border-[#EADBCE] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-[#8A7A75] uppercase tracking-wider block">1. Omzet Hari Ini</span>
                    <h3 class="text-3xl font-extrabold text-[#3D231D] tracking-tight mt-2">
                        Rp {{ number_format($omzetHariIni, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-[#D9A05B]/15 text-[#C88A42] flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>

            <!-- Sub-keterangan: jumlah transaksi lunas -->
            <div class="mt-4 pt-4 border-t border-[#EADBCE]/60 flex items-center gap-2 text-xs font-medium text-emerald-700">
                <i class="fa-solid fa-circle-check text-sm text-emerald-500"></i>
                <span><strong>{{ $transaksiPaidCount }}</strong> transaksi lunas hari ini</span>
            </div>
        </div>

        <!-- 2. TRANSAKSI HARI INI -->
        <div class="bg-white rounded-2xl p-6 border border-[#EADBCE] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-[#8A7A75] uppercase tracking-wider block">2. Transaksi Hari Ini</span>
                    <h3 class="text-3xl font-extrabold text-[#3D231D] tracking-tight mt-2">
                        {{ $totalTransaksi }} <span class="text-base font-normal text-[#8A7A75]">Order</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

            <!-- Sub-keterangan: Dine In & Take Away -->
            <div class="mt-4 pt-4 border-t border-[#EADBCE]/60 flex items-center gap-3 text-xs text-[#8A7A75]">
                <span class="inline-flex items-center gap-1.5 font-semibold text-[#3D231D]">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    {{ $totalDineIn }} Dine In
                </span>
                <span>•</span>
                <span class="inline-flex items-center gap-1.5 font-semibold text-[#3D231D]">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    {{ $totalTakeAway }} Take Away
                </span>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- BARIS 2: 3. STATUS MEJA (RINGKASAN & DENAH CEPAT)           -->
    <!-- ============================================================ -->
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-[#EADBCE]">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-[#8A7A75] uppercase tracking-wider">3. Status Meja</span>
                </div>
                <h3 class="text-lg font-bold text-[#3D231D] mt-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-chair text-[#C88A42]"></i>
                    <span>Ketersediaan Meja Kafe</span>
                    <span class="text-xs font-normal text-[#8A7A75]">({{ $totalMeja }} Total Meja)</span>
                </h3>
            </div>

            <!-- Ringkasan Statistik Status Meja -->
            <div class="flex items-center gap-2 flex-wrap text-xs font-bold">
                <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ $mejaTersedia }} Tersedia (Kosong)
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    {{ $mejaTerisi }} Terisi
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    {{ $mejaDipesan }} Dipesan
                </span>
            </div>
        </div>

        <!-- Denah Cepat Ketersediaan Meja -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 mt-5">
            @forelse($cafeTables as $table)
                <div class="p-3 rounded-xl border text-center transition-all duration-200
                    {{ $table->status === 'available' ? 'bg-emerald-50/50 border-emerald-200' : '' }}
                    {{ $table->status === 'occupied' ? 'bg-rose-50/50 border-rose-200' : '' }}
                    {{ $table->status === 'reserved' ? 'bg-amber-50/50 border-amber-200' : '' }}">
                    
                    <div class="w-2 h-2 rounded-full mx-auto mb-1.5
                        {{ $table->status === 'available' ? 'bg-emerald-500' : '' }}
                        {{ $table->status === 'occupied' ? 'bg-rose-500' : '' }}
                        {{ $table->status === 'reserved' ? 'bg-amber-500' : '' }}"></div>

                    <h4 class="text-xs font-extrabold text-[#3D231D]">{{ $table->table_number }}</h4>
                    <span class="text-[10px] text-[#8A7A75] block mt-0.5">{{ $table->capacity }} Kursi</span>

                    <span class="inline-block text-[9px] font-extrabold uppercase tracking-wider mt-2 px-1.5 py-0.5 rounded
                        {{ $table->status === 'available' ? 'text-emerald-700 bg-emerald-100' : '' }}
                        {{ $table->status === 'occupied' ? 'text-rose-700 bg-rose-100' : '' }}
                        {{ $table->status === 'reserved' ? 'text-amber-700 bg-amber-100' : '' }}">
                        {{ $table->status === 'available' ? 'Kosong' : ($table->status === 'occupied' ? 'Terisi' : 'Dipesan') }}
                    </span>
                </div>
            @empty
                <p class="col-span-full text-center text-xs text-[#8A7A75] py-4">Belum ada meja terdaftar.</p>
            @endforelse
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- BARIS 3: 4. MENU TERLARIS & 5. MENU HABIS (GRID 2 SEIMBANG) -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 4. MENU TERLARIS -->
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-[#EADBCE]">
                    <div>
                        <span class="text-xs font-bold text-[#8A7A75] uppercase tracking-wider block">4. Menu Terlaris</span>
                        <h3 class="text-base font-bold text-[#3D231D] mt-0.5 flex items-center gap-2">
                            <i class="fa-solid fa-trophy text-[#C88A42]"></i>
                            <span>Paling Banyak Dipesan</span>
                        </h3>
                    </div>
                    <span class="text-xs text-[#8A7A75] font-medium">Periode Berjalan</span>
                </div>

                <div class="divide-y divide-[#EADBCE]/50 mt-2">
                    @forelse($menuTerlaris as $index => $item)
                        <div class="py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center font-extrabold text-xs
                                    {{ $index === 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : '' }}
                                    {{ $index === 1 ? 'bg-slate-100 text-slate-700 border border-slate-300' : '' }}
                                    {{ $index === 2 ? 'bg-orange-100 text-orange-800 border border-orange-300' : '' }}
                                    {{ $index > 2 ? 'bg-gray-100 text-gray-600' : '' }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="text-xs font-bold text-[#3D231D]">{{ $item->menu->name ?? 'Menu' }}</h4>
                                    <span class="text-[10px] text-[#8A7A75]">{{ $item->menu->category->name ?? 'Kategori' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-extrabold text-[#3D231D]">{{ $item->total_sold }} Terjual</span>
                                <span class="text-[11px] text-[#C88A42] font-semibold block">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-[#8A7A75]">
                            <i class="fa-solid fa-mug-saucer text-2xl mb-1 text-[#D9A05B]"></i>
                            <p>Belum ada data transaksi menu.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-[#EADBCE] mt-4">
                <a href="{{ route('pos.index') }}" class="w-full block py-2 text-center text-xs font-bold text-[#C88A42] hover:text-[#3D231D] transition-colors">
                    Buka Terminal POS untuk Buat Pesanan &rarr;
                </a>
            </div>
        </div>

        <!-- 5. MENU HABIS (OUT OF STOCK) -->
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-[#EADBCE]">
                    <div>
                        <span class="text-xs font-bold text-[#8A7A75] uppercase tracking-wider block">5. Menu Habis</span>
                        <h3 class="text-base font-bold text-[#3D231D] mt-0.5 flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                            <span>Stok Kosong (Out of Stock)</span>
                        </h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold 
                        {{ $menuHabis->isNotEmpty() ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $menuHabis->count() }} Menu
                    </span>
                </div>

                <div class="divide-y divide-[#EADBCE]/50 mt-2">
                    @forelse($menuHabis as $item)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-[#3D231D] flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    <span>{{ $item->name }}</span>
                                </h4>
                                <span class="text-[10px] text-[#8A7A75] ml-3.5">
                                    {{ $item->category->name ?? 'Kategori' }} • {{ $item->formatted_price }}
                                </span>
                            </div>

                            <button type="button" 
                                    onclick="restockMenu({{ $item->id }}, this)" 
                                    class="px-3 py-1 bg-[#FAF6F0] hover:bg-emerald-600 hover:text-white border border-[#EADBCE] text-emerald-700 font-bold text-[11px] rounded-lg transition-colors cursor-pointer">
                                Restock
                            </button>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-emerald-700">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center text-lg mb-2">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <p class="font-bold">Semua Menu Tersedia</p>
                            <p class="text-[11px] text-[#8A7A75] mt-0.5">Tidak ada menu yang berstatus habis saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-[#EADBCE] mt-4 text-center">
                <span class="text-[11px] text-[#8A7A75] italic">
                    Gunakan tombol "Restock" di atas untuk mengaktifkan kembali ketersediaan stok menu.
                </span>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function restockMenu(menuId, btn) {
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        fetch(`/menus/${menuId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                btn.disabled = false;
                btn.textContent = 'Restock';
                alert(data.message || 'Gagal mengubah status menu');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.textContent = 'Restock';
            alert('Terjadi kesalahan jaringan.');
        });
    }
</script>
@endpush
