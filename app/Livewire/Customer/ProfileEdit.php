<?php

/**
 * Customer profile and KYC document management page.
 *
 * I keep this separate from the Settings/Profile component because this one
 * handles the business-specific KYC fields (Ghana Card, TIN, documents),
 * whereas the Settings profile is for basic account info (name/email).
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use App\Enums\KycStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Profile & KYC')]
#[Layout('layouts.app')]
class ProfileEdit extends Component
{
    use WithFileUploads;

    public string $phone = '';
    public string $address = '';
    public string $ghana_card_number = '';
    public string $tin_number = '';

    public string $verificationCode = '';
    public bool $showPhoneVerificationModal = false;

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:5120')]
    public $ghana_card_file = null;

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:5120')]
    public $tin_file = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->ghana_card_number = $user->ghana_card_number ?? '';
        $this->tin_number = $user->tin_number ?? '';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'address'            => ['required', 'string', 'max:500'],
            'ghana_card_number'  => ['required_without:tin_number', 'nullable', 'string', 'max:50'],
            'tin_number'         => ['required_without:ghana_card_number', 'nullable', 'string', 'max:50'],
            'ghana_card_file'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'tin_file'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'ghana_card_number.required_without' => 'Please provide your Ghana Card number or TIN.',
            'tin_number.required_without'         => 'Please provide your TIN or Ghana Card number.',
        ]);

        $user = Auth::user();

        $data = [
            'address'           => $this->address,
            'ghana_card_number' => $this->ghana_card_number ?: null,
            'tin_number'        => $this->tin_number ?: null,
        ];

        if ($this->ghana_card_file) {
            $data['ghana_card_path'] = $this->ghana_card_file->store(
                "kyc/{$user->uuid}",
                'private'
            );
        }

        if ($this->tin_file) {
            $data['tin_path'] = $this->tin_file->store(
                "kyc/{$user->uuid}",
                'private'
            );
        }

        // If the user's KYC was previously rejected, re-submitting documents
        // resets the status back to Pending for admin review.
        if ($user->kyc_status === KycStatus::NeedsResubmission) {
            $data['kyc_status'] = KycStatus::Pending;
            $data['kyc_notes'] = null;
        }

        $user->update($data);

        $this->dispatch('toast', message: __('Profile and KYC updated successfully.'));
    }

    public function updatePhone(): void
    {
        $this->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user = Auth::user();
        
        $data = ['phone' => $this->phone];
        
        if ($this->phone !== $user->phone) {
            $data['phone_verified_at'] = null;
        }
        
        $user->update($data);
        $this->dispatch('toast', message: __('Phone number saved.'));
    }

    public function sendPhoneVerificationCode(): void
    {
        $user = Auth::user();
        
        if (empty($user->phone)) {
            $this->addError('phone', 'Please update your phone number first.');
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'phone_verification_code' => $code,
        ]);

        // Mocking SMS send
        \Illuminate\Support\Facades\Log::info("SMS Verification Code for {$user->phone}: {$code}");
        
        $this->showPhoneVerificationModal = true;
        $this->dispatch('toast', message: __('Verification code sent to your phone.'));
    }

    public function verifyPhone(): void
    {
        $this->validate([
            'verificationCode' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if ($user->phone_verification_code === $this->verificationCode) {
            $user->update([
                'phone_verified_at' => now(),
                'phone_verification_code' => null,
            ]);
            
            $this->showPhoneVerificationModal = false;
            $this->verificationCode = '';
            $this->dispatch('toast', message: __('Phone number verified successfully.'));
        } else {
            $this->addError('verificationCode', 'Invalid verification code.');
        }
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function hasGhanaCardDoc(): bool
    {
        return filled(Auth::user()->ghana_card_path);
    }

    #[Computed]
    public function hasTinDoc(): bool
    {
        return filled(Auth::user()->tin_path);
    }

    public function render()
    {
        return view('livewire.customer.profile-edit');
    }
}
