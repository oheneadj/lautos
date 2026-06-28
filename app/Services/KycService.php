<?php

/**
 * Handles KYC status transitions for customer accounts.
 *
 * @author Ohene Adjei
 */

namespace App\Services;

use App\Enums\KycStatus;
use App\Events\KycResubmissionRequested;
use App\Events\KycVerified;
use App\Models\User;
use InvalidArgumentException;

class KycService
{
    /**
     * Marks a customer's KYC documents as verified.
     */
    public function verify(User $customer): void
    {
        // Without this, an admin could mark a customer Verified with no
        // documents on file at all — e.g. after a prior rejection that was
        // never resubmitted — and kyc_status gates delivery eligibility.
        // A customer only ever needs one of the two documents (see
        // ProfileEditRequest's required_without rules), so I only block
        // verification when both are missing, not when either one is.
        if (empty($customer->ghana_card_path) && empty($customer->tin_path)) {
            throw new InvalidArgumentException('This customer has no KYC documents on file to verify.');
        }

        $customer->update([
            'kyc_status' => KycStatus::Verified,
            'kyc_notes' => null,
        ]);

        KycVerified::dispatch($customer);
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
