@extends('layouts.app')

@section('title', 'Denah & Meja Kafe')
@section('page_title', 'Manajemen Denah & Meja')
@section('page_subtitle', 'Pengaturan Kapasitas, Nomor Meja, dan Ketersediaan Tempat Duduk')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- ADD TABLE FORM -->
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 h-fit">
        <h3 class="text-sm font-bold text-[#3D231D] mb-1 flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-[#C88A42]"></i>
            <span>Tambah Meja Baru</span>
        </h3>
        <p class="text-xs text-[#8A7A75] mb-4">Tambahkan nomor meja untuk Terminal POS kasir.</p>

        <form action="{{ route('tables.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Nomor / Nama Meja *</label>
                <input type="text" name="table_number" required placeholder="Contoh: Meja 09 atau VIP-1"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kapasitas Kursi (Orang) *</label>
                <input type="number" name="capacity" value="4" min="1" max="50" required
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Status Awal *</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                    <option value="available">Tersedia (Kosong)</option>
                    <option value="occupied">Terisi (Ada Pelanggan)</option>
                    <option value="reserved">Dipesan (Reservasi)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Simpan Meja</span>
            </button>
        </form>
    </div>

    <!-- TABLES GRID & FLOOR PLAN -->
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-[#3D231D]">Denah Meja Aktif</h3>
                    <p class="text-xs text-[#8A7A75] mt-0.5">Ubah status meja secara langsung melalui tombol di setiap kartu</p>
                </div>

                <!-- Legend -->
                <div class="flex items-center gap-3 text-[11px] font-semibold">
                    <span class="flex items-center gap-1 text-emerald-700"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Tersedia</span>
                    <span class="flex items-center gap-1 text-rose-700"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Terisi</span>
                    <span class="flex items-center gap-1 text-amber-700"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Dipesan</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($tables as $table)
                    <div class="p-4 rounded-2xl border-2 transition-all duration-200 flex flex-col justify-between
                        {{ $table->status === 'available' ? 'bg-emerald-50/40 border-emerald-300' : '' }}
                        {{ $table->status === 'occupied' ? 'bg-rose-50/40 border-rose-300' : '' }}
                        {{ $table->status === 'reserved' ? 'bg-amber-50/40 border-amber-300' : '' }}">
                        
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-black text-[#3D231D]">{{ $table->table_number }}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $table->status === 'available' ? 'bg-emerald-200 text-emerald-900' : '' }}
                                    {{ $table->status === 'occupied' ? 'bg-rose-200 text-rose-900' : '' }}
                                    {{ $table->status === 'reserved' ? 'bg-amber-200 text-amber-900' : '' }}">
                                    {{ $table->status === 'available' ? 'Tersedia' : ($table->status === 'occupied' ? 'Terisi' : 'Dipesan') }}
                                </span>
                            </div>

                            <p class="text-xs text-[#8A7A75] mb-3">
                                <i class="fa-solid fa-users text-[#C88A42] mr-1"></i> Kapasitas: <strong>{{ $table->capacity }} Orang</strong>
                            </p>

                            @if($table->currentOrder)
                                <div class="bg-white/80 p-2 rounded-xl border border-[#EADBCE] text-[11px] mb-3">
                                    <span class="text-[#8A7A75] block">Order Aktif:</span>
                                    <span class="font-bold text-[#3D231D]">{{ $table->currentOrder->order_number }}</span>
                                    <span class="text-[#C88A42] font-semibold block">Rp {{ number_format($table->currentOrder->total_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Status Quick Switch -->
                        <div class="pt-3 border-t border-[#EADBCE]/80 flex items-center justify-between gap-1">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="changeTableStatus({{ $table->id }}, 'available')" title="Tandai Tersedia"
                                        class="px-2 py-1 bg-white hover:bg-emerald-500 hover:text-white border border-[#EADBCE] rounded-lg text-[10px] font-bold text-emerald-700 transition-colors">
                                    Kosong
                                </button>
                                <button type="button" onclick="changeTableStatus({{ $table->id }}, 'occupied')" title="Tandai Terisi"
                                        class="px-2 py-1 bg-white hover:bg-rose-500 hover:text-white border border-[#EADBCE] rounded-lg text-[10px] font-bold text-rose-700 transition-colors">
                                    Terisi
                                </button>
                                <button type="button" onclick="changeTableStatus({{ $table->id }}, 'reserved')" title="Tandai Reservasi"
                                        class="px-2 py-1 bg-white hover:bg-amber-500 hover:text-white border border-[#EADBCE] rounded-lg text-[10px] font-bold text-amber-700 transition-colors">
                                    Reservasi
                                </button>
                            </div>

                            <div class="flex items-center gap-1">
                                <button type="button" onclick="openEditTable({{ $table->id }}, '{{ addslashes($table->table_number) }}', {{ $table->capacity }}, '{{ $table->status }}')"
                                        class="p-1.5 text-[#8A7A75] hover:text-[#3D231D] cursor-pointer" title="Edit Meja">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>

                                <form action="{{ route('tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Hapus meja ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-600 cursor-pointer" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-[#8A7A75]">Belum ada meja yang terdaftar.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Table -->
<div id="modal-edit-table" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D]">Edit Data Meja</h3>
            <button type="button" onclick="document.getElementById('modal-edit-table').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="form-edit-table" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Nomor / Nama Meja *</label>
                <input type="text" id="edit-table-number" name="table_number" required
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kapasitas Kursi (Orang) *</label>
                <input type="number" id="edit-table-capacity" name="capacity" min="1" max="50" required
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Status Meja *</label>
                <select id="edit-table-status" name="status" class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                    <option value="available">Tersedia (Kosong)</option>
                    <option value="occupied">Terisi (Ada Pelanggan)</option>
                    <option value="reserved">Dipesan (Reservasi)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer">
                Simpan Perubahan Meja
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditTable(id, tableNumber, capacity, status) {
        document.getElementById('form-edit-table').action = `/tables/${id}`;
        document.getElementById('edit-table-number').value = tableNumber;
        document.getElementById('edit-table-capacity').value = capacity;
        document.getElementById('edit-table-status').value = status;
        document.getElementById('modal-edit-table').classList.remove('hidden');
    }

    function changeTableStatus(tableId, newStatus) {
        fetch(`/tables/${tableId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => alert('Gagal mengubah status meja.'));
    }
</script>
@endpush
