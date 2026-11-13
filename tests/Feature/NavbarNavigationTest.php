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
            'reports.index',
            'settings.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($user)->get(route($routeName));
            $response->assertStatus(200);
        }

        // Pastikan menu Voucher & Promo tidak ada di navbar
        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashboardResponse->assertDontSee('Voucher &amp; Promo', false);
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

        // Di halaman kasir (POS), menu Dashboard tidak boleh muncul
        $posResponse = $this->actingAs($cashier)->get(route('pos.index'));
        $posResponse->assertStatus(200);
        $posResponse->assertDontSee('<span>Dashboard</span>', false);

        // Halaman operasional kasir tetap dapat diakses
        $cashierRoutes = [
            'pos.index',
            'bills.index',
            'kitchen.index',
            'menus.index',
            'tables.index',
            'reports.index',
        ];

        foreach ($cashierRoutes as $routeName) {
            $response = $this->actingAs($cashier)->get(route($routeName));
            $response->assertStatus(200);
        }
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
