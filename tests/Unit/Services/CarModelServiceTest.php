<?php

namespace Tests\Unit\Services;

use App\Models\CarModel;
use App\Models\CarTrim;
use App\Models\Make;
use App\Services\CarModelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests syncing a car model's trims from a plain list of names — the logic
 * behind the admin "Manage Trims" action on a make's edit page.
 */
class CarModelServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_keeps_existing_trims_that_are_still_in_the_list(): void
    {
        $model = CarModel::factory()->for(Make::factory())->create();
        $keep = CarTrim::factory()->for($model, 'carModel')->create(['name' => 'LE']);

        app(CarModelService::class)->syncTrims($model, ['LE']);

        $this->assertDatabaseHas('car_trims', ['id' => $keep->id, 'name' => 'LE']);
    }

    #[Test]
    public function it_creates_new_trims_that_were_not_there_before(): void
    {
        $model = CarModel::factory()->for(Make::factory())->create();

        app(CarModelService::class)->syncTrims($model, ['Sport', 'XLE']);

        $this->assertDatabaseHas('car_trims', ['car_model_id' => $model->id, 'name' => 'Sport']);
        $this->assertDatabaseHas('car_trims', ['car_model_id' => $model->id, 'name' => 'XLE']);
    }

    #[Test]
    public function it_removes_trims_that_are_no_longer_in_the_list(): void
    {
        $model = CarModel::factory()->for(Make::factory())->create();
        $remove = CarTrim::factory()->for($model, 'carModel')->create(['name' => 'Old Trim']);

        app(CarModelService::class)->syncTrims($model, ['New Trim']);

        $this->assertDatabaseMissing('car_trims', ['id' => $remove->id]);
        $this->assertDatabaseHas('car_trims', ['car_model_id' => $model->id, 'name' => 'New Trim']);
    }

    #[Test]
    public function it_only_touches_trims_belonging_to_the_given_model(): void
    {
        $model = CarModel::factory()->for(Make::factory())->create();
        $otherModel = CarModel::factory()->for(Make::factory())->create();
        $otherTrim = CarTrim::factory()->for($otherModel, 'carModel')->create(['name' => 'Unrelated']);

        app(CarModelService::class)->syncTrims($model, ['Sport']);

        $this->assertDatabaseHas('car_trims', ['id' => $otherTrim->id, 'name' => 'Unrelated']);
    }
}
