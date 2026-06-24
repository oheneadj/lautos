<?php

namespace Tests\Unit\Channels;

use App\Channels\GiantSmsChannel;
use App\Channels\GiantSmsMessage;
use App\Jobs\SendGiantSms;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the custom GiantSMS notification channel — this just decides
 * whether to queue a SendGiantSms job; the job itself (and its retry
 * behaviour) is tested separately in SendGiantSmsTest.
 */
class GiantSmsChannelTest extends TestCase
{
    private function notificationWithBody(string $body): Notification
    {
        return new class($body) extends Notification {
            public function __construct(private string $body)
            {
            }

            public function toGiantSms($notifiable): GiantSmsMessage
            {
                return new GiantSmsMessage($this->body);
            }
        };
    }

    #[Test]
    public function it_queues_an_sms_job_when_an_api_key_and_phone_are_present(): void
    {
        config(['services.giantsms.api_key' => 'test-key']);
        Queue::fake();

        $user = new User(['phone' => '0551234567']);

        (new GiantSmsChannel())->send($user, $this->notificationWithBody('Test message'));

        Queue::assertPushed(SendGiantSms::class, fn ($job) => $job->phone === '0551234567' && $job->message === 'Test message');
    }

    #[Test]
    public function it_skips_silently_when_no_api_key_is_configured(): void
    {
        config(['services.giantsms.api_key' => null]);
        Queue::fake();

        $user = new User(['phone' => '0551234567']);

        (new GiantSmsChannel())->send($user, $this->notificationWithBody('Test message'));

        Queue::assertNotPushed(SendGiantSms::class);
    }

    #[Test]
    public function it_skips_silently_when_the_notifiable_has_no_phone_number(): void
    {
        config(['services.giantsms.api_key' => 'test-key']);
        Queue::fake();

        $user = new User(['phone' => null]);

        (new GiantSmsChannel())->send($user, $this->notificationWithBody('Test message'));

        Queue::assertNotPushed(SendGiantSms::class);
    }
}
