<?php

/**
 * Seeds the two application roles and their permissions.
 * FilamentShield generates per-resource permissions; here we only seed
 * the top-level roles so shield:install has a base to build on.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // I reset the permission cache before seeding so stale entries don't cause conflicts.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer',    'guard_name' => 'web']);
    }
}
