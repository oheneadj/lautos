<?php

declare(strict_types=1);

/**
 * Adds a phone number field with SMS verification to the Filament Breezy
 * "My Profile" page for admin/staff users.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Admin;

use App\Http\Requests\ProfilePhoneInfoRequest;
use App\Services\GiantSmsService;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class ProfilePhoneInfo extends MyProfileComponent
{
    protected string $view = 'livewire.admin.profile-phone-info';

    public ?array $data = [];

    public $user;

    public array $only = ['phone'];

    public static $sort = 15;

    public string $verificationCode = '';

    public bool $showPhoneVerificationModal = false;

    public function mount(): void
    {
        $this->user = filament('filament-breezy')->auth()->user();

        $this->form->fill($this->user->only($this->only));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->all();

        $data['phone'] = preg_replace('/\s+/', '', $data['phone'] ?? '');

        $request = new ProfilePhoneInfoRequest;
        Validator::make(
            ['data' => $data],
            $request->rules(),
            $request->messages()
        )->validate();

        // I clear the verified flag whenever the number changes, same as the
        // customer-side profile does, so a new number always has to be re-verified.
        if ($data['phone'] !== $this->user->phone) {
            $data['phone_verified_at'] = null;
        }

        $this->user->update($data);

        Notification::make()
            ->success()
            ->title('Phone number saved.')
            ->send();
    }

    public function sendPhoneVerificationCode(): void
    {
        if (empty($this->user->phone)) {
            $this->addError('data.phone', 'Please save your phone number first.');

            return;
        }

        // Same protection as the customer-side ProfileEdit — without this,
        // a compromised admin session could spam unlimited real SMS sends.
        $sendKey = 'send-phone-otp:'.$this->user->id;
        if (RateLimiter::tooManyAttempts($sendKey, maxAttempts: 1)) {
            $this->addError('data.phone', 'Please wait a minute before requesting another code.');

            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->user->update([
            'phone_verification_code' => $code,
            'phone_verification_code_expires_at' => now()->addMinutes(10),
        ]);

        // Synchronous, same as the customer side — an admin clicking "send"
        // should know right away if the gateway call failed.
        try {
            app(GiantSmsService::class)->send(
                $this->user->phone,
                "Your Livingston Autos verification code is {$code}.",
                'otp'
            );
        } catch (\RuntimeException $e) {
            $this->addError('data.phone', 'Could not send the verification code. Please try again.');

            return;
        }

        RateLimiter::hit($sendKey, decaySeconds: 60);

        $this->showPhoneVerificationModal = true;

        Notification::make()
            ->success()
            ->title('Verification code sent to your phone.')
            ->send();
    }

    public function verifyPhone(): void
    {
        $this->validate([
            'verificationCode' => 'required|string|size:6',
        ]);

        // Caps guess attempts the same way the customer side does — the
        // code itself never expired before this fix, so without a limit
        // here it was brute-forceable.
        $verifyKey = 'verify-phone-otp:'.$this->user->id;
        if (RateLimiter::tooManyAttempts($verifyKey, maxAttempts: 5)) {
            $this->addError('verificationCode', 'Too many attempts. Please request a new code and try again later.');

            return;
        }

        $codeExpired = $this->user->phone_verification_code_expires_at === null
            || $this->user->phone_verification_code_expires_at->isPast();

        if (! $codeExpired && hash_equals((string) $this->user->phone_verification_code, $this->verificationCode)) {
            $this->user->update([
                'phone_verified_at' => now(),
                'phone_verification_code' => null,
                'phone_verification_code_expires_at' => null,
            ]);

            RateLimiter::clear($verifyKey);

            $this->showPhoneVerificationModal = false;
            $this->verificationCode = '';

            Notification::make()
                ->success()
                ->title('Phone number verified successfully.')
                ->send();
        } else {
            RateLimiter::hit($verifyKey, decaySeconds: 600);

            $message = $codeExpired
                ? 'This code has expired. Please request a new one.'
                : 'Invalid verification code.';

            $this->addError('verificationCode', $message);
        }
    }
}
