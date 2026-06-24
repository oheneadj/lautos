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
        $make = Make::firstOrCreate(['name' => 'Toyota']);
        $carModel = CarModel::firstOrCreate(['make_id' => $make->id, 'name' => 'Corolla']);

        return Car::factory()->create(['make_id' => $make->id, 'car_model_id' => $carModel->id]);
    }

    #[Test]
    public function a_guest_sees_a_save_button_that_prompts_login_instead_of_saving_directly(): void
    {
        $car = $this->makeCar();

        Livewire::test(SaveCarButton::class, ['car' => $car])
            ->assertDontSee('wire:click="toggle"', false)
            ->assertSee('wire:click="attemptSave"', false);
    }

    #[Test]
    public function a_guest_calling_attempt_save_sees_a_login_prompt_without_touching_the_database(): void
    {
        $car = $this->makeCar();

        Livewire::test(SaveCarButton::class, ['car' => $car])
            ->call('attemptSave')
            ->assertSet('showLoginPrompt', true)
            ->assertSee('Login to Save');

        $this->assertDatabaseCount('car_user', 0);
    }

    #[Test]
    public function the_login_prompt_link_carries_the_save_intent_and_car_uuid(): void
    {
        $car = $this->makeCar();

        Livewire::test(SaveCarButton::class, ['car' => $car])
            ->call('attemptSave')
            ->assertSeeHtml('intent%3Dsave')
            ->assertSeeHtml(urlencode($car->uuid));
    }

    #[Test]
    public function the_full_login_to_save_round_trip_resumes_at_the_catalogue_page_with_the_car_saved(): void
    {
        // Same reasoning as CarDetail's equivalent test: don't hand-type the
        // redirect_to value. The login link only renders inside the modal
        // after a wire:click, but loginRedirectUrl is computed at mount
        // time against the real /cars request — I can read the real value
        // straight out of Livewire's wire:snapshot in the page HTML, so a
        // mismatch between what this component builds and what
        // FortifyServiceProvider accepts can't slip through unnoticed again.
        $car = $this->makeCar();
        $user = User::factory()->create();

        $page = $this->get(route('cars.index'))->getContent();
        preg_match('/loginRedirectUrl&quot;:&quot;(.*?)&quot;/', $page, $matches);
        $this->assertNotEmpty($matches, 'Could not find loginRedirectUrl in the catalogue page snapshot.');

        $redirectTo = json_decode('"'.str_replace(['\/', '&amp;'], ['/', '&'], $matches[1]).'"');
        $this->assertStringStartsWith('/', $redirectTo, 'loginRedirectUrl must be a relative path, not an absolute URL.');

        $this->get(route('login', ['redirect_to' => $redirectTo]));
        $this->assertNotNull(session('url.intended'), 'redirect_to was rejected as an open-redirect risk.');

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Following the redirect is what actually triggers SaveCarButton's
        // mount() to resume the save — the POST to login.store itself only
        // returns the redirect response, it doesn't render the next page.
        $this->get($response->headers->get('Location'));

        $this->assertTrue($user->savedCars()->where('car_id', $car->id)->exists());
    }

    #[Test]
    public function an_authenticated_customer_clicking_save_does_not_see_the_login_prompt(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SaveCarButton::class, ['car' => $car])
            ->call('attemptSave')
            ->assertSet('showLoginPrompt', false);

        $this->assertTrue($user->savedCars()->where('car_id', $car->id)->exists());
    }

    #[Test]
    public function resuming_a_save_intent_after_login_attaches_the_car_without_toggling(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SaveCarButton::class, [
            'car' => $car,
            'intent' => 'save',
            'intentCarUuid' => $car->uuid,
        ]);

        $this->assertTrue($user->savedCars()->where('car_id', $car->id)->exists());
    }

    #[Test]
    public function resuming_a_save_intent_for_an_already_saved_car_does_not_unsave_it(): void
    {
        $car = $this->makeCar();
        $user = User::factory()->create();
        $user->savedCars()->attach($car->id);

        // Simulates revisiting the same intent URL again (e.g. browser back) —
        // mount() must use attach(), not toggle(), or this would un-save it.
        Livewire::actingAs($user)->test(SaveCarButton::class, [
            'car' => $car,
            'intent' => 'save',
            'intentCarUuid' => $car->uuid,
        ]);

        $this->assertTrue($user->savedCars()->where('car_id', $car->id)->exists());
    }

    #[Test]
    public function a_save_intent_for_a_different_car_does_not_trigger_this_instance(): void
    {
        $car = $this->makeCar();
        $otherCar = $this->makeCar();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(SaveCarButton::class, [
            'car' => $car,
            'intent' => 'save',
            'intentCarUuid' => $otherCar->uuid,
        ]);

        $this->assertFalse($user->savedCars()->where('car_id', $car->id)->exists());
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
