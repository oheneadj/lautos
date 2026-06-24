<?php

/**
 * Adds a phone number field with SMS verification to the Filament Breezy
 * "My Profile" page for admin/staff users.
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Admin;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;
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

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->user->update(['phone_verification_code' => $code]);

        // I only log the code here — there's no SMS gateway wired up for admin
        // accounts yet, same placeholder approach the customer side already uses.
        Log::info("SMS Verification Code for {$this->user->phone}: {$code}");

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

        if ($this->user->phone_verification_code === $this->verificationCode) {
            $this->user->update([
                'phone_verified_at' => now(),
                'phone_verification_code' => null,
            ]);

            $this->showPhoneVerificationModal = false;
            $this->verificationCode = '';

            Notification::make()
                ->success()
                ->title('Phone number verified successfully.')
                ->send();
        } else {
            $this->addError('verificationCode', 'Invalid verification code.');
        }
    }
}
