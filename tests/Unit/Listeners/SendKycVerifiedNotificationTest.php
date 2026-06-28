<?php

namespace Tests\Unit\Listeners;

use App\Events\KycVerified;
use App\Listeners\SendKycVerifiedNotification;
use App\Models\User;
use App\Notifications\KycVerifiedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that the customer is notified the moment their KYC is verified.
 */
class SendKycVerifiedNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_notifies_the_customer_whose_kyc_was_verified(): void
    {
        Notification::fake();

        $customer = User::factory()->create();

        (new SendKycVerifiedNotification)->handle(new KycVerified($customer));

        Notification::assertSentTo($customer, KycVerifiedNotification::class);
    }
}
