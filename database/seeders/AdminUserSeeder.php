<?php

/**
 * Creates the default super_admin account for local development.
 * Production credentials must be set via .env — never commit real passwords.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@livingstonautos.com')],
            [
                'uuid'       => Str::uuid(),
                'name'       => 'Super Admin',
                'password'   => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'is_admin'   => true,
                'kyc_status' => 'verified',
            ]
        );

        $admin->assignRole('super_admin');
    }
}
