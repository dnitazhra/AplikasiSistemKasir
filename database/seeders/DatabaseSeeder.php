<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CafeTable;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\MenuVariantOption;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailVariant;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Manager d\'nale',
                'email' => 'admin@dnalecaffe.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );

        $cashier = User::firstOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Siti Kasir',
                'email' => 'kasir@dnalecaffe.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081234567891',
            ]
        );

        $barista = User::firstOrCreate(
            ['username' => 'barista'],
            [
                'name' => 'Rian Barista',
                'email' => 'barista@dnalecaffe.com',
                'password' => Hash::make('password'),
                'role' => 'barista',
                'phone' => '081234567892',
            ]
        );

        // 2. Settings
        $settings = [
            'store_name' => "d'nale caffe",
            'store_tagline' => 'Artisan Coffee & Good Vibes',
            'store_address' => 'Jl. Boulevard Kopi No. 8, Jakarta Selatan',
            'store_phone' => '+62 812-3456-7890',
            'tax_percentage' => '10',
            'service_charge' => '0',
            'currency_symbol' => 'Rp',
        ];
        foreach ($settings as $key => $val) {
            Setting::firstOrCreate(['key_name' => $key], ['value' => $val]);
        }

        // 3. Cafe Tables
        $tables = [
            ['table_number' => 'Meja 01', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'Meja 02', 'capacity' => 4, 'status' => 'occupied'],
            ['table_number' => 'Meja 03', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'Meja 04', 'capacity' => 6, 'status' => 'reserved'],
            ['table_number' => 'Meja 05', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'Meja 06', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'Meja 07', 'capacity' => 8, 'status' => 'occupied'],
            ['table_number' => 'Meja 08', 'capacity' => 2, 'status' => 'available'],
        ];
        foreach ($tables as $tbl) {
            CafeTable::firstOrCreate(['table_number' => $tbl['table_number']], $tbl);
        }

        // 4. Vouchers
        $vouchers = [
            [
                'code' => 'DNCWELCOME',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'min_order_amount' => 50000.00,
                'is_active' => true,
            ],
            [
                'code' => 'HEMAT10K',
                'discount_type' => 'fixed',
                'discount_value' => 10000.00,
                'min_order_amount' => 40000.00,
                'is_active' => true,
            ],
            [
                'code' => 'COFFEELOVER',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_order_amount' => 80000.00,
                'is_active' => true,
            ],
        ];
        foreach ($vouchers as $v) {
            Voucher::firstOrCreate(['code' => $v['code']], $v);
        }

        // 5. Categories
        $catSignature = Category::firstOrCreate(['slug' => 'signature-coffee'], ['name' => 'Signature Coffee']);
        $catClassic = Category::firstOrCreate(['slug' => 'espresso-classic'], ['name' => 'Espresso & Classic']);
        $catNonCoffee = Category::firstOrCreate(['slug' => 'non-coffee-tea'], ['name' => 'Non-Coffee & Tea']);
        $catPastry = Category::firstOrCreate(['slug' => 'pastry-bakery'], ['name' => 'Pastry & Bakery']);
        $catFood = Category::firstOrCreate(['slug' => 'main-course-snacks'], ['name' => 'Main Course & Snacks']);

        // 6. Menus and Variants
        // Menu 1: d'nale Aren Latte
        $menu1 = Menu::firstOrCreate(
            ['name' => 'd\'nale Aren Latte'],
            [
                'category_id' => $catSignature->id,
                'description' => 'Espresso arabika premium dengan susu segar dan gula aren organik khas d\'nale.',
                'price' => 28000.00,
                'image' => null,
                'is_available' => true,
            ]
        );
        $vSize1 = MenuVariant::firstOrCreate(['menu_id' => $menu1->id, 'name' => 'Ukuran']);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vSize1->id, 'name' => 'Regular (12oz)'], ['additional_price' => 0.00]);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vSize1->id, 'name' => 'Large (16oz)'], ['additional_price' => 5000.00]);

        $vSugar1 = MenuVariant::firstOrCreate(['menu_id' => $menu1->id, 'name' => 'Level Gula']);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vSugar1->id, 'name' => 'Normal Sugar (100%)'], ['additional_price' => 0.00]);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vSugar1->id, 'name' => 'Less Sugar (50%)'], ['additional_price' => 0.00]);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vSugar1->id, 'name' => 'No Sugar (0%)'], ['additional_price' => 0.00]);

        // Menu 2: Caramel Macchiato
        $menu2 = Menu::firstOrCreate(
            ['name' => 'Caramel Macchiato'],
            [
                'category_id' => $catSignature->id,
                'description' => 'Espresso dipadu sirup vanila, susu berbuih, dan saus karamel mewah.',
                'price' => 34000.00,
                'image' => null,
                'is_available' => true,
            ]
        );
        $vSize2 = MenuVariant::firstOrCreate(['menu_id' => $menu2->id, 'name' => 'Ukuran']);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vSize2->id, 'name' => 'Regular'], ['additional_price' => 0.00]);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vSize2->id, 'name' => 'Large'], ['additional_price' => 6000.00]);

        // Menu 3: Americano Double Shot
        $menu3 = Menu::firstOrCreate(
            ['name' => 'Americano Double Shot'],
            [
                'category_id' => $catClassic->id,
                'description' => 'Dua shot espresso murni dengan air dingin atau panas, aroma bold dan segar.',
                'price' => 22000.00,
                'image' => null,
                'is_available' => true,
            ]
        );
        $vTemp3 = MenuVariant::firstOrCreate(['menu_id' => $menu3->id, 'name' => 'Suhu']);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vTemp3->id, 'name' => 'Iced'], ['additional_price' => 0.00]);
        MenuVariantOption::firstOrCreate(['menu_variant_id' => $vTemp3->id, 'name' => 'Hot'], ['additional_price' => 0.00]);

        // Menu 4: Spanish Latte
        $menu4 = Menu::firstOrCreate(
            ['name' => 'Spanish Latte'],
            [
                'category_id' => $catClassic->id,
                'description' => 'Perpaduan lembut espresso dengan susu kental manis dan susu segar.',
                'price' => 30000.00,
                'image' => null,
                'is_available' => true,
            ]
        );

        // Menu 5: Matcha Kyoto Latte
        $menu5 = Menu::firstOrCreate(
            ['name' => 'Matcha Kyoto Latte'],
            [
                'category_id' => $catNonCoffee->id,
                'description' => 'Bubuk matcha murni dari Uji Kyoto dengan susu segar yang creamy.',
                'price' => 32000.00,
                'image' => null,
                'is_available' => true,
            ]
        );

        // Menu 6: Belgian Chocolate Velvet
        $menu6 = Menu::firstOrCreate(
            ['name' => 'Belgian Chocolate Velvet'],
            [
                'category_id' => $catNonCoffee->id,
                'description' => 'Cokelat Belgia pekat dengan tekstur velvety yang nikmat.',
                'price' => 32000.00,
                'image' => null,
                'is_available' => true,
            ]
        );

        // Menu 7: Almond Croissant
        $menu7 = Menu::firstOrCreate(
            ['name' => 'Almond Croissant'],
            [
                'category_id' => $catPastry->id,
                'description' => 'Croissant renyah berlapis dengan isian krim almond dan taburan kacang almond panggang.',
                'price' => 28000.00,
                'image' => null,
                'is_available' => true,
            ]
        );

        // Menu 8: Cinnamon Glazed Roll
        $menu8 = Menu::firstOrCreate(
            ['name' => 'Cinnamon Glazed Roll'],
            [
                'category_id' => $catPastry->id,
                'description' => 'Roti gulung kayu manis harum dengan glaze cream cheese lembut.',
                'price' => 25000.00,
                'image' => null,
                'is_available' => true,
            ]
        );

        // Menu 9: Truffle Parmesan Fries
        $menu9 = Menu::firstOrCreate(
            ['name' => 'Truffle Parmesan Fries'],
            [
                'category_id' => $catFood->id,
                'description' => 'Kentang goreng renyah dengan minyak truffle aromatik dan taburan keju parmesan.',
                'price' => 29000.00,
                'image' => null,
                'is_available' => true,
            ]
        );

        // Menu 10: Nasi Goreng Spesial d'nale (Out of stock / habis to demo alert widget)
        $menu10 = Menu::firstOrCreate(
            ['name' => 'Nasi Goreng Spesial d\'nale'],
            [
                'category_id' => $catFood->id,
                'description' => 'Nasi goreng racikan chef dengan potongan ayam, telur mata sapi, sate ayam dan kerupuk.',
                'price' => 38000.00,
                'image' => null,
                'is_available' => false, // HABIS
            ]
        );

        // Menu 11: Smoked Beef Spaghetti Carbonara (Out of stock)
        $menu11 = Menu::firstOrCreate(
            ['name' => 'Smoked Beef Carbonara'],
            [
                'category_id' => $catFood->id,
                'description' => 'Spaghetti al dente dengan saus carbonara creamy dan irisan smoked beef gurih.',
                'price' => 42000.00,
                'image' => null,
                'is_available' => false, // HABIS
            ]
        );

        // 7. Seed Sample Orders for today to populate Dashboard
        $table2 = CafeTable::where('table_number', 'Meja 02')->first();
        $table7 = CafeTable::where('table_number', 'Meja 07')->first();

        // Order 1: Completed paid order
        $order1 = Order::firstOrCreate(
            ['order_number' => 'DNC-20261005-0001'],
            [
                'user_id' => $cashier->id,
                'cafe_table_id' => $table2 ? $table2->id : null,
                'voucher_id' => null,
                'order_type' => 'dine_in',
                'subtotal' => 90000.00,
                'discount_amount' => 0.00,
                'total_amount' => 90000.00,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'kitchen_status' => 'ready',
                'created_at' => now()->subHours(2),
            ]
        );
        OrderDetail::firstOrCreate([
            'order_id' => $order1->id,
            'menu_id' => $menu1->id,
            'quantity' => 2,
            'unit_price' => 28000.00,
            'subtotal' => 56000.00,
            'notes' => 'Less ice',
        ]);
        OrderDetail::firstOrCreate([
            'order_id' => $order1->id,
            'menu_id' => $menu2->id,
            'quantity' => 1,
            'unit_price' => 34000.00,
            'subtotal' => 34000.00,
            'notes' => 'Extra caramel',
        ]);

        // Order 2: Cooking order
        $order2 = Order::firstOrCreate(
            ['order_number' => 'DNC-20261005-0002'],
            [
                'user_id' => $cashier->id,
                'cafe_table_id' => $table7 ? $table7->id : null,
                'voucher_id' => null,
                'order_type' => 'dine_in',
                'subtotal' => 89000.00,
                'discount_amount' => 10000.00,
                'total_amount' => 79000.00,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'kitchen_status' => 'cooking',
                'created_at' => now()->subMinutes(30),
            ]
        );
        OrderDetail::firstOrCreate([
            'order_id' => $order2->id,
            'menu_id' => $menu1->id,
            'quantity' => 1,
            'unit_price' => 28000.00,
            'subtotal' => 28000.00,
            'notes' => 'Normal sugar',
        ]);
        OrderDetail::firstOrCreate([
            'order_id' => $order2->id,
            'menu_id' => $menu5->id,
            'quantity' => 1,
            'unit_price' => 32000.00,
            'subtotal' => 32000.00,
            'notes' => 'Ice matcha',
        ]);
        OrderDetail::firstOrCreate([
            'order_id' => $order2->id,
            'menu_id' => $menu9->id,
            'quantity' => 1,
            'unit_price' => 29000.00,
            'subtotal' => 29000.00,
            'notes' => 'Saus sambal terpisah',
        ]);

        // Order 3: Take Away order pending
        $order3 = Order::firstOrCreate(
            ['order_number' => 'DNC-20261005-0003'],
            [
                'user_id' => $cashier->id,
                'cafe_table_id' => null,
                'voucher_id' => null,
                'order_type' => 'take_away',
                'subtotal' => 50000.00,
                'discount_amount' => 0.00,
                'total_amount' => 50000.00,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'kitchen_status' => 'pending',
                'created_at' => now()->subMinutes(10),
            ]
        );
        OrderDetail::firstOrCreate([
            'order_id' => $order3->id,
            'menu_id' => $menu3->id,
            'quantity' => 1,
            'unit_price' => 22000.00,
            'subtotal' => 22000.00,
            'notes' => 'Iced Americano',
        ]);
        OrderDetail::firstOrCreate([
            'order_id' => $order3->id,
            'menu_id' => $menu7->id,
            'quantity' => 1,
            'unit_price' => 28000.00,
            'subtotal' => 28000.00,
            'notes' => 'Dipanaskan sebentar',
        ]);
    }
}
