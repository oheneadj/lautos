<?php

namespace Tests\Unit\Services;

use App\Enums\SmsLogStatus;
use App\Services\GiantSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the GiantSMS gateway wrapper — correct auth/body shape per the
 * GiantSMS docs, and that every attempt (success or failure) gets written
 * to sms_logs so delivery problems can be traced from the admin panel.
 */
class GiantSmsServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_authenticates_with_a_basic_token_and_sends_the_correct_fields(): void
    {
        config(['services.giantsms.api_key' => 'test-token', 'services.giantsms.sender_id' => 'LivingstonA']);
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true, 'message' => 'Successfully Sent'], 200)]);

        app(GiantSmsService::class)->send('0551234567', 'Hello there');

        Http::assertSent(function (HttpRequest $request) {
            return $request->url() === 'https://api.giantsms.com/api/v1/send'
                && $request->hasHeader('Authorization', 'Basic test-token')
                && $request['from'] === 'LivingstonA'
                && $request['to'] === '0551234567'
                && $request['msg'] === 'Hello there';
        });
    }

    #[Test]
    public function it_logs_a_successful_send_to_the_database(): void
    {
        config(['services.giantsms.api_key' => 'test-token']);
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => true], 200)]);

        $log = app(GiantSmsService::class)->send('0551234567', 'Hello there', 'otp');

        $this->assertSame(SmsLogStatus::Sent, $log->status);
        $this->assertDatabaseHas('sms_logs', [
            'id' => $log->id,
            'phone' => '0551234567',
            'message' => 'Hello there',
            'context' => 'otp',
            'status' => SmsLogStatus::Sent->value,
            'http_status' => 200,
        ]);
    }

    #[Test]
    public function it_logs_a_failed_send_and_still_throws_so_the_caller_knows(): void
    {
        config(['services.giantsms.api_key' => 'test-token']);
        Http::fake(['api.giantsms.com/*' => Http::response(['status' => false, 'message' => 'Invalid number'], 422)]);

        try {
            app(GiantSmsService::class)->send('0551234567', 'Hello there');
            $this->fail('Expected a RuntimeException to be thrown.');
        } catch (\RuntimeException) {
            // expected
        }

        $this->assertDatabaseHas('sms_logs', [
            'phone' => '0551234567',
            'status' => SmsLogStatus::Failed->value,
            'http_status' => 422,
        ]);
    }

    #[Test]
    public function it_fails_fast_and_still_logs_when_no_api_token_is_configured(): void
    {
        config(['services.giantsms.api_key' => null]);
        Http::fake();

        try {
            app(GiantSmsService::class)->send('0551234567', 'Hello there');
            $this->fail('Expected a RuntimeException to be thrown.');
        } catch (\RuntimeException) {
            // expected
        }

        Http::assertNothingSent();
        $this->assertDatabaseHas('sms_logs', [
            'phone' => '0551234567',
            'status' => SmsLogStatus::Failed->value,
        ]);
    }
}
