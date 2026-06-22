<?php

namespace Tests\Feature\Customer;

use App\Enums\CarStatus;
use App\Enums\KycStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Livewire\Cars\CarDetail;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests placing an order from the car detail page (US-39).
 */
class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(array $attributes = []): Car
    {
        $make = Make::create(['name' => 'Toyota']);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(array_merge([
            'make_id' => $make->id,
            'car_model_id' => $carModel->id,
            'status' => CarStatus::Available,
        ], $attributes));
    }

    #[Test]
    public function a_guest_sees_a_create_account_prompt_instead_of_an_order_button(): void
    {
        $car = $this->makeCar();

        $this->get(route('cars.show', $car->slug))
            ->assertSee('Create Account to Order')
            ->assertDontSee('wire:click="openOrderModal"', false);
    }

    #[Test]
    public function an_authenticated_customer_can_place_an_order(): void
    {
        Event::fake([OrderPlaced::class]);

        $car = $this->makeCar();
        $user = User::factory()->create(['kyc_status' => KycStatus::Verified]);

        $component = Livewire::actingAs($user)->test(CarDetail::class, ['car' => $car]);

        $component->call('confirmOrder');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => OrderStatus::PendingPayment->value,
        ]);
        $this->assertSame(CarStatus::Reserved, $car->refresh()->status);
        Event::assertDispatched(OrderPlaced::class);
    }

    #[Test]
    public function placing_an_order_redirects_to_the_new_orders_detail_page(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create(['kyc_status' => KycStatus::Verified]);

        Livewire::actingAs($user)
            ->test(CarDetail::class, ['car' => $car])
            ->call('confirmOrder')
            ->assertRedirect(route('dashboard.orders.show', $car->orders()->first()->uuid));
    }

    #[Test]
    public function a_customer_with_incomplete_kyc_sees_a_warning_but_can_still_order(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        Livewire::actingAs($user)
            ->test(CarDetail::class, ['car' => $car])
            ->set('showOrderModal', true)
            ->assertSee('finish KYC before your car can be delivered')
            ->call('confirmOrder');

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'car_id' => $car->id]);
    }

    #[Test]
    public function an_already_reserved_car_cannot_be_ordered_twice(): void
    {
        $car = $this->makeCar(['status' => CarStatus::Reserved]);
        $user = User::factory()->create(['kyc_status' => KycStatus::Verified]);

        Livewire::actingAs($user)
            ->test(CarDetail::class, ['car' => $car])
            ->call('confirmOrder')
            ->assertHasErrors('order');

        $this->assertDatabaseMissing('orders', ['user_id' => $user->id, 'car_id' => $car->id]);
    }

    #[Test]
    public function a_guest_cannot_call_confirm_order_directly(): void
    {
        $car = $this->makeCar();

        Livewire::test(CarDetail::class, ['car' => $car])
            ->call('confirmOrder')
            ->assertForbidden();

        $this->assertDatabaseMissing('orders', ['car_id' => $car->id]);
    }
}
