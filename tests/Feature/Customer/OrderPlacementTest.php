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
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
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
    public function a_guest_sees_a_login_prompt_instead_of_an_order_button(): void
    {
        $car = $this->makeCar();

        $this->get(route('cars.show', $car->slug))
            ->assertSee('Login to Order')
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
        // Placing an order no longer locks the car — only a confirmed payment does,
        // so other customers can still order it while this one is unpaid.
        $this->assertSame(CarStatus::Available, $car->refresh()->status);
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
    public function a_customer_with_an_unverified_email_cannot_place_an_order(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->unverified()->create(['kyc_status' => KycStatus::Verified]);

        Livewire::actingAs($user)
            ->test(CarDetail::class, ['car' => $car])
            ->set('showOrderModal', true)
            ->assertSee('verify your email address')
            ->call('confirmOrder')
            ->assertHasErrors('order');

        $this->assertDatabaseMissing('orders', ['user_id' => $user->id, 'car_id' => $car->id]);
    }

    #[Test]
    public function resending_verification_from_the_order_modal_sends_a_new_email(): void
    {
        Notification::fake();

        $car = $this->makeCar();
        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)
            ->test(CarDetail::class, ['car' => $car])
            ->call('openOrderModal')
            ->call('resendVerification')
            ->assertSet('verificationJustSent', true)
            ->assertSee('Email sent — check your inbox');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[Test]
    public function resuming_an_order_intent_after_login_reopens_the_modal(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create(['kyc_status' => KycStatus::Verified]);

        Livewire::actingAs($user)
            ->test(CarDetail::class, ['car' => $car, 'intent' => 'order'])
            ->assertSet('showOrderModal', true);
    }

    #[Test]
    public function an_order_intent_does_not_auto_open_the_modal_for_a_guest(): void
    {
        $car = $this->makeCar();

        Livewire::test(CarDetail::class, ['car' => $car, 'intent' => 'order'])
            ->assertSet('showOrderModal', false);
    }

    #[Test]
    public function the_full_login_to_order_round_trip_resumes_at_the_car_page_with_the_modal_open(): void
    {
        // I deliberately don't hand-type the redirect_to value here — the whole point
        // of this test is to catch a mismatch between what CarDetail actually generates
        // and what FortifyServiceProvider actually accepts, which a hand-typed relative
        // path in a unit test would hide (and did, the first time this was built).
        $car = $this->makeCar();
        $user = User::factory()->create(['kyc_status' => KycStatus::Verified]);

        $page = $this->get(route('cars.show', $car->slug))->getContent();
        preg_match('/href="([^"]*\/login\?redirect_to=[^"]*)"/', $page, $matches);
        $this->assertNotEmpty($matches, 'Could not find the "Login to Order" link on the car page.');

        $loginUrl = html_entity_decode($matches[1]);

        $this->get($loginUrl);
        $this->assertNotNull(session('url.intended'), 'redirect_to was rejected as an open-redirect risk — it must be a relative path.');

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('cars.show', $car->slug).'?intent=order');
    }

    #[Test]
    public function the_login_link_carries_the_order_intent_so_it_can_resume_after_login(): void
    {
        $car = $this->makeCar();

        $this->get(route('cars.show', $car->slug))
            ->assertSeeText('Login to Order')
            ->assertSee('intent%3Dorder', false);
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
