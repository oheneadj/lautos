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
            'site_logo_path'       => null,
            'facebook_url'         => '',
            'instagram_url'        => '',
            'twitter_url'          => '',
            'exchange_rate_usd_to_ghs' => '15.00',
            'whatsapp_number'      => '+233550000000',
            'demurrage_warning'    => 'Clearing fees and demurrage charges are paid separately at the port and are not included in this price. Delays in clearing your car after arrival may attract additional storage penalties from the shipping line and Ghana customs.',
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
