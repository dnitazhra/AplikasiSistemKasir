@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan Penjualan')
@section('page_subtitle', 'Analitik Pendapatan, Transaksi, dan Kinerja Penjualan Kafe')

@section('header_actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.index', array_merge(request()->query(), ['print' => 1])) }}" target="_blank"
           class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition-all active:scale-95">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Laporan</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ─────────────────────────────────────────────── --}}
    {{-- FILTER WAKTU & PARAMETER                        --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-dnale border border-[#EADBCE] p-5 print:hidden">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
            {{-- Quick Presets --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <span class="text-[#8A7A75] font-semibold flex-shrink-0 mr-1">Periode Cepat:</span>
                <a href="{{ route('reports.index', ['preset' => 'today']) }}"
                   class="px-3.5 py-1.5 rounded-xl font-bold transition-all flex-shrink-0 {{ $preset === 'today' ? 'bg-[#D9A05B] text-white shadow-xs' : 'bg-[#FAF6F0] text-[#3D231D] hover:bg-[#F0E6D8]' }}">
                    Hari Ini
                </a>
                <a href="{{ route('reports.index', ['preset' => 'yesterday']) }}"
                   class="px-3.5 py-1.5 rounded-xl font-bold transition-all flex-shrink-0 {{ $preset === 'yesterday' ? 'bg-[#D9A05B] text-white shadow-xs' : 'bg-[#FAF6F0] text-[#3D231D] hover:bg-[#F0E6D8]' }}">
                    Kemarin
                </a>
                <a href="{{ route('reports.index', ['preset' => '7_days']) }}"
                   class="px-3.5 py-1.5 rounded-xl font-bold transition-all flex-shrink-0 {{ $preset === '7_days' ? 'bg-[#D9A05B] text-white shadow-xs' : 'bg-[#FAF6F0] text-[#3D231D] hover:bg-[#F0E6D8]' }}">
                    7 Hari Terakhir
                </a>
                <a href="{{ route('reports.index', ['preset' => 'this_month']) }}"
                   class="px-3.5 py-1.5 rounded-xl font-bold transition-all flex-shrink-0 {{ $preset === 'this_month' ? 'bg-[#D9A05B] text-white shadow-xs' : 'bg-[#FAF6F0] text-[#3D231D] hover:bg-[#F0E6D8]' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('reports.index', ['preset' => 'all_time']) }}"
                   class="px-3.5 py-1.5 rounded-xl font-bold transition-all flex-shrink-0 {{ $preset === 'all_time' ? 'bg-[#D9A05B] text-white shadow-xs' : 'bg-[#FAF6F0] text-[#3D231D] hover:bg-[#F0E6D8]' }}">
                    Semua Data
                </a>
            </div>

            {{-- Custom Filters Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2 border-t border-[#F5EDE3]">
                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                           class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                           class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1">Metode Bayar</label>
                    <select name="payment_method" class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai / Cash</option>
                        <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1">Tipe Pesanan</label>
                    <select name="order_type" class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="dine_in" {{ $orderType === 'dine_in' ? 'selected' : '' }}>Dine In (Makan di Tempat)</option>
                        <option value="take_away" {{ $orderType === 'take_away' ? 'selected' : '' }}>Take Away (Bungkus)</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter text-[10px]"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('reports.index') }}" class="px-3 py-2 bg-white hover:bg-[#FAF6F0] border border-[#EADBCE] text-[#3D231D] text-xs font-semibold rounded-xl transition-colors" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left text-xs text-[#C88A42]"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Info banner cetak / rentang aktif --}}
    <div class="flex items-center justify-between text-xs text-[#8A7A75] px-1">
        <div>
            Rentang Periode Laporan: 
            <strong class="text-[#3D231D]">{{ $startDate->translatedFormat('d F Y') }}</strong> s/d 
            <strong class="text-[#3D231D]">{{ $endDate->translatedFormat('d F Y') }}</strong>
        </div>
        <div class="print:block hidden text-right">
            Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB
        </div>
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- RINGKASAN METRIK UTAMA (KPI CARDS)             --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- 1. Total Pendapatan --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-dnale flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Total Omzet</p>
                <h3 class="text-xl font-extrabold text-[#3D231D] mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-emerald-600 font-semibold mt-1">
                    <i class="fa-solid fa-circle-check mr-0.5"></i> Transaksi Lunas
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#C88A42] to-[#D9A05B] text-white flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        {{-- 2. Total Transaksi --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-dnale flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-xl font-extrabold text-[#3D231D] mt-1">{{ number_format($totalPaidOrders) }} <span class="text-xs font-normal text-[#8A7A75]">struk</span></h3>
                <p class="text-[10px] text-[#C88A42] font-semibold mt-1">
                    <i class="fa-solid fa-receipt mr-0.5"></i> Pesanan Berhasil
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#FDF6ED] border border-[#EADBCE] text-[#C88A42] flex items-center justify-center text-xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>

        {{-- 3. Rata-rata Transaksi (AOV) --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-dnale flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Rata-rata Transaksi</p>
                <h3 class="text-xl font-extrabold text-[#3D231D] mt-1">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-[#8A7A75] font-semibold mt-1">Per Struk Penjualan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>

        {{-- 4. Total Diskon Diberikan --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-dnale flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Potongan Promo</p>
                <h3 class="text-xl font-extrabold text-[#3D231D] mt-1">Rp {{ number_format($totalDiscountGiven, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-rose-500 font-semibold mt-1">Voucher Diskon Kasir</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 text-rose-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- BREAKDOWN METODE PEMBAYARAN & MENU TERLARIS    --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Breakdown Metode & Tipe Pesanan --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-6 shadow-dnale space-y-6">
            <div>
                <h4 class="text-sm font-bold text-[#3D231D] flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-credit-card text-[#C88A42]"></i>
                    <span>Kanal Pembayaran</span>
                </h4>
                
                <div class="space-y-4">
                    {{-- Cash --}}
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="flex items-center gap-1.5 text-[#3D231D]">
                                <i class="fa-solid fa-money-bill text-emerald-600"></i> Tunai (Cash)
                            </span>
                            <span class="text-[#3D231D]">Rp {{ number_format($cashRevenue, 0, ',', '.') }} <span class="text-[10px] text-[#8A7A75]">({{ $cashOrdersCount }}x)</span></span>
                        </div>
                        <div class="w-full h-2.5 bg-[#FAF6F0] rounded-full overflow-hidden border border-[#EADBCE]">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $totalRevenue > 0 ? ($cashRevenue / $totalRevenue * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- QRIS --}}
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="flex items-center gap-1.5 text-[#3D231D]">
                                <i class="fa-solid fa-qrcode text-blue-600"></i> QRIS Dinamis
                            </span>
                            <span class="text-[#3D231D]">Rp {{ number_format($qrisRevenue, 0, ',', '.') }} <span class="text-[10px] text-[#8A7A75]">({{ $qrisOrdersCount }}x)</span></span>
                        </div>
                        <div class="w-full h-2.5 bg-[#FAF6F0] rounded-full overflow-hidden border border-[#EADBCE]">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $totalRevenue > 0 ? ($qrisRevenue / $totalRevenue * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-[#F5EDE3]">
                <h4 class="text-sm font-bold text-[#3D231D] flex items-center gap-2 mb-3">
                    <i class="fa-solid fa-utensils text-[#C88A42]"></i>
                    <span>Tipe Pesanan</span>
                </h4>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-[#FAF6F0] p-3 rounded-xl border border-[#EADBCE]">
                        <span class="text-[11px] font-bold text-[#8A7A75] block">Dine In (Di Kafe)</span>
                        <strong class="text-base text-[#3D231D] block mt-0.5">{{ $dineInCount }} Pesanan</strong>
                    </div>
                    <div class="bg-[#FAF6F0] p-3 rounded-xl border border-[#EADBCE]">
                        <span class="text-[11px] font-bold text-[#8A7A75] block">Take Away</span>
                        <strong class="text-base text-[#3D231D] block mt-0.5">{{ $takeAwayCount }} Pesanan</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Menu Terlaris dalam Periode --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#EADBCE] p-6 shadow-dnale">
            <h4 class="text-sm font-bold text-[#3D231D] flex items-center justify-between mb-4">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-trophy text-[#D9A05B]"></i>
                    <span>5 Menu Terlaris Periode Ini</span>
                </span>
                <span class="text-[11px] font-medium text-[#8A7A75]">Berdasarkan Kuantitas Terjual</span>
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#F5EDE3] text-[#8A7A75] font-bold uppercase text-[10px]">
                            <th class="pb-2.5">Peringkat</th>
                            <th class="pb-2.5">Menu</th>
                            <th class="pb-2.5">Kategori</th>
                            <th class="pb-2.5 text-center">Terjual</th>
                            <th class="pb-2.5 text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5EDE3]">
                        @forelse($topMenus as $idx => $item)
                            <tr>
                                <td class="py-3 font-bold text-[#3D231D]">
                                    <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs
                                        {{ $idx === 0 ? 'bg-amber-100 text-amber-800 font-extrabold' : ($idx === 1 ? 'bg-slate-200 text-slate-700' : ($idx === 2 ? 'bg-amber-50 text-amber-900' : 'bg-stone-100 text-stone-600')) }}">
                                        {{ $idx + 1 }}
                                    </span>
                                </td>
                                <td class="py-3 font-bold text-[#3D231D]">
                                    {{ $item->menu->name ?? 'Menu Telah Dihapus' }}
                                </td>
                                <td class="py-3 text-[#8A7A75]">
                                    {{ $item->menu->category->name ?? '-' }}
                                </td>
                                <td class="py-3 text-center font-extrabold text-[#C88A42]">
                                    {{ $item->total_sold }} porsi
                                </td>
                                <td class="py-3 text-right font-bold text-[#3D231D]">
                                    Rp {{ number_format($item->total_sales, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-[#8A7A75]">Belum ada penjualan menu pada periode yang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- TABEL DAFTAR TRANSAKSI LENGKAP                 --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-dnale overflow-hidden">
        <div class="p-5 border-b border-[#F5EDE3] flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-[#3D231D]">Rincian Transaksi Penjualan</h4>
                <p class="text-xs text-[#8A7A75] mt-0.5">Daftar struk transaksi pada rentang periode yang difilter</p>
            </div>
            @if(!$isPrint && method_exists($orders, 'total'))
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE]">
                    Total: {{ $orders->total() }} Transaksi
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#FAF6F0] text-[#8A7A75] font-bold uppercase text-[10px] tracking-wider border-b border-[#EADBCE]">
                    <tr>
                        <th class="py-3 px-4">No. Order</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4">Tipe / Meja</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                        <th class="py-3 px-4 text-right">Diskon</th>
                        <th class="py-3 px-4 text-right">Total Bayar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5EDE3]">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-[#FAF6F0]/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-[#3D231D]">
                                #{{ $ord->order_number }}
                            </td>
                            <td class="py-3.5 px-4 text-[#8A7A75]">
                                <div>{{ $ord->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-[#B9A99A]">{{ $ord->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-[#3D231D]">
                                {{ $ord->user->name ?? 'Kasir' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-[#3D231D]">
                                    {{ $ord->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                </span>
                                @if($ord->cafeTable)
                                    <span class="text-[10px] text-[#C88A42] block">Meja: {{ $ord->cafeTable->table_number }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-semibold uppercase text-[11px] text-[#3D231D]">
                                <span class="px-2 py-0.5 rounded-md {{ $ord->payment_method === 'cash' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $ord->payment_method }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-[#8A7A75]">
                                Rp {{ number_format($ord->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-rose-500">
                                {{ $ord->discount_amount > 0 ? '-Rp ' . number_format($ord->discount_amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-[#3D231D]">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $ord->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($ord->payment_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $ord->payment_status === 'paid' ? 'Lunas' : $ord->payment_status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-[#8A7A75]">
                                <i class="fa-solid fa-file-invoice text-3xl mb-2 text-[#D9A05B]"></i>
                                <p class="text-xs font-semibold">Tidak ada transaksi ditemukan pada kriteria dan rentang tanggal ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!$isPrint && method_exists($orders, 'hasPages') && $orders->hasPages())
            <div class="p-4 border-t border-[#F5EDE3] flex justify-center print:hidden">
                {{ $orders->links() }}
            </div>
        @endif
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
