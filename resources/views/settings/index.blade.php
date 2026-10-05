@extends('layouts.app')

@section('title', 'Pengaturan Profil & Sistem')
@section('page_title', 'Pengaturan Kafe & Profil')
@section('page_subtitle', 'Konfigurasi Identitas Toko, Tarif Pajak, dan Akun Pengguna')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- CAFE & RECEIPT SETTINGS -->
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 sm:p-8">
        <h3 class="text-base font-bold text-[#3D231D] mb-1 flex items-center gap-2">
            <i class="fa-solid fa-store text-[#C88A42]"></i>
            <span>Identitas Kafe & Struk Pembayaran</span>
        </h3>
        <p class="text-xs text-[#8A7A75] mb-5">Data ini dicetak pada struk kasir dan ditampilkan di aplikasi.</p>

        <form action="{{ route('settings.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Nama Kafe *</label>
                <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'd\'nale caffe' }}" required
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Slogan / Tagline</label>
                <input type="text" name="store_tagline" value="{{ $settings['store_tagline'] ?? 'Artisan Coffee & Good Vibes' }}"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Alamat Lengkap</label>
                <input type="text" name="store_address" value="{{ $settings['store_address'] ?? 'Jl. Boulevard Kopi No. 8, Jakarta' }}"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">No. Telepon Kafe</label>
                    <input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '+62 812-3456-7890' }}"
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Pajak Resto (%)</label>
                    <input type="number" name="tax_percentage" value="{{ $settings['tax_percentage'] ?? '10' }}" min="0" max="100"
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Pesan Footer Struk</label>
                <input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? 'Terima kasih atas kunjungan Anda! Follow IG: @dnalecaffe' }}"
                       class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
            </div>

            <div class="pt-3">
                <button type="submit" class="px-5 py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengaturan Kafe</span>
                </button>
            </div>
        </form>
    </div>

    <!-- USER PROFILE SETTINGS -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 sm:p-8">
            <h3 class="text-base font-bold text-[#3D231D] mb-1 flex items-center gap-2">
                <i class="fa-solid fa-user-gear text-[#C88A42]"></i>
                <span>Profil Akun Saya</span>
            </h3>
            <p class="text-xs text-[#8A7A75] mb-5">Perbarui nama pengguna dan kata sandi Anda.</p>

            <form action="{{ route('settings.profile') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Nama Anda *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Email Akun *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                           class="w-full px-3.5 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
                </div>

                <div class="pt-3 border-t border-[#EADBCE]">
                    <span class="text-xs font-bold text-[#3D231D] block mb-2">Ganti Kata Sandi (Opsional)</span>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] text-[#8A7A75] mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password" placeholder="••••••••"
                                   class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] text-[#8A7A75] mb-1">Password Baru</label>
                                <input type="password" name="new_password" placeholder="Min. 6 karakter"
                                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] text-[#8A7A75] mb-1">Konfirmasi Password</label>
                                <input type="password" name="new_password_confirmation" placeholder="Ulangi password"
                                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-[#D9A05B] to-[#C88A42] hover:brightness-105 text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                        Perbarui Profil Saya
                    </button>
                </div>
            </form>
        </div>

        <!-- SYSTEM BADGE INFO -->
        <div class="bg-[#FAF6F0] p-5 rounded-2xl border border-[#EADBCE] text-xs text-[#8A7A75] space-y-1.5">
            <h4 class="font-bold text-[#3D231D] uppercase tracking-wider text-[11px] mb-2 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-[#C88A42]"></i>
                <span>Informasi Sistem POS</span>
            </h4>
            <div class="flex justify-between"><span>Aplikasi:</span><strong class="text-[#3D231D]">d'nale caffe v1.0</strong></div>
            <div class="flex justify-between"><span>Framework:</span><span class="text-[#3D231D]">Laravel {{ app()->version() }} & Tailwind CSS</span></div>
            <div class="flex justify-between"><span>PHP Version:</span><span class="text-[#3D231D]">{{ phpversion() }}</span></div>
            <div class="flex justify-between"><span>Server Time:</span><span class="text-[#3D231D]">{{ now()->format('d M Y H:i') }} WIB</span></div>
        </div>
    </div>
</div>
@endsection
