<?php

namespace Tests\Feature\Public;

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
 * Tests the "X Reservations" social-proof badge on the car card and detail page.
 */
class CarReservationCountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_reservation_count_shows_on_the_catalogue_card_and_detail_page(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $model = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $model->id]);

        $firstBuyer = User::factory()->create();
        $secondBuyer = User::factory()->create();
        Order::factory()->create(['user_id' => $firstBuyer->id, 'car_id' => $car->id]);
        Order::factory()->create(['user_id' => $secondBuyer->id, 'car_id' => $car->id]);

        $this->get(route('cars.index'))->assertOk()->assertSee('2 Reservations');
        $this->get(route('cars.show', $car->slug))->assertOk()->assertSee('2 Reservations');
    }

    #[Test]
    public function the_badge_is_hidden_for_a_car_with_no_orders(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $model = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $model->id]);

        $this->get(route('cars.show', $car->slug))->assertOk()->assertDontSee('Reservations');
    }

    #[Test]
    public function cancelled_orders_do_not_count_toward_the_reservation_badge(): void
    {
        $make = Make::create(['name' => 'Toyota']);
        $model = CarModel::create(['make_id' => $make->id, 'name' => 'Corolla']);
        $car = Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $model->id]);

        $winner = User::factory()->create();
        $loser = User::factory()->create();
        Order::factory()->create(['user_id' => $winner->id, 'car_id' => $car->id, 'status' => OrderStatus::PaymentConfirmed]);
        Order::factory()->create(['user_id' => $loser->id, 'car_id' => $car->id, 'status' => OrderStatus::Cancelled]);

        $this->get(route('cars.index'))->assertOk()->assertSee('1 Reservation')->assertDontSee('2 Reservations');
        $this->get(route('cars.show', $car->slug))->assertOk()->assertSee('1 Reservation')->assertDontSee('2 Reservations');
    }
}
