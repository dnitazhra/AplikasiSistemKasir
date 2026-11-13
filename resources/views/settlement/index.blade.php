@extends('layouts.app')

@section('title', 'Tutup Kasir (Settlement Shift)')

@section('page_title')
    <div class="flex items-center gap-2.5 flex-wrap">
        <span>Tutup Kasir (Settlement Shift)</span>
        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
            Hari Ini
        </span>
    </div>
@endsection

@section('page_subtitle', 'Rekapitulasi setoran kasir dan rincian penjualan')

@section('header_actions')
    <div class="flex items-center gap-2.5">
        {{-- Tombol Cetak Bukti Settlement --}}
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition-all active:scale-95 cursor-pointer">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Bukti Settlement</span>
        </button>

        {{-- Tombol Ke Laporan Penjualan --}}
        <a href="{{ route('reports.index') }}"
           class="inline-flex items-center gap-2 bg-white hover:bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] hover:border-[#D9A05B] px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition-all active:scale-95">
            <i class="fa-solid fa-chart-line text-[#C88A42]"></i>
            <span>Ke Laporan Penjualan</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 1. INFORMASI KASIR & TOKO (KARTU GELAP)         --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-[#2A1713] via-[#3D231D] to-[#4A2E2B] rounded-2xl p-6 text-white border border-[#5A3834] shadow-xl relative overflow-hidden">
        {{-- Background icon watermark --}}
        <div class="absolute -right-6 -bottom-6 text-white/[0.04] text-9xl pointer-events-none select-none">
            <i class="fa-solid fa-receipt"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            {{-- Info Toko --}}
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#C88A42] to-[#D9A05B] flex items-center justify-center text-white shadow-md">
                        <i class="fa-solid fa-mug-hot text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold tracking-wide text-white">{{ $storeName }}</h3>
                        <p class="text-xs text-[#D9A05B] font-medium tracking-wider uppercase">Artisan POS & Bar</p>
                    </div>
                </div>
                <div class="text-xs text-[#FAF6F0]/85 flex flex-wrap items-center gap-x-5 gap-y-1 pt-0.5">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-[#D9A05B]"></i>
                        <span>{{ $storeAddress }}</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-[#D9A05B]"></i>
                        <span>{{ $storePhone }}</span>
                    </span>
                </div>
            </div>

            {{-- Info Kasir Bertugas & Waktu --}}
            <div class="bg-white/10 backdrop-blur-xs rounded-xl p-4 border border-white/15 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#D9A05B] text-white flex items-center justify-center font-bold text-base shadow-sm">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-[#D9A05B] font-bold uppercase tracking-wider">Kasir Bertugas:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                Shift Bertugas
                            </span>
                        </div>
                        <p class="text-base font-extrabold text-white mt-0.5">{{ auth()->user()->name ?? 'Kasir Utama' }}</p>
                    </div>
                </div>

                <div class="sm:border-l sm:border-white/15 sm:pl-4 text-xs text-[#FAF6F0]/85">
                    <span class="block text-[11px] text-[#D9A05B] font-semibold">Waktu Settlement:</span>
                    <span class="font-bold text-white">{{ now()->translatedFormat('d F Y') }}</span>
                    <span class="text-[11px] block text-[#FAF6F0]/70">{{ now()->translatedFormat('H:i') }} WIB</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 2. KARTU STATISTIK UTAMA (RINGKASAN)            --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Omset Lunas --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Total Omset Lunas</p>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1">
                    <i class="fa-solid fa-circle-check mr-0.5"></i> Uang masuk hari ini
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#C88A42] to-[#D9A05B] text-white flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">{{ number_format($totalPaidOrders) }}</h3>
                <p class="text-[11px] text-[#C88A42] font-semibold mt-1">
                    <i class="fa-solid fa-receipt mr-0.5"></i> Struk berhasil dibayar
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#FDF6ED] border border-[#EADBCE] text-[#C88A42] flex items-center justify-center text-xl shadow-2xs">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>

        {{-- Produk Terjual --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Produk Terjual</p>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">{{ number_format($totalItemsSold) }}</h3>
                <p class="text-[11px] text-amber-700 font-semibold mt-1">
                    <i class="fa-solid fa-boxes-stacked mr-0.5"></i> Total porsi/cup menu
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center text-xl shadow-2xs">
                <i class="fa-solid fa-utensils"></i>
            </div>
        </div>

        {{-- Rata-Rata Transaksi --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Rata-Rata Transaksi</p>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-blue-600 font-semibold mt-1">
                    <i class="fa-solid fa-scale-balanced mr-0.5"></i> Per pesanan pelanggan
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center text-xl shadow-2xs">
                <i class="fa-solid fa-calculator"></i>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 3. RINCIAN PENERIMAAN BERDASARKAN METODE BAYAR  --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-[#EADBCE] p-6 shadow-xs space-y-4">
        <div>
            <h4 class="text-base font-extrabold text-[#3D231D] flex items-center gap-2">
                <i class="fa-solid fa-cash-register text-[#C88A42]"></i>
                <span>Rincian Penerimaan Berdasarkan Metode Pembayaran</span>
            </h4>
            <p class="text-xs text-[#8A7A75] mt-1">
                Gunakan kolom Tunai untuk mencocokkan jumlah uang fisik di laci kasir (Cash Drawer).
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
            {{-- 1. Tunai --}}
            <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5 uppercase tracking-wide">
                            <i class="fa-solid fa-money-bill-1-wave text-emerald-600"></i> Tunai
                        </span>
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                            {{ $cashCount }} Transaksi
                        </span>
                    </div>
                    <div class="text-xl font-extrabold text-[#3D231D] mt-2.5">
                        Rp {{ number_format($cashTotal, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-[11px] text-emerald-700 font-medium mt-3 pt-2 border-t border-emerald-200/60 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-[10px]"></i>
                    <span>Uang Fisik di Laci Kasir</span>
                </div>
            </div>

            {{-- 2. QRIS --}}
            <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-blue-800 flex items-center gap-1.5 uppercase tracking-wide">
                            <i class="fa-solid fa-qrcode text-blue-600"></i> QRIS
                        </span>
                        <span class="text-[11px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">
                            {{ $qrisCount }} Transaksi
                        </span>
                    </div>
                    <div class="text-xl font-extrabold text-[#3D231D] mt-2.5">
                        Rp {{ number_format($qrisTotal, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-[11px] text-blue-700 font-medium mt-3 pt-2 border-t border-blue-200/60 flex items-center gap-1.5">
                    <i class="fa-solid fa-building-columns text-[10px]"></i>
                    <span>Masuk ke Rekening / E-Wallet</span>
                </div>
            </div>

            {{-- 3. Debit / Kredit --}}
            <div class="p-4 rounded-xl bg-purple-50/70 border border-purple-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-purple-800 flex items-center gap-1.5 uppercase tracking-wide">
                            <i class="fa-solid fa-credit-card text-purple-600"></i> Debit / Kredit
                        </span>
                        <span class="text-[11px] font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full">
                            {{ $cardCount }} Transaksi
                        </span>
                    </div>
                    <div class="text-xl font-extrabold text-[#3D231D] mt-2.5">
                        Rp {{ number_format($cardTotal, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-[11px] text-purple-700 font-medium mt-3 pt-2 border-t border-purple-200/60 flex items-center gap-1.5">
                    <i class="fa-solid fa-building-columns text-[10px]"></i>
                    <span>Masuk ke Rekening / E-Wallet</span>
                </div>
            </div>

            {{-- 4. Transfer --}}
            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5 uppercase tracking-wide">
                            <i class="fa-solid fa-money-bill-transfer text-amber-700"></i> Transfer
                        </span>
                        <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">
                            {{ $transferCount }} Transaksi
                        </span>
                    </div>
                    <div class="text-xl font-extrabold text-[#3D231D] mt-2.5">
                        Rp {{ number_format($transferTotal, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-[11px] text-amber-800 font-medium mt-3 pt-2 border-t border-amber-200/60 flex items-center gap-1.5">
                    <i class="fa-solid fa-building-columns text-[10px]"></i>
                    <span>Masuk ke Rekening / E-Wallet</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 4. RINCIAN MENU YANG BERHASIL TERJUAL HARI INI  --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#F5EDE3] flex items-center justify-between flex-wrap gap-2">
            <div>
                <h4 class="text-base font-extrabold text-[#3D231D] flex items-center gap-2">
                    <i class="fa-solid fa-utensils text-[#C88A42]"></i>
                    <span>Rincian Menu yang Berhasil Terjual Hari Ini</span>
                </h4>
                <p class="text-xs text-[#8A7A75] mt-0.5">
                    Daftar item makanan dan minuman beserta kuantitas dan total nilai penjualan.
                </p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE]">
                {{ $soldMenus->count() }} Jenis Menu
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#FAF6F0] text-[#8A7A75] font-bold uppercase text-[10px] tracking-wider border-b border-[#EADBCE]">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-4">Nama Menu</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4 text-center">Qty Terjual</th>
                        <th class="py-3.5 px-4 text-right">Rata-Rata Harga</th>
                        <th class="py-3.5 px-4 text-right">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5EDE3]">
                    @forelse($soldMenus as $index => $item)
                        @php
                            $avgPrice = $item->total_qty > 0 ? ($item->total_sales / $item->total_qty) : $item->avg_price;
                        @endphp
                        <tr class="hover:bg-[#FAF6F0]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-bold text-[#8A7A75]">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-[#3D231D]">
                                {{ $item->menu->name ?? 'Menu Telah Dihapus' }}
                            </td>
                            <td class="py-3.5 px-4 text-[#8A7A75]">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF6F0] border border-[#EADBCE] text-[#3D231D]">
                                    {{ $item->menu->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center justify-center font-extrabold text-xs px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 border border-amber-200">
                                    {{ $item->total_qty }} porsi
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-[#8A7A75]">
                                Rp {{ number_format($avgPrice, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-[#3D231D]">
                                Rp {{ number_format($item->total_sales, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#8A7A75]">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-[#FAF6F0] border border-[#EADBCE] flex items-center justify-center text-[#D9A05B] text-2xl shadow-2xs">
                                    <i class="fa-solid fa-mug-saucer"></i>
                                </div>
                                <p class="text-xs font-bold text-[#3D231D]">Belum ada produk yang terjual hari ini.</p>
                                <p class="text-[11px] text-[#8A7A75] mt-1">Transaksi penjualan hari ini akan terdata secara otomatis setelah lunas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@if($isPrint)
    @push('scripts')
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>
    @endpush
@endif
@endsection

