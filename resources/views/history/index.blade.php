@extends('layouts.app')

@section('title', 'Riwayat Transaksi Hari Ini')
@section('page_title', 'Riwayat Transaksi Hari Ini')
@section('page_subtitle', 'Rekapitulasi pesanan lunas khusus hari ini.')

@section('header_actions')
    <div class="flex items-center gap-2.5">
        {{-- Tombol Lihat Tutup Kasir (Settlement) --}}
        <a href="{{ route('settlement.index') }}"
           class="inline-flex items-center gap-2 bg-white hover:bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] hover:border-[#D9A05B] px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition-all active:scale-95 cursor-pointer">
            <i class="fa-solid fa-wallet text-[#C88A42]"></i>
            <span>Lihat Tutup Kasir (Settlement)</span>
        </a>

        {{-- Tombol Buka Kasir --}}
        <a href="{{ route('pos.index') }}"
           class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition-all active:scale-95 cursor-pointer">
            <i class="fa-solid fa-cash-register text-xs"></i>
            <span>Buka Kasir</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 1. KARTU STATISTIK RINGKASAN (BAGIAN ATAS)      --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Transaksi Lunas (Ikon Nota) --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Total Transaksi Lunas</p>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">{{ number_format($totalPaidOrders) }}</h3>
                <p class="text-[11px] text-[#C88A42] font-semibold mt-1">
                    <i class="fa-solid fa-receipt mr-0.5"></i> Transaksi Berhasil
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#FDF6ED] border border-[#EADBCE] text-[#C88A42] flex items-center justify-center text-xl shadow-2xs">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
        </div>

        {{-- Total Item Terjual (Ikon Cangkir Kopi) --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Total Item Terjual</p>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">{{ number_format($totalItemsSold) }}</h3>
                <p class="text-[11px] text-amber-700 font-semibold mt-1">
                    <i class="fa-solid fa-boxes-stacked mr-0.5"></i> Porsi / Cup Hari Ini
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center text-xl shadow-2xs">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
        </div>

        {{-- Total Omset Hari Ini (Ikon Keuangan/Grafik Hijau) --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] p-5 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider">Total Omset Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1">
                    <i class="fa-solid fa-circle-check mr-0.5"></i> Kas Masuk Lunas
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-xl shadow-2xs">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 2. FILTER PENCARIAN & TABEL                     --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-xs border border-[#EADBCE] p-4">
        <form method="GET" action="{{ route('history.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            {{-- Kolom Pencarian --}}
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#8A7A75]">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari no struk atau nama pelanggan..."
                       class="w-full pl-9 pr-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none transition-all">
            </div>

            {{-- Dropdown Filter Metode Pembayaran --}}
            <div class="w-full md:w-60">
                <select name="payment_method"
                        class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none transition-all">
                    <option value="">Semua Metode Pembayaran</option>
                    <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>

            {{-- Tombol Aksi: Filter (Warna Cokelat Tua) & Reset --}}
            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit"
                        class="flex-1 md:flex-none px-5 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('history.index') }}"
                   class="px-4 py-2.5 bg-white hover:bg-[#FAF6F0] border border-[#EADBCE] hover:border-[#D9A05B] text-[#3D231D] text-xs font-semibold rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                   title="Reset Filter">
                    <i class="fa-solid fa-rotate-left text-xs text-[#C88A42]"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 3. TABEL RIWAYAT TRANSAKSI                      --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#F5EDE3] flex items-center justify-between flex-wrap gap-2">
            <div>
                <h4 class="text-base font-extrabold text-[#3D231D]">Daftar Struk Transaksi Hari Ini</h4>
                <p class="text-xs text-[#8A7A75] mt-0.5">Daftar transaksi penjualan lunas pada tanggal {{ now()->translatedFormat('d F Y') }}</p>
            </div>
            @if(method_exists($orders, 'total'))
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE]">
                    Total: {{ $orders->total() }} Transaksi
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#FAF6F0] text-[#8A7A75] font-bold uppercase text-[10px] tracking-wider border-b border-[#EADBCE]">
                    <tr>
                        <th class="py-3.5 px-4 whitespace-nowrap">NO. STRUK</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">JAM</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">MEJA / TIPE</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">PELANGGAN</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">KASIR</th>
                        <th class="py-3.5 px-4">ITEM PESANAN</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">METODE</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">TOTAL</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">CETAK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5EDE3]">
                    @forelse($orders as $ord)
                        @php
                            $itemsSummary = $ord->orderDetails->map(function($d) {
                                return $d->quantity . 'x ' . ($d->menu->name ?? 'Menu');
                            })->implode(', ');

                            $receiptPayload = [
                                'order_number' => $ord->order_number,
                                'time' => $ord->created_at->format('d/m/Y H:i') . ' WIB',
                                'cashier' => $ord->user->name ?? 'Kasir Utama',
                                'customer' => 'Pelanggan Umum',
                                'order_type' => $ord->order_type === 'dine_in' ? 'Dine In' : 'Take Away',
                                'table' => $ord->cafeTable ? $ord->cafeTable->table_number : null,
                                'payment_method' => strtoupper($ord->payment_method),
                                'subtotal' => 'Rp ' . number_format($ord->subtotal, 0, ',', '.'),
                                'total' => 'Rp ' . number_format($ord->total_amount, 0, ',', '.'),
                                'items' => $ord->orderDetails->map(function($d) {
                                    return [
                                        'qty' => $d->quantity,
                                        'name' => $d->menu->name ?? 'Menu',
                                        'price' => 'Rp ' . number_format($d->subtotal, 0, ',', '.')
                                    ];
                                })
                            ];
                        @endphp
                        <tr class="hover:bg-[#FAF6F0]/60 transition-colors">
                            {{-- NO. STRUK --}}
                            <td class="py-3.5 px-4 font-mono font-bold text-[#3D231D] whitespace-nowrap">
                                #{{ $ord->order_number }}
                            </td>

                            {{-- JAM --}}
                            <td class="py-3.5 px-4 text-[#8A7A75] whitespace-nowrap">
                                <span class="font-semibold text-[#3D231D]">{{ $ord->created_at->format('H:i') }}</span>
                                <span class="text-[10px] text-[#B9A99A] block">WIB</span>
                            </td>

                            {{-- MEJA / TIPE --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-[#3D231D] block">
                                    {{ $ord->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                </span>
                                @if($ord->cafeTable)
                                    <span class="text-[10px] font-semibold text-[#C88A42] block">
                                        <i class="fa-solid fa-chair text-[9px] mr-0.5"></i>Meja: {{ $ord->cafeTable->table_number }}
                                    </span>
                                @endif
                            </td>

                            {{-- PELANGGAN --}}
                            <td class="py-3.5 px-4 whitespace-nowrap font-medium text-[#3D231D]">
                                Pelanggan Umum
                            </td>

                            {{-- KASIR --}}
                            <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-[#3D231D]">
                                {{ $ord->user->name ?? 'Kasir Utama' }}
                            </td>

                            {{-- ITEM PESANAN --}}
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-[11px] text-[#3D231D] font-medium leading-relaxed truncate" title="{{ $itemsSummary }}">
                                    {{ $itemsSummary ?: '-' }}
                                </p>
                            </td>

                            {{-- METODE --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                                    {{ $ord->payment_method === 'cash' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $ord->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}
                                </span>
                            </td>

                            {{-- TOTAL --}}
                            <td class="py-3.5 px-4 text-right font-extrabold text-[#3D231D] whitespace-nowrap">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </td>

                            {{-- CETAK --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button"
                                        onclick="printHistoryReceipt({{ json_encode($receiptPayload) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#FAF6F0] hover:bg-[#3D231D] text-[#3D231D] hover:text-white border border-[#EADBCE] text-xs font-semibold transition-all duration-200 shadow-2xs hover:shadow active:scale-95 cursor-pointer"
                                        title="Cetak Ulang Struk">
                                    <i class="fa-solid fa-print text-[11px] text-[#C88A42]"></i>
                                    <span>Cetak</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-16 text-center text-[#8A7A75]">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-[#FAF6F0] border border-[#EADBCE] flex items-center justify-center text-[#D9A05B] text-2xl shadow-2xs">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <p class="text-sm font-bold text-[#3D231D]">Belum ada transaksi lunas pada hari ini.</p>
                                <p class="text-xs text-[#8A7A75] mt-1">Transaksi yang diselesaikan di Terminal Kasir (POS) akan tercatat otomatis di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders, 'hasPages') && $orders->hasPages())
            <div class="p-4 border-t border-[#F5EDE3] flex justify-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL PREVIEW & CETAK STRUK --}}
<div id="modal-history-receipt" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6 relative">
        <button type="button" onclick="closeHistoryReceiptModal()" class="absolute top-4 right-4 text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div id="history-receipt-content" class="text-xs font-mono text-[#231411] space-y-3">
            <div class="text-center pb-2 border-b border-dashed border-[#8A7A75]">
                <h3 class="font-bold text-sm text-[#3D231D]">d'nale caffe</h3>
                <p class="text-[10px] text-[#8A7A75]">Artisan Coffee & Good Vibes</p>
                <p class="text-[10px] text-[#8A7A75]">Jl. Merdeka No. 12, Bandung</p>
            </div>

            <div class="space-y-1 text-[11px] pb-2 border-b border-dashed border-[#8A7A75]">
                <div class="flex justify-between">
                    <span class="text-[#8A7A75]">No. Struk:</span>
                    <span id="rcp-number" class="font-bold"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8A7A75]">Waktu:</span>
                    <span id="rcp-time"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8A7A75]">Kasir:</span>
                    <span id="rcp-cashier"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8A7A75]">Tipe / Meja:</span>
                    <span id="rcp-type" class="font-bold"></span>
                </div>
            </div>

            <div id="rcp-items" class="space-y-1.5 pb-2 border-b border-dashed border-[#8A7A75] text-[11px]">
            </div>

            <div class="space-y-1 text-[11px] pb-2 border-b border-dashed border-[#8A7A75]">
                <div class="flex justify-between">
                    <span class="text-[#8A7A75]">Subtotal:</span>
                    <span id="rcp-subtotal" class="font-bold"></span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-[#3D231D]">
                    <span>Total Bayar:</span>
                    <span id="rcp-total"></span>
                </div>
                <div class="flex justify-between text-[10px] text-[#8A7A75]">
                    <span>Metode:</span>
                    <span id="rcp-method" class="font-bold uppercase"></span>
                </div>
            </div>

            <div class="text-center text-[10px] text-[#8A7A75] pt-1">
                <p>Terima kasih atas kunjungan Anda!</p>
                <p>Silakan berkunjung kembali</p>
            </div>
        </div>

        <div class="mt-5 pt-3 border-t border-[#F5EDE3] flex items-center gap-2">
            <button type="button" onclick="closeHistoryReceiptModal()" class="flex-1 py-2.5 bg-[#FAF6F0] hover:bg-stone-200 text-[#3D231D] text-xs font-bold rounded-xl transition-colors cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="executePrintReceipt()" class="flex-1 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printHistoryReceipt(data) {
        document.getElementById('rcp-number').textContent = '#' + data.order_number;
        document.getElementById('rcp-time').textContent = data.time;
        document.getElementById('rcp-cashier').textContent = data.cashier;
        document.getElementById('rcp-type').textContent = data.table ? `${data.order_type} (${data.table})` : data.order_type;
        document.getElementById('rcp-subtotal').textContent = data.subtotal;
        document.getElementById('rcp-total').textContent = data.total;
        document.getElementById('rcp-method').textContent = data.payment_method;

        const itemsContainer = document.getElementById('rcp-items');
        itemsContainer.innerHTML = '';
        data.items.forEach(it => {
            const row = document.createElement('div');
            row.className = 'flex justify-between';
            row.innerHTML = `<span>${it.qty}x ${it.name}</span><span class="font-semibold">${it.price}</span>`;
            itemsContainer.appendChild(row);
        });

        document.getElementById('modal-history-receipt').classList.remove('hidden');
    }

    function closeHistoryReceiptModal() {
        document.getElementById('modal-history-receipt').classList.add('hidden');
    }

    function executePrintReceipt() {
        const content = document.getElementById('history-receipt-content');
        if (!content) return;
        const win = window.open('', '_blank', 'width=420,height=650');
        if (!win) {
            window.print();
            return;
        }
        win.document.title = 'Struk Pembayaran - d\'nale caffe';
        const style = win.document.createElement('style');
        style.textContent = 'body{font-family:monospace;font-size:12px;margin:16px;color:#231411;background:#fff}.text-center{text-align:center}.flex{display:flex;justify-content:space-between;margin-bottom:3px}.font-bold{font-weight:bold}.border-b{border-bottom:1px dashed #8A7A75;padding-bottom:6px;margin-bottom:6px}';
        win.document.head.appendChild(style);
        const div = win.document.createElement('div');
        div.innerHTML = content.innerHTML;
        win.document.body.appendChild(div);
        win.document.close();
        win.focus();
        setTimeout(function() {
            win.print();
            win.close();
        }, 300);
    }
</script>
@endpush

