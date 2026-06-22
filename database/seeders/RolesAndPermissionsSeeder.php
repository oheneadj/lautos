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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * The permissions staff_admin gets out of the box, per docs.md's default
     * permission matrix (US-04) — everything else stays "Configurable",
     * i.e. a Super Admin can grant it later from the Roles & Permissions UI.
     */
    private const STAFF_ADMIN_PERMISSIONS = [
        'ViewAny:Order', 'View:Order', 'Create:Order', 'Update:Order',
        'Delete:Order', 'Reorder:Order', 'Replicate:Order',
    ];

    public function run(): void
    {
        // I reset the permission cache before seeding so stale entries don't cause conflicts.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // This permission isn't tied to a Filament resource, so Shield never generates it —
        // I seed it directly. Super Admin always passes via the Gate::before bypass.
        Permission::firstOrCreate(['name' => 'update_exchange_rate', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer',    'guard_name' => 'web']);

        $staffAdmin = Role::firstOrCreate(['name' => 'staff_admin', 'guard_name' => 'web']);
        $staffAdmin->syncPermissions(self::STAFF_ADMIN_PERMISSIONS);
    }
}
