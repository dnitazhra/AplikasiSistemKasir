@extends('layouts.app')

@section('title', 'Tambah Menu Baru')
@section('page_title', 'Tambah Menu Baru')
@section('page_subtitle', 'Masukkan Rincian Menu dan Pilihan Varian')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 sm:p-8">
        
        <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- BASIC INFO -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Kategori Menu *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Harga Dasar (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="500" placeholder="Contoh: 28000"
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                    @error('price')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Nama Menu *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Aren Latte Double Shot"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                @error('name')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload with Live Preview -->
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Foto Produk Menu (Opsional)</label>
                <div class="flex items-center gap-4 p-4 bg-[#FAF6F0] rounded-xl border border-[#EADBCE]">
                    <div id="image-preview-container" class="w-16 h-16 rounded-xl bg-white border border-[#EADBCE] flex items-center justify-center text-[#D9A05B] overflow-hidden flex-shrink-0">
                        <i class="fa-solid fa-image text-2xl text-[#C88A42]" id="preview-placeholder"></i>
                        <img id="image-preview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" id="menu-image" accept="image/*" onchange="previewMenuImage(this)"
                               class="text-xs text-[#8A7A75] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#3D231D] file:text-white hover:file:bg-[#2A1713] cursor-pointer">
                        <p class="text-[11px] text-[#8A7A75] mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB).</p>
                    </div>
                </div>
                @error('image')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1.5 uppercase tracking-wider">Deskripsi Menu</label>
                <textarea name="description" rows="3" placeholder="Jelaskan aroma, cita rasa, dan bahan baku khas..."
                          class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Availability Switch -->
            <div class="flex items-center gap-3 p-3.5 bg-[#FAF6F0] rounded-xl border border-[#EADBCE]">
                <input type="checkbox" name="is_available" id="is_available" value="1" checked class="w-4 h-4 rounded text-[#D9A05B] border-[#EADBCE] focus:ring-[#D9A05B]">
                <label for="is_available" class="text-xs font-bold text-[#3D231D] cursor-pointer">
                    Menu Tersedia untuk Dijual (Aktif di POS)
                </label>
            </div>

            <!-- DYNAMIC VARIANTS SECTION -->
            <div class="pt-4 border-t border-[#EADBCE]">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#3D231D]">Variasi / Opsi Menu (Opsional)</h4>
                        <p class="text-[11px] text-[#8A7A75]">Contoh: Ukuran (Regular/Large), Kadar Gula, Panas/Dingin</p>
                    </div>
                    <button type="button" onclick="addVariantGroup()" class="px-3 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-bold text-[#3D231D] transition-colors">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Grup Variasi
                    </button>
                </div>

                <div id="variants-container" class="space-y-4">
                    <!-- Dynamic variants will appear here -->
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#EADBCE]">
                <a href="{{ route('menus.index') }}" class="px-4 py-2.5 bg-[#FAF6F0] hover:bg-[#EADBCE] text-[#3D231D] font-bold text-xs rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                    Simpan Menu Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewMenuImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('preview-placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }

    let variantCount = 0;

    function addVariantGroup() {
        const idx = variantCount++;
        const container = document.getElementById('variants-container');

        const card = document.createElement('div');
        card.id = `variant-card-${idx}`;
        card.className = 'p-4 bg-[#FAF6F0] rounded-xl border border-[#EADBCE] space-y-3';

        card.innerHTML = `
            <div class="flex items-center justify-between">
                <input type="text" name="variants[${idx}][name]" placeholder="Nama Grup (cth: Ukuran / Suhu)" required
                       class="px-3 py-1.5 bg-white border border-[#EADBCE] rounded-lg text-xs font-bold text-[#3D231D] w-64 outline-none focus:ring-1 focus:ring-[#D9A05B]">
                <button type="button" onclick="document.getElementById('variant-card-${idx}').remove()" class="text-xs text-rose-500 hover:text-rose-700 font-bold">
                    <i class="fa-solid fa-trash-can mr-1"></i>Hapus Grup
                </button>
            </div>

            <div class="space-y-2">
                <span class="text-[10px] font-bold text-[#8A7A75] uppercase block">Opsi Pilihan & Harga Tambahan:</span>
                <div id="options-container-${idx}" class="space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="text" name="variants[${idx}][options][0][name]" placeholder="Nama Opsi (cth: Regular)" required
                               class="flex-1 px-3 py-1.5 bg-white border border-[#EADBCE] rounded-lg text-xs text-[#3D231D] outline-none">
                        <div class="relative w-36">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-[#8A7A75]">+Rp</span>
                            <input type="number" name="variants[${idx}][options][0][additional_price]" value="0" min="0" placeholder="0"
                                   class="w-full pl-9 pr-2.5 py-1.5 bg-white border border-[#EADBCE] rounded-lg text-xs text-[#3D231D] outline-none">
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addOptionRow(${idx})" class="text-[11px] text-[#C88A42] hover:text-[#3D231D] font-bold mt-1">
                    + Tambah Opsi Lain
                </button>
            </div>
        `;

        container.appendChild(card);
    }

    function addOptionRow(variantIdx) {
        const container = document.getElementById(`options-container-${variantIdx}`);
        const optIdx = container.children.length;

        const row = document.createElement('div');
        row.className = 'flex items-center gap-2';
        row.innerHTML = `
            <input type="text" name="variants[${variantIdx}][options][${optIdx}][name]" placeholder="Nama Opsi (cth: Large)" required
                   class="flex-1 px-3 py-1.5 bg-white border border-[#EADBCE] rounded-lg text-xs text-[#3D231D] outline-none">
            <div class="relative w-36">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-[#8A7A75]">+Rp</span>
                <input type="number" name="variants[${variantIdx}][options][${optIdx}][additional_price]" value="5000" min="0" placeholder="0"
                       class="w-full pl-9 pr-2.5 py-1.5 bg-white border border-[#EADBCE] rounded-lg text-xs text-[#3D231D] outline-none">
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-xs px-1">×</button>
        `;
        container.appendChild(row);
    }
</script>
@endpush
