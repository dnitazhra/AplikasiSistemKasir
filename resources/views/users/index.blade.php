@extends('layouts.app')

@section('title', 'Karyawan & Role')
@section('page_title', 'Manajemen Karyawan & Role')
@section('page_subtitle', 'Kelola Akun Akses Kasir, Barista, dan Administrator Kafe')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- ADD USER FORM -->
    <div class="bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6 h-fit">
        <h3 class="text-sm font-bold text-[#3D231D] mb-1 flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-[#C88A42]"></i>
            <span>Tambah Karyawan Baru</span>
        </h3>
        <p class="text-xs text-[#8A7A75] mb-4">Buat akun untuk kasir, barista, atau manajer.</p>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Nama Lengkap *</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Username *</label>
                <input type="text" name="username" required placeholder="Contoh: budi_kasir"
                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Email *</label>
                <input type="email" name="email" required placeholder="Contoh: budi@dnalecaffe.com"
                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] focus:ring-2 focus:ring-[#D9A05B] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Peran / Role *</label>
                <select name="role" required class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none">
                    <option value="cashier">Kasir (Cashier)</option>
                    <option value="barista">Barista (Dapur / Bar)</option>
                    <option value="admin">Administrator / Manajer</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">No. Telepon (WhatsApp)</label>
                <input type="text" name="phone" placeholder="08123456789"
                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1 uppercase tracking-wider">Kata Sandi Awal *</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                       class="w-full px-3.5 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Simpan Karyawan</span>
            </button>
        </form>
    </div>

    <!-- USERS LIST TABLE -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-[#EADBCE] shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-[#3D231D]">Daftar Karyawan Aktif</h3>
                <p class="text-xs text-[#8A7A75] mt-0.5">Terdapat {{ $users->count() }} akun terdaftar</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#EADBCE] text-[#8A7A75] font-semibold uppercase tracking-wider">
                        <th class="pb-3">Karyawan</th>
                        <th class="pb-3">Username</th>
                        <th class="pb-3">Email & Kontak</th>
                        <th class="pb-3">Peran (Role)</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EADBCE]/50">
                    @foreach($users as $u)
                        <tr class="hover:bg-[#FAF6F0]/50 transition-colors">
                            <td class="py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#FAF6F0] border border-[#EADBCE] text-[#D9A05B] font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-[#3D231D]">{{ $u->name }}</h5>
                                        <span class="text-[10px] text-[#8A7A75]">ID: #{{ $u->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 font-mono text-[11px] text-[#3D231D]">
                                {{ $u->username }}
                            </td>
                            <td class="py-3.5">
                                <div class="text-[#3D231D]">{{ $u->email }}</div>
                                <div class="text-[10px] text-[#8A7A75]">{{ $u->phone ?? '-' }}</div>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                    {{ $u->role === 'admin' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $u->role === 'barista' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $u->role === 'cashier' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openEditUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->username) }}', '{{ addslashes($u->email) }}', '{{ $u->role }}', '{{ addslashes($u->phone ?? '') }}')"
                                            class="p-1.5 text-[#8A7A75] hover:text-[#3D231D]" title="Edit Karyawan">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    @if(auth()->id() !== $u->id)
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus karyawan {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-600" title="Hapus Karyawan">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div id="modal-edit-user" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-[#EADBCE] shadow-2xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-[#EADBCE] mb-4">
            <h3 class="text-sm font-bold text-[#3D231D]">Edit Data Karyawan</h3>
            <button type="button" onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="text-[#8A7A75] hover:text-[#3D231D]">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="form-edit-user" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">Nama Lengkap:</label>
                <input type="text" id="edit-name" name="name" required
                       class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">Username:</label>
                <input type="text" id="edit-username" name="username" required
                       class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">Email:</label>
                <input type="email" id="edit-email" name="email" required
                       class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">Peran / Role:</label>
                <select id="edit-role" name="role" required class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-bold text-[#3D231D] outline-none">
                    <option value="cashier">Kasir</option>
                    <option value="barista">Barista</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">No. Telepon:</label>
                <input type="text" id="edit-phone" name="phone"
                       class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs font-semibold text-[#3D231D] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3D231D] mb-1">Ubah Kata Sandi (Kosongkan bila tidak diubah):</label>
                <input type="password" name="password" placeholder="••••••••"
                       class="w-full px-3 py-2 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-xs text-[#3D231D] outline-none">
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#3D231D] hover:bg-[#2A1713] text-white rounded-xl text-xs font-bold shadow-xs mt-2">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditUser(id, name, username, email, role, phone) {
        document.getElementById('form-edit-user').action = `/users/${id}`;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-username').value = username;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value = role;
        document.getElementById('edit-phone').value = phone;
        document.getElementById('modal-edit-user').classList.remove('hidden');
    }
</script>
@endpush
