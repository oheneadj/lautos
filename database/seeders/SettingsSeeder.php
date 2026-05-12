<?php

/**
 * Seeds default site settings so the admin panel has values to display
 * before Mr. Seth customises them through the UI.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name'            => 'Livingston Autos',
            'contact_email'        => 'info@livingstonautos.com',
            'contact_phone'        => '',
            'contact_address'      => 'Accra, Ghana',
            'payment_instructions' => 'Please transfer payment to our bank account and upload proof of payment to complete your order.',
            'bank_name'            => '',
            'bank_account_name'    => 'Livingston Autos Ltd',
            'bank_account_number'  => '',
            'momo_number'          => '',
            'momo_name'            => 'Livingston Autos',
            'about_us'             => '',
        ];

        foreach ($defaults as $key => $value) {
            // I use updateOrInsert so re-running the seeder is idempotent.
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
