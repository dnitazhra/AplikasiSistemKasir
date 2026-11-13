<?php

namespace Database\Seeders;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\MenuVariantOption;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. USERS ─────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => "Manager d'nale",
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

        User::firstOrCreate(
            ['username' => 'barista'],
            [
                'name' => 'Rian Barista',
                'email' => 'barista@dnalecaffe.com',
                'password' => Hash::make('password'),
                'role' => 'barista',
                'phone' => '081234567892',
            ]
        );

        // ── 2. SETTINGS ───────────────────────────────────────────────────
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

        // ── 3. CAFE TABLES ────────────────────────────────────────────────
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

        // ── 4. VOUCHERS ───────────────────────────────────────────────────
        $vouchers = [
            ['code' => 'DNCWELCOME',  'discount_type' => 'percentage', 'discount_value' => 15.00, 'min_order_amount' => 50000.00, 'is_active' => true],
            ['code' => 'HEMAT10K',    'discount_type' => 'fixed',      'discount_value' => 10000.00, 'min_order_amount' => 40000.00, 'is_active' => true],
            ['code' => 'COFFEELOVER', 'discount_type' => 'percentage', 'discount_value' => 20.00, 'min_order_amount' => 80000.00, 'is_active' => true],
        ];
        foreach ($vouchers as $v) {
            Voucher::firstOrCreate(['code' => $v['code']], $v);
        }

        // ── 5. CATEGORIES — 5 kategori standar ───────────────────────────
        $catCoffee = Category::firstOrCreate(['slug' => 'coffee'], ['name' => 'Coffee']);
        $catNonCoffee = Category::firstOrCreate(['slug' => 'non-coffee'], ['name' => 'Non Coffee']);
        $catDessert = Category::firstOrCreate(['slug' => 'dessert'], ['name' => 'Dessert']);
        $catMainCourse = Category::firstOrCreate(['slug' => 'main-course'], ['name' => 'Main Course']);
        $catSnacks = Category::firstOrCreate(['slug' => 'snacks'], ['name' => 'Snacks']);

        // ── 6. MENUS ──────────────────────────────────────────────────────
        // Varian standar untuk semua minuman kopi
        $coffeeVariants = [
            ['name' => 'Ukuran',       'options' => [['Regular', 0], ['Large', 5000]]],
            ['name' => 'Tingkat Gula', 'options' => [['Normal Sugar', 0], ['Less Sugar', 0], ['No Sugar', 0]]],
            ['name' => 'Suhu',         'options' => [['Iced', 0], ['Hot', 0]]],
        ];

        // Varian suhu saja untuk minuman non-coffee
        $drinkVariants = [
            ['name' => 'Suhu', 'options' => [['Iced', 0], ['Hot', 0]]],
        ];

        // Helper closure: buat menu + variannya
        $makeMenu = function (
            Category $category,
            string $name,
            string $description,
            float $price,
            bool $isAvailable = true,
            array $variants = []
        ): Menu {
            $menu = Menu::firstOrCreate(
                ['name' => $name],
                [
                    'category_id' => $category->id,
                    'description' => $description,
                    'price' => $price,
                    'image' => null,
                    'is_available' => $isAvailable,
                ]
            );

            // Pastikan category_id sesuai jika menu sudah ada
            if ($menu->category_id !== $category->id) {
                $menu->update(['category_id' => $category->id]);
            }

            foreach ($variants as $vDef) {
                $variant = MenuVariant::firstOrCreate([
                    'menu_id' => $menu->id,
                    'name' => $vDef['name'],
                ]);

                foreach ($vDef['options'] as [$optName, $optPrice]) {
                    MenuVariantOption::firstOrCreate(
                        ['menu_variant_id' => $variant->id, 'name' => $optName],
                        ['additional_price' => $optPrice]
                    );
                }
            }

            return $menu;
        };

        // ─── COFFEE (5 menu) ──────────────────────────────────────────────
        $americano = $makeMenu(
            $catCoffee, 'Americano',
            'Dua shot espresso murni dengan air panas atau dingin, bold dan segar.',
            22000, true, $coffeeVariants
        );
        $cappuccino = $makeMenu(
            $catCoffee, 'Cappuccino',
            'Espresso klasik dengan foam susu tebal yang creamy dan aroma harum.',
            26000, true, $coffeeVariants
        );
        $vanillaLatte = $makeMenu(
            $catCoffee, 'Vanilla Latte',
            'Espresso lembut berpadu sirup vanilla premium dan susu steamed segar.',
            28000, true, $coffeeVariants
        );
        $spanishLatte = $makeMenu(
            $catCoffee, 'Spanish Latte',
            'Perpaduan espresso kuat dengan susu kental manis yang creamy dan manis.',
            28000, true, $coffeeVariants
        );
        $kopiAren = $makeMenu(
            $catCoffee, 'Kopi Susu Gula Aren',
            'Espresso arabika dengan susu segar dan gula aren organik khas nusantara.',
            25000, true, $coffeeVariants
        );

        // ─── NON COFFEE (7 menu) ─────────────────────────────────────────
        $matcha = $makeMenu(
            $catNonCoffee, 'Matcha',
            'Bubuk matcha premium dari Uji Kyoto, creamy dan aromatik dengan susu segar.',
            28000, true, $drinkVariants
        );
        $chocolate = $makeMenu(
            $catNonCoffee, 'Chocolate',
            'Cokelat Belgia pekat dengan susu segar, kaya rasa dan sangat memanjakan.',
            26000, true, $drinkVariants
        );
        $milo = $makeMenu(
            $catNonCoffee, 'Milo',
            'Minuman malt cokelat klasik Milo disajikan dingin atau hangat dengan susu.',
            22000, true, $drinkVariants
        );
        $thaiTea = $makeMenu(
            $catNonCoffee, 'Thai Tea',
            'Teh Thailand otentik dengan paduan susu kental manis yang legit dan segar.',
            22000, true, $drinkVariants
        );
        $lychee = $makeMenu(
            $catNonCoffee, 'Lychee Tea',
            'Teh ringan berpadu sirup leci segar dengan aroma bunga yang harum.',
            20000, true, $drinkVariants
        );
        $lemon = $makeMenu(
            $catNonCoffee, 'Lemon Tea',
            'Teh dingin menyegarkan dengan perasan lemon asli dan sentuhan mint.',
            18000, true, $drinkVariants
        );
        $mangoYakult = $makeMenu(
            $catNonCoffee, 'Mango Yakult',
            'Perpaduan mangga manis segar dengan Yakult probiotik, asam segar dan nikmat.',
            22000, true, $drinkVariants
        );

        // ─── DESSERT (8 menu) ────────────────────────────────────────────
        $churros = $makeMenu(
            $catDessert, 'Churros',
            'Churros renyah berlumur kayu manis gula halus, disajikan dengan saus cokelat.',
            28000
        );
        $cromboloni = $makeMenu(
            $catDessert, 'Cromboloni',
            'Perpaduan croissant dan bomboloni, diisi krim lembut dan dibalut gula halus.',
            32000
        );
        $croissant = $makeMenu(
            $catDessert, 'Croissant',
            'Croissant mentega berlapis-lapis, renyah di luar dan lembut mengembang di dalam.',
            25000
        );
        $tiramisu = $makeMenu(
            $catDessert, 'Tiramisu',
            'Tiramisu klasik Italia dengan lapisan mascarpone lembut dan espresso premium.',
            35000
        );
        $cheesecake = $makeMenu(
            $catDessert, 'Cheesecake',
            'Cheesecake New York style creamy dengan base biskuit renyah dan topping buah.',
            35000
        );
        $brownies = $makeMenu(
            $catDessert, 'Brownies',
            'Brownies cokelat pekat fudgy di dalam, sedikit renyah di luar, disajikan hangat.',
            25000
        );
        $muffinCake = $makeMenu(
            $catDessert, 'Muffin Cake',
            'Muffin lembut dan fluffy tersedia dalam rasa cokelat chip dan blueberry.',
            22000
        );
        $lavaCake = $makeMenu(
            $catDessert, 'Lava Cake',
            'Kue cokelat hangat dengan lelehan cokelat lumer di tengah, disajikan dengan es krim.',
            36000
        );

        // ─── MAIN COURSE (6 menu) ────────────────────────────────────────
        $nasiGoreng = $makeMenu(
            $catMainCourse, 'Nasi Goreng',
            'Nasi goreng racikan chef dengan ayam, telur mata sapi, dan kerupuk renyah.',
            35000
        );
        $kwetiau = $makeMenu(
            $catMainCourse, 'Kwetiau Goreng',
            'Kwetiau goreng gurih dengan campuran seafood, telur, dan sayuran segar.',
            35000
        );
        $carbonara = $makeMenu(
            $catMainCourse, 'Spaghetti Carbonara',
            'Spaghetti al dente dengan saus carbonara creamy dan irisan smoked beef gurih.',
            42000
        );
        $chickenSteak = $makeMenu(
            $catMainCourse, 'Chicken Steak',
            'Dada ayam juicy dengan saus mushroom creamy, disajikan dengan kentang dan salad.',
            48000
        );
        $tenderloin = $makeMenu(
            $catMainCourse, 'Tenderloin Steak',
            'Tenderloin sapi pilihan dipanggang medium-well dengan saus pepper black yang kaya.',
            65000
        );
        $nasiAyamPop = $makeMenu(
            $catMainCourse, 'Nasi Ayam Pop',
            'Ayam kampung rebus empuk bumbu Minang dengan nasi putih pulen dan sambal hijau.',
            32000, false // out of stock — simulasi menu habis
        );

        // ─── SNACKS (5 menu) ─────────────────────────────────────────────
        $mixPlatter = $makeMenu(
            $catSnacks, 'Mix Platter',
            'Kombinasi aneka camilan: kentang goreng, onion ring, nugget, dan saus pilihan.',
            45000
        );
        $frenchFries = $makeMenu(
            $catSnacks, 'French Fries',
            'Kentang goreng renyah seasoned, disajikan dengan saus sambal dan mayones.',
            22000
        );
        $cireng = $makeMenu(
            $catSnacks, 'Cireng',
            'Aci goreng crispy khas Sunda dengan isian bumbu rujak pedas manis.',
            18000
        );
        $otakOtak = $makeMenu(
            $catSnacks, 'Otak-Otak',
            'Otak-otak ikan tenggiri bakar dengan bumbu rempah, disajikan dengan saus kacang.',
            20000
        );
        $onionRing = $makeMenu(
            $catSnacks, 'Onion Ring',
            'Ring bawang bombay dibalut tepung crispy renyah, disajikan dengan saus ranch.',
            22000, false // out of stock — simulasi menu habis
        );

        // ── 7. SAMPLE ORDERS (data demo untuk Dashboard) ─────────────────
        $table2 = CafeTable::where('table_number', 'Meja 02')->first();
        $table7 = CafeTable::where('table_number', 'Meja 07')->first();

        $order1 = Order::firstOrCreate(
            ['order_number' => 'DNC-20261005-0001'],
            [
                'user_id' => $cashier->id,
                'cafe_table_id' => $table2?->id,
                'voucher_id' => null,
                'order_type' => 'dine_in',
                'subtotal' => 76000.00,
                'discount_amount' => 0.00,
                'total_amount' => 76000.00,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'kitchen_status' => 'served',
                'created_at' => now()->subHours(2),
            ]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order1->id, 'menu_id' => $americano->id],
            ['quantity' => 2, 'unit_price' => 22000, 'subtotal' => 44000, 'notes' => 'Less ice']
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order1->id, 'menu_id' => $cappuccino->id],
            ['quantity' => 1, 'unit_price' => 26000, 'subtotal' => 26000, 'notes' => null]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order1->id, 'menu_id' => $churros->id],
            ['quantity' => 1, 'unit_price' => 28000, 'subtotal' => 28000, 'notes' => 'Extra sauce']
        );

        $order2 = Order::firstOrCreate(
            ['order_number' => 'DNC-20261005-0002'],
            [
                'user_id' => $cashier->id,
                'cafe_table_id' => $table7?->id,
                'voucher_id' => null,
                'order_type' => 'dine_in',
                'subtotal' => 105000.00,
                'discount_amount' => 10000.00,
                'total_amount' => 95000.00,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'kitchen_status' => 'cooking',
                'created_at' => now()->subMinutes(30),
            ]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order2->id, 'menu_id' => $spanishLatte->id],
            ['quantity' => 1, 'unit_price' => 28000, 'subtotal' => 28000, 'notes' => null]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order2->id, 'menu_id' => $matcha->id],
            ['quantity' => 1, 'unit_price' => 28000, 'subtotal' => 28000, 'notes' => 'Iced']
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order2->id, 'menu_id' => $nasiGoreng->id],
            ['quantity' => 1, 'unit_price' => 35000, 'subtotal' => 35000, 'notes' => 'Pedas sedang']
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order2->id, 'menu_id' => $frenchFries->id],
            ['quantity' => 1, 'unit_price' => 22000, 'subtotal' => 22000, 'notes' => null]
        );

        $order3 = Order::firstOrCreate(
            ['order_number' => 'DNC-20261005-0003'],
            [
                'user_id' => $cashier->id,
                'cafe_table_id' => null,
                'voucher_id' => null,
                'order_type' => 'take_away',
                'subtotal' => 48000.00,
                'discount_amount' => 0.00,
                'total_amount' => 48000.00,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'kitchen_status' => 'pending',
                'created_at' => now()->subMinutes(10),
            ]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order3->id, 'menu_id' => $kopiAren->id],
            ['quantity' => 1, 'unit_price' => 25000, 'subtotal' => 25000, 'notes' => null]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order3->id, 'menu_id' => $croissant->id],
            ['quantity' => 1, 'unit_price' => 25000, 'subtotal' => 25000, 'notes' => 'Dipanaskan']
        );
    }
}
