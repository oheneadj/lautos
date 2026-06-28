<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests "Continue with Google" — redirect, successful callback, and a
 * cancelled/failed OAuth attempt (US-36 social registration).
 */
class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogleUser(string $id, string $email, string $name): SocialiteUser
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = $id;
        $socialiteUser->email = $email;
        $socialiteUser->name = $name;

        return $socialiteUser;
    }

    #[Test]
    public function visiting_the_redirect_route_sends_the_guest_to_google(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/fake'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.redirect'))->assertRedirect('https://accounts.google.com/fake');
    }

    #[Test]
    public function a_successful_callback_logs_the_customer_in(): void
    {
        $googleUser = $this->fakeGoogleUser('google-1', 'driver@example.com', 'Driver Customer');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'driver@example.com', 'google_id' => 'google-1']);
    }

    #[Test]
    public function a_failed_callback_redirects_to_login_without_authenticating(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andThrow(new \Exception('invalid state'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))->assertRedirect(route('login'));

        $this->assertGuest();
    }

    #[Test]
    public function an_already_authenticated_user_cannot_visit_the_redirect_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('auth.google.redirect'))->assertRedirect(route('home'));
    }

    #[Test]
    public function a_guest_cannot_visit_the_link_route(): void
    {
        $this->get(route('auth.google.link'))->assertRedirect(route('login'));
    }

    #[Test]
    public function an_authenticated_customer_can_connect_their_google_account(): void
    {
        $user = User::factory()->create(['google_id' => null]);
        $googleUser = $this->fakeGoogleUser('google-link-1', 'unrelated@example.com', 'Whoever Google Says');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/fake'));
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->actingAs($user)->get(route('auth.google.link'))->assertRedirect('https://accounts.google.com/fake');
        $this->get(route('auth.google.callback'))->assertRedirect(route('security.edit'));

        $this->assertSame('google-link-1', $user->refresh()->google_id);
    }

    #[Test]
    public function connecting_a_google_identity_already_used_by_someone_else_does_not_link_it(): void
    {
        $other = User::factory()->create(['google_id' => 'google-taken']);
        $me = User::factory()->create(['google_id' => null]);
        $googleUser = $this->fakeGoogleUser('google-taken', 'whoever@example.com', 'Whoever');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/fake'));
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->actingAs($me)->get(route('auth.google.link'));
        $this->get(route('auth.google.callback'))->assertRedirect(route('security.edit'));

        $this->assertNull($me->refresh()->google_id);
        $this->assertSame('google-taken', $other->refresh()->google_id);
    }
}
