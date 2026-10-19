@extends('layouts.app')

@section('title', 'Denah & Status Meja')
@section('page_title', 'Denah & Status Meja')
@section('page_subtitle', 'Kelola tata letak, kapasitas kursi, dan status ketersediaan meja kafe')

@section('header_actions')
    <div class="flex items-center gap-2.5">
        {{-- Tombol Muat Ulang di Pojok Kanan Atas --}}
        <button type="button" 
                onclick="refreshTablesPage(this)"
                id="btn-refresh-tables"
                class="inline-flex items-center gap-2 bg-white hover:bg-[#FAF6F0] border border-[#EADBCE] hover:border-[#D9A05B] text-[#3D231D] px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition-all duration-200 active:scale-95 group cursor-pointer"
                title="Muat Ulang Denah Meja">
            <i class="fa-solid fa-rotate text-[#C88A42] group-hover:rotate-180 transition-transform duration-500"></i>
            <span>Muat Ulang</span>
        </button>

        {{-- Tombol Tambah Meja Baru --}}
        <button type="button"
                onclick="document.getElementById('modal-add-table').classList.remove('hidden')"
                class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all duration-200 active:scale-95 cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Meja</span>
        </button>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 1. KARTU RINGKASAN STATUS (4 KOTAK)             --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Kotak 1: Total Meja (Latar oranye muda) --}}
        <div class="bg-[#FFF7ED] rounded-2xl border border-[#FED7AA] p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-[11px] font-bold text-[#C2410C] uppercase tracking-wider">Total Meja</p>
                <h3 class="text-2xl font-extrabold text-[#9A3412] mt-1">{{ $stats['total'] ?? 0 }}</h3>
                <p class="text-[11px] text-[#EA580C] font-medium mt-0.5">Kapasitas terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/80 border border-[#FED7AA] text-[#EA580C] flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-table-cells-large"></i>
            </div>
        </div>

        {{-- Kotak 2: Tersedia (Latar hijau muda) --}}
        <div class="bg-[#ECFDF5] rounded-2xl border border-[#A7F3D0] p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-[11px] font-bold text-[#047857] uppercase tracking-wider">Tersedia</p>
                <h3 class="text-2xl font-extrabold text-[#065F46] mt-1">{{ $stats['available'] ?? 0 }}</h3>
                <p class="text-[11px] text-[#059669] font-medium mt-0.5">Siap ditempati pelanggan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/80 border border-[#A7F3D0] text-[#059669] flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        {{-- Kotak 3: Terisi (Latar kuning muda) --}}
        <div class="bg-[#FEFCE8] rounded-2xl border border-[#FEF08A] p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-[11px] font-bold text-[#A16207] uppercase tracking-wider">Terisi</p>
                <h3 class="text-2xl font-extrabold text-[#854D0E] mt-1">{{ $stats['occupied'] ?? 0 }}</h3>
                <p class="text-[11px] text-[#CA8A04] font-medium mt-0.5">Ada pelanggan aktif</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/80 border border-[#FEF08A] text-[#CA8A04] flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        {{-- Kotak 4: Dipesan (Latar abu-abu kebiruan) --}}
        <div class="bg-[#F1F5F9] rounded-2xl border border-[#CBD5E1] p-5 shadow-xs flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Dipesan</p>
                <h3 class="text-2xl font-extrabold text-[#1E293B] mt-1">{{ $stats['reserved'] ?? 0 }}</h3>
                <p class="text-[11px] text-[#64748B] font-medium mt-0.5">Reservasi mendatang</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/80 border border-[#CBD5E1] text-[#475569] flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-bookmark"></i>
            </div>
        </div>

    </div>

    {{-- ─────────────────────────────────────────────── --}}
    {{-- 2. FILTER & KARTU MEJA PER AREA (INDOOR & OUTDOOR) --}}
    {{-- ─────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-3 bg-white p-3.5 rounded-2xl border border-[#EADBCE] shadow-xs">
        <div class="flex items-center gap-2 overflow-x-auto text-xs">
            <span class="text-[#8A7A75] font-semibold text-xs mr-1 hidden sm:inline">Filter Area:</span>
            <button type="button" onclick="filterArea('all', this)"
                    class="area-filter-btn px-4 py-2 rounded-xl font-bold transition-all bg-[#D9A05B] text-white shadow-xs cursor-pointer">
                Semua Area ({{ $stats['total'] ?? 0 }})
            </button>
            <button type="button" onclick="filterArea('INDOOR', this)"
                    class="area-filter-btn px-4 py-2 rounded-xl font-bold transition-all bg-[#FAF6F0] text-[#3D231D] hover:bg-[#F0E6D8] border border-[#EADBCE] cursor-pointer">
                <i class="fa-solid fa-couch mr-1 text-[#C88A42]"></i> INDOOR ({{ $groupedTables->has('INDOOR') ? $groupedTables->get('INDOOR')->count() : 0 }})
            </button>
            <button type="button" onclick="filterArea('OUTDOOR', this)"
                    class="area-filter-btn px-4 py-2 rounded-xl font-bold transition-all bg-[#FAF6F0] text-[#3D231D] hover:bg-[#F0E6D8] border border-[#EADBCE] cursor-pointer">
                <i class="fa-solid fa-tree mr-1 text-emerald-600"></i> OUTDOOR ({{ $groupedTables->has('OUTDOOR') ? $groupedTables->get('OUTDOOR')->count() : 0 }})
            </button>
        </div>

        <div class="text-xs text-[#8A7A75] font-medium hidden md:block">
            Area Meja: <strong class="text-[#3D231D]">INDOOR</strong> &amp; <strong class="text-[#3D231D]">OUTDOOR</strong>
        </div>
    </div>

    @forelse($groupedTables as $areaName => $areaTables)
        <div id="area-section-{{ $areaName }}" class="area-group-section bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 space-y-5" data-area="{{ $areaName }}">
            {{-- Header Kategori Area --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-[#F5EDE3]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $areaName === 'OUTDOOR' ? 'bg-emerald-100 text-emerald-800' : 'bg-[#FAF6F0] text-[#C88A42]' }} border border-[#EADBCE] flex items-center justify-center font-bold text-sm">
                        @if($areaName === 'OUTDOOR')
                            <i class="fa-solid fa-tree"></i>
                        @else
                            <i class="fa-solid fa-couch"></i>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-[#3D231D] tracking-wide uppercase">{{ $areaName }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FAF6F0] text-[#3D231D] border border-[#EADBCE]">
                                {{ $areaTables->count() }} Meja
                            </span>
                        </div>
                        <p class="text-xs text-[#8A7A75] mt-0.5">Zona meja area {{ strtolower($areaName) }} kafe</p>
                    </div>
                </div>

                {{-- Status Count Summary for Area --}}
                <div class="flex items-center gap-2 text-[11px] font-semibold text-[#8A7A75]">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ $areaTables->where('status', 'available')->count() }} Tersedia
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        {{ $areaTables->where('status', 'occupied')->count() }} Terisi
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                        {{ $areaTables->where('status', 'reserved')->count() }} Dipesan
                    </span>
                </div>
            </div>

            {{-- Grid Kartu Meja dalam Area Ini --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($areaTables as $table)
                    <div class="rounded-2xl border-2 p-4 flex flex-col justify-between transition-all duration-200 hover:shadow-md
                        {{ $table->status === 'available' ? 'bg-white border-emerald-200 hover:border-emerald-400' : '' }}
                        {{ $table->status === 'occupied' ? 'bg-white border-amber-200 hover:border-amber-400' : '' }}
                        {{ $table->status === 'reserved' ? 'bg-white border-slate-200 hover:border-slate-400' : '' }}">

                        {{-- Bagian Atas Kartu: Nomor Meja & Status di Pojok Kanan Atas --}}
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <h4 class="text-base font-extrabold text-[#3D231D] tracking-tight">
                                        {{ $table->table_number }}
                                    </h4>
                                    {{-- Kapasitas (misal: '2 orang') --}}
                                    <div class="flex items-center gap-1.5 text-xs text-[#8A7A75] mt-0.5">
                                        <i class="fa-solid fa-users text-[#C88A42] text-[11px]"></i>
                                        <span class="font-medium">{{ $table->capacity }} orang</span>
                                    </div>
                                </div>

                                {{-- Status di Pojok Kanan Atas --}}
                                <div>
                                    @if($table->status === 'available')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>
                                            Tersedia
                                        </span>
                                    @elseif($table->status === 'occupied')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#FEFCE8] text-[#A16207] border border-[#FEF08A]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#CA8A04]"></span>
                                            Terisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#64748B]"></span>
                                            Dipesan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Informasi Order Aktif jika ada --}}
                            @if($table->currentOrder)
                                <div class="bg-[#FAF6F0] p-2.5 rounded-xl border border-[#EADBCE] text-[11px] my-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[#8A7A75]">Order Aktif:</span>
                                        <span class="font-mono font-bold text-[#3D231D]">#{{ $table->currentOrder->order_number }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-0.5">
                                        <span class="text-[#8A7A75]">Total:</span>
                                        <span class="font-extrabold text-[#C88A42]">Rp {{ number_format($table->currentOrder->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Switch Cepat Status Meja --}}
                            <div class="mt-3 pt-2.5 border-t border-[#F5EDE3]">
                                <span class="block text-[10px] text-[#8A7A75] font-semibold mb-1.5">Ubah Status Cepat:</span>
                                <div class="grid grid-cols-3 gap-1">
                                    <button type="button" 
                                            onclick="changeTableStatus({{ $table->id }}, 'available')" 
                                            class="py-1 px-1.5 rounded-lg text-[10px] font-bold text-center transition-colors cursor-pointer {{ $table->status === 'available' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        Kosong
                                    </button>
                                    <button type="button" 
                                            onclick="changeTableStatus({{ $table->id }}, 'occupied')" 
                                            class="py-1 px-1.5 rounded-lg text-[10px] font-bold text-center transition-colors cursor-pointer {{ $table->status === 'occupied' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                                        Terisi
                                    </button>
                                    <button type="button" 
                                            onclick="changeTableStatus({{ $table->id }}, 'reserved')" 
                                            class="py-1 px-1.5 rounded-lg text-[10px] font-bold text-center transition-colors cursor-pointer {{ $table->status === 'reserved' ? 'bg-slate-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                        Reservasi
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Bawah: Dua Tombol: Ubah (ikon pensil) dan Hapus (teks merah) --}}
                        <div class="mt-4 pt-3 border-t border-[#F5EDE3] flex items-center justify-between gap-2">
                            {{-- Tombol Ubah (ikon pensil) --}}
                            <button type="button" 
                                    onclick="openEditTable({{ $table->id }}, '{{ addslashes($table->table_number) }}', {{ $table->capacity }}, '{{ addslashes($table->area ?: 'INDOOR') }}', '{{ $table->status }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#FAF6F0] hover:bg-[#3D231D] text-[#3D231D] hover:text-white border border-[#EADBCE] text-xs font-semibold transition-all duration-200 cursor-pointer">
                                <i class="fa-solid fa-pen text-[11px] text-[#C88A42]"></i>
                                <span>Ubah</span>
                            </button>

                            {{-- Tombol Hapus (teks merah) --}}
                            <form action="{{ route('tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Hapus {{ addslashes($table->table_number) }}? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-rose-50 text-rose-600 hover:text-rose-700 text-xs font-semibold transition-all duration-200 cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-12 text-center text-[#8A7A75]">
            <i class="fa-solid fa-table-cells-large text-4xl mb-3 text-[#D9A05B]"></i>
            <h4 class="text-base font-bold text-[#3D231D]">Belum Ada Meja Terdaftar</h4>
            <p class="text-xs text-[#8A7A75] mt-1 max-w-sm mx-auto">Klik tombol 'Tambah Meja' di kanan atas untuk mendaftarkan meja kafe Anda.</p>
            <button type="button" 
                    onclick="document.getElementById('modal-add-table').classList.remove('hidden')"
                    class="mt-4 inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Meja Sekarang</span>
            </button>
        </div>
    @endforelse

</div>

{{-- ─────────────────────────────────────────────── --}}
{{-- MODAL TAMBAH MEJA                               --}}
{{-- ─────────────────────────────────────────────── --}}
<div id="modal-add-table" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-base font-bold text-[#3D231D] flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-[#C88A42]"></i>
                <span>Tambah Meja Baru</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-add-table').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form action="{{ route('tables.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Nomor / Nama Meja *</label>
                <input type="text" name="table_number" required placeholder="Contoh: Meja 09 atau Meja 10"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kapasitas (Orang) *</label>
                    <input type="number" name="capacity" value="2" min="1" max="50" required
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kategori Area *</label>
                    <select name="area" required class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                        <option value="INDOOR">INDOOR</option>
                        <option value="OUTDOOR">OUTDOOR</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Status Awal *</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                    <option value="available">Tersedia (Kosong)</option>
                    <option value="occupied">Terisi (Ada Pelanggan)</option>
                    <option value="reserved">Dipesan (Reservasi)</option>
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-add-table').classList.add('hidden')"
                        class="px-4 py-2.5 bg-[#FAF6F0] hover:bg-stone-200 text-[#3D231D] rounded-xl text-xs font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Simpan Meja</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─────────────────────────────────────────────── --}}
{{-- MODAL EDIT MEJA                                 --}}
{{-- ─────────────────────────────────────────────── --}}
<div id="modal-edit-table" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-base font-bold text-[#3D231D] flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-[#C88A42]"></i>
                <span>Edit Data Meja</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-edit-table').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D] cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
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

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kapasitas (Orang) *</label>
                    <input type="number" id="edit-table-capacity" name="capacity" min="1" max="50" required
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kategori Area *</label>
                    <select id="edit-table-area" name="area" required class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                        <option value="INDOOR">INDOOR</option>
                        <option value="OUTDOOR">OUTDOOR</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Status Meja *</label>
                <select id="edit-table-status" name="status" class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                    <option value="available">Tersedia (Kosong)</option>
                    <option value="occupied">Terisi (Ada Pelanggan)</option>
                    <option value="reserved">Dipesan (Reservasi)</option>
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-edit-table').classList.add('hidden')"
                        class="px-4 py-2.5 bg-[#FAF6F0] hover:bg-stone-200 text-[#3D231D] rounded-xl text-xs font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi Filter Tampilan Berdasarkan Kategori Area (INDOOR & OUTDOOR)
    function filterArea(area, btn) {
        document.querySelectorAll('.area-filter-btn').forEach(b => {
            b.classList.remove('bg-[#D9A05B]', 'text-white', 'shadow-xs');
            b.classList.add('bg-[#FAF6F0]', 'text-[#3D231D]', 'border', 'border-[#EADBCE]');
        });
        if (btn) {
            btn.classList.remove('bg-[#FAF6F0]', 'text-[#3D231D]', 'border', 'border-[#EADBCE]');
            btn.classList.add('bg-[#D9A05B]', 'text-white', 'shadow-xs');
        }

        document.querySelectorAll('.area-group-section').forEach(sec => {
            if (area === 'all' || sec.dataset.area === area) {
                sec.classList.remove('hidden');
            } else {
                sec.classList.add('hidden');
            }
        });
    }

    // Fungsi Muat Ulang Halaman Meja
    function refreshTablesPage(btn) {
        if (btn) {
            btn.classList.add('opacity-75', 'pointer-events-none');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spin');
            }
        }
        window.location.reload();
    }

    // Buka Modal Edit Meja (Hanya INDOOR dan OUTDOOR)
    function openEditTable(id, tableNumber, capacity, area, status) {
        document.getElementById('form-edit-table').action = `/tables/${id}`;
        document.getElementById('edit-table-number').value = tableNumber;
        document.getElementById('edit-table-capacity').value = capacity;
        document.getElementById('edit-table-area').value = (area === 'OUTDOOR') ? 'OUTDOOR' : 'INDOOR';
        document.getElementById('edit-table-status').value = status;
        document.getElementById('modal-edit-table').classList.remove('hidden');
    }

    // Ubah Status Meja Secara Cepat via AJAX
    async function changeTableStatus(tableId, newStatus) {
        try {
            const res = await fetch(`/tables/${tableId}/status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: newStatus })
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (err) {
            alert('Gagal mengubah status meja.');
        }
    }
</script>
@endpush
