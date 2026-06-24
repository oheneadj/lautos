<x-filament::section :aside="true" heading="Phone Number" description="Used for SMS notifications and account recovery.">
    <form wire:submit.prevent="submit" class="space-y-6">

        {{ $this->form }}

        @if ($user->phone)
            <p class="text-sm">
                @if ($user->phone_verified_at)
                    <span class="text-success font-medium">Verified</span>
                @else
                    <span class="text-warning font-medium">Not verified</span>
                    —
                    <button type="button" wire:click="sendPhoneVerificationCode" class="underline">Send verification code</button>
                @endif
            </p>
        @endif

        <div class="text-right">
            <x-filament::button type="submit" form="submit">
                Save
            </x-filament::button>
        </div>
    </form>

    @if ($showPhoneVerificationModal)
        <form wire:submit.prevent="verifyPhone" class="mt-6 space-y-4 border-t pt-6">
            <x-filament::input.wrapper :valid="! $errors->has('verificationCode')">
                <x-filament::input
                    type="text"
                    wire:model="verificationCode"
                    placeholder="Enter 6-digit code"
                    maxlength="6"
                />
            </x-filament::input.wrapper>
            @error('verificationCode')
                <p class="text-sm text-danger-600">{{ $message }}</p>
            @enderror

            <x-filament::button type="submit">
                Verify
            </x-filament::button>
        </form>
    @endif
</x-filament::section>
