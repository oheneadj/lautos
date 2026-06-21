<?php

namespace Tests\Unit\Models;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the Car model's price-conversion and WhatsApp deeplink accessors.
 */
class CarTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(): Car
    {
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'price_usd_cents' => 1000000,
            'shipping_cost_usd_cents' => 200000,
            'year' => 2020,
        ]);
    }

    #[Test]
    public function ghs_prices_are_calculated_from_the_configured_exchange_rate(): void
    {
        Setting::set('exchange_rate_usd_to_ghs', '15');

        $car = $this->makeCar();

        $this->assertSame(150000.0, $car->price_ghs);
        $this->assertSame(30000.0, $car->shipping_cost_ghs);
        $this->assertSame(180000.0, $car->total_ghs);
    }

    #[Test]
    public function ghs_prices_fall_back_to_a_default_rate_when_unset(): void
    {
        $car = $this->makeCar();

        // I check it doesn't error or return zero when no admin has set a rate yet.
        $this->assertGreaterThan(0, $car->price_ghs);
    }

    #[Test]
    public function whatsapp_url_is_null_when_no_number_is_configured(): void
    {
        Setting::set('whatsapp_number', '');

        $car = $this->makeCar();

        $this->assertNull($car->whatsapp_enquiry_url);
    }

    #[Test]
    public function whatsapp_url_includes_the_car_details_in_the_prefilled_message(): void
    {
        Setting::set('whatsapp_number', '+233 55 123 4567');

        $car = $this->makeCar();

        $this->assertStringContainsString('wa.me/233551234567', $car->whatsapp_enquiry_url);
        $this->assertStringContainsString('2020', $car->whatsapp_enquiry_url);
        $this->assertStringContainsString('Corolla', $car->whatsapp_enquiry_url);
    }

    #[Test]
    public function slug_is_generated_from_year_make_and_model(): void
    {
        $car = $this->makeCar();

        $this->assertSame('2020-toyota-corolla', $car->slug);
    }

    #[Test]
    public function a_duplicate_year_make_and_model_gets_a_unique_suffix(): void
    {
        $first = $this->makeCar();
        $second = $this->makeCar();

        $this->assertSame('2020-toyota-corolla', $first->slug);
        $this->assertSame('2020-toyota-corolla-2', $second->slug);
    }
}
