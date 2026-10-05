@extends('layouts.app')

@section('title', 'Terminal POS')
@section('page_title', 'Terminal Kasir (POS)')
@section('page_subtitle', 'Pencatatan Pesanan & Pembayaran Cepat')

@section('content')
<div class="h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-5">

    <!-- LEFT SECTION: MENU CATALOG & FILTERS (FLEX-1) -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-5 overflow-hidden">
        
        <!-- TOP CONTROLS: ORDER TYPE, TABLE SELECTOR, LIVE SEARCH -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-4 border-b border-[#EADBCE]">
            
            <!-- Order Type Switcher -->
            <div class="inline-flex p-1 bg-[#FAF6F0] rounded-xl border border-[#EADBCE]">
                <button type="button" id="btn-type-dine-in" onclick="setOrderType('dine_in')"
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 bg-[#3D231D] text-white shadow-xs">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Dine In</span>
                </button>
                <button type="button" id="btn-type-take-away" onclick="setOrderType('take_away')"
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-[#8A7A75] hover:text-[#3D231D]">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>Take Away</span>
                </button>
            </div>

            <!-- Table Selector (Visible if Dine In) -->
            <div id="table-select-wrapper" class="flex items-center gap-2">
                <span class="text-xs font-semibold text-[#8A7A75] whitespace-nowrap"><i class="fa-solid fa-chair text-[#C88A42] mr-1"></i>Pilih Meja:</span>
                <select id="select-table" class="bg-[#FAF6F0] border border-[#EADBCE] rounded-xl px-3 py-2 text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                    <option value="">-- Pilih Nomor Meja --</option>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}" 
                                data-status="{{ $table->status }}" 
                                {{ request('table') == $table->id ? 'selected' : '' }}
                                {{ $table->status !== 'available' ? 'class=text-rose-600' : '' }}>
                            {{ $table->table_number }} (Kapasitas: {{ $table->capacity }}) - {{ $table->status === 'available' ? 'Tersedia' : ($table->status === 'occupied' ? 'Terisi' : 'Dipesan') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Live Search Input -->
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#8A7A75]"></i>
                <input type="text" id="pos-search" oninput="filterMenus()"
                       placeholder="Cari kopi, pastry, makanan..." 
                       class="w-full pl-9 pr-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>
        </div>

        <!-- CATEGORIES FILTER TABS -->
        <div class="py-3 flex items-center gap-2 overflow-x-auto no-scrollbar border-b border-[#EADBCE]">
            <button type="button" onclick="filterCategory('all', this)"
                    class="cat-pill active px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all bg-[#D9A05B] text-white shadow-xs">
                Semua Kategori ({{ $menus->count() }})
            </button>
            @foreach($categories as $cat)
                <button type="button" onclick="filterCategory('{{ $cat->id }}', this)"
                        class="cat-pill px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all bg-[#FAF6F0] text-[#3D231D] hover:bg-[#EADBCE]">
                    {{ $cat->name }} ({{ $cat->menus_count }})
                </button>
            @endforeach
        </div>

        <!-- MENU ITEMS GRID (SCROLLABLE) -->
        <div class="flex-1 overflow-y-auto pt-4 pr-1">
            <div id="menus-container" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">
                @foreach($menus as $menu)
                    <div class="menu-card bg-[#FAF6F0]/60 hover:bg-white border border-[#EADBCE] hover:border-[#D9A05B] rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-200 hover:shadow-md cursor-pointer group"
                         data-category="{{ $menu->category_id }}"
                         data-name="{{ strtolower($menu->name) }}"
                         onclick="openMenuModal({{ $menu->id }})">
                        
                        <div>
                            <!-- Header Item & Badge -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white border border-[#EADBCE] text-[#8A7A75]">
                                    {{ $menu->category->name ?? 'Menu' }}
                                </span>
                                @if($menu->variants->isNotEmpty())
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#D9A05B]/15 text-[#C88A42]">
                                        +Variasi
                                    </span>
                                @endif
                            </div>

                            <!-- Icon / Visual Thumbnail -->
                            <div class="w-full h-24 rounded-xl bg-white border border-[#EADBCE]/50 flex items-center justify-center text-[#D9A05B] group-hover:scale-105 transition-transform duration-200 mb-3 shadow-xs overflow-hidden">
                                @if($menu->image && file_exists(public_path($menu->image)))
                                    <img src="{{ asset($menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                @elseif(str_contains(strtolower($menu->name), 'croissant') || str_contains(strtolower($menu->name), 'roll'))
                                    <i class="fa-solid fa-bread-slice text-3xl text-amber-600"></i>
                                @elseif(str_contains(strtolower($menu->name), 'fries') || str_contains(strtolower($menu->name), 'nasi') || str_contains(strtolower($menu->name), 'spaghetti'))
                                    <i class="fa-solid fa-bowl-food text-3xl text-amber-700"></i>
                                @elseif(str_contains(strtolower($menu->name), 'tea') || str_contains(strtolower($menu->name), 'matcha'))
                                    <i class="fa-solid fa-leaf text-3xl text-emerald-600"></i>
                                @else
                                    <i class="fa-solid fa-mug-hot text-3xl text-[#8A5A44]"></i>
                                @endif
                            </div>

                            <h4 class="text-xs font-bold text-[#3D231D] leading-snug line-clamp-1">{{ $menu->name }}</h4>
                            <p class="text-[10px] text-[#8A7A75] line-clamp-2 mt-0.5 leading-tight">
                                {{ $menu->description ?? 'Nikmat dan segar disajikan dari d\'nale caffe.' }}
                            </p>
                        </div>

                        <div class="mt-3 pt-2 border-t border-[#EADBCE]/60 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-[#3D231D]">{{ $menu->formatted_price }}</span>
                            <span class="w-7 h-7 rounded-lg bg-[#3D231D] group-hover:bg-[#D9A05B] text-white flex items-center justify-center text-xs shadow-xs transition-colors">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div id="no-menus-message" class="hidden text-center py-12 text-[#8A7A75]">
                <i class="fa-solid fa-mug-saucer text-3xl mb-2 text-[#D9A05B]"></i>
                <p class="text-xs font-semibold">Tidak ditemukan menu yang sesuai pencarian.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SECTION: INTERACTIVE CART & BILLING PANEL (WIDTH: 420px) -->
    <div class="w-full lg:w-[420px] flex-shrink-0 flex flex-col bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-5 overflow-hidden">
        
        <!-- CART HEADER -->
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE]">
            <div>
                <h3 class="text-sm font-bold text-[#3D231D] flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-[#C88A42]"></i>
                    <span>Keranjang Pesanan</span>
                </h3>
                <span id="cart-item-count" class="text-[11px] text-[#8A7A75]">0 item dipilih</span>
            </div>

            <button type="button" onclick="clearCart()" class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-trash-can"></i>
                <span>Reset</span>
            </button>
        </div>

        <!-- CART ITEMS LIST (SCROLLABLE) -->
        <div id="cart-list" class="flex-1 overflow-y-auto py-3 space-y-2.5 pr-1">
            <!-- Empty state -->
            <div id="cart-empty" class="text-center py-14 text-[#8A7A75]">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-[#FAF6F0] flex items-center justify-center text-[#D9A05B] text-2xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <p class="text-xs font-semibold text-[#3D231D]">Keranjang Masih Kosong</p>
                <p class="text-[11px] text-[#8A7A75] mt-0.5">Pilih menu di sebelah kiri untuk menambahkan pesanan.</p>
            </div>
        </div>

        <!-- BILLING CALCULATIONS -->
        <div class="pt-3 border-t border-[#EADBCE] space-y-3">
            <!-- Price Breakdown (Subtotal & Total Pembayaran) -->
            <div class="bg-[#FAF6F0] p-3.5 rounded-xl border border-[#EADBCE] space-y-2 text-xs">
                <div class="flex justify-between text-[#8A7A75]">
                    <span>Subtotal</span>
                    <span id="calc-subtotal" class="font-bold text-[#3D231D]">Rp 0</span>
                </div>
                <div class="flex justify-between text-[#8A7A75] border-t border-[#EADBCE]/60 pt-2">
                    <span class="text-xs font-bold text-[#3D231D]">Total Pembayaran</span>
                    <span id="calc-total" class="text-base font-extrabold text-[#3D231D]">Rp 0</span>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button type="button" onclick="processCheckout('cash')" id="btn-pay-cash"
                        class="py-3 px-3 rounded-xl bg-[#3D231D] hover:bg-[#2A1713] text-white font-bold text-xs shadow-sm flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-money-bill-wave text-amber-400"></i>
                    <span>Tunai (Cash)</span>
                </button>

                <button type="button" onclick="processCheckout('qris')" id="btn-pay-qris"
                        class="py-3 px-3 rounded-xl bg-gradient-to-r from-[#D9A05B] to-[#C88A42] hover:brightness-105 text-white font-bold text-xs shadow-sm flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-qrcode"></i>
                    <span>Bayar QRIS</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL 1: CUSTOMIZE MENU VARIANTS & NOTES       -->
<!-- ============================================== -->
<div id="modal-menu" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-[#EADBCE] shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <div class="p-5 border-b border-[#EADBCE] flex items-center justify-between bg-[#FAF6F0]">
            <div>
                <h3 id="modal-menu-name" class="text-base font-bold text-[#3D231D]">Nama Menu</h3>
                <span id="modal-menu-price" class="text-xs font-extrabold text-[#C88A42]">Rp 0</span>
            </div>
            <button type="button" onclick="closeMenuModal()" class="w-8 h-8 rounded-full bg-white text-[#8A7A75] hover:text-[#3D231D] flex items-center justify-center border border-[#EADBCE]">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-5 max-h-[60vh] overflow-y-auto space-y-4">
            <!-- Dynamic Variants Container -->
            <div id="modal-variants-container" class="space-y-4"></div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">Catatan Tambahan (Opsional):</label>
                <input type="text" id="modal-menu-notes" placeholder="Contoh: Less ice, jangan pakai sedotan..."
                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
            </div>

            <!-- Quantity Selector -->
            <div class="flex items-center justify-between pt-2">
                <span class="text-xs font-bold text-[#3D231D]">Jumlah Pesanan:</span>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="modalChangeQty(-1)" class="w-8 h-8 rounded-lg bg-[#FAF6F0] border border-[#EADBCE] font-bold text-[#3D231D] hover:bg-[#D9A05B] hover:text-white flex items-center justify-center">-</button>
                    <span id="modal-qty" class="text-sm font-extrabold text-[#3D231D] w-6 text-center">1</span>
                    <button type="button" onclick="modalChangeQty(1)" class="w-8 h-8 rounded-lg bg-[#FAF6F0] border border-[#EADBCE] font-bold text-[#3D231D] hover:bg-[#D9A05B] hover:text-white flex items-center justify-center">+</button>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-[#EADBCE] bg-[#FAF6F0] flex items-center justify-between">
            <div>
                <span class="text-[10px] text-[#8A7A75] block">Subtotal Item:</span>
                <span id="modal-item-total" class="text-sm font-extrabold text-[#3D231D]">Rp 0</span>
            </div>
            <button type="button" onclick="confirmAddToCart()" 
                    class="px-5 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Tambahkan ke Keranjang</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL 2: CASH CHECKOUT / CALCULATOR            -->
<!-- ============================================== -->
<div id="modal-cash" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-base font-bold text-[#3D231D] flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
                <span>Pembayaran Tunai (Cash)</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-cash').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D]">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="bg-[#FAF6F0] p-4 rounded-xl text-center border border-[#EADBCE]">
                <span class="text-xs text-[#8A7A75]">Total Tagihan:</span>
                <h4 id="cash-modal-total" class="text-2xl font-extrabold text-[#3D231D] mt-0.5">Rp 0</h4>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">Uang Diterima dari Pelanggan:</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-[#8A7A75]">Rp</span>
                    <input type="number" id="input-cash-given" oninput="calculateChange()" placeholder="0"
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-[#EADBCE] rounded-xl text-base font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                </div>
            </div>

            <!-- Quick Nominal Buttons -->
            <div>
                <span class="text-[11px] text-[#8A7A75] font-semibold block mb-1.5">Pilihan Cepat Uang Pas:</span>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" onclick="setCashPreset('exact')" class="px-2 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-bold text-[#3D231D] transition-colors">Uang Pas</button>
                    <button type="button" onclick="setCashPreset(50000)" class="px-2 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-bold text-[#3D231D] transition-colors">50.000</button>
                    <button type="button" onclick="setCashPreset(100000)" class="px-2 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-bold text-[#3D231D] transition-colors">100.000</button>
                    <button type="button" onclick="setCashPreset(200000)" class="px-2 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-bold text-[#3D231D] transition-colors">200.000</button>
                </div>
            </div>

            <!-- Kembalian Display -->
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-800">Kembalian:</span>
                <span id="cash-modal-change" class="text-lg font-black text-emerald-700">Rp 0</span>
            </div>

            <button type="button" onclick="submitOrder('cash')" id="btn-confirm-cash"
                    class="w-full py-3 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-circle"></i>
                <span>Selesaikan & Cetak Struk</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL 3: QRIS CHECKOUT DYNAMIC DISPLAY         -->
<!-- ============================================== -->
<div id="modal-qris" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6 text-center">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-base font-bold text-[#3D231D] flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-[#C88A42]"></i>
                <span>QRIS d'nale caffe</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-qris').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D]">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="bg-[#FAF6F0] p-3 rounded-xl border border-[#EADBCE]">
                <span class="text-xs text-[#8A7A75]">Total yang Harus Dibayar:</span>
                <h4 id="qris-modal-total" class="text-2xl font-black text-[#3D231D] mt-0.5">Rp 0</h4>
            </div>

            <!-- QR Code Simulation Graphic -->
            <div class="p-4 bg-white border-2 border-dashed border-[#D9A05B] rounded-2xl inline-block shadow-sm">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=DNALE-CAFFE-ORDER-PAYMENT" 
                     alt="QRIS Code" class="w-44 h-44 mx-auto rounded-lg">
                <p class="text-[10px] text-[#8A7A75] font-semibold mt-2">NMID: ID1020260089201 • GoPay, OVO, BCA, Dana</p>
            </div>

            <p class="text-xs text-[#8A7A75]">Minta pelanggan memindai QRIS di atas melalui aplikasi e-wallet atau mobile banking.</p>

            <button type="button" onclick="submitOrder('qris')" id="btn-confirm-qris"
                    class="w-full py-3 bg-gradient-to-r from-[#D9A05B] to-[#C88A42] text-white text-xs font-bold rounded-xl shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>Konfirmasi Pembayaran QRIS Lunas</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL 4: THERMAL RECEIPT MODAL (PRINT READY)   -->
<!-- ============================================== -->
<div id="modal-receipt" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-check"></i>
                <span>Transaksi Sukses!</span>
            </span>
            <button type="button" onclick="closeReceiptModal()" class="text-[#8A7A75] hover:text-[#3D231D]">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Printable Receipt Area -->
        <div id="printable-receipt" class="bg-[#FAF6F0] p-4 rounded-xl border border-[#EADBCE] font-mono text-[11px] leading-relaxed text-[#231411]">
            <div class="text-center pb-2 border-b border-dashed border-[#8A7A75]">
                <h4 class="font-extrabold text-sm uppercase tracking-wider text-[#3D231D]">d'nale caffe</h4>
                <p class="text-[10px] text-[#8A7A75]">Jl. Boulevard Kopi No. 8, Jakarta</p>
                <p class="text-[10px] text-[#8A7A75]">Telp: +62 812-3456-7890</p>
            </div>

            <div class="py-2 border-b border-dashed border-[#8A7A75] text-[10px] space-y-0.5">
                <div class="flex justify-between"><span>No:</span><span id="rcp-order-number" class="font-bold">-</span></div>
                <div class="flex justify-between"><span>Waktu:</span><span id="rcp-date">-</span></div>
                <div class="flex justify-between"><span>Kasir:</span><span id="rcp-cashier">-</span></div>
                <div class="flex justify-between"><span>Tipe:</span><span id="rcp-type" class="font-bold">-</span></div>
                <div class="flex justify-between"><span>Meja:</span><span id="rcp-table">-</span></div>
            </div>

            <div id="rcp-items" class="py-2 border-b border-dashed border-[#8A7A75] space-y-1.5">
                <!-- Items populated via JS -->
            </div>

            <div class="py-2 space-y-1 border-b border-dashed border-[#8A7A75] text-[10px]">
                <div class="flex justify-between"><span>Subtotal:</span><span id="rcp-subtotal">Rp 0</span></div>
                <div class="flex justify-between font-bold text-xs pt-1 border-t border-dotted border-[#8A7A75]"><span>TOTAL:</span><span id="rcp-total">Rp 0</span></div>
                <div class="flex justify-between"><span>Metode:</span><span id="rcp-payment-method">CASH</span></div>
                <div id="rcp-row-cash" class="flex justify-between"><span>Tunai:</span><span id="rcp-cash">Rp 0</span></div>
                <div id="rcp-row-change" class="flex justify-between font-bold"><span>Kembali:</span><span id="rcp-change">Rp 0</span></div>
            </div>

            <div class="text-center pt-3 text-[10px] text-[#8A7A75]">
                <p>Terima kasih atas kunjungan Anda!</p>
                <p>Follow IG: @dnalecaffe</p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="grid grid-cols-2 gap-2 mt-4">
            <button type="button" onclick="printReceipt()" class="py-2.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] text-xs font-bold rounded-xl text-[#3D231D] flex items-center justify-center gap-1.5 transition-colors">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk</span>
            </button>
            <button type="button" onclick="closeReceiptModal()" class="py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition-colors">
                <i class="fa-solid fa-plus"></i>
                <span>Order Baru</span>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // State management for POS
    const menusData = @json($menus);
    let orderType = 'dine_in';
    let cart = [];
    let currentModalMenu = null;
    let modalSelectedVariants = [];
    let modalQuantity = 1;

    // Set Order Type (Dine In / Take Away)
    function setOrderType(type) {
        orderType = type;
        const btnDine = document.getElementById('btn-type-dine-in');
        const btnTake = document.getElementById('btn-type-take-away');
        const tableWrapper = document.getElementById('table-select-wrapper');

        if (type === 'dine_in') {
            btnDine.className = 'px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 bg-[#3D231D] text-white shadow-xs';
            btnTake.className = 'px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-[#8A7A75] hover:text-[#3D231D]';
            tableWrapper.style.display = 'flex';
        } else {
            btnTake.className = 'px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 bg-[#3D231D] text-white shadow-xs';
            btnDine.className = 'px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-[#8A7A75] hover:text-[#3D231D]';
            tableWrapper.style.display = 'none';
        }
    }

    // Filter Menus by Category
    function filterCategory(catId, button) {
        document.querySelectorAll('.cat-pill').forEach(btn => {
            btn.className = 'cat-pill px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all bg-[#FAF6F0] text-[#3D231D] hover:bg-[#EADBCE]';
        });
        button.className = 'cat-pill active px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all bg-[#D9A05B] text-white shadow-xs';

        const cards = document.querySelectorAll('.menu-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const matchesCat = (catId === 'all' || card.dataset.category === catId);
            const matchesSearch = card.dataset.name.includes(document.getElementById('pos-search').value.toLowerCase().trim());

            if (matchesCat && matchesSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('no-menus-message').classList.toggle('hidden', visibleCount > 0);
    }

    // Filter Menus by Search
    function filterMenus() {
        const searchVal = document.getElementById('pos-search').value.toLowerCase().trim();
        const activeCatBtn = document.querySelector('.cat-pill.active');
        const cards = document.querySelectorAll('.menu-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const matchesSearch = card.dataset.name.includes(searchVal);
            card.style.display = matchesSearch ? 'flex' : 'none';
            if (matchesSearch) visibleCount++;
        });

        document.getElementById('no-menus-message').classList.toggle('hidden', visibleCount > 0);
    }

    // Open Menu Customize Modal
    function openMenuModal(menuId) {
        const menu = menusData.find(m => m.id === menuId);
        if (!menu) return;

        currentModalMenu = menu;
        modalQuantity = 1;
        modalSelectedVariants = [];

        document.getElementById('modal-menu-name').textContent = menu.name;
        document.getElementById('modal-menu-price').textContent = 'Rp ' + Number(menu.price).toLocaleString('id-ID');
        document.getElementById('modal-menu-notes').value = '';
        document.getElementById('modal-qty').textContent = '1';

        const variantsContainer = document.getElementById('modal-variants-container');
        variantsContainer.innerHTML = '';

        if (menu.variants && menu.variants.length > 0) {
            menu.variants.forEach((v, vIdx) => {
                const group = document.createElement('div');
                group.className = 'space-y-1.5';
                
                let optionsHtml = '';
                v.options.forEach((opt, oIdx) => {
                    const extraPrice = Number(opt.additional_price);
                    const extraText = extraPrice > 0 ? ` (+Rp ${extraPrice.toLocaleString('id-ID')})` : '';
                    const isChecked = oIdx === 0 ? 'checked' : '';

                    optionsHtml += `
                        <label class="flex items-center justify-between p-2.5 bg-[#FAF6F0] rounded-xl border border-[#EADBCE] cursor-pointer hover:border-[#D9A05B]">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="variant_${vIdx}" value="${opt.name}" data-price="${extraPrice}" ${isChecked} onchange="recalculateModalPrice()" class="text-[#D9A05B] focus:ring-[#D9A05B]">
                                <span class="text-xs font-semibold text-[#3D231D]">${opt.name}</span>
                            </div>
                            <span class="text-[11px] font-bold text-[#C88A42]">${extraText}</span>
                        </label>
                    `;
                });

                group.innerHTML = `
                    <label class="block text-xs font-bold text-[#3D231D]">${v.name}:</label>
                    <div class="space-y-1.5">${optionsHtml}</div>
                `;
                variantsContainer.appendChild(group);
            });
        }

        recalculateModalPrice();
        document.getElementById('modal-menu').classList.remove('hidden');
    }

    function closeMenuModal() {
        document.getElementById('modal-menu').classList.add('hidden');
    }

    function modalChangeQty(delta) {
        modalQuantity = Math.max(1, modalQuantity + delta);
        document.getElementById('modal-qty').textContent = modalQuantity;
        recalculateModalPrice();
    }

    function recalculateModalPrice() {
        if (!currentModalMenu) return;
        let basePrice = Number(currentModalMenu.price);
        let extraTotal = 0;

        document.querySelectorAll('#modal-variants-container input[type="radio"]:checked').forEach(radio => {
            extraTotal += Number(radio.dataset.price || 0);
        });

        const totalPerUnit = basePrice + extraTotal;
        const grandTotal = totalPerUnit * modalQuantity;
        document.getElementById('modal-item-total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    function confirmAddToCart() {
        if (!currentModalMenu) return;

        const selectedVariants = [];
        document.querySelectorAll('#modal-variants-container input[type="radio"]:checked').forEach(radio => {
            selectedVariants.push({
                name: radio.value,
                additional_price: Number(radio.dataset.price || 0)
            });
        });

        const notes = document.getElementById('modal-menu-notes').value.trim();

        // Calculate unit price including variants
        const variantsExtra = selectedVariants.reduce((sum, v) => sum + v.additional_price, 0);
        const unitPrice = Number(currentModalMenu.price) + variantsExtra;

        // Generate cart item unique key based on menu and variants
        const variantKey = selectedVariants.map(v => v.name).sort().join('|');
        const existingIdx = cart.findIndex(c => c.menu_id === currentModalMenu.id && c.variantKey === variantKey && c.notes === notes);

        if (existingIdx > -1) {
            cart[existingIdx].quantity += modalQuantity;
            cart[existingIdx].subtotal = cart[existingIdx].quantity * unitPrice;
        } else {
            cart.push({
                menu_id: currentModalMenu.id,
                name: currentModalMenu.name,
                unit_price: unitPrice,
                quantity: modalQuantity,
                subtotal: unitPrice * modalQuantity,
                notes: notes,
                variants: selectedVariants,
                variantKey: variantKey,
            });
        }

        closeMenuModal();
        renderCart();
    }

    // Render Cart HTML
    function renderCart() {
        const cartList = document.getElementById('cart-list');
        const emptyState = document.getElementById('cart-empty');
        const countEl = document.getElementById('cart-item-count');

        if (cart.length === 0) {
            cartList.innerHTML = '';
            cartList.appendChild(emptyState);
            countEl.textContent = '0 item dipilih';
            updateTotals();
            return;
        }

        cartList.innerHTML = '';
        let totalItems = 0;

        cart.forEach((item, idx) => {
            totalItems += item.quantity;
            const itemRow = document.createElement('div');
            itemRow.className = 'p-3 bg-[#FAF6F0] rounded-xl border border-[#EADBCE] flex flex-col gap-2 transition-all';

            let variantBadges = '';
            if (item.variants && item.variants.length > 0) {
                variantBadges = item.variants.map(v => `<span class="inline-block text-[9px] bg-white border border-[#EADBCE] text-[#8A7A75] px-1.5 py-0.5 rounded mr-1">${v.name}</span>`).join('');
            }

            let notesHtml = item.notes ? `<div class="text-[10px] text-[#C88A42] italic">"${item.notes}"</div>` : '';

            itemRow.innerHTML = `
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                        <h5 class="text-xs font-bold text-[#3D231D]">${item.name}</h5>
                        <div class="mt-1">${variantBadges}</div>
                        ${notesHtml}
                    </div>
                    <span class="text-xs font-extrabold text-[#3D231D]">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-[#EADBCE]/50">
                    <span class="text-[10px] text-[#8A7A75]">@ Rp ${item.unit_price.toLocaleString('id-ID')}</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="updateCartItemQty(${idx}, -1)" class="w-6 h-6 rounded bg-white border border-[#EADBCE] text-[#3D231D] hover:bg-rose-500 hover:text-white flex items-center justify-center font-bold text-xs">-</button>
                        <span class="text-xs font-extrabold text-[#3D231D] w-4 text-center">${item.quantity}</span>
                        <button type="button" onclick="updateCartItemQty(${idx}, 1)" class="w-6 h-6 rounded bg-white border border-[#EADBCE] text-[#3D231D] hover:bg-[#D9A05B] hover:text-white flex items-center justify-center font-bold text-xs">+</button>
                        <button type="button" onclick="removeCartItem(${idx})" class="w-6 h-6 rounded text-rose-500 hover:text-rose-700 ml-1 flex items-center justify-center">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>
            `;
            cartList.appendChild(itemRow);
        });

        countEl.textContent = `${totalItems} item dipilih`;
        updateTotals();
    }

    function updateCartItemQty(index, delta) {
        if (!cart[index]) return;
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        } else {
            cart[index].subtotal = cart[index].quantity * cart[index].unit_price;
        }
        renderCart();
    }

    function removeCartItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        if (cart.length === 0) return;
        if (!confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang?')) return;
        cart = [];
        renderCart();
    }

    // Calculate Subtotal & Grand Total
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
        const total = subtotal;

        document.getElementById('calc-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('calc-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Checkout Flow (Cash or QRIS)
    function processCheckout(method) {
        if (cart.length === 0) {
            alert('Keranjang masih kosong. Silakan pilih menu.');
            return;
        }

        if (orderType === 'dine_in') {
            const tableVal = document.getElementById('select-table').value;
            if (!tableVal) {
                alert('Silakan pilih nomor meja untuk pesanan Dine In.');
                document.getElementById('select-table').focus();
                return;
            }
        }

        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);

        if (method === 'cash') {
            document.getElementById('cash-modal-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('input-cash-given').value = total;
            calculateChange();
            document.getElementById('modal-cash').classList.remove('hidden');
        } else if (method === 'qris') {
            document.getElementById('qris-modal-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('modal-qris').classList.remove('hidden');
        }
    }

    function calculateChange() {
        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
        const given = Number(document.getElementById('input-cash-given').value || 0);
        const change = Math.max(0, given - total);

        document.getElementById('cash-modal-change').textContent = 'Rp ' + change.toLocaleString('id-ID');
    }

    function setCashPreset(val) {
        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);

        if (val === 'exact') {
            document.getElementById('input-cash-given').value = total;
        } else {
            document.getElementById('input-cash-given').value = val;
        }
        calculateChange();
    }

    // Submit Order to Server API
    function submitOrder(method) {
        const tableId = orderType === 'dine_in' ? document.getElementById('select-table').value : null;
        const cashGiven = method === 'cash' ? Number(document.getElementById('input-cash-given').value || 0) : null;

        const payload = {
            order_type: orderType,
            cafe_table_id: tableId,
            payment_method: method,
            payment_status: 'paid',
            cash_given: cashGiven,
            items: cart.map(item => ({
                menu_id: item.menu_id,
                quantity: item.quantity,
                notes: item.notes,
                variants: item.variants,
            }))
        };

        const btn = method === 'cash' ? document.getElementById('btn-confirm-cash') : document.getElementById('btn-confirm-qris');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses Transaksi...';

        fetch('{{ route("pos.order") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Selesai';

            if (data.success) {
                // Close modals
                document.getElementById('modal-cash').classList.add('hidden');
                document.getElementById('modal-qris').classList.add('hidden');

                // Render Receipt
                showReceipt(data.receipt);

                // Reset cart
                cart = [];
                renderCart();
            } else {
                alert(data.message || 'Gagal menyimpan pesanan.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Coba Lagi';
            alert('Terjadi kesalahan saat memproses transaksi.');
        });
    }

    // Show Receipt Modal
    function showReceipt(rcp) {
        document.getElementById('rcp-order-number').textContent = rcp.order_number;
        document.getElementById('rcp-date').textContent = rcp.date;
        document.getElementById('rcp-cashier').textContent = rcp.cashier;
        document.getElementById('rcp-type').textContent = rcp.order_type;
        document.getElementById('rcp-table').textContent = rcp.table_number;

        const itemsContainer = document.getElementById('rcp-items');
        itemsContainer.innerHTML = '';
        rcp.items.forEach(it => {
            const vText = it.variants && it.variants.length > 0 ? ` (${it.variants.join(', ')})` : '';
            const row = document.createElement('div');
            row.innerHTML = `
                <div class="flex justify-between">
                    <span>${it.quantity}x ${it.name}${vText}</span>
                    <span>${it.formatted_subtotal}</span>
                </div>
            `;
            itemsContainer.appendChild(row);
        });

        document.getElementById('rcp-subtotal').textContent = rcp.formatted_subtotal;
        document.getElementById('rcp-total').textContent = rcp.formatted_total;
        document.getElementById('rcp-payment-method').textContent = rcp.payment_method;

        if (rcp.cash_given) {
            document.getElementById('rcp-row-cash').classList.remove('hidden');
            document.getElementById('rcp-row-change').classList.remove('hidden');
            document.getElementById('rcp-cash').textContent = rcp.formatted_cash_given;
            document.getElementById('rcp-change').textContent = rcp.formatted_change;
        } else {
            document.getElementById('rcp-row-cash').classList.add('hidden');
            document.getElementById('rcp-row-change').classList.add('hidden');
        }

        document.getElementById('modal-receipt').classList.remove('hidden');
    }

    function closeReceiptModal() {
        document.getElementById('modal-receipt').classList.add('hidden');
    }

    function printReceipt() {
        const receiptContent = document.getElementById('printable-receipt').innerHTML;
        const win = window.open('', '', 'width=400,height=600');
        win.document.write(`
            <html>
                <head>
                    <title>Struk d'nale caffe</title>
                    <style>
                        body { font-family: monospace; font-size: 12px; margin: 10px; }
                        .text-center { text-align: center; }
                        .flex { display: flex; justify-content: space-between; margin-bottom: 3px; }
                        .font-bold { font-weight: bold; }
                        .border-b { border-bottom: 1px dashed #333; padding-bottom: 6px; margin-bottom: 6px; }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                    <script>window.onload = function() { window.print(); window.close(); }<\/script>
                </body>
            </html>
        `);
        win.document.close();
    }
</script>
@endpush
