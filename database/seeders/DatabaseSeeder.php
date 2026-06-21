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
        $this->call([
            ShieldPermissionsSeeder::class,
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            MakesSeeder::class,
            CarModelsSeeder::class,
            CarSeeder::class,
            BlogPostSeeder::class,
        ]);
    }
}
