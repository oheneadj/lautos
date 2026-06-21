<?php

namespace Tests\Unit\Models;

use App\Models\Make;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the Make model's slug generation.
 */
class MakeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function slug_is_generated_from_the_name(): void
    {
        $make = Make::create(['name' => 'Toyota']);

        $this->assertSame('toyota', $make->slug);
    }

    #[Test]
    public function route_key_name_is_slug(): void
    {
        $make = Make::create(['name' => 'Toyota']);

        $this->assertSame('slug', $make->getRouteKeyName());
        $this->assertSame('toyota', $make->getRouteKey());
    }
}
