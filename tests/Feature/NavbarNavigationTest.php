<?php

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\User;
use Tests\TestCase;

class NavbarNavigationTest extends TestCase
{
    /**
     * Dapatkan user admin untuk pengujian.
     */
    protected function getAdminUser(): User
    {
        return User::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'name' => 'Admin Tester',
                'email' => 'admin_test@dnalecaffe.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '081234567899',
            ]
        );
    }

    /**
     * Memastikan seluruh halaman navbar dapat diakses dengan status HTTP 200.
     */
    public function test_all_navbar_routes_are_accessible_by_authenticated_user(): void
    {
        $user = $this->getAdminUser();

        $routes = [
            'dashboard',
            'pos.index',
            'bills.index',
            'kitchen.index',
            'menus.index',
            'categories.index',
            'tables.index',
            'users.index',
            'history.index',
            'reports.index',
            'settlement.index',
            'settings.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($user)->get(route($routeName));
            $response->assertStatus(200);
        }

        // Pastikan menu Voucher & Promo tidak ada di navbar admin
        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashboardResponse->assertDontSee('Voucher &amp; Promo', false);

        // Pastikan kartu Omzet Bulan Ini, Omzet Hari Ini, dan Transaksi Hari Ini tampil di dashboard
        $dashboardResponse->assertSee('Omzet Hari Ini');
        $dashboardResponse->assertSee('Transaksi Hari Ini');
        $dashboardResponse->assertSee('Omzet Bulan Ini');

        // Pastikan menu History, Settlement, Terminal Kasir, dan Bills tidak muncul di sidebar admin
        $dashboardResponse->assertDontSee('<span>Terminal Kasir</span>', false);
        $dashboardResponse->assertDontSee('<span>Bills / Bill Aktif</span>', false);
        $dashboardResponse->assertDontSee('<span>Riwayat Transaksi (History)</span>', false);
        $dashboardResponse->assertDontSee('<span>Tutup Kasir (Settlement)</span>', false);

        // Pastikan pada halaman laporan penjualan (Report/Laporan Penjualan) tidak ada bagian Potongan Promo
        $reportsResponse = $this->actingAs($user)->get(route('reports.index'));
        $reportsResponse->assertStatus(200);
        $reportsResponse->assertDontSee('Potongan Promo');
    }

    /**
     * Dapatkan user kasir untuk pengujian.
     */
    protected function getCashierUser(): User
    {
        return User::firstOrCreate(
            ['username' => 'cashier_test'],
            [
                'name' => 'Cashier Tester',
                'email' => 'cashier_test@dnalecaffe.com',
                'password' => bcrypt('password'),
                'role' => 'cashier',
                'phone' => '081234567888',
            ]
        );
    }

    /**
     * Memastikan kasir tidak dapat mengakses dashboard dan dashboard tidak ada di navbar kasir.
     */
    public function test_cashier_cannot_access_dashboard_and_dashboard_is_not_on_cashier_navbar(): void
    {
        $cashier = $this->getCashierUser();

        // Kasir mengakses dashboard akan diredirect ke pos.index
        $dashboardResponse = $this->actingAs($cashier)->get(route('dashboard'));
        $dashboardResponse->assertRedirect(route('pos.index'));

        // Di halaman kasir (POS), menu Dashboard tidak boleh muncul, tetapi menu kasir tetap muncul
        $posResponse = $this->actingAs($cashier)->get(route('pos.index'));
        $posResponse->assertStatus(200);
        $posResponse->assertDontSee('<span>Dashboard</span>', false);
        $posResponse->assertSee('<span>Terminal Kasir</span>', false);
        $posResponse->assertSee('<span>Bills / Bill Aktif</span>', false);
        $posResponse->assertSee('<span>History</span>', false);
        $posResponse->assertSee('<span>Settlement</span>', false);

        // Halaman operasional kasir tetap dapat diakses
        $cashierRoutes = [
            'pos.index',
            'bills.index',
            'kitchen.index',
            'menus.index',
            'tables.index',
            'history.index',
            'settlement.index',
            'reports.index',
        ];

        foreach ($cashierRoutes as $routeName) {
            $response = $this->actingAs($cashier)->get(route($routeName));
            $response->assertStatus(200);
        }

        // Pastikan di halaman Report kasir tidak ada bagian Potongan Promo
        $cashierReportResponse = $this->actingAs($cashier)->get(route('reports.index'));
        $cashierReportResponse->assertStatus(200);
        $cashierReportResponse->assertDontSee('Potongan Promo');
    }

    /**
     * Memastikan halaman riwayat transaksi (History) menampilkan metrik, filter, dan tabel transaksi hari ini.
     */
    public function test_history_page_renders_correctly_with_metrics_filter_and_table(): void
    {
        $cashier = $this->getCashierUser();

        $response = $this->actingAs($cashier)->get(route('history.index'));
        $response->assertStatus(200);

        // Header
        $response->assertSee('Riwayat Transaksi Hari Ini');
        $response->assertSee('Rekapitulasi pesanan lunas khusus hari ini.');
        $response->assertSee('Lihat Tutup Kasir (Settlement)');
        $response->assertSee('Buka Kasir');

        // Statistik Ringkasan
        $response->assertSee('Total Transaksi Lunas');
        $response->assertSee('Total Item Terjual');
        $response->assertSee('Total Omset Hari Ini');

        // Filter
        $response->assertSee('placeholder="Cari no struk atau nama pelanggan..."', false);
        $response->assertSee('Semua Metode Pembayaran');
        $response->assertSee('Filter');
        $response->assertSee('Reset');

        // Tabel Header
        $response->assertSee('NO. STRUK');
        $response->assertSee('JAM');
        $response->assertSee('MEJA / TIPE');
        $response->assertSee('PELANGGAN');
        $response->assertSee('KASIR');
        $response->assertSee('ITEM PESANAN');
        $response->assertSee('METODE');
        $response->assertSee('TOTAL');
        $response->assertSee('CETAK');
    }

    /**
     * Memastikan halaman settlement (Tutup Kasir) menampilkan informasi shift, metrik, metode pembayaran, dan rincian menu.
     */
    public function test_settlement_page_renders_correctly_with_shift_info_and_metrics(): void
    {
        $cashier = $this->getCashierUser();

        $response = $this->actingAs($cashier)->get(route('settlement.index'));
        $response->assertStatus(200);

        // Header
        $response->assertSee('Tutup Kasir (Settlement Shift)');
        $response->assertSee('Hari Ini');
        $response->assertSee('Rekapitulasi setoran kasir dan rincian penjualan');
        $response->assertSee('Cetak Bukti Settlement');
        $response->assertSee('Ke Laporan Penjualan');

        // Informasi Toko & Kasir
        $response->assertSee("d'nale caffe");
        $response->assertSee('Jl. Merdeka No. 12, Bandung');
        $response->assertSee('Shift Bertugas');

        // Ringkasan Statistik
        $response->assertSee('Total Omset Lunas');
        $response->assertSee('Uang masuk hari ini');
        $response->assertSee('Total Transaksi');
        $response->assertSee('Struk berhasil dibayar');
        $response->assertSee('Produk Terjual');
        $response->assertSee('Total porsi/cup menu');
        $response->assertSee('Rata-Rata Transaksi');
        $response->assertSee('Per pesanan pelanggan');

        // Metode Pembayaran
        $response->assertSee('Rincian Penerimaan Berdasarkan Metode Pembayaran');
        $response->assertSee('Gunakan kolom Tunai untuk mencocokkan jumlah uang fisik di laci kasir (Cash Drawer).');
        $response->assertSee('Uang Fisik di Laci Kasir');
        $response->assertSee('Masuk ke Rekening / E-Wallet');

        // Rincian Menu
        $response->assertSee('Rincian Menu yang Berhasil Terjual Hari Ini');
        $response->assertSee('Daftar item makanan dan minuman beserta kuantitas dan total nilai penjualan.');
    }

    /**
     * Memastikan kasir dapat melihat meja dan mengubah status meja menjadi kosong/tersedia.
     */
    public function test_cashier_can_view_tables_and_update_table_status(): void
    {
        $cashier = $this->getCashierUser();

        $table = CafeTable::firstOrCreate(
            ['table_number' => 'Meja Test Kasir'],
            [
                'capacity' => 4,
                'area' => 'INDOOR',
                'status' => 'occupied',
            ]
        );
        $table->update(['status' => 'occupied']);

        // Kasir dapat melihat daftar meja dan tombol "Tandai Sudah Kosong"
        $response = $this->actingAs($cashier)->get(route('tables.index'));
        $response->assertStatus(200);
        $response->assertSee('Meja Test Kasir');
        $response->assertSee('Tandai Sudah Kosong (Tersedia)');

        // Kasir dapat mengubah status meja menjadi available via endpoint /tables/{table}/status
        $statusResponse = $this->actingAs($cashier)->postJson(route('tables.update-status', $table), [
            'status' => 'available',
        ]);
        $statusResponse->assertStatus(200);
        $statusResponse->assertJson(['success' => true]);

        $this->assertEquals('available', $table->fresh()->status);
    }

    /**
     * Memastikan kategori area meja hanya INDOOR dan OUTDOOR.
     */
    public function test_tables_page_only_supports_indoor_and_outdoor_areas(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('tables.index'));
        $response->assertStatus(200);
        $response->assertSee('INDOOR');
        $response->assertSee('OUTDOOR');
        $response->assertDontSee('<option value="VIP">', false);
        $response->assertDontSee('<option value="BAR">', false);

        // Menambahkan area VIP harus gagal validasi
        $invalidResponse = $this->actingAs($user)->post(route('tables.store'), [
            'table_number' => 'Meja Test VIP',
            'capacity' => 4,
            'area' => 'VIP',
            'status' => 'available',
        ]);
        $invalidResponse->assertSessionHasErrors('area');
    }
}
