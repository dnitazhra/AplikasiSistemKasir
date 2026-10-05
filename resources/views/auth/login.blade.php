<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas — d'nale caffe</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CDN -->
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
                            cream: '#FAF6F0',
                            accent: '#D9A05B',
                            accentHover: '#C88A42',
                            border: '#EADBCE',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-[#FAF6F0] flex items-center justify-center p-4 relative overflow-hidden font-sans">

    <!-- Background Coffee Theme Accents -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#D9A05B]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#3D231D]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-[#EADBCE] overflow-hidden z-10 transition-all duration-300">
        
        <!-- Header Banner with Dark Chocolate & Warm Amber -->
        <div class="bg-gradient-to-r from-[#3D231D] via-[#4A2E2B] to-[#2A1713] p-8 text-center text-white relative">
            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-[#C88A42] to-[#D9A05B] flex items-center justify-center text-white shadow-lg shadow-[#3D231D]/30 transform -rotate-3 hover:rotate-0 transition-transform">
                <i class="fa-solid fa-mug-hot text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center justify-center gap-1.5">
                d'nale <span class="text-[#D9A05B] font-light">caffe</span>
            </h1>
            <p class="text-xs text-[#FAF6F0]/80 mt-1 font-medium tracking-wide">Sistem Kasir & Operasional Kafe Terpadu</p>
        </div>

        <!-- Form Card Body -->
        <div class="p-8">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-[#3D231D]">Masuk Petugas</h2>
                <p class="text-xs text-[#8A7A75] mt-0.5">Silakan masuk menggunakan akun kasir, barista, atau admin.</p>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-3.5 rounded-xl text-xs font-medium mb-5">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-3.5 rounded-xl text-xs font-medium mb-5">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username or Email -->
                <div>
                    <label for="login" class="block text-xs font-semibold text-[#3D231D] mb-1.5 uppercase tracking-wider">Username atau Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#8A7A75]">
                            <i class="fa-regular fa-user text-sm"></i>
                        </div>
                        <input type="text" id="login" name="login" value="{{ old('login', 'kasir') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-sm text-[#3D231D] placeholder-[#8A7A75] focus:outline-none focus:ring-2 focus:ring-[#D9A05B] focus:border-[#D9A05B] transition-all"
                               placeholder="admin / kasir / barista">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-[#3D231D] uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#8A7A75]">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" value="password" required
                               class="w-full pl-10 pr-11 py-2.5 bg-[#FAF6F0] border border-[#EADBCE] rounded-xl text-sm text-[#3D231D] placeholder-[#8A7A75] focus:outline-none focus:ring-2 focus:ring-[#D9A05B] focus:border-[#D9A05B] transition-all"
                               placeholder="••••••••">
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#8A7A75] hover:text-[#3D231D]">
                            <i class="fa-regular fa-eye text-sm" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#D9A05B] border-[#EADBCE] focus:ring-[#D9A05B]">
                        <span class="text-xs text-[#8A7A75]">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 px-4 bg-gradient-to-r from-[#3D231D] to-[#4A2E2B] hover:from-[#2A1713] hover:to-[#3D231D] text-white font-semibold rounded-xl text-sm shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 group">
                    <span>Masuk ke Sistem POS</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- Quick Demo Login Presets -->
            <div class="mt-6 pt-5 border-t border-[#EADBCE]">
                <p class="text-[11px] font-semibold text-[#8A7A75] uppercase tracking-wider text-center mb-3">Klik Cepat Akun Demo (Preset)</p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="setPreset('admin', 'password')" class="px-2.5 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-medium text-[#3D231D] transition-all text-center">
                        <i class="fa-solid fa-crown text-[10px] mr-1 text-[#C88A42]"></i>Admin
                    </button>
                    <button type="button" onclick="setPreset('kasir', 'password')" class="px-2.5 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-medium text-[#3D231D] transition-all text-center">
                        <i class="fa-solid fa-cash-register text-[10px] mr-1 text-[#C88A42]"></i>Kasir
                    </button>
                    <button type="button" onclick="setPreset('barista', 'password')" class="px-2.5 py-1.5 bg-[#FAF6F0] hover:bg-[#D9A05B] hover:text-white border border-[#EADBCE] rounded-lg text-xs font-medium text-[#3D231D] transition-all text-center">
                        <i class="fa-solid fa-mug-saucer text-[10px] mr-1 text-[#C88A42]"></i>Barista
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-[#FAF6F0] px-8 py-3.5 border-t border-[#EADBCE] text-center text-[11px] text-[#8A7A75]">
            &copy; {{ date('Y') }} d'nale caffe • Password default: <span class="font-mono font-semibold text-[#3D231D]">password</span>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-password');
        const passInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        toggleBtn.addEventListener('click', () => {
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            eyeIcon.className = isPassword ? 'fa-regular fa-eye-slash text-sm' : 'fa-regular fa-eye text-sm';
        });

        function setPreset(username, password) {
            document.getElementById('login').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
