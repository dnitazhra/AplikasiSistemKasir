@extends('layouts.app')

@section('title', 'Voucher & Promo')
@section('page_title', 'Voucher & Promo Diskon')
@section('page_subtitle', 'Kelola Kode Promo, Diskon Persentase, dan Potongan Transaksi Kasir')

@section('content')
<div class="space-y-6">

    <!-- STATS OVERVIEW -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#EADBCE] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-[#8A7A75] uppercase tracking-wider block">Total Voucher</span>
                <h3 class="text-2xl font-extrabold text-[#3D231D] mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#FAF6F0] text-[#C88A42] flex items-center justify-center text-lg border border-[#EADBCE]">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#EADBCE] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Voucher Aktif</span>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['active'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-200">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#EADBCE] shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Total Dipakai POS</span>
                <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ $stats['total_redeemed'] }}x</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg border border-blue-200">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ADD VOUCHER FORM -->
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 h-fit">
            <h3 class="text-sm font-bold text-[#3D231D] mb-1 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-[#C88A42]"></i>
                <span>Tambah Voucher Baru</span>
            </h3>
            <p class="text-xs text-[#8A7A75] mb-4">Kode promo yang dapat digunakan kasir saat checkout.</p>

            <form action="{{ route('vouchers.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kode Voucher *</label>
                    <input type="text" name="code" required placeholder="Contoh: KOPIHEMAT10"
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] uppercase tracking-wider outline-none focus:ring-2 focus:ring-[#D9A05B]">
                    @error('code')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Tipe Diskon *</label>
                        <select name="discount_type" id="add-discount-type" onchange="updateDiscountLabel(this.value, 'add')" class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                            <option value="fixed">Nominal (Rp)</option>
                            <option value="percentage">Persen (%)</option>
                        </select>
                    </div>

                    <div>
                        <label id="add-label-val" class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Nilai Potongan *</label>
                        <input type="number" name="discount_value" required min="1" step="1" placeholder="cth: 10000"
                               class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Min. Belanja (Rp)</label>
                    <input type="number" name="min_order_amount" value="0" min="0" step="1000" placeholder="0 jika tanpa batas"
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
                </div>

                <div class="flex items-center gap-3 p-3 bg-[#FAF6F0] rounded-xl border border-[#EADBCE]">
                    <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="w-4 h-4 rounded text-[#D9A05B] border-[#EADBCE] focus:ring-[#D9A05B]">
                    <label for="add_is_active" class="text-xs font-bold text-[#3D231D] cursor-pointer">
                        Voucher Langsung Aktif
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Voucher</span>
                </button>
            </form>
        </div>

        <!-- VOUCHERS LIST TABLE -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#3D231D]">Daftar Voucher Promo</h3>
                    <p class="text-xs text-[#8A7A75] mt-0.5">Terdapat {{ $vouchers->count() }} voucher di sistem</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#EADBCE] text-[#8A7A75] font-semibold uppercase tracking-wider">
                            <th class="pb-3">Kode Promo</th>
                            <th class="pb-3">Potongan</th>
                            <th class="pb-3">Min. Belanja</th>
                            <th class="pb-3">Digunakan</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EADBCE]/50">
                        @forelse($vouchers as $v)
                            <tr class="hover:bg-[#FAF6F0]/50 transition-colors">
                                <td class="py-3.5">
                                    <span class="font-mono font-black text-sm text-[#3D231D] tracking-wider px-2 py-1 bg-[#FAF6F0] rounded-lg border border-[#EADBCE]">
                                        {{ $v->code }}
                                    </span>
                                </td>
                                <td class="py-3.5">
                                    @if($v->discount_type === 'percentage')
                                        <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            {{ (int)$v->discount_value }}% OFF
                                        </span>
                                    @else
                                        <span class="font-bold text-[#3D231D]">
                                            Rp {{ number_format($v->discount_value, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-[#8A7A75]">
                                    {{ $v->min_order_amount > 0 ? 'Rp ' . number_format($v->min_order_amount, 0, ',', '.') : 'Tanpa Min.' }}
                                </td>
                                <td class="py-3.5 font-bold text-[#3D231D]">
                                    {{ $v->orders_count }}x
                                </td>
                                <td class="py-3.5">
                                    <button type="button" onclick="toggleVoucherActive({{ $v->id }}, this)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold cursor-pointer transition-colors
                                            {{ $v->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $v->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <span>{{ $v->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </td>
                                <td class="py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="openEditVoucher({{ $v->id }}, '{{ addslashes($v->code) }}', '{{ $v->discount_type }}', {{ (float)$v->discount_value }}, {{ (float)$v->min_order_amount }}, {{ $v->is_active ? 'true' : 'false' }})"
                                                class="p-1.5 text-[#8A7A75] hover:text-[#3D231D] cursor-pointer" title="Edit Voucher">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>

                                        <form action="{{ route('vouchers.destroy', $v) }}" method="POST" onsubmit="return confirm('Hapus voucher {{ $v->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-600 cursor-pointer" title="Hapus Voucher">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[#8A7A75]">
                                    <i class="fa-solid fa-ticket text-3xl mb-2 text-[#D9A05B]"></i>
                                    <p>Belum ada voucher promo yang dibuat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Voucher -->
<div id="modal-edit-voucher" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D]">Edit Voucher Promo</h3>
            <button type="button" onclick="document.getElementById('modal-edit-voucher').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="form-edit-voucher" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kode Voucher *</label>
                <input type="text" id="edit-v-code" name="code" required
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] uppercase tracking-wider outline-none focus:ring-2 focus:ring-[#D9A05B]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Tipe Diskon *</label>
                    <select id="edit-v-type" name="discount_type" class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                        <option value="fixed">Nominal (Rp)</option>
                        <option value="percentage">Persen (%)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Nilai Potongan *</label>
                    <input type="number" id="edit-v-value" name="discount_value" required min="1" step="1"
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Min. Belanja (Rp)</label>
                <input type="number" id="edit-v-min" name="min_order_amount" min="0" step="1000"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
            </div>

            <div class="flex items-center gap-3 p-3 bg-[#FAF6F0] rounded-xl border border-[#EADBCE]">
                <input type="checkbox" name="is_active" id="edit-v-active" value="1" class="w-4 h-4 rounded text-[#D9A05B] border-[#EADBCE] focus:ring-[#D9A05B]">
                <label for="edit-v-active" class="text-xs font-bold text-[#3D231D] cursor-pointer">
                    Voucher Aktif
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateDiscountLabel(type, form) {
        const label = document.getElementById(form + '-label-val');
        if (label) {
            label.textContent = type === 'percentage' ? 'Persen Diskon (%) *' : 'Nominal Potongan (Rp) *';
        }
    }

    function openEditVoucher(id, code, type, value, min, isActive) {
        document.getElementById('form-edit-voucher').action = `/vouchers/${id}`;
        document.getElementById('edit-v-code').value = code;
        document.getElementById('edit-v-type').value = type;
        document.getElementById('edit-v-value').value = value;
        document.getElementById('edit-v-min').value = min;
        document.getElementById('edit-v-active').checked = isActive;
        document.getElementById('modal-edit-voucher').classList.remove('hidden');
    }

    function toggleVoucherActive(id, btn) {
        fetch(`/vouchers/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => alert('Gagal mengubah status voucher.'));
    }
</script>
@endpush
