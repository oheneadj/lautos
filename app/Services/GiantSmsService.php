<?php

/**
 * Sends SMS through the GiantSMS Ghana API and records every attempt
 * (request and response) to sms_logs so a delivery problem can be
 * diagnosed without needing server log access.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Enums\SmsLogStatus;
use App\Models\SmsLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GiantSmsService
{
    /**
     * Sends a single SMS and logs the outcome — both to the application
     * log (for real-time tailing) and to the sms_logs table (for later
     * lookup in the admin panel).
     *
     * @throws \RuntimeException if the gateway call fails
     */
    public function send(string $phone, string $message, ?string $context = null): SmsLog
    {
        $token   = config('services.giantsms.api_key');
        $baseUrl = config('services.giantsms.base_url', 'https://api.giantsms.com/api/v1');
        $from    = config('services.giantsms.sender_id', 'LivingstonA');

        $payload = [
            'from' => $from,
            'to'   => $phone,
            'msg'  => $message,
        ];

        // I log and bail out before making a doomed request — a blank Basic
        // auth header would still hit the live API and just come back 401.
        if (empty($token)) {
            $log = SmsLog::create([
                'phone'         => $phone,
                'message'       => $message,
                'status'        => SmsLogStatus::Failed,
                'context'       => $context,
                'error_message' => 'No GiantSMS API token configured (GIANTSMS_API_KEY is empty).',
            ]);

            Log::error('GiantSMS: no API token configured', ['to' => $phone]);

            throw new \RuntimeException('GiantSMS delivery failed: no API token configured.');
        }

        Log::info('GiantSMS: sending message', ['to' => $phone, 'context' => $context]);

        try {
            // I use Basic auth with the token straight from .env — GiantSMS expects
            // the token itself as the Basic auth password, not a request body field.
            // A 15s timeout means a slow/unresponsive gateway fails fast with a
            // clear error instead of leaving the user stuck waiting indefinitely
            // — the gateway can still process the SMS on its end even if our
            // request times out waiting for its response.
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$token,
            ])->timeout(15)->post("{$baseUrl}/send", $payload);
        } catch (ConnectionException $e) {
            SmsLog::create([
                'phone'         => $phone,
                'message'       => $message,
                'status'        => SmsLogStatus::Failed,
                'context'       => $context,
                'error_message' => "GiantSMS connection failed: {$e->getMessage()}",
            ]);

            Log::error('GiantSMS: connection failed', ['to' => $phone, 'error' => $e->getMessage()]);

            // I normalise to RuntimeException so callers only ever need to
            // catch one exception type, whether the gateway timed out or
            // responded with an error status.
            throw new \RuntimeException("GiantSMS delivery failed: {$e->getMessage()}", previous: $e);
        }

        $status = $response->successful() ? SmsLogStatus::Sent : SmsLogStatus::Failed;

        $log = SmsLog::create([
            'phone'         => $phone,
            'message'       => $message,
            'status'        => $status,
            'context'       => $context,
            'http_status'   => $response->status(),
            'response_body' => $response->body(),
            'error_message' => $status === SmsLogStatus::Failed ? "GiantSMS request failed with status {$response->status()}" : null,
        ]);

        if ($status === SmsLogStatus::Sent) {
            Log::info('GiantSMS: message sent', ['to' => $phone, 'response' => $response->json()]);
        } else {
            Log::error('GiantSMS: message failed', ['to' => $phone, 'status' => $response->status(), 'body' => $response->body()]);

            throw new \RuntimeException("GiantSMS delivery failed with status {$response->status()}: {$response->body()}");
        }

        return $log;
    }
}
