@extends('layouts.app')

@section('title', 'Bill Aktif')
@section('page_title', 'Bill Aktif')
@section('page_subtitle', 'Semua transaksi yang belum selesai di seluruh outlet.')

@section('header_actions')
    <a href="{{ route('pos.index') }}"
       class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all duration-200">
        <i class="fa-solid fa-cash-register"></i>
        <span>Buka Kasir</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ─── KARTU STATISTIK ─── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Card 1: Meja Tersedia --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-xl text-emerald-500"></i>
            </div>
            <div>
                <p class="text-3xl font-black text-[#3D231D] leading-none">
                    {{ $tables->where('status', 'available')->count() }}
                </p>
                <p class="text-xs text-[#8A7A75] font-medium mt-1">Meja tersedia</p>
            </div>
        </div>

        {{-- Card 2: Meja Terisi --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-regular fa-clock text-xl text-gray-400"></i>
            </div>
            <div>
                <p class="text-3xl font-black text-[#3D231D] leading-none">
                    {{ $tables->where('status', 'occupied')->count() }}
                </p>
                <p class="text-xs text-[#8A7A75] font-medium mt-1">Meja terisi</p>
            </div>
        </div>

        {{-- Card 3: Bill Terbuka --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-regular fa-floppy-disk text-xl text-gray-400"></i>
            </div>
            <div>
                <p class="text-3xl font-black text-[#3D231D] leading-none">
                    {{ $stats['pending_payment'] }}
                </p>
                <p class="text-xs text-[#8A7A75] font-medium mt-1">Bill terbuka</p>
            </div>
        </div>
    </div>

    {{-- ─── FILTER TIPE ORDER ─── --}}
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm px-5 py-4">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-xs font-semibold text-[#8A7A75]">Tipe :</span>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('bills.index', ['status' => 'all']) }}"
                   class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all duration-150
                          {{ $statusFilter === 'all' ? 'bg-[#3D231D] text-white shadow-sm' : 'bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] hover:bg-[#EADBCE]' }}">
                    Semua
                </a>
                <a href="{{ route('bills.index', ['status' => 'dine_in']) }}"
                   class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all duration-150
                          {{ $statusFilter === 'dine_in' ? 'bg-[#3D231D] text-white shadow-sm' : 'bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] hover:bg-[#EADBCE]' }}">
                    Dine In
                </a>
                <a href="{{ route('bills.index', ['status' => 'take_away']) }}"
                   class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all duration-150
                          {{ $statusFilter === 'take_away' ? 'bg-[#3D231D] text-white shadow-sm' : 'bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] hover:bg-[#EADBCE]' }}">
                    Take Away
                </a>
            </div>
        </div>
    </div>

    {{-- ─── TABEL DAFTAR BILL AKTIF ─── --}}
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                {{-- Header --}}
                <thead>
                    <tr class="bg-[#FDF6ED] border-b border-[#EADBCE]">
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            No. Struk
                        </th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            Meja / Tipe
                        </th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            Pelanggan
                        </th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            Item
                        </th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            Dibuat
                        </th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            Kasir
                        </th>
                        <th class="px-5 py-3.5 text-right text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            Total Sementara
                        </th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold text-[#3D231D] uppercase tracking-wider whitespace-nowrap">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- Body --}}
                <tbody class="divide-y divide-[#EADBCE]">
                    @forelse($orders as $order)
                        <tr class="hover:bg-[#FAF6F0] transition-colors">
                            {{-- No. Struk --}}
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs font-bold text-[#3D231D]">{{ $order->order_number }}</span>
                                <div class="mt-0.5">
                                    <span class="inline-block text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                                        {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Meja / Tipe --}}
                            <td class="px-5 py-4">
                                @if($order->cafeTable)
                                    <span class="text-xs font-bold text-[#3D231D]">{{ $order->cafeTable->table_number }}</span>
                                    <br>
                                @endif
                                <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-md mt-0.5
                                    {{ $order->order_type === 'dine_in' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                </span>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="px-5 py-4">
                                <span class="text-xs text-[#3D231D]">—</span>
                            </td>

                            {{-- Item --}}
                            <td class="px-5 py-4 text-center">
                                <span class="text-xs font-bold text-[#3D231D]">{{ $order->orderDetails->count() }}</span>
                            </td>

                            {{-- Dibuat --}}
                            <td class="px-5 py-4">
                                <span class="text-xs text-[#3D231D]">{{ $order->created_at->format('H:i') }}</span>
                                <span class="text-[10px] text-[#8A7A75] block">{{ $order->created_at->format('d/m/Y') }}</span>
                            </td>

                            {{-- Kasir --}}
                            <td class="px-5 py-4">
                                <span class="text-xs text-[#3D231D]">{{ $order->user->name ?? '—' }}</span>
                            </td>

                            {{-- Total Sementara --}}
                            <td class="px-5 py-4 text-right">
                                <span class="text-sm font-bold text-[#3D231D]">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($order->payment_status === 'pending')
                                        <button type="button"
                                                onclick="openPayModal({{ $order->id }}, '{{ $order->order_number }}', {{ $order->total_amount }})"
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap">
                                            Lunasi
                                        </button>
                                    @endif
                                    <button type="button"
                                            onclick="showBillReceipt({{ $order->id }})"
                                            class="px-3 py-1.5 bg-white hover:bg-[#FDF6ED] border border-[#EADBCE] text-[#3D231D] rounded-lg text-[11px] font-bold transition-colors cursor-pointer"
                                            title="Lihat Struk">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-[#B0A090]">
                                    <i class="fa-regular fa-receipt text-4xl opacity-40"></i>
                                    <p class="text-sm font-medium">Tidak ada bill aktif. Semua transaksi sudah selesai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ─── MODAL LUNASI TAGIHAN ─── --}}
<div id="modal-pay-bill" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D]">Pelunasan Tagihan</h3>
            <button type="button" onclick="closePayModal()" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div class="bg-[#FAF6F0] p-3 rounded-xl border border-[#EADBCE] text-center">
                <span id="pay-order-number" class="text-xs text-[#8A7A75] font-mono">—</span>
                <h4 id="pay-order-total" class="text-2xl font-black text-[#3D231D] mt-1">Rp 0</h4>
            </div>
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5">Metode Pembayaran:</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="selectPayMethod('cash', this)"
                            class="pay-method-btn p-2.5 bg-[#3D231D] text-white rounded-xl text-xs font-bold text-center cursor-pointer">Tunai</button>
                    <button type="button" onclick="selectPayMethod('qris', this)"
                            class="pay-method-btn p-2.5 bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] rounded-xl text-xs font-bold text-center cursor-pointer">QRIS</button>
                </div>
            </div>
            <button type="button" onclick="submitBillPayment()" id="btn-submit-pay"
                    class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm cursor-pointer transition-colors">
                Konfirmasi Lunas
            </button>
        </div>
    </div>
</div>

{{-- ─── MODAL STRUK ─── --}}
<div id="modal-receipt-bill" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D] flex items-center gap-1.5">
                <i class="fa-solid fa-receipt text-[#C88A42]"></i>
                <span>Struk Pesanan</span>
            </h3>
            <button type="button" onclick="closeReceiptModal()" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div id="printable-bill-receipt" class="flex-1 overflow-y-auto bg-[#FAF6F0]/40 p-4 rounded-xl border border-[#EADBCE] font-mono text-[11px] text-[#3D231D] space-y-3">
            <div class="text-center border-b border-dashed border-[#8A7A75]/40 pb-3">
                <h4 class="font-extrabold text-sm">{{ $storeSettings['store_name'] ?? "d'nale caffe" }}</h4>
                <p class="text-[10px] text-[#8A7A75]">{{ $storeSettings['store_tagline'] ?? 'Artisan Coffee & Good Vibes' }}</p>
                <p class="text-[10px] text-[#8A7A75]">{{ $storeSettings['store_address'] ?? 'Jl. Boulevard Kopi No. 8' }}</p>
            </div>
            <div class="border-b border-dashed border-[#8A7A75]/40 pb-2 space-y-0.5 text-[10px] text-[#8A7A75]">
                <div class="flex justify-between"><span>No. Order:</span><strong id="rcp-bill-no" class="text-[#3D231D]">-</strong></div>
                <div class="flex justify-between"><span>Tanggal:</span><span id="rcp-bill-date" class="text-[#3D231D]">-</span></div>
                <div class="flex justify-between"><span>Kasir:</span><span id="rcp-bill-cashier" class="text-[#3D231D]">-</span></div>
                <div class="flex justify-between"><span>Tipe / Meja:</span><span id="rcp-bill-table" class="text-[#3D231D]">-</span></div>
            </div>
            <div id="rcp-bill-items" class="space-y-1.5 border-b border-dashed border-[#8A7A75]/40 pb-2"></div>
            <div class="space-y-1 text-[11px]">
                <div class="flex justify-between"><span>Subtotal:</span><span id="rcp-bill-subtotal" class="font-bold">Rp 0</span></div>
                <div id="rcp-bill-discount-row" class="flex justify-between text-rose-600 hidden"><span>Diskon:</span><span id="rcp-bill-discount">- Rp 0</span></div>
                <div class="flex justify-between text-xs font-black pt-1 border-t border-dashed border-[#8A7A75]/40">
                    <span>TOTAL:</span><span id="rcp-bill-total">Rp 0</span>
                </div>
                <div class="flex justify-between text-[10px] text-[#8A7A75] pt-1"><span>Pembayaran:</span><span id="rcp-bill-method" class="font-bold text-[#3D231D]">—</span></div>
            </div>
            <div class="text-center pt-2 text-[10px] text-[#8A7A75] italic">
                <p>{{ $storeSettings['receipt_footer'] ?? 'Terima kasih atas kunjungan Anda!' }}</p>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-[#EADBCE] flex items-center gap-2">
            <button type="button" onclick="closeReceiptModal()"
                    class="flex-1 py-2.5 bg-[#FAF6F0] hover:bg-[#EADBCE] text-[#3D231D] text-xs font-bold rounded-xl transition-colors cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="printBillReceipt()"
                    class="flex-1 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Order data map untuk modal struk ──
    const ordersMap = {
        @foreach($orders as $o)
        {{ $o->id }}: {
            order_number: "{{ $o->order_number }}",
            date: "{{ $o->created_at->format('d/m/Y H:i') }} WIB",
            cashier: "{{ addslashes($o->user->name ?? 'Kasir') }}",
            type_table: "{{ $o->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}{{ $o->cafeTable ? ' • ' . $o->cafeTable->table_number : '' }}",
            subtotal: "Rp {{ number_format($o->subtotal, 0, ',', '.') }}",
            discount_amount: {{ (float)$o->discount_amount }},
            formatted_discount: "Rp {{ number_format($o->discount_amount, 0, ',', '.') }}",
            total_amount: "Rp {{ number_format($o->total_amount, 0, ',', '.') }}",
            payment_method: "{{ strtoupper($o->payment_method) }} ({{ $o->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }})",
            items: [
                @foreach($o->orderDetails as $od)
                {
                    name: "{{ addslashes($od->menu->name ?? 'Menu') }}",
                    quantity: {{ $od->quantity }},
                    subtotal: "Rp {{ number_format($od->subtotal, 0, ',', '.') }}",
                    variants: [@foreach($od->variants as $v)"{{ addslashes($v->variant_option_name) }}",@endforeach]
                },
                @endforeach
            ]
        },
        @endforeach
    };

    // ── State ──
    let activePayOrderId = null;
    let selectedPayMethod = 'cash';

    // ── Modal: Lunasi ──
    function openPayModal(orderId, orderNumber, total) {
        activePayOrderId = orderId;
        document.getElementById('pay-order-number').textContent = orderNumber;
        document.getElementById('pay-order-total').textContent = 'Rp ' + Number(total).toLocaleString('id-ID');
        document.getElementById('modal-pay-bill').style.display = 'flex';
    }
    function closePayModal() {
        document.getElementById('modal-pay-bill').style.display = 'none';
    }

    function selectPayMethod(method, btn) {
        selectedPayMethod = method;
        document.querySelectorAll('.pay-method-btn').forEach(b => {
            b.className = 'pay-method-btn p-2.5 bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] rounded-xl text-xs font-bold text-center cursor-pointer';
        });
        btn.className = 'pay-method-btn p-2.5 bg-[#3D231D] text-white rounded-xl text-xs font-bold text-center cursor-pointer';
    }

    function submitBillPayment() {
        if (!activePayOrderId) return;
        const btn = document.getElementById('btn-submit-pay');
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        fetch(`/bills/${activePayOrderId}/pay`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ payment_method: selectedPayMethod })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.textContent = 'Konfirmasi Lunas';
            if (data.success) {
                closePayModal();
                window.location.reload();
            } else {
                alert(data.message || 'Gagal melunasi tagihan.');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = 'Konfirmasi Lunas';
            alert('Terjadi kesalahan jaringan.');
        });
    }

    // ── Modal: Struk ──
    function showBillReceipt(orderId) {
        const order = ordersMap[orderId];
        if (!order) return;

        document.getElementById('rcp-bill-no').textContent = order.order_number;
        document.getElementById('rcp-bill-date').textContent = order.date;
        document.getElementById('rcp-bill-cashier').textContent = order.cashier;
        document.getElementById('rcp-bill-table').textContent = order.type_table;

        const itemsEl = document.getElementById('rcp-bill-items');
        itemsEl.innerHTML = '';
        order.items.forEach(it => {
            const vText = it.variants.length > 0 ? ` (${it.variants.join(', ')})` : '';
            const row = document.createElement('div');
            row.className = 'flex justify-between';
            row.innerHTML = `<span class="max-w-[200px] truncate">${it.quantity}x ${it.name}${vText}</span><span>${it.subtotal}</span>`;
            itemsEl.appendChild(row);
        });

        document.getElementById('rcp-bill-subtotal').textContent = order.subtotal;
        const discRow = document.getElementById('rcp-bill-discount-row');
        if (order.discount_amount > 0) {
            discRow.classList.remove('hidden');
            document.getElementById('rcp-bill-discount').textContent = '- ' + order.formatted_discount;
        } else {
            discRow.classList.add('hidden');
        }
        document.getElementById('rcp-bill-total').textContent = order.total_amount;
        document.getElementById('rcp-bill-method').textContent = order.payment_method;

        document.getElementById('modal-receipt-bill').style.display = 'flex';
    }
    function closeReceiptModal() {
        document.getElementById('modal-receipt-bill').style.display = 'none';
    }

    function printBillReceipt() {
        const html = document.getElementById('printable-bill-receipt').innerHTML;
        const w = window.open('', '', 'width=380,height=600');
        w.document.write(`<!DOCTYPE html><html><head><title>Struk d'nale caffe</title>
            <style>body{font-family:monospace;font-size:12px;margin:10px;color:#111}
            .text-center{text-align:center}.flex{display:flex;justify-content:space-between;margin-bottom:3px}
            .font-bold,.font-extrabold,.font-black{font-weight:bold}
            .border-b{border-bottom:1px dashed #aaa;padding-bottom:5px;margin-bottom:5px}
            .hidden{display:none}.italic{font-style:italic}</style></head>
            <body>${html}<script>window.onload=function(){window.print();window.close()}<\/script></body></html>`);
        w.document.close();
    }
</script>
@endpush
