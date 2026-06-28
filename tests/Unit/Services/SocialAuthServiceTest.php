<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests resolving a Google identity into a local account (Socialite login/registration).
 */
class SocialAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSocialiteUser(string $id, string $email, string $name): SocialiteUser
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = $id;
        $socialiteUser->email = $email;
        $socialiteUser->name = $name;

        return $socialiteUser;
    }

    #[Test]
    public function a_brand_new_google_user_gets_a_local_account_created(): void
    {
        $googleUser = $this->makeSocialiteUser('google-123', 'new@example.com', 'New Customer');

        $user = (new SocialAuthService)->findOrCreateFromGoogle($googleUser);

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'google_id' => 'google-123',
            'name' => 'New Customer',
        ]);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->phone);
        $this->assertFalse($user->has_password);
    }

    #[Test]
    public function signing_in_with_google_using_an_email_that_already_has_a_password_account_is_rejected(): void
    {
        // I don't auto-link by email match — our own registration never
        // verifies email ownership, so an attacker could pre-register the
        // victim's email and silently inherit the victim's Google identity
        // the moment the real owner signs in with Google. This must throw,
        // not link.
        $existing = User::factory()->create(['email' => 'existing@example.com']);
        $googleUser = $this->makeSocialiteUser('google-456', 'existing@example.com', 'Existing Customer');

        $this->expectException(RuntimeException::class);

        (new SocialAuthService)->findOrCreateFromGoogle($googleUser);
    }

    #[Test]
    public function linking_google_to_the_authenticated_users_account_succeeds(): void
    {
        $user = User::factory()->create(['email' => 'me@example.com', 'google_id' => null]);
        $googleUser = $this->makeSocialiteUser('google-999', 'doesnotmatter@example.com', 'Me');

        (new SocialAuthService)->linkGoogleAccount($user, $googleUser);

        $this->assertSame('google-999', $user->refresh()->google_id);
    }

    #[Test]
    public function linking_a_google_identity_already_used_by_a_different_account_is_rejected(): void
    {
        $other = User::factory()->create(['google_id' => 'google-already-taken']);
        $me = User::factory()->create(['google_id' => null]);
        $googleUser = $this->makeSocialiteUser('google-already-taken', 'whatever@example.com', 'Whoever');

        $this->expectException(RuntimeException::class);

        (new SocialAuthService)->linkGoogleAccount($me, $googleUser);
    }

    #[Test]
    public function a_returning_google_user_is_matched_by_google_id_not_recreated(): void
    {
        $googleUser = $this->makeSocialiteUser('google-789', 'returning@example.com', 'Returning Customer');

        $service = new SocialAuthService;
        $first = $service->findOrCreateFromGoogle($googleUser);
        $second = $service->findOrCreateFromGoogle($googleUser);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, User::where('google_id', 'google-789')->count());
    }
}
