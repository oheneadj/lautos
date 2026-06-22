<?php

/**
 * Handles KYC status transitions for customer accounts.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Enums\KycStatus;
use App\Events\KycResubmissionRequested;
use App\Models\User;

class KycService
{
    /**
     * Marks a customer's KYC documents as verified.
     */
    public function verify(User $customer): void
    {
        $customer->update([
            'kyc_status' => KycStatus::Verified,
            'kyc_notes' => null,
        ]);
    }

    /**
     * Sends a customer's KYC back for resubmission with a reason.
     */
    public function requestResubmission(User $customer, string $reason): void
    {
        $customer->update([
            'kyc_status' => KycStatus::NeedsResubmission,
            'kyc_notes' => $reason,
        ]);

        KycResubmissionRequested::dispatch($customer, $reason);
    }
}
