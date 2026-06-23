<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests resolving a Google identity into a local account (Socialite login/registration).
 */
class SocialAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSocialiteUser(string $id, string $email, string $name): SocialiteUser
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = $id;
        $socialiteUser->email = $email;
        $socialiteUser->name = $name;

        return $socialiteUser;
    }

    #[Test]
    public function a_brand_new_google_user_gets_a_local_account_created(): void
    {
        $googleUser = $this->makeSocialiteUser('google-123', 'new@example.com', 'New Customer');

        $user = (new SocialAuthService())->findOrCreateFromGoogle($googleUser);

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'google_id' => 'google-123',
            'name' => 'New Customer',
        ]);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->phone);
    }

    #[Test]
    public function an_existing_email_gets_linked_to_the_google_account_instead_of_duplicated(): void
    {
        $existing = User::factory()->create(['email' => 'existing@example.com']);
        $googleUser = $this->makeSocialiteUser('google-456', 'existing@example.com', 'Existing Customer');

        $user = (new SocialAuthService())->findOrCreateFromGoogle($googleUser);

        $this->assertSame($existing->id, $user->id);
        $this->assertSame('google-456', $user->refresh()->google_id);
        $this->assertSame(1, User::where('email', 'existing@example.com')->count());
    }

    #[Test]
    public function a_returning_google_user_is_matched_by_google_id_not_recreated(): void
    {
        $googleUser = $this->makeSocialiteUser('google-789', 'returning@example.com', 'Returning Customer');

        $service = new SocialAuthService();
        $first = $service->findOrCreateFromGoogle($googleUser);
        $second = $service->findOrCreateFromGoogle($googleUser);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, User::where('google_id', 'google-789')->count());
    }
}
