<?php

namespace Tests\Feature;

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
}
