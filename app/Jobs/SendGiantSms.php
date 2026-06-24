<?php

/**
 * Sends a single SMS via the GiantSMS REST API, with its own retry/backoff
 * separate from the rest of the notification — mail and the database
 * channel succeed immediately regardless of whether the SMS gateway is
 * temporarily down.
 *
 * @author Ohene Adjei
 */

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendGiantSms implements ShouldQueue
{
    use Queueable;

    /** I retry a failed send up to 3 times before giving up. */
    public int $tries = 3;

    public function __construct(public string $phone, public string $message)
    {
    }

    /**
     * I space retries out (1 min, 5 min, 15 min) rather than hammering the
     * gateway again immediately after a failure.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(): void
    {
        $apiKey = config('services.giantsms.api_key');
        $baseUrl = config('services.giantsms.base_url', 'https://api.giantsms.com/api/v1');

        $response = Http::post("{$baseUrl}/send", [
            'api_key' => $apiKey,
            'from' => config('services.giantsms.sender_id', 'LivingstonA'),
            'to' => $this->phone,
            'message' => $this->message,
        ]);

        if ($response->failed()) {
            // I throw here so Laravel's queue retry/backoff actually kicks
            // in — returning quietly would mark this attempt as successful.
            throw new \RuntimeException("GiantSMS delivery failed with status {$response->status()}: {$response->body()}");
        }
    }

    /**
     * Called once all retry attempts are exhausted — this is the
     * "failed deliveries are logged" half of the requirement; the
     * "can be retried" half already happened via $tries/backoff().
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GiantSMS delivery failed permanently after retries', [
            'phone' => $this->phone,
            'message' => $exception->getMessage(),
        ]);
    }
}
