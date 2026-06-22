<?php

namespace Tests\Feature\Customer;

use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the customer's "my orders" list page (US-40).
 */
class OrderListTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(string $makeName = 'Toyota'): Car
    {
        $make = Make::create(['name' => $makeName]);
        $carModel = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
    }

    #[Test]
    public function guest_cannot_view_the_orders_list(): void
    {
        $this->get(route('dashboard.orders'))->assertRedirect(route('login'));
    }

    #[Test]
    public function a_customer_only_sees_their_own_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myCar = $this->makeCar('Toyota');
        $otherCar = $this->makeCar('Hyundai');

        Order::factory()->create(['user_id' => $user->id, 'car_id' => $myCar->id]);
        Order::factory()->create(['user_id' => $otherUser->id, 'car_id' => $otherCar->id]);

        $response = $this->actingAs($user)->get(route('dashboard.orders'));

        $response->assertOk();
        $response->assertSee('Toyota');
        $response->assertDontSee('Hyundai');
    }

    #[Test]
    public function orders_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        $pendingCar = $this->makeCar('Toyota');
        $deliveredCar = $this->makeCar('Hyundai');

        Order::factory()->create([
            'user_id' => $user->id,
            'car_id' => $pendingCar->id,
            'status' => OrderStatus::PendingPayment,
        ]);
        Order::factory()->create([
            'user_id' => $user->id,
            'car_id' => $deliveredCar->id,
            'status' => OrderStatus::Delivered,
        ]);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Customer\OrderList::class)
            ->set('statusFilter', OrderStatus::Delivered->value)
            ->assertSee('Hyundai')
            ->assertDontSee('Toyota');
    }
}
