@extends('layouts.app')

@section('title', 'Dapur & Bar')
@section('page_title', 'Dapur & Bar')
@section('page_subtitle', 'Pesanan yang sudah dibayar dan masih menunggu fulfillment.')

@section('header_actions')
    <button type="button"
            onclick="refreshKitchen(this)"
            class="inline-flex items-center gap-2 bg-white hover:bg-[#FAF6F0] border border-[#EADBCE] hover:border-[#D9A05B] text-[#3D231D] px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all duration-200 cursor-pointer group active:scale-95">
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

    {{-- ─── AREA UTAMA ─── --}}
    @if($orders->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm flex flex-col items-center justify-center py-24 px-6 text-center">
            <div class="w-20 h-20 rounded-2xl bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center mb-6">
                <i class="fa-solid fa-mug-hot text-4xl text-[#D9A05B]"></i>
            </div>
            <h3 class="text-lg font-bold text-[#3D231D] mb-2">Belum ada pesanan menunggu</h3>
            <p class="text-sm text-[#8A7A75] max-w-sm leading-relaxed">
                Pesanan akan muncul otomatis setelah kasir menyelesaikan pembayaran.
            </p>
        </div>
    @else
        {{-- ─── COUNTER BADGE STRIP ─── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-hourglass-start text-amber-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-[#3D231D] leading-none">{{ $counts['pending'] }}</p>
                    <p class="text-[11px] text-[#8A7A75] mt-0.5">Menunggu</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-fire-burner text-blue-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-[#3D231D] leading-none">{{ $counts['cooking'] }}</p>
                    <p class="text-[11px] text-[#8A7A75] mt-0.5">Diproses</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bell-concierge text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-[#3D231D] leading-none">{{ $counts['ready'] }}</p>
                    <p class="text-[11px] text-[#8A7A75] mt-0.5">Siap Antar</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-gray-400"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-[#3D231D] leading-none">{{ $counts['served'] }}</p>
                    <p class="text-[11px] text-[#8A7A75] mt-0.5">Selesai Hari Ini</p>
                </div>
            </div>
        </div>

        {{-- ─── FILTER TAB ─── --}}
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm px-5 py-4">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-semibold text-[#8A7A75]">Status :</span>
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach([
                        'active'  => 'Semua Aktif',
                        'pending' => 'Menunggu',
                        'cooking' => 'Diproses',
                        'ready'   => 'Siap Antar',
                        'served'  => 'Selesai',
                    ] as $val => $label)
                        <a href="{{ route('kitchen.index', ['status' => $val]) }}"
                           class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all duration-150
                                  {{ $statusFilter === $val
                                      ? 'bg-[#3D231D] text-white shadow-sm'
                                      : 'bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE] hover:bg-[#EADBCE]' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ─── GRID KARTU PESANAN ─── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($orders as $order)
                @php
                    $elapsed = $order->created_at->diffInMinutes(now());
                    $statusColor = match($order->kitchen_status) {
                        'pending' => ['bg' => 'bg-amber-100',  'text' => 'text-amber-700',  'dot' => 'bg-amber-400'],
                        'cooking' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dot' => 'bg-blue-400'],
                        'ready'   => ['bg' => 'bg-emerald-100','text' => 'text-emerald-700','dot' => 'bg-emerald-500'],
                        'served'  => ['bg' => 'bg-gray-100',   'text' => 'text-gray-500',   'dot' => 'bg-gray-400'],
                        default   => ['bg' => 'bg-gray-100',   'text' => 'text-gray-500',   'dot' => 'bg-gray-400'],
                    };
                    $nextStatus = match($order->kitchen_status) {
                        'pending' => 'cooking',
                        'cooking' => 'ready',
                        'ready'   => 'served',
                        default   => null,
                    };
                    $nextLabel = match($order->kitchen_status) {
                        'pending' => 'Mulai Proses',
                        'cooking' => 'Tandai Siap',
                        'ready'   => 'Sudah Disajikan',
                        default   => null,
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm hover:shadow-md transition-all flex flex-col"
                     id="order-card-{{ $order->id }}">

                    {{-- Card Header --}}
                    <div class="px-5 pt-5 pb-4 border-b border-[#EADBCE] flex items-start justify-between gap-3">
                        <div>
                            <span class="font-mono text-sm font-black text-[#3D231D]">{{ $order->order_number }}</span>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md
                                    {{ $order->order_type === 'dine_in' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                    <i class="fa-solid {{ $order->order_type === 'dine_in' ? 'fa-chair' : 'fa-bag-shopping' }} text-[9px]"></i>
                                    {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                </span>
                                @if($order->cafeTable)
                                    <span class="text-[11px] font-semibold text-[#C88A42]">
                                        <i class="fa-solid fa-table-cells-large text-[9px] mr-0.5"></i>
                                        {{ $order->cafeTable->table_number }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold {{ $statusColor['bg'] }} {{ $statusColor['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusColor['dot'] }} inline-block"></span>
                                {{ ucfirst($order->kitchen_status) }}
                            </span>
                            <p class="text-[10px] text-[#8A7A75] mt-1">
                                {{ $order->created_at->format('H:i') }} •
                                <span class="{{ $elapsed >= 15 ? 'text-rose-500 font-bold' : '' }}">
                                    {{ $elapsed }}m lalu
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Item List --}}
                    <div class="px-5 py-4 flex-1 space-y-2.5">
                        @foreach($order->orderDetails as $detail)
                            <div class="flex items-start gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center text-[11px] font-black text-[#C88A42] flex-shrink-0 mt-0.5">
                                    {{ $detail->quantity }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#3D231D] leading-snug">{{ $detail->menu->name ?? '—' }}</p>
                                    @if($detail->variants->count())
                                        <p class="text-[10px] text-[#8A7A75] mt-0.5">
                                            {{ $detail->variants->pluck('variant_option_name')->implode(', ') }}
                                        </p>
                                    @endif
                                    @if(!empty($detail->notes))
                                        <p class="text-[10px] text-amber-600 mt-0.5 italic">
                                            <i class="fa-solid fa-note-sticky text-[9px]"></i> {{ $detail->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Card Footer --}}
                    <div class="px-5 pb-5 pt-3 border-t border-[#EADBCE] flex items-center justify-between gap-2">
                        <span class="text-[11px] text-[#8A7A75]">
                            <i class="fa-solid fa-user text-[9px] mr-0.5"></i>
                            {{ $order->user->name ?? '—' }}
                        </span>
                        @if($nextStatus)
                            <button type="button"
                                    onclick="updateKitchenStatus({{ $order->id }}, '{{ $nextStatus }}', this)"
                                    class="px-4 py-2 rounded-xl text-[11px] font-bold transition-all duration-150 cursor-pointer
                                           {{ $order->kitchen_status === 'pending' ? 'bg-amber-500 hover:bg-amber-600 text-white' : '' }}
                                           {{ $order->kitchen_status === 'cooking' ? 'bg-blue-500 hover:bg-blue-600 text-white' : '' }}
                                           {{ $order->kitchen_status === 'ready'   ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : '' }}">
                                {{ $nextLabel }}
                            </button>
                        @else
                            <span class="text-[11px] text-emerald-600 font-semibold">
                                <i class="fa-solid fa-circle-check mr-0.5"></i> Selesai
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function refreshKitchen(btn) {
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75');
            const svg = btn.querySelector('svg');
            if (svg) svg.classList.add('animate-spin');
        }
        window.location.reload();
    }

    function updateKitchenStatus(orderId, newStatus, btn) {
        btn.disabled = true;
        const original = btn.textContent.trim();
        btn.textContent = '...';

        fetch(`/kitchen/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ kitchen_status: newStatus })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Reload halaman untuk refresh semua counter & status
                window.location.reload();
            } else {
                btn.disabled = false;
                btn.textContent = original;
                alert(data.message || 'Gagal memperbarui status.');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = original;
            alert('Terjadi kesalahan jaringan.');
        });
    }
</script>
@endpush
