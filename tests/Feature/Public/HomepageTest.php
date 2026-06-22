<?php

namespace Tests\Feature\Public;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the homepage (Epic 9) — featured cars and the floating WhatsApp
 * button that's included via the public layout on every page.
 */
class HomepageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_available_cars_as_featured(): void
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        $car = Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Sold,
        ]);
        $car->update(['status' => CarStatus::Available]);

        $this->get('/')->assertOk()->assertSee($carModel->name);
    }

    #[Test]
    public function the_whatsapp_button_links_to_the_configured_number_with_the_default_message(): void
    {
        Setting::set('whatsapp_number', '+233 55 123 4567');

        $this->get('/')
            ->assertOk()
            ->assertSee('wa.me/233551234567', false)
            ->assertSee('Chat with us on WhatsApp', false);
    }

    #[Test]
    public function the_whatsapp_button_is_hidden_when_no_number_is_configured(): void
    {
        Setting::set('whatsapp_number', '');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('wa.me/');
    }
}
