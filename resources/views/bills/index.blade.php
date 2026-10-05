@extends('layouts.app')

@section('title', 'Bill Aktif & Tagihan')
@section('page_title', 'Bill Aktif & Pemantauan Meja')
@section('page_subtitle', 'Pantau Pesanan Berjalan, Tagihan Meja, dan Pelunasan')

@section('content')
<div class="space-y-6">

    <!-- STATS OVERVIEW -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-[#EADBCE] shadow-sm">
            <span class="text-[11px] font-bold text-[#8A7A75] uppercase">Total Order Hari Ini</span>
            <h3 class="text-xl font-extrabold text-[#3D231D] mt-1">{{ $stats['total_active'] }}</h3>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#EADBCE] shadow-sm">
            <span class="text-[11px] font-bold text-rose-600 uppercase">Belum Lunas (Pending)</span>
            <h3 class="text-xl font-extrabold text-rose-600 mt-1">{{ $stats['pending_payment'] }}</h3>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#EADBCE] shadow-sm">
            <span class="text-[11px] font-bold text-blue-600 uppercase">Sedang Diproses Dapur</span>
            <h3 class="text-xl font-extrabold text-blue-600 mt-1">{{ $stats['in_kitchen'] }}</h3>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-[#EADBCE] shadow-sm">
            <span class="text-[11px] font-bold text-emerald-600 uppercase">Telah Disajikan</span>
            <h3 class="text-xl font-extrabold text-emerald-600 mt-1">{{ $stats['served_today'] }}</h3>
        </div>
    </div>

    <!-- FILTER TABS & CONTROLS -->
    <div class="bg-white p-4 rounded-2xl border border-[#EADBCE] shadow-sm flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-2 overflow-x-auto">
            <a href="{{ route('bills.index', ['status' => 'all']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'all' ? 'bg-[#3D231D] text-white' : 'bg-[#FAF6F0] text-[#3D231D] hover:bg-[#EADBCE]' }}">
                Semua Bill
            </a>
            <a href="{{ route('bills.index', ['status' => 'unpaid']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'unpaid' ? 'bg-rose-600 text-white' : 'bg-[#FAF6F0] text-rose-700 hover:bg-rose-100' }}">
                Belum Bayar
            </a>
            <a href="{{ route('bills.index', ['status' => 'active_kitchen']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'active_kitchen' ? 'bg-blue-600 text-white' : 'bg-[#FAF6F0] text-blue-700 hover:bg-blue-100' }}">
                Antrean Dapur
            </a>
            <a href="{{ route('bills.index', ['status' => 'dine_in']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'dine_in' ? 'bg-amber-600 text-white' : 'bg-[#FAF6F0] text-amber-800 hover:bg-amber-100' }}">
                Dine In (Per Meja)
            </a>
            <a href="{{ route('bills.index', ['status' => 'take_away']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'take_away' ? 'bg-purple-600 text-white' : 'bg-[#FAF6F0] text-purple-800 hover:bg-purple-100' }}">
                Take Away
            </a>
        </div>

        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-gradient-to-r from-[#D9A05B] to-[#C88A42] hover:brightness-105 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Buat Order Baru</span>
        </a>
    </div>

    <!-- BILLS CARDS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($orders as $order)
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm hover:shadow-md transition-all p-5 flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between pb-3 border-b border-[#EADBCE]">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-[#3D231D]">{{ $order->order_number }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase
                                    {{ $order->order_type === 'dine_in' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                </span>
                            </div>
                            @if($order->cafeTable)
                                <div class="text-xs font-bold text-[#C88A42] mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-chair"></i>
                                    <span>{{ $order->cafeTable->table_number }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="text-right">
                            <span class="text-xs font-extrabold text-[#3D231D] block">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-[#8A7A75]">{{ $order->created_at->format('H:i') }} WIB</span>
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="py-3 space-y-1 text-xs">
                        @foreach($order->orderDetails as $detail)
                            <div class="flex items-center justify-between text-[#3D231D]">
                                <span class="truncate max-w-[200px]">
                                    <strong>{{ $detail->quantity }}x</strong> {{ $detail->menu->name ?? 'Menu' }}
                                </span>
                                <span class="font-semibold text-[11px] text-[#8A7A75]">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Badges & Action -->
                <div class="pt-3 border-t border-[#EADBCE]">
                    <div class="flex items-center justify-between mb-3 text-[11px]">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#8A7A75]">Dapur:</span>
                            <span class="font-bold uppercase 
                                {{ $order->kitchen_status === 'pending' ? 'text-amber-600' : '' }}
                                {{ $order->kitchen_status === 'cooking' ? 'text-blue-600' : '' }}
                                {{ $order->kitchen_status === 'ready' ? 'text-emerald-600' : '' }}
                                {{ $order->kitchen_status === 'served' ? 'text-gray-600' : '' }}">
                                {{ ucfirst($order->kitchen_status) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#8A7A75]">Status:</span>
                            <span class="px-2 py-0.5 rounded font-extrabold text-[10px] uppercase
                                {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($order->payment_status === 'pending')
                            <button type="button" onclick="openPayModal({{ $order->id }}, '{{ $order->order_number }}', {{ $order->total_amount }})"
                                    class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                                <i class="fa-solid fa-money-bill-wave"></i>
                                <span>Lunasi Tagihan</span>
                            </button>
                        @else
                            <div class="flex-1 text-center py-2 bg-[#FAF6F0] rounded-xl text-[11px] text-[#8A7A75] font-semibold truncate border border-[#EADBCE]">
                                <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> {{ strtoupper($order->payment_method) }} • Lunas
                            </div>
                        @endif

                        <button type="button" onclick="showBillReceipt({{ $order->id }})"
                                class="px-3.5 py-2 bg-white hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] text-[#3D231D] rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer" title="Lihat & Cetak Struk">
                            <i class="fa-solid fa-print"></i>
                            <span>Struk</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-[#EADBCE] p-12 text-center text-[#8A7A75]">
                <i class="fa-solid fa-receipt text-3xl mb-2 text-[#D9A05B]"></i>
                <p class="text-xs font-semibold">Tidak ada tagihan/bill aktif untuk filter ini.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Pay Pending Bill -->
<div id="modal-pay-bill" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D]">Pelunasan Tagihan</h3>
            <button type="button" onclick="document.getElementById('modal-pay-bill').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="bg-[#FAF6F0] p-3 rounded-xl border border-[#EADBCE] text-center">
                <span id="pay-order-number" class="text-xs text-[#8A7A75] font-mono">DNC-XXXX</span>
                <h4 id="pay-order-total" class="text-2xl font-black text-[#3D231D] mt-1">Rp 0</h4>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5">Metode Pembayaran:</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="selectPayMethod('cash', this)" class="pay-method-btn active p-2.5 bg-[#3D231D] text-white rounded-xl text-xs font-bold text-center cursor-pointer">Tunai</button>
                    <button type="button" onclick="selectPayMethod('qris', this)" class="pay-method-btn p-2.5 bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] rounded-xl text-xs font-bold text-center cursor-pointer">QRIS</button>
                </div>
            </div>

            <button type="button" onclick="submitBillPayment()" id="btn-submit-pay"
                    class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                Konfirmasi Lunas
            </button>
        </div>
    </div>
</div>

<!-- Modal Print & View Receipt -->
<div id="modal-receipt-bill" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D] flex items-center gap-1.5">
                <i class="fa-solid fa-receipt text-[#C88A42]"></i>
                <span>Struk Pesanan</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-receipt-bill').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Printable Area -->
        <div id="printable-bill-receipt" class="flex-1 overflow-y-auto bg-[#FAF6F0]/40 p-4 rounded-xl border border-[#EADBCE] font-mono text-[11px] text-[#3D231D] space-y-3">
            <div class="text-center border-b border-dashed border-[#8A7A75]/40 pb-3">
                <h4 class="font-extrabold text-sm text-[#3D231D]">{{ $storeSettings['store_name'] ?? "d'nale caffe" }}</h4>
                <p class="text-[10px] text-[#8A7A75]">{{ $storeSettings['store_tagline'] ?? 'Artisan Coffee & Good Vibes' }}</p>
                <p class="text-[10px] text-[#8A7A75]">{{ $storeSettings['store_address'] ?? 'Jl. Boulevard Kopi No. 8, Jakarta' }}</p>
                <p class="text-[10px] text-[#8A7A75]">Telp: {{ $storeSettings['store_phone'] ?? '+62 812-3456-7890' }}</p>
            </div>

            <div class="border-b border-dashed border-[#8A7A75]/40 pb-2 space-y-0.5 text-[10px] text-[#8A7A75]">
                <div class="flex justify-between"><span>No. Order:</span><strong id="rcp-bill-no" class="text-[#3D231D]">-</strong></div>
                <div class="flex justify-between"><span>Tanggal:</span><span id="rcp-bill-date" class="text-[#3D231D]">-</span></div>
                <div class="flex justify-between"><span>Kasir:</span><span id="rcp-bill-cashier" class="text-[#3D231D]">-</span></div>
                <div class="flex justify-between"><span>Tipe / Meja:</span><span id="rcp-bill-table" class="text-[#3D231D]">-</span></div>
            </div>

            <!-- Items Container -->
            <div id="rcp-bill-items" class="space-y-1.5 border-b border-dashed border-[#8A7A75]/40 pb-2">
                <!-- Dynamically generated rows -->
            </div>

            <!-- Totals -->
            <div class="space-y-1 text-[11px]">
                <div class="flex justify-between"><span>Subtotal:</span><span id="rcp-bill-subtotal" class="font-bold">Rp 0</span></div>
                <div id="rcp-bill-discount-row" class="flex justify-between text-rose-600 hidden"><span>Diskon Voucher:</span><span id="rcp-bill-discount">- Rp 0</span></div>
                <div class="flex justify-between text-xs font-black pt-1 border-t border-dashed border-[#8A7A75]/40"><span>TOTAL:</span><span id="rcp-bill-total">Rp 0</span></div>
                <div class="flex justify-between text-[10px] text-[#8A7A75] pt-1"><span>Pembayaran:</span><span id="rcp-bill-method" class="font-bold text-[#3D231D]">CASH (LUNAS)</span></div>
            </div>

            <div class="text-center pt-2 text-[10px] text-[#8A7A75] italic">
                <p>{{ $storeSettings['receipt_footer'] ?? 'Terima kasih atas kunjungan Anda! Follow IG: @dnalecaffe' }}</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 pt-3 border-t border-[#EADBCE] flex items-center gap-2">
            <button type="button" onclick="document.getElementById('modal-receipt-bill').classList.add('hidden')"
                    class="flex-1 py-2.5 bg-[#FAF6F0] hover:bg-[#EADBCE] text-[#3D231D] text-xs font-bold rounded-xl transition-colors cursor-pointer">
                Tutup
            </button>
            <button type="button" onclick="printBillReceipt()"
                    class="flex-1 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ordersMap = {
        @foreach($orders as $o)
            {{ $o->id }}: {
                order_number: "{{ $o->order_number }}",
                date: "{{ $o->created_at->format('d/m/Y H:i') }} WIB",
                cashier: "{{ $o->user->name ?? 'Kasir' }}",
                type_table: "{{ $o->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}{{ $o->cafeTable ? ' • ' . $o->cafeTable->table_number : '' }}",
                subtotal: "Rp {{ number_format($o->subtotal, 0, ',', '.') }}",
                discount_amount: {{ (float)$o->discount_amount }},
                formatted_discount: "Rp {{ number_format($o->discount_amount, 0, ',', '.') }}",
                total_amount: "Rp {{ number_format($o->total_amount, 0, ',', '.') }}",
                payment_method: "{{ strtoupper($o->payment_method) }} ({{ strtoupper($o->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas') }})",
                items: [
                    @foreach($o->orderDetails as $od)
                        {
                            name: "{{ addslashes($od->menu->name ?? 'Menu') }}",
                            quantity: {{ $od->quantity }},
                            subtotal: "Rp {{ number_format($od->subtotal, 0, ',', '.') }}",
                            variants: [
                                @foreach($od->variants as $v)
                                    "{{ addslashes($v->variant_option_name) }}",
                                @endforeach
                            ]
                        },
                    @endforeach
                ]
            },
        @endforeach
    };

    let activePayOrderId = null;
    let selectedPayMethod = 'cash';

    function openPayModal(orderId, orderNumber, total) {
        activePayOrderId = orderId;
        document.getElementById('pay-order-number').textContent = orderNumber;
        document.getElementById('pay-order-total').textContent = 'Rp ' + Number(total).toLocaleString('id-ID');
        document.getElementById('modal-pay-bill').classList.remove('hidden');
    }

    function selectPayMethod(method, btn) {
        selectedPayMethod = method;
        document.querySelectorAll('.pay-method-btn').forEach(b => {
            b.className = 'pay-method-btn p-2.5 bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] rounded-xl text-xs font-bold text-center cursor-pointer';
        });
        btn.className = 'pay-method-btn active p-2.5 bg-[#3D231D] text-white rounded-xl text-xs font-bold text-center cursor-pointer';
    }

    function submitBillPayment() {
        if (!activePayOrderId) return;

        const btn = document.getElementById('btn-submit-pay');
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        fetch(`/bills/${activePayOrderId}/pay`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ payment_method: selectedPayMethod })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Gagal melunasi tagihan.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            alert('Terjadi kesalahan jaringan.');
        });
    }

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
            const vText = it.variants && it.variants.length > 0 ? ` (${it.variants.join(', ')})` : '';
            const row = document.createElement('div');
            row.innerHTML = `
                <div class="flex justify-between">
                    <span class="max-w-[200px] truncate">${it.quantity}x ${it.name}${vText}</span>
                    <span>${it.subtotal}</span>
                </div>
            `;
            itemsEl.appendChild(row);
        });

        document.getElementById('rcp-bill-subtotal').textContent = order.subtotal;
        if (order.discount_amount > 0) {
            document.getElementById('rcp-bill-discount-row').classList.remove('hidden');
            document.getElementById('rcp-bill-discount').textContent = '- ' + order.formatted_discount;
        } else {
            document.getElementById('rcp-bill-discount-row').classList.add('hidden');
        }

        document.getElementById('rcp-bill-total').textContent = order.total_amount;
        document.getElementById('rcp-bill-method').textContent = order.payment_method;

        document.getElementById('modal-receipt-bill').classList.remove('hidden');
    }

    function printBillReceipt() {
        const receiptHtml = document.getElementById('printable-bill-receipt').innerHTML;
        const printWin = window.open('', '', 'width=380,height=600');
        printWin.document.write(`
            <html>
                <head>
                    <title>Struk Pembayaran d'nale caffe</title>
                    <style>
                        body { font-family: monospace; font-size: 12px; margin: 10px; color: #111; }
                        .text-center { text-align: center; }
                        .flex { display: flex; justify-content: space-between; margin-bottom: 3px; }
                        .font-bold, .font-extrabold, .font-black { font-weight: bold; }
                        .border-b { border-bottom: 1px dashed #444; padding-bottom: 5px; margin-bottom: 5px; }
                        .hidden { display: none; }
                        .italic { font-style: italic; }
                    </style>
                </head>
                <body>
                    ${receiptHtml}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.close();
                        };
                    <\/script>
                </body>
            </html>
        `);
        printWin.document.close();
    }
</script>
@endpush
