<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename categories to standardized names and reassign menus accordingly.
     * Old → New mapping:
     *   Signature Coffee  → Coffee
     *   Espresso & Classic → Coffee     (merge into Coffee)
     *   Non-Coffee & Tea  → Non Coffee
     *   Pastry & Bakery   → Dessert
     *   Main Course & Snacks → split into Main Course + Snacks
     */
    public function up(): void
    {
        // ── 1. Rename / upsert the 5 target categories ──────────────────
        $targets = [
            ['name' => 'Coffee',      'slug' => 'coffee'],
            ['name' => 'Non Coffee',  'slug' => 'non-coffee'],
            ['name' => 'Snacks',      'slug' => 'snacks'],
            ['name' => 'Dessert',     'slug' => 'dessert'],
            ['name' => 'Main Course', 'slug' => 'main-course'],
        ];

        foreach ($targets as $t) {
            $exists = DB::table('categories')->where('slug', $t['slug'])->first();
            if (! $exists) {
                DB::table('categories')->insert([
                    'name'       => $t['name'],
                    'slug'       => $t['slug'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('categories')
                    ->where('slug', $t['slug'])
                    ->update(['name' => $t['name'], 'updated_at' => now()]);
            }
        }

        // Fetch resolved IDs
        $catCoffee     = DB::table('categories')->where('slug', 'coffee')->value('id');
        $catNonCoffee  = DB::table('categories')->where('slug', 'non-coffee')->value('id');
        $catSnacks     = DB::table('categories')->where('slug', 'snacks')->value('id');
        $catDessert    = DB::table('categories')->where('slug', 'dessert')->value('id');
        $catMainCourse = DB::table('categories')->where('slug', 'main-course')->value('id');

        // ── 2. Reassign menus from old categories ───────────────────────

        // Signature Coffee  → Coffee
        $oldSignature = DB::table('categories')->where('slug', 'signature-coffee')->value('id');
        if ($oldSignature) {
            DB::table('menus')->where('category_id', $oldSignature)->update(['category_id' => $catCoffee]);
        }

        // Espresso & Classic → Coffee
        $oldEspresso = DB::table('categories')->where('slug', 'espresso-classic')->value('id');
        if ($oldEspresso) {
            DB::table('menus')->where('category_id', $oldEspresso)->update(['category_id' => $catCoffee]);
        }

        // Non-Coffee & Tea → Non Coffee
        $oldNonCoffee = DB::table('categories')->where('slug', 'non-coffee-tea')->value('id');
        if ($oldNonCoffee) {
            DB::table('menus')->where('category_id', $oldNonCoffee)->update(['category_id' => $catNonCoffee]);
        }

        // Pastry & Bakery → Dessert
        $oldPastry = DB::table('categories')->where('slug', 'pastry-bakery')->value('id');
        if ($oldPastry) {
            DB::table('menus')->where('category_id', $oldPastry)->update(['category_id' => $catDessert]);
        }

        // Main Course & Snacks → split:
        //   names containing keywords for snacks → Snacks
        //   everything else → Main Course
        $oldFood = DB::table('categories')->where('slug', 'main-course-snacks')->value('id');
        if ($oldFood) {
            $snackKeywords = ['fries', 'nugget', 'snack', 'chips', 'waffle'];
            $foodMenus = DB::table('menus')->where('category_id', $oldFood)->get();

            foreach ($foodMenus as $menu) {
                $lower = strtolower($menu->name . ' ' . ($menu->description ?? ''));
                $isSnack = false;
                foreach ($snackKeywords as $kw) {
                    if (str_contains($lower, $kw)) {
                        $isSnack = true;
                        break;
                    }
                }
                DB::table('menus')
                    ->where('id', $menu->id)
                    ->update(['category_id' => $isSnack ? $catSnacks : $catMainCourse]);
            }
        }

        // ── 3. Delete old categories that are now empty ─────────────────
        $oldSlugs = ['signature-coffee', 'espresso-classic', 'non-coffee-tea', 'pastry-bakery', 'main-course-snacks'];
        foreach ($oldSlugs as $slug) {
            $id = DB::table('categories')->where('slug', $slug)->value('id');
            if ($id) {
                $count = DB::table('menus')->where('category_id', $id)->count();
                if ($count === 0) {
                    DB::table('categories')->where('id', $id)->delete();
                }
            }
        }
    }

    public function down(): void
    {
        // Restore old categories and reassign menus back
        $olds = [
            ['name' => 'Signature Coffee',      'slug' => 'signature-coffee'],
            ['name' => 'Espresso & Classic',     'slug' => 'espresso-classic'],
            ['name' => 'Non-Coffee & Tea',       'slug' => 'non-coffee-tea'],
            ['name' => 'Pastry & Bakery',        'slug' => 'pastry-bakery'],
            ['name' => 'Main Course & Snacks',   'slug' => 'main-course-snacks'],
        ];

        foreach ($olds as $o) {
            $exists = DB::table('categories')->where('slug', $o['slug'])->exists();
            if (! $exists) {
                DB::table('categories')->insert([
                    'name'       => $o['name'],
                    'slug'       => $o['slug'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
