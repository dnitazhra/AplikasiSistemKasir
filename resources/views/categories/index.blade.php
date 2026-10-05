@extends('layouts.app')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori Menu Kafe')
@section('page_subtitle', 'Kelola Pengelompokan Minuman & Makanan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- ADD CATEGORY FORM -->
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 h-fit">
        <h3 class="text-sm font-bold text-[#3D231D] mb-1 flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-[#C88A42]"></i>
            <span>Tambah Kategori Baru</span>
        </h3>
        <p class="text-xs text-[#8A7A75] mb-4">Buat kategori baru untuk mengelompokkan menu di POS.</p>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Nama Kategori *</label>
                <input type="text" name="name" required placeholder="Contoh: Mocktails & Refresher"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Simpan Kategori</span>
            </button>
        </form>
    </div>

    <!-- CATEGORIES LIST TABLE -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-[#3D231D]">Daftar Kategori Aktif</h3>
                <p class="text-xs text-[#8A7A75] mt-0.5">Total terdapat {{ $categories->count() }} kategori</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#EADBCE] text-[#8A7A75] font-semibold uppercase tracking-wider">
                        <th class="pb-3">No</th>
                        <th class="pb-3">Nama Kategori</th>
                        <th class="pb-3">Slug URL</th>
                        <th class="pb-3">Jumlah Menu</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EADBCE]/50">
                    @forelse($categories as $idx => $cat)
                        <tr class="hover:bg-[#FAF6F0]/50 transition-colors">
                            <td class="py-3 text-[#8A7A75]">{{ $idx + 1 }}</td>
                            <td class="py-3 font-bold text-[#3D231D]">
                                {{ $cat->name }}
                            </td>
                            <td class="py-3 text-[#8A7A75] font-mono text-[11px]">{{ $cat->slug }}</td>
                            <td class="py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FAF6F0] border border-[#EADBCE] text-[#3D231D]">
                                    {{ $cat->menus_count }} menu
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openEditCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                                            class="p-1.5 text-[#8A7A75] hover:text-[#3D231D]" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Menu yang terkait akan ikut terhapus!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-600" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-[#8A7A75]">Belum ada kategori yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Category -->
<div id="modal-edit-category" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D]">Edit Kategori</h3>
            <button type="button" onclick="document.getElementById('modal-edit-category').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D]">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="form-edit-cat" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5">Nama Kategori:</label>
                <input type="text" id="edit-cat-name" name="name" required
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none focus:ring-2 focus:ring-[#D9A05B]">
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white rounded-xl text-xs font-bold">
                Perbarui Kategori
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditCategory(id, name) {
        document.getElementById('form-edit-cat').action = `/categories/${id}`;
        document.getElementById('edit-cat-name').value = name;
        document.getElementById('modal-edit-category').classList.remove('hidden');
    }
</script>
@endpush
