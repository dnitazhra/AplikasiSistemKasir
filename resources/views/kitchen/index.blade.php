@extends('layouts.app')

@section('title', 'Layar Dapur & Bar (KDS)')
@section('page_title', 'Kitchen Display System (KDS)')
@section('page_subtitle', 'Antrean Pesanan Barista & Dapur Realtime')

@section('content')
<div class="space-y-6">

    <!-- TOP CONTROL BAR: STATUS FILTERS & AUTO REFRESH -->
    <div class="bg-white rounded-2xl border border-[#EADBCE] p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Status Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <a href="{{ route('kitchen.index', ['status' => 'active']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $statusFilter === 'active' ? 'bg-[#3D231D] text-white shadow-xs' : 'bg-[#FAF6F0] text-[#3D231D] hover:bg-[#EADBCE]' }}">
                <span>Semua Aktif</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'active' ? 'bg-[#D9A05B] text-white' : 'bg-[#EADBCE] text-[#3D231D]' }}">
                    {{ $counts['pending'] + $counts['cooking'] + $counts['ready'] }}
                </span>
            </a>

            <a href="{{ route('kitchen.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-[#FAF6F0] text-amber-800 hover:bg-amber-100' }}">
                <i class="fa-regular fa-clock"></i>
                <span>Menunggu (Pending)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500/20">
                    {{ $counts['pending'] }}
                </span>
            </a>

            <a href="{{ route('kitchen.index', ['status' => 'cooking']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $statusFilter === 'cooking' ? 'bg-blue-600 text-white shadow-xs' : 'bg-[#FAF6F0] text-blue-800 hover:bg-blue-100' }}">
                <i class="fa-solid fa-fire-burner"></i>
                <span>Dimasak / Diseduh</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-500/20">
                    {{ $counts['cooking'] }}
                </span>
            </a>

            <a href="{{ route('kitchen.index', ['status' => 'ready']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $statusFilter === 'ready' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-[#FAF6F0] text-emerald-800 hover:bg-emerald-100' }}">
                <i class="fa-solid fa-bell"></i>
                <span>Siap Saji</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/20">
                    {{ $counts['ready'] }}
                </span>
            </a>
        </div>

        <!-- Live Sync Controls -->
        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
            <button type="button" id="btn-sound-toggle" onclick="toggleSound()" class="px-3 py-1.5 bg-[#FAF6F0] hover:bg-[#EADBCE] text-[#3D231D] border border-[#EADBCE] rounded-xl text-xs font-bold flex items-center gap-1.5 transition-colors cursor-pointer" title="Nyalakan/Matikan Bunyi Bel Pesanan">
                <i class="fa-solid fa-volume-high text-[#C88A42]" id="sound-icon"></i>
                <span id="sound-label">Bel: ON</span>
            </button>

            <div class="flex items-center gap-2 text-xs font-medium text-[#8A7A75]">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Auto-refresh: <strong id="refresh-counter">5s</strong></span>
            </div>

            <button type="button" onclick="fetchLiveOrders()" class="p-2 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] transition-colors cursor-pointer" title="Refresh Sekarang">
                <i class="fa-solid fa-arrows-rotate" id="refresh-icon"></i>
            </button>
        </div>
    </div>

    <!-- ORDER TICKETS GRID -->
    <div id="tickets-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
        @forelse($orders as $order)
            <div id="order-card-{{ $order->id }}" 
                 class="bg-white rounded-2xl border-2 shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-200
                 {{ $order->kitchen_status === 'pending' ? 'border-amber-300' : '' }}
                 {{ $order->kitchen_status === 'cooking' ? 'border-blue-400 shadow-md ring-2 ring-blue-100' : '' }}
                 {{ $order->kitchen_status === 'ready' ? 'border-emerald-400' : '' }}
                 {{ $order->kitchen_status === 'served' ? 'border-gray-200 opacity-70' : '' }}">
                
                <div>
                    <!-- TICKET HEADER -->
                    <div class="p-4 border-b border-[#EADBCE] flex items-center justify-between
                        {{ $order->kitchen_status === 'pending' ? 'bg-amber-50' : '' }}
                        {{ $order->kitchen_status === 'cooking' ? 'bg-blue-50' : '' }}
                        {{ $order->kitchen_status === 'ready' ? 'bg-emerald-50' : '' }}
                        {{ $order->kitchen_status === 'served' ? 'bg-gray-50' : '' }}">
                        
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-[#3D231D]">{{ $order->order_number }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase
                                    {{ $order->order_type === 'dine_in' ? 'bg-amber-200 text-amber-900' : 'bg-blue-200 text-blue-900' }}">
                                    {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                </span>
                            </div>

                            @if($order->cafeTable)
                                <div class="text-xs font-bold text-[#C88A42] mt-0.5">
                                    <i class="fa-solid fa-chair mr-1"></i>{{ $order->cafeTable->table_number }}
                                </div>
                            @endif
                        </div>

                        <div class="text-right">
                            <span class="text-xs font-bold text-[#3D231D] block">{{ $order->created_at->format('H:i') }}</span>
                            <span class="text-[10px] text-[#8A7A75]">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- TICKET ITEMS LIST -->
                    <div class="p-4 space-y-3">
                        @foreach($order->orderDetails as $detail)
                            <div class="pb-2.5 border-b border-[#EADBCE]/50 last:border-none last:pb-0">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-6 h-6 rounded-lg bg-[#FAF6F0] border border-[#EADBCE] text-xs font-black text-[#3D231D] flex items-center justify-center flex-shrink-0">
                                        {{ $detail->quantity }}x
                                    </span>
                                    <div class="flex-1">
                                        <h5 class="text-xs font-bold text-[#3D231D] leading-tight">{{ $detail->menu->name ?? 'Menu' }}</h5>
                                        
                                        <!-- Variant options -->
                                        @if($detail->variants->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($detail->variants as $var)
                                                    <span class="text-[9px] font-semibold bg-[#FAF6F0] text-[#8A7A75] px-1.5 py-0.5 rounded border border-[#EADBCE]">
                                                        {{ $var->variant_option_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Notes -->
                                        @if($detail->notes)
                                            <div class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-md border border-amber-200 mt-1.5">
                                                <i class="fa-regular fa-comment-dots mr-1"></i>"{{ $detail->notes }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- TICKET FOOTER & STATUS ACTION BUTTON -->
                <div class="p-4 border-t border-[#EADBCE] bg-[#FAF6F0]/80">
                    <div class="flex items-center justify-between mb-2.5 text-xs">
                        <span class="text-[#8A7A75]">Status:</span>
                        <span class="font-extrabold uppercase tracking-wider text-[11px]
                            {{ $order->kitchen_status === 'pending' ? 'text-amber-700' : '' }}
                            {{ $order->kitchen_status === 'cooking' ? 'text-blue-700' : '' }}
                            {{ $order->kitchen_status === 'ready' ? 'text-emerald-700' : '' }}
                            {{ $order->kitchen_status === 'served' ? 'text-gray-600' : '' }}">
                            {{ ucfirst($order->kitchen_status) }}
                        </span>
                    </div>

                    @if($order->kitchen_status === 'pending')
                        <button type="button" onclick="updateOrderStatus({{ $order->id }}, 'cooking')"
                                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center justify-center gap-2 transition-colors">
                            <i class="fa-solid fa-fire-burner"></i>
                            <span>Mulai Proses (Cooking)</span>
                        </button>
                    @elseif($order->kitchen_status === 'cooking')
                        <button type="button" onclick="updateOrderStatus({{ $order->id }}, 'ready')"
                                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center justify-center gap-2 transition-colors">
                            <i class="fa-solid fa-bell"></i>
                            <span>Siap Saji (Ready)</span>
                        </button>
                    @elseif($order->kitchen_status === 'ready')
                        <button type="button" onclick="updateOrderStatus({{ $order->id }}, 'served')"
                                class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white rounded-xl text-xs font-bold shadow-xs flex items-center justify-center gap-2 transition-colors">
                            <i class="fa-solid fa-check"></i>
                            <span>Sajikan ke Pelanggan (Served)</span>
                        </button>
                    @else
                        <div class="text-center py-1 text-xs text-gray-500 font-semibold">
                            Pesanan Telah Selesai
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-[#EADBCE] p-12 text-center text-[#8A7A75]">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-[#FAF6F0] flex items-center justify-center text-3xl text-[#D9A05B]">
                    <i class="fa-solid fa-mug-saucer"></i>
                </div>
                <h4 class="text-base font-bold text-[#3D231D]">Tidak Ada Antrean Pesanan</h4>
                <p class="text-xs text-[#8A7A75] mt-1">Saat ini belum ada pesanan baru yang perlu disiapkan oleh Barista & Dapur.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    let countdown = 5;
    const counterEl = document.getElementById('refresh-counter');
    const refreshIcon = document.getElementById('refresh-icon');

    let soundEnabled = true;
    let lastPendingCount = {{ $counts['pending'] }};
    let lastTotalActive = {{ $counts['pending'] + $counts['cooking'] + $counts['ready'] }};

    function toggleSound() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('sound-icon');
        const label = document.getElementById('sound-label');
        if (soundEnabled) {
            icon.className = 'fa-solid fa-volume-high text-[#C88A42]';
            label.textContent = 'Bel: ON';
            playKitchenChime();
        } else {
            icon.className = 'fa-solid fa-volume-xmark text-[#8A7A75]';
            label.textContent = 'Bel: OFF';
        }
    }

    function playKitchenChime() {
        if (!soundEnabled) return;
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
            osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.12); // A5
            gain.gain.setValueAtTime(0.25, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.7);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.7);
        } catch (e) {
            // Audio policy might prevent auto-play before interaction
        }
    }

    // Countdown timer for automatic sync
    setInterval(() => {
        countdown--;
        if (counterEl) counterEl.textContent = countdown + 's';
        if (countdown <= 0) {
            countdown = 5;
            fetchLiveOrders();
        }
    }, 1000);

    // Update Status via AJAX
    function updateOrderStatus(orderId, newStatus) {
        fetch(`/kitchen/${orderId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ kitchen_status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal mengubah status');
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan.');
        });
    }

    // Live AJAX Polling
    function fetchLiveOrders() {
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        fetch('{{ route("kitchen.orders_json") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            if (data.success && data.counts) {
                const newPending = data.counts.pending || 0;
                const newTotal = (data.counts.pending || 0) + (data.counts.cooking || 0) + (data.counts.ready || 0);

                if (newPending > lastPendingCount) {
                    playKitchenChime();
                }

                if (newPending !== lastPendingCount || newTotal !== lastTotalActive) {
                    window.location.reload();
                }
            }
        })
        .catch(err => {
            if (refreshIcon) refreshIcon.classList.remove('fa-spin');
        });
    }
</script>
@endpush
