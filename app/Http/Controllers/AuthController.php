<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Pastikan tabel termigrasi dan akun default selalu tersedia.
     */
    private function ensureDemoAccounts(): void
    {
        try {
            // Jalankan migrasi fresh jika tabel orders atau kolom payment_status belum ada
            if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'payment_status')) {
                Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            } elseif (!Schema::hasTable('users') || !Schema::hasColumn('users', 'username')) {
                Artisan::call('migrate', ['--force' => true]);
            }

            // Daftarkan/reset password akun default agar selalu valid
            $usersToEnsure = [
                [
                    'name' => 'Manager d\'nale',
                    'username' => 'admin',
                    'email' => 'admin@dnalecaffe.com',
                    'role' => 'admin',
                    'phone' => '081234567890',
                ],
                [
                    'name' => 'Siti Kasir',
                    'username' => 'kasir',
                    'email' => 'kasir@dnalecaffe.com',
                    'role' => 'cashier',
                    'phone' => '081234567891',
                ],
                [
                    'name' => 'Rian Barista',
                    'username' => 'barista',
                    'email' => 'barista@dnalecaffe.com',
                    'role' => 'barista',
                    'phone' => '081234567892',
                ],
            ];

            foreach ($usersToEnsure as $u) {
                User::updateOrCreate(
                    ['username' => $u['username']],
                    [
                        'name' => $u['name'],
                        'email' => $u['email'],
                        'password' => Hash::make('password'),
                        'role' => $u['role'],
                        'phone' => $u['phone'],
                    ]
                );
            }

            // Jalankan seeder jika katalog menu / meja masih kosong
            if (Category::count() === 0 || CafeTable::count() === 0) {
                (new DatabaseSeeder())->run();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function showLogin(): View|RedirectResponse
    {
        $this->ensureDemoAccounts();

        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->ensureDemoAccounts();

        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = strtolower(trim((string) $credentials['login']));
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attemptCredentials = [
            $fieldType => $loginInput,
            'password' => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($attemptCredentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if ($user->role === 'barista') {
                return redirect()->intended(route('kitchen.index'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'login' => 'Username/Email atau password tidak sesuai.',
        ])->onlyInput('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
