<?php

declare(strict_types=1);

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
use App\Events\KycDocumentsSubmitted;
use App\Http\Requests\ProfileEditRequest;
use App\Services\GiantSmsService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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

    public string $name = '';

    public string $email = '';

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
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->ghana_card_number = $user->ghana_card_number ?? '';
        $this->tin_number = $user->tin_number ?? '';
    }

    public function updateProfile(): void
    {
        // I clean the inputs before validation
        $this->ghana_card_number = strtoupper(trim($this->ghana_card_number));
        $this->tin_number = strtoupper(trim($this->tin_number));

        $request = new ProfileEditRequest;
        $rules = $request->rules();
        // Overwrite the email rule to include the unique check with the current user's ID
        $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.Auth::id()];

        $this->validate($rules, $request->messages());

        $user = Auth::user();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'ghana_card_number' => $this->ghana_card_number ?: null,
            'tin_number' => $this->tin_number ?: null,
        ];

        if ($this->email !== $user->getOriginal('email')) {
            $user->email_verified_at = null;
        }

        $newDocumentUploaded = (bool) $this->ghana_card_file || (bool) $this->tin_file;

        if ($this->ghana_card_file) {
            // I delete the old file first so a replaced document doesn't leave an orphaned upload behind.
            if ($user->ghana_card_path) {
                Storage::disk('private')->delete($user->ghana_card_path);
            }

            $data['ghana_card_path'] = $this->ghana_card_file->store(
                "kyc/{$user->uuid}",
                'private'
            );
        }

        if ($this->tin_file) {
            if ($user->tin_path) {
                Storage::disk('private')->delete($user->tin_path);
            }

            $data['tin_path'] = $this->tin_file->store(
                "kyc/{$user->uuid}",
                'private'
            );
        }

        // Any new document upload puts KYC back in front of an admin for review,
        // regardless of whether it was previously rejected or never reviewed yet.
        if ($newDocumentUploaded) {
            $user->kyc_status = KycStatus::Pending;
            $user->kyc_notes = null;
        }

        // I fill once, here, after every key (including the file paths above)
        // has been added to $data — filling earlier meant ghana_card_path and
        // tin_path were computed but never actually assigned to the model.
        $user->fill($data);
        $user->save();

        if ($newDocumentUploaded) {
            KycDocumentsSubmitted::dispatch($user);
        }

        $this->reset(['ghana_card_file', 'tin_file']);

        $this->dispatch('toast', message: __('Profile and KYC updated successfully.'));
    }

    public function updatePhone(): void
    {
        $this->phone = preg_replace('/\s+/', '', $this->phone);

        $this->validate(
            ['phone' => ['required', 'string', 'regex:/^(?:\+233|0)\d{9}$/']],
            ['phone.regex' => 'Phone number must be a valid Ghanaian number (e.g., 0244000000 or +233244000000).']
        );

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

        // Without this, nothing stops repeated clicks from sending unlimited
        // real SMS at the business's cost — there's no uniqueness check on
        // phone either, so this is the only thing capping abuse per account.
        $sendKey = 'send-phone-otp:'.$user->id;
        if (RateLimiter::tooManyAttempts($sendKey, maxAttempts: 1)) {
            $this->addError('phone', 'Please wait a minute before requesting another code.');

            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'phone_verification_code' => $code,
            'phone_verification_code_expires_at' => now()->addMinutes(10),
        ]);

        // I send this synchronously (not queued) so the user gets immediate
        // feedback if the gateway is down, instead of a "sent" toast for a
        // code that never arrives.
        try {
            app(GiantSmsService::class)->send(
                $user->phone,
                "Your Livingston Autos verification code is {$code}.",
                'otp'
            );
        } catch (\RuntimeException $e) {
            $this->addError('phone', 'Could not send the verification code. Please try again.');

            return;
        }

        RateLimiter::hit($sendKey, decaySeconds: 60);

        $this->showPhoneVerificationModal = true;
        $this->dispatch('toast', message: __('Verification code sent to your phone.'));
    }

    public function verifyPhone(): void
    {
        $this->validate([
            'verificationCode' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        // Caps guess attempts at 5 per 10 minutes — the code itself never
        // expired before this fix, so without a limit here it was brute-forceable.
        $verifyKey = 'verify-phone-otp:'.$user->id;
        if (RateLimiter::tooManyAttempts($verifyKey, maxAttempts: 5)) {
            $this->addError('verificationCode', 'Too many attempts. Please request a new code and try again later.');

            return;
        }

        $codeExpired = $user->phone_verification_code_expires_at === null
            || $user->phone_verification_code_expires_at->isPast();

        if (! $codeExpired && hash_equals((string) $user->phone_verification_code, $this->verificationCode)) {
            $user->update([
                'phone_verified_at' => now(),
                'phone_verification_code' => null,
                'phone_verification_code_expires_at' => null,
            ]);

            RateLimiter::clear($verifyKey);

            $this->showPhoneVerificationModal = false;
            $this->verificationCode = '';
            $this->dispatch('toast', message: __('Phone number verified successfully.'));
        } else {
            RateLimiter::hit($verifyKey, decaySeconds: 600);

            $message = $codeExpired
                ? 'This code has expired. Please request a new one.'
                : 'Invalid verification code.';

            $this->addError('verificationCode', $message);
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

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard.index', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        $this->dispatch('toast', message: __('A new verification link has been sent to your email address.'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    public function render()
    {
        return view('livewire.customer.profile-edit');
    }
}
