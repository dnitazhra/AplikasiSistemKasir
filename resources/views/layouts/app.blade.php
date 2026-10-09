<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — d'nale caffe POS</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS (CDN fallback + fast responsive rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dnale: {
                            sidebar: '#3D231D',
                            sidebarDark: '#2A1713',
                            sidebarHover: '#4A2E2B',
                            primary: '#3D231D',
                            primaryLight: '#4A2E2B',
                            cream: '#FAF6F0',
                            creamCard: '#FFFFFF',
                            accent: '#D9A05B',
                            accentHover: '#C88A42',
                            accentLight: '#FDF6ED',
                            border: '#EADBCE',
                            muted: '#8A7A75',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'dnale': '0 4px 20px -2px rgba(61, 35, 29, 0.06), 0 2px 6px -1px rgba(61, 35, 29, 0.04)',
                        'dnale-lg': '0 10px 25px -4px rgba(61, 35, 29, 0.12), 0 4px 10px -2px rgba(61, 35, 29, 0.06)',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF6F0;
        }

        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1ECE4;
        }
        ::-webkit-scrollbar-thumb {
            background: #D9A05B;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #C88A42;
        }

        /* Sidebar Dark Scrollbar */
        #nav-menu::-webkit-scrollbar-track {
            background: #2A1713;
        }
        #nav-menu::-webkit-scrollbar-thumb {
            background: #4A2E2B;
            border-radius: 9999px;
        }
        #nav-menu::-webkit-scrollbar-thumb:hover {
            background: #D9A05B;
        }
        
        @keyframes pulseSlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.03); }
        }
        .animate-pulse-slow {
            animation: pulseSlow 3s infinite ease-in-out;
        }
    </style>

    @stack('styles')
</head>
<body class="h-screen overflow-hidden bg-[#FAF6F0] text-[#2D2422] antialiased flex flex-col md:flex-row">

    <!-- 1. NAVBAR / SIDEBAR KIRI (STICKY / FIXED HEIGHT h-screen) -->
    <aside id="sidebar" class="w-full md:w-72 md:h-screen bg-[#3D231D] text-[#FAF6F0] flex-shrink-0 flex flex-col justify-between shadow-2xl z-30 transition-all duration-300">
        <div class="flex flex-col flex-1 min-h-0">
            <!-- BRAND LOGO HEADER (FIXED TOP) -->
            <div class="p-6 border-b border-[#4A2E2B] flex items-center justify-between flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#C88A42] to-[#D9A05B] flex items-center justify-center text-white shadow-lg group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-mug-hot text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-white flex items-center gap-1.5">
                            d'nale <span class="text-[#D9A05B] font-light">caffe</span>
                        </h1>
                        <p class="text-xs text-[#D9A05B]/80 font-medium tracking-wider uppercase">Artisan POS & Bar</p>
                    </div>
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobile-toggle" class="md:hidden text-[#FAF6F0] p-2 hover:bg-[#4A2E2B] rounded-lg">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

            <!-- NAVIGATION MENU (INDEPENDENT INTERNAL SCROLL) -->
            <nav id="nav-menu" class="p-4 flex-1 overflow-y-auto hidden md:block">

                <!-- ─── KATEGORI: MENU UTAMA ─── -->
                <p class="px-3 py-1.5 text-[11px] font-semibold tracking-wider text-[#D9A05B]/70 uppercase mb-1">Menu Utama</p>
                <div class="space-y-1 mb-4">

                    <!-- Dashboard (AKTIF) -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#D9A05B] text-white shadow-md font-semibold' : 'text-[#FAF6F0]/80 hover:bg-[#4A2E2B] hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie text-base w-5 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-[#D9A05B]' }}"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- ─── KATEGORI: OPERASIONAL ─── -->
                <p class="px-3 py-1.5 text-[11px] font-semibold tracking-wider text-[#D9A05B]/70 uppercase mb-1">Operasional</p>
                <div class="space-y-1 mb-4">

                    <!-- Bill Aktif (AKTIF) -->
                    <a href="{{ route('bills.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('bills.*') ? 'bg-[#D9A05B] text-white shadow-md font-semibold' : 'text-[#FAF6F0]/80 hover:bg-[#4A2E2B] hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice text-base w-5 text-center {{ request()->routeIs('bills.*') ? 'text-white' : 'text-[#D9A05B]' }}"></i>
                        <span>Bill Aktif</span>
                    </a>

                    <!-- Dapur & Bar (AKTIF) -->
                    <a href="{{ route('kitchen.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('kitchen.*') ? 'bg-[#D9A05B] text-white shadow-md font-semibold' : 'text-[#FAF6F0]/80 hover:bg-[#4A2E2B] hover:text-white' }}">
                        <i class="fa-solid fa-blender text-base w-5 text-center {{ request()->routeIs('kitchen.*') ? 'text-white' : 'text-[#D9A05B]' }}"></i>
                        <span>Dapur &amp; Bar</span>
                    </a>

                </div>

                <!-- ─── KATEGORI: MANAJEMEN DATA ─── -->
                <p class="px-3 py-1.5 text-[11px] font-semibold tracking-wider text-[#D9A05B]/70 uppercase mb-1">Manajemen Data</p>
                <div class="space-y-1 mb-4">

                    <!-- Kategori Produk (DISABLED) -->
                    <span tabindex="-1"
                          class="disabled flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm text-[#FAF6F0]/80"
                          style="opacity:0.45; pointer-events:none; cursor:not-allowed;">
                        <i class="fa-solid fa-tags text-base w-5 text-center text-[#D9A05B]"></i>
                        <span>Kategori Produk</span>
                        <span class="ml-auto text-[10px] bg-[#4A2E2B] text-[#D9A05B]/60 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider">Soon</span>
                    </span>

                    <!-- Denah Meja (DISABLED) -->
                    <span tabindex="-1"
                          class="disabled flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm text-[#FAF6F0]/80"
                          style="opacity:0.45; pointer-events:none; cursor:not-allowed;">
                        <i class="fa-solid fa-table-cells-large text-base w-5 text-center text-[#D9A05B]"></i>
                        <span>Denah Meja</span>
                        <span class="ml-auto text-[10px] bg-[#4A2E2B] text-[#D9A05B]/60 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider">Soon</span>
                    </span>

                    <!-- Kelola Karyawan (DISABLED) -->
                    <span tabindex="-1"
                          class="disabled flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm text-[#FAF6F0]/80"
                          style="opacity:0.45; pointer-events:none; cursor:not-allowed;">
                        <i class="fa-solid fa-users text-base w-5 text-center text-[#D9A05B]"></i>
                        <span>Kelola Karyawan</span>
                        <span class="ml-auto text-[10px] bg-[#4A2E2B] text-[#D9A05B]/60 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider">Soon</span>
                    </span>
                </div>

                <!-- ─── KATEGORI: LAPORAN & SISTEM ─── -->
                <p class="px-3 py-1.5 text-[11px] font-semibold tracking-wider text-[#D9A05B]/70 uppercase mb-1">Laporan &amp; Sistem</p>
                <div class="space-y-1 mb-2">

                    <!-- Laporan Penjualan (DISABLED) -->
                    <span tabindex="-1"
                          class="disabled flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm text-[#FAF6F0]/80"
                          style="opacity:0.45; pointer-events:none; cursor:not-allowed;">
                        <i class="fa-solid fa-chart-line text-base w-5 text-center text-[#D9A05B]"></i>
                        <span>Laporan Penjualan</span>
                        <span class="ml-auto text-[10px] bg-[#4A2E2B] text-[#D9A05B]/60 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider">Soon</span>
                    </span>

                    <!-- Pengaturan Kafe (DISABLED) -->
                    <span tabindex="-1"
                          class="disabled flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-sm text-[#FAF6F0]/80"
                          style="opacity:0.45; pointer-events:none; cursor:not-allowed;">
                        <i class="fa-solid fa-gear text-base w-5 text-center text-[#D9A05B]"></i>
                        <span>Pengaturan Kafe</span>
                        <span class="ml-auto text-[10px] bg-[#4A2E2B] text-[#D9A05B]/60 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider">Soon</span>
                    </span>
                </div>

            </nav>
        </div>

        <!-- USER PROFILE FOOTER & LOGOUT (FIXED BOTTOM) -->
        <div class="p-4 border-t border-[#4A2E2B] bg-[#2A1713]/60 flex-shrink-0">
            <div class="flex items-center justify-between mb-3 px-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#D9A05B]/20 border border-[#D9A05B]/40 flex items-center justify-center text-[#D9A05B] font-bold text-sm">
                        A
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white leading-tight truncate w-32">Admin d'nale</p>
                        <span class="inline-block text-[10px] font-medium uppercase tracking-wider px-2 py-0.5 rounded-full mt-0.5 bg-amber-500/20 text-amber-300">
                            Admin
                        </span>
                    </div>
                </div>
            </div>

            <!-- Logout Form -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-xl text-rose-300 hover:text-white hover:bg-rose-600/80 transition-all duration-200">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout dari Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- 2. TAMPILAN KONTEN UTAMA (KANAN - INDEPENDENT SCROLL AREA) -->
    <div id="main-content-scroll" class="flex-1 flex flex-col h-screen overflow-y-auto min-w-0 bg-[#FAF6F0]">

        <!-- TOPBAR HEADER (PINNED / STICKY AT TOP) -->
        <header class="bg-white border-b border-[#EADBCE] sticky top-0 z-20 shadow-sm px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold text-[#3D231D] tracking-tight">@yield('page_title', 'Dashboard Ringkasan')</h2>
                <div class="flex items-center gap-2 text-xs text-[#8A7A75] mt-0.5">
                    <span>d'nale caffe</span>
                    <span>/</span>
                    <span class="text-[#C88A42] font-medium">@yield('page_subtitle', 'Sistem Kasir & Manajemen')</span>
                </div>
            </div>

            <!-- Topbar status widgets / actions -->
            <div class="flex items-center gap-3 flex-wrap">
                @hasSection('header_actions')
                    @yield('header_actions')
                @else
                    @if(request()->routeIs('dashboard'))
                        <!-- Tombol Muat Ulang Dashboard (Clean Minimalist Header) -->
                        <button type="button" 
                                onclick="refreshDashboard(this)" 
                                id="btn-refresh-dashboard"
                                class="inline-flex items-center gap-2 bg-[#FAF6F0] hover:bg-white border border-[#EADBCE] hover:border-[#D9A05B] text-[#3D231D] px-4 py-2 rounded-xl text-xs font-semibold shadow-xs hover:shadow-sm transition-all duration-200 cursor-pointer group active:scale-95"
                                title="Muat Ulang Data Dashboard">
                            <!-- Lucide Refresh-CW / Rotate Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#C88A42] transition-transform duration-500 group-hover:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                <path d="M3 3v5h5"/>
                                <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                                <path d="M16 16h5v5"/>
                            </svg>
                            <span>Muat Ulang</span>
                        </button>
                    @else
                        <!-- Live Clock -->
                        <div class="hidden sm:flex items-center gap-2 bg-[#FAF6F0] border border-[#EADBCE] px-3.5 py-1.5 rounded-xl text-xs font-medium text-[#3D231D]">
                            <i class="fa-regular fa-clock text-[#C88A42]"></i>
                            <span id="realtime-clock">{{ now()->translatedFormat('l, d M Y') }} • {{ now()->format('H:i') }} WIB</span>
                        </div>

                        <!-- Quick POS Shortcut -->
                        <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#D9A05B] to-[#C88A42] hover:brightness-105 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all duration-200">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>Order Baru</span>
                        </a>
                    @endif
                @endif
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        <div class="px-6 pt-5">
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-sm flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
        </div>

        <!-- MAIN PAGE CONTENT -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="px-6 py-4 border-t border-[#EADBCE] text-center text-xs text-[#8A7A75] bg-white">
            <p>&copy; {{ date('Y') }} <strong>d'nale caffe</strong> — Sistem Kasir & Manajemen Kafe Modern. Built with Laravel & Tailwind CSS.</p>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        // Global refresh function for dashboard / header button
        function refreshDashboard(btn) {
            if (btn) {
                btn.classList.add('opacity-75', 'pointer-events-none');
                const svg = btn.querySelector('svg') || btn.querySelector('i');
                if (svg) {
                    svg.classList.add('animate-spin');
                }
            }
            window.location.reload();
        }

        // Mobile sidebar toggle
        const toggleBtn = document.getElementById('mobile-toggle');
        const navMenu = document.getElementById('nav-menu');
        if (toggleBtn && navMenu) {
            toggleBtn.addEventListener('click', () => {
                navMenu.classList.toggle('hidden');
            });
        }

        // Live clock updater
        function updateClock() {
            const clockEl = document.getElementById('realtime-clock');
            if (!clockEl) return;
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${dayName}, ${date} ${month} ${year} • ${hours}:${minutes}:${seconds} WIB`;
        }
        setInterval(updateClock, 1000);
    </script>
    @stack('scripts')
</body>
</html>
