<?php

namespace Tests\Feature\Public;

use App\Livewire\Cars\SaveCarButton;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the heart/save toggle button used on car cards and the detail page.
 */
class SaveCarButtonTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(): Car
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
    }

    #[Test]
    public function a_guest_sees_a_login_link_instead_of_a_toggle_button(): void
    {
        $car = $this->makeCar();

        Livewire::test(SaveCarButton::class, ['car' => $car])
            ->assertSee('Login')
            ->assertDontSee('wire:click="toggle"', false);
    }

    #[Test]
    public function a_customer_can_save_and_unsave_a_car(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)->test(SaveCarButton::class, ['car' => $car]);

        $this->assertFalse($user->savedCars()->where('car_id', $car->id)->exists());

        $component->call('toggle');
        $this->assertTrue($user->savedCars()->where('car_id', $car->id)->exists());

        $component->call('toggle');
        $this->assertFalse($user->savedCars()->where('car_id', $car->id)->exists());
    }

    #[Test]
    public function toggling_twice_in_a_row_does_not_attach_the_same_car_twice(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create();

        // Simulates a double-click: two toggle calls back to back should
        // leave the car saved exactly once, not throw a unique constraint
        // violation or attach it twice.
        $user->savedCars()->toggle($car->id);
        $user->savedCars()->toggle($car->id);
        $user->savedCars()->toggle($car->id);

        $this->assertSame(1, $user->savedCars()->where('car_id', $car->id)->count());
    }
}
