<?php

/**
 * Seeds the Shield-style CRUD permissions (ViewAny:Car, Create:Order, etc.)
 * for every Filament resource that has a Policy in app/Policies. I derive
 * the entity list from the policy files themselves — rather than calling
 * `shield:generate` here, which prompts interactively even with
 * --no-interaction in this package version and can't run from a seeder —
 * so this stays in sync automatically as resources/policies are added.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ShieldPermissionsSeeder extends Seeder
{
    /** The standard set of CRUD-style actions Shield generates per entity. */
    private const ACTIONS = [
        'ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny',
        'Restore', 'RestoreAny', 'Replicate', 'Reorder',
        'ForceDelete', 'ForceDeleteAny',
    ];

    public function run(): void
    {
        $entities = collect(File::files(app_path('Policies')))
            ->map(fn ($file) => str($file->getFilenameWithoutExtension())->replace('Policy', ''))
            ->values();

        foreach ($entities as $entity) {
            foreach (self::ACTIONS as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}:{$entity}",
                    'guard_name' => 'web',
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
