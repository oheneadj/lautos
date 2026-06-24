<?php

namespace Tests\Unit\Enums;

use App\Enums\CarBodyType;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the CarBodyType enum's display labels.
 */
class CarBodyTypeTest extends TestCase
{
    #[Test]
    public function every_case_has_a_label(): void
    {
        foreach (CarBodyType::cases() as $case) {
            $this->assertNotEmpty($case->label());
        }
    }

    #[Test]
    public function pickup_truck_and_van_minivan_have_friendly_labels(): void
    {
        $this->assertSame('Pickup Truck', CarBodyType::PickupTruck->label());
        $this->assertSame('Van / Minivan', CarBodyType::VanMinivan->label());
        $this->assertSame('SUV', CarBodyType::Suv->label());
    }
}
