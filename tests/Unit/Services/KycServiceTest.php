<?php

namespace Tests\Unit\Services;

use App\Enums\KycStatus;
use App\Events\KycResubmissionRequested;
use App\Events\KycVerified;
use App\Models\User;
use App\Services\KycService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the KYC status transition rules.
 */
class KycServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function verifying_a_customer_with_both_documents_on_file_succeeds(): void
    {
        Event::fake([KycVerified::class]);

        $customer = User::factory()->create([
            'kyc_status' => KycStatus::Pending,
            'ghana_card_path' => 'kyc/some-uuid/card.jpg',
            'tin_path' => 'kyc/some-uuid/tin.jpg',
        ]);

        (new KycService)->verify($customer);

        $this->assertSame(KycStatus::Verified, $customer->refresh()->kyc_status);
        Event::assertDispatched(KycVerified::class);
    }

    #[Test]
    public function verifying_a_customer_with_no_documents_on_file_is_rejected(): void
    {
        $customer = User::factory()->create([
            'kyc_status' => KycStatus::Pending,
            'ghana_card_path' => null,
            'tin_path' => null,
        ]);

        $this->expectException(InvalidArgumentException::class);

        (new KycService)->verify($customer);

        $this->assertSame(KycStatus::Pending, $customer->refresh()->kyc_status);
    }

    #[Test]
    public function verifying_a_customer_with_only_one_document_on_file_succeeds(): void
    {
        // A customer only ever needs to provide one of Ghana Card or TIN
        // (see ProfileEditRequest's required_without rules), so having
        // just one on file is enough to verify.
        $customer = User::factory()->create([
            'kyc_status' => KycStatus::Pending,
            'ghana_card_path' => 'kyc/some-uuid/card.jpg',
            'tin_path' => null,
        ]);

        (new KycService)->verify($customer);

        $this->assertSame(KycStatus::Verified, $customer->refresh()->kyc_status);
    }

    #[Test]
    public function requesting_resubmission_still_works_without_a_document_check(): void
    {
        Event::fake([KycResubmissionRequested::class]);

        // Resubmission doesn't require existing documents — that's exactly
        // the case where they might be missing or rejected.
        $customer = User::factory()->create(['kyc_status' => KycStatus::Pending]);

        (new KycService)->requestResubmission($customer, 'Photo was blurry.');

        $this->assertSame(KycStatus::NeedsResubmission, $customer->refresh()->kyc_status);
        $this->assertSame('Photo was blurry.', $customer->kyc_notes);
        Event::assertDispatched(KycResubmissionRequested::class);
    }
}
