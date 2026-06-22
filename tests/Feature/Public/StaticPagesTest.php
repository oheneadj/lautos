<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the About Us and Payment Information static pages (US-31 / US-33).
 */
class StaticPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_about_page_loads(): void
    {
        $this->get('/about')->assertOk()->assertSee('About Livingston Autos');
    }

    #[Test]
    public function the_payment_info_page_shows_bank_and_momo_details_from_settings(): void
    {
        Setting::set('bank_name', 'GCB Bank');
        Setting::set('bank_account_name', 'Livingston Autos Ltd');
        Setting::set('bank_account_number', '1234567890');
        Setting::set('momo_number', '0550000000');
        Setting::set('momo_name', 'Livingston Autos');
        Setting::set('demurrage_warning', 'Clearing fees apply separately at the port.');

        $this->get('/payment-info')
            ->assertOk()
            ->assertSee('GCB Bank')
            ->assertSee('1234567890')
            ->assertSee('0550000000')
            ->assertSee('Clearing fees apply separately at the port.');
    }

    #[Test]
    public function the_payment_info_page_updates_immediately_when_settings_change(): void
    {
        Setting::set('bank_name', 'Old Bank');
        $this->get('/payment-info')->assertSee('Old Bank');

        Setting::set('bank_name', 'New Bank');
        $this->get('/payment-info')->assertSee('New Bank')->assertDontSee('Old Bank');
    }
}
