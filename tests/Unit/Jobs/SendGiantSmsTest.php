<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendGiantSms;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the queued SMS-sending job — this is where the real GiantSMS API
 * call happens (via GiantSmsService), with its own retry/backoff so a
 * flaky gateway doesn't affect the rest of a notification (US-46 / T-46-2).
 */
class SendGiantSmsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_the_sms_via_the_giantsms_api(): void
    {
        config(['services.giantsms.api_key' => 'test-token', 'services.giantsms.sender_id' => 'LivingstonA']);
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true], 200)]);

        (new SendGiantSms('0551234567', 'Test message'))->handle(app(\App\Services\GiantSmsService::class));

        Http::assertSent(function (HttpRequest $request) {
            return $request->url() === 'https://api.giantsms.com/api/v1/send'
                && $request->hasHeader('Authorization', 'Basic test-token')
                && $request['to'] === '0551234567'
                && $request['msg'] === 'Test message';
        });
    }

    #[Test]
    public function it_throws_when_the_api_call_fails_so_the_queue_retries_it(): void
    {
        config(['services.giantsms.api_key' => 'test-token']);
        Http::fake(['api.giantsms.com/*' => Http::response(['error' => 'bad request'], 500)]);

        $this->expectException(\RuntimeException::class);

        (new SendGiantSms('0551234567', 'Test message'))->handle(app(\App\Services\GiantSmsService::class));
    }

    #[Test]
    public function it_retries_up_to_three_times(): void
    {
        $job = new SendGiantSms('0551234567', 'Test message');

        $this->assertSame(3, $job->tries);
    }

    #[Test]
    public function it_backs_off_before_each_retry(): void
    {
        $job = new SendGiantSms('0551234567', 'Test message');

        $this->assertSame([60, 300, 900], $job->backoff());
    }

    #[Test]
    public function it_logs_an_error_once_retries_are_exhausted(): void
    {
        Log::shouldReceive('error')->once()->with(
            'GiantSMS delivery failed permanently after retries',
            \Mockery::on(fn ($context) => $context['phone'] === '0551234567')
        );

        (new SendGiantSms('0551234567', 'Test message'))->failed(new \RuntimeException('Gateway timeout'));
    }
}
