<?php

/**
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // I run only the seeders that are safe on a live database in
        // production — permissions/roles Filament needs, the real admin
        // account (idempotent via firstOrCreate), default settings, and
        // real car-make/model/FAQ reference data. CarSeeder, OrderSeeder,
        // and BlogPostSeeder are fake demo content and must never touch
        // a live store, so they're skipped outright outside local/testing.
        $this->call([
            ShieldPermissionsSeeder::class,
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            MakesSeeder::class,
            MakeLogosSeeder::class,
            CarModelsSeeder::class,
            FaqSeeder::class,
        ]);

        if (! app()->environment('production')) {
            $this->call([
                CarSeeder::class,
                OrderSeeder::class,
                BlogPostSeeder::class,
            ]);
        }
    }
}
