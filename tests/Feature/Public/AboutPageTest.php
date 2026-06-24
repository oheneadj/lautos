<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the About page's contact phone number reads from the global
 * Setting instead of a hardcoded placeholder.
 */
class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_configured_contact_phone_as_a_tel_link(): void
    {
        Setting::set('contact_phone', '+233 55 123 4567');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('+233 55 123 4567');
    }

    #[Test]
    public function the_tel_link_strips_non_digit_characters(): void
    {
        Setting::set('contact_phone', '+233 55 123 4567');

        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('tel:233551234567', escape: false);
    }

    #[Test]
    public function it_hides_the_call_box_when_no_phone_is_configured(): void
    {
        Setting::set('contact_phone', '');

        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee('Call Anytime');
    }
}
