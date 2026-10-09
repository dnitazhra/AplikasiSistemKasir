@extends('layouts.app')

@section('title', 'Kategori Menu')

@section('page_title', 'Kategori Menu')
@section('page_subtitle', 'Kelompokkan menu cafe, misalnya Coffee, Non-Coffee, atau Dessert')

@section('header_actions')
    <button type="button" onclick="openAddModal()"
            class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-sm transition-all duration-200 active:scale-95">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Tambah Kategori</span>
    </button>
@endsection

@section('content')

{{-- ─────────────────────────────────────────────── --}}
{{-- INFO SUMMARY                                    --}}
{{-- ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-[0_2px_12px_-2px_rgba(61,35,29,0.07)] px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-tags text-[#C88A42]"></i>
        </div>
        <div>
            <p class="text-xs text-[#8A7A75] font-medium">Total Kategori</p>
            <p class="text-xl font-extrabold text-[#3D231D]">{{ $categories->count() }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-[0_2px_12px_-2px_rgba(61,35,29,0.07)] px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-utensils text-[#C88A42]"></i>
        </div>
        <div>
            <p class="text-xs text-[#8A7A75] font-medium">Total Produk</p>
            <p class="text-xl font-extrabold text-[#3D231D]">{{ $categories->sum('menus_count') }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-[0_2px_12px_-2px_rgba(61,35,29,0.07)] px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-layer-group text-emerald-500"></i>
        </div>
        <div>
            <p class="text-xs text-[#8A7A75] font-medium">Kategori Terisi</p>
            <p class="text-xl font-extrabold text-[#3D231D]">{{ $categories->where('menus_count', '>', 0)->count() }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-[0_2px_12px_-2px_rgba(61,35,29,0.07)] px-5 py-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-folder-open text-amber-500"></i>
        </div>
        <div>
            <p class="text-xs text-[#8A7A75] font-medium">Kategori Kosong</p>
            <p class="text-xl font-extrabold text-[#3D231D]">{{ $categories->where('menus_count', 0)->count() }}</p>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────── --}}
{{-- TABEL KATEGORI                                  --}}
{{-- ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-[0_2px_12px_-2px_rgba(61,35,29,0.08)] border border-[#EADBCE] overflow-hidden">

    {{-- Table Header --}}
    <div class="px-6 py-4 border-b border-[#EADBCE] flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center">
                <i class="fa-solid fa-tags text-[#C88A42] text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-[#3D231D]">Semua Kategori</h3>
                <p class="text-xs text-[#8A7A75]">{{ $categories->count() }} kategori terdaftar</p>
            </div>
        </div>
        <button type="button" onclick="openAddModal()"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#C88A42] hover:text-[#3D231D] transition-colors duration-200">
            <i class="fa-solid fa-plus text-[10px]"></i>
            Tambah Baru
        </button>
    </div>

    @if($categories->isEmpty())
        <div class="p-16 text-center">
            <div class="w-14 h-14 bg-[#FAF6F0] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-tags text-[#D9A05B] text-xl"></i>
            </div>
            <p class="text-[#3D231D] font-semibold mb-1">Belum ada kategori</p>
            <p class="text-[#8A7A75] text-sm mb-5">Buat kategori pertama untuk mengelompokkan menu kafe.</p>
            <button type="button" onclick="openAddModal()"
                    class="inline-flex items-center gap-2 bg-[#3D231D] hover:bg-[#2A1713] text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Kategori
            </button>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#FAF6F0] border-b border-[#EADBCE]">
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold tracking-widest uppercase text-[#3D231D]">Kategori</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold tracking-widest uppercase text-[#3D231D] hidden sm:table-cell">Keterangan</th>
                        <th class="text-center px-6 py-3.5 text-[11px] font-bold tracking-widest uppercase text-[#3D231D]">Jumlah</th>
                        <th class="text-right px-6 py-3.5 text-[11px] font-bold tracking-widest uppercase text-[#3D231D]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5EDE3]">
                    @foreach($categories as $category)
                    <tr class="hover:bg-[#FAF6F0]/70 transition-colors duration-150 group">

                        {{-- Nama Kategori --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-sm
                                    {{ $loop->index % 5 === 0 ? 'bg-amber-100 text-amber-700' :
                                       ($loop->index % 5 === 1 ? 'bg-emerald-100 text-emerald-700' :
                                       ($loop->index % 5 === 2 ? 'bg-blue-100 text-blue-700' :
                                       ($loop->index % 5 === 3 ? 'bg-purple-100 text-purple-700' : 'bg-rose-100 text-rose-700'))) }}">
                                    {{ strtoupper(substr($category->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-[#2D2422]">{{ $category->name }}</p>
                                    <p class="text-[11px] text-[#8A7A75] font-mono">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Keterangan / Deskripsi --}}
                        <td class="px-6 py-4 hidden sm:table-cell">
                            @php
                                $descriptions = [
                                    'coffee'        => 'Aneka racikan kopi espresso based dan manual brew',
                                    'espresso'      => 'Aneka racikan kopi espresso based dan manual brew',
                                    'dessert'       => 'Hidangan penutup manis',
                                    'makanan berat' => 'Menu hidangan utama mengenyangkan',
                                    'makanan'       => 'Menu hidangan utama mengenyangkan',
                                    'non coffee'    => 'Minuman segar tanpa kafein',
                                    'non-coffee'    => 'Minuman segar tanpa kafein',
                                    'snacks'        => 'Camilan ringan dan kudapan gurih',
                                    'pastry'        => 'Kue dan roti artisan segar',
                                    'minuman'       => 'Aneka pilihan minuman segar',
                                ];
                                $key  = strtolower($category->name);
                                $desc = $descriptions[$key] ?? 'Produk dalam kategori ' . $category->name;
                            @endphp
                            <p class="text-sm text-[#8A7A75]">{{ $desc }}</p>
                        </td>

                        {{-- Jumlah Produk --}}
                        <td class="px-6 py-4 text-center">
                            @if($category->menus_count > 0)
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2.5 py-1 rounded-xl bg-[#FDF6ED] border border-[#EADBCE] text-sm font-bold text-[#C88A42]">
                                    {{ $category->menus_count }}
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2.5 py-1 rounded-xl bg-[#FAF6F0] border border-[#EADBCE] text-sm font-semibold text-[#B9A99A]">
                                    0
                                </span>
                            @endif
                        </td>

                        {{-- Tombol Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                        class="inline-flex items-center gap-1.5 bg-[#FAF6F0] hover:bg-[#3D231D] hover:text-white border border-[#EADBCE] hover:border-[#3D231D] text-[#3D231D] px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                    Ubah
                                </button>

                                @if($category->menus_count === 0)
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                          onsubmit="return confirm('Hapus kategori \'{{ addslashes($category->name) }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 bg-white hover:bg-rose-50 border border-[#EADBCE] hover:border-rose-200 text-rose-500 hover:text-rose-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span title="Tidak bisa dihapus: kategori masih memiliki {{ $category->menus_count }} produk"
                                          class="inline-flex items-center gap-1.5 bg-[#FAF6F0] border border-[#EADBCE] text-[#B9A99A] px-3 py-1.5 rounded-lg text-xs font-semibold cursor-not-allowed"
                                          style="opacity:0.5">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        Hapus
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Table Footer --}}
        <div class="px-6 py-3.5 border-t border-[#EADBCE] bg-[#FAF6F0]/50 flex items-center justify-between">
            <p class="text-xs text-[#8A7A75]">
                Total <span class="font-semibold text-[#3D231D]">{{ $categories->count() }}</span> kategori
                dengan <span class="font-semibold text-[#3D231D]">{{ $categories->sum('menus_count') }}</span> produk
            </p>
            <a href="{{ route('menus.index') }}"
               class="text-xs font-semibold text-[#C88A42] hover:text-[#3D231D] transition-colors flex items-center gap-1">
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                Lihat Semua Produk
            </a>
        </div>
    @endif
</div>

{{-- ─────────────────────────────────────────────── --}}
{{-- MODAL: TAMBAH KATEGORI                          --}}
{{-- ─────────────────────────────────────────────── --}}
<div id="modal-add" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#2D2422]/50 backdrop-blur-sm" onclick="closeAddModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-[#EADBCE]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center">
                    <i class="fa-solid fa-plus text-[#C88A42]"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-[#2D2422]">Tambah Kategori</h3>
                    <p class="text-xs text-[#8A7A75]">Buat kelompok baru untuk menu kafe</p>
                </div>
            </div>
            <button type="button" onclick="closeAddModal()"
                    class="w-8 h-8 rounded-lg hover:bg-[#FAF6F0] flex items-center justify-center text-[#8A7A75] hover:text-[#3D231D] transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="px-6 py-5">
                <label class="block text-xs font-bold text-[#3D231D] mb-2 tracking-wide uppercase">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="add-name"
                       placeholder="Contoh: Signature Drink, Dessert..."
                       required autofocus
                       class="w-full px-4 py-3 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] placeholder-[#B9A99A] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all">
                <p class="text-[11px] text-[#8A7A75] mt-2">
                    <i class="fa-solid fa-circle-info text-[#C88A42] mr-1"></i>
                    Slug URL akan digenerate otomatis dari nama.
                </p>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-[#EADBCE] bg-[#FAF6F0]/60 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-[#EADBCE] bg-white text-[#3D231D] hover:bg-[#FAF6F0] transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-xl bg-[#3D231D] hover:bg-[#2A1713] text-white shadow-sm transition-all active:scale-95">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─────────────────────────────────────────────── --}}
{{-- MODAL: UBAH KATEGORI                            --}}
{{-- ─────────────────────────────────────────────── --}}
<div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#2D2422]/50 backdrop-blur-sm" onclick="closeEditModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-[#EADBCE]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#FDF6ED] border border-[#EADBCE] flex items-center justify-center">
                    <i class="fa-solid fa-pen text-[#C88A42]"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-[#2D2422]">Ubah Kategori</h3>
                    <p class="text-xs text-[#8A7A75]">Perbarui nama kategori</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()"
                    class="w-8 h-8 rounded-lg hover:bg-[#FAF6F0] flex items-center justify-center text-[#8A7A75] hover:text-[#3D231D] transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="edit-form" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="px-6 py-5">
                <label class="block text-xs font-bold text-[#3D231D] mb-2 tracking-wide uppercase">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="edit-name"
                       placeholder="Nama kategori..."
                       required
                       class="w-full px-4 py-3 text-sm rounded-xl border border-[#EADBCE] bg-[#FAF6F0] text-[#2D2422] placeholder-[#B9A99A] focus:outline-none focus:ring-2 focus:ring-[#D9A05B]/40 focus:border-[#D9A05B] transition-all">
                <p class="text-[11px] text-[#8A7A75] mt-2">
                    <i class="fa-solid fa-circle-info text-[#C88A42] mr-1"></i>
                    Slug URL akan diperbarui otomatis.
                </p>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-[#EADBCE] bg-[#FAF6F0]/60 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-[#EADBCE] bg-white text-[#3D231D] hover:bg-[#FAF6F0] transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-xl bg-[#3D231D] hover:bg-[#2A1713] text-white shadow-sm transition-all active:scale-95">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Modal Tambah ──
    function openAddModal() {
        const modal = document.getElementById('modal-add');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => document.getElementById('add-name').focus(), 100);
    }
    function closeAddModal() {
        const modal = document.getElementById('modal-add');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ── Modal Ubah ──
    function openEditModal(id, name) {
        const modal    = document.getElementById('modal-edit');
        const form     = document.getElementById('edit-form');
        const nameInput = document.getElementById('edit-name');

        form.action  = `/categories/${id}`;
        nameInput.value = name;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => nameInput.focus(), 100);
    }
    function closeEditModal() {
        const modal = document.getElementById('modal-edit');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
        }
    });

    // Auto-buka modal tambah jika ada error validasi dari store
    @if($errors->any() && old('_action') === 'store')
        openAddModal();
    @elseif($errors->any())
        openAddModal();
    @endif
</script>
@endpush
