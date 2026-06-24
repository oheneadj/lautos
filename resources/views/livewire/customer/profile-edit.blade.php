    <div class="space-y-6 max-w-4xl">
        {{-- Page Header --}}
        <div>
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('Profile & KYC Documents') }}</h1>
            <p class="text-[14px] text-base-content/50 mt-1">{{ __('Manage your contact details and identity verification documents.') }}</p>
        </div>

        {{-- KYC Status Alerts --}}
        @if ($this->user->kyc_status === \App\Enums\KycStatus::NeedsResubmission)
            <x-ui.alert type="error" title="KYC Update Required">
                {{ __('There was an issue verifying your documents. Please review and re-upload them.') }}
                @if ($this->user->kyc_notes)
                    <div class="mt-2 text-[13px] italic border-l-2 border-red-400 pl-3">"{{ $this->user->kyc_notes }}"</div>
                @endif
            </x-ui.alert>
        @elseif ($this->user->kyc_status === \App\Enums\KycStatus::Pending && ($this->hasGhanaCardDoc || $this->hasTinDoc))
            <x-ui.alert type="info" title="KYC In Review">
                {{ __('Your documents have been submitted and are pending review by our team.') }}
            </x-ui.alert>
        @elseif ($this->user->kyc_status === \App\Enums\KycStatus::Pending && !($this->hasGhanaCardDoc || $this->hasTinDoc))
            <x-ui.alert type="warning" title="KYC Required">
                {{ __('Please upload your Ghana Card or TIN to verify your identity and unlock placing orders.') }}
            </x-ui.alert>
        @elseif ($this->user->kyc_status === \App\Enums\KycStatus::Verified)
            <x-ui.alert type="success" title="KYC Verified">
                {{ __('Your identity has been verified. You can now place car orders.') }}
            </x-ui.alert>
        @endif

        {{-- Phone Number Card --}}
        <x-ui.card class="overflow-hidden">
            <form wire:submit="updatePhone">
                <div class="p-6 md:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-base-content/5 pb-2.5">
                        <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60">
                            {{ __('Phone Verification') }}
                        </h2>
                        @if (filled($this->user->phone) && is_null($this->user->phone_verified_at))
                            <x-ui.badge type="error">{{ __('Unverified') }}</x-ui.badge>
                        @elseif (filled($this->user->phone) && $this->user->phone_verified_at)
                            <x-ui.badge type="success">{{ __('Verified') }}</x-ui.badge>
                        @endif
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                        <div class="flex-1">
                            <x-ui.input
                                label="{{ __('Phone Number') }}"
                                type="tel"
                                id="phone"
                                wire:model="phone"
                                placeholder="+233..."
                                error="{{ $errors->first('phone') }}"
                                required
                            />
                        </div>
                        <div class="flex gap-2">
                            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updatePhone">
                                {{ __('Save') }}
                            </x-ui.button>
                            @if (filled($this->user->phone) && is_null($this->user->phone_verified_at))
                                <x-ui.button type="button" wire:click="sendPhoneVerificationCode" variant="outline">{{ __('Verify') }}</x-ui.button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </x-ui.card>

        {{-- Profile Form Card --}}
        <x-ui.card class="overflow-hidden">
            <form wire:submit="updateProfile">
                <div class="p-6 md:p-8 space-y-8">
                    {{-- Basic Info Section --}}
                    <div>
                        <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 border-b border-base-content/5 pb-2.5 mb-6">
                            {{ __('Basic Info') }}
                        </h2>
                        <div class="grid gap-5">
                            <div>
                                <x-ui.input
                                    label="{{ __('Full Name') }}"
                                    id="name"
                                    wire:model="name"
                                    error="{{ $errors->first('name') }}"
                                    required
                                />
                            </div>
                            <div>
                                <x-ui.input
                                    label="{{ __('Email Address') }}"
                                    type="email"
                                    id="email"
                                    wire:model="email"
                                    error="{{ $errors->first('email') }}"
                                    required
                                />
                                @if ($this->hasUnverifiedEmail)
                                    <p class="mt-3 text-[12px] text-base-content/60">
                                        {{ __('Your email address is unverified.') }}
                                        <button class="font-semibold cursor-pointer underline text-primary hover:no-underline" wire:click.prevent="resendVerificationNotification">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>
                                @endif
                            </div>
                            <p class="text-[12px] text-base-content/40">
                                {{ __('To change your password, visit') }}
                                <a href="{{ route('security.edit') }}" wire:navigate class="font-semibold text-primary hover:underline">{{ __('Security Settings') }}</a>.
                            </p>
                        </div>
                    </div>

                    {{-- Contact Info Section --}}
                    <div>
                        <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60 border-b border-base-content/5 pb-2.5 mb-6">
                            {{ __('Delivery Address') }}
                        </h2>
                        <div class="grid gap-5">

                            <div>
                                <x-ui.textarea
                                    label="{{ __('Delivery / Home Address') }}"
                                    id="address"
                                    wire:model="address"
                                    placeholder="House No, Street, City, Region..."
                                    rows="3"
                                    error="{{ $errors->first('address') }}"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    {{-- Identification Section --}}
                    <div>
                        @php
                            $kycStatus = $this->user->kyc_status;
                            $isVerified = $kycStatus === \App\Enums\KycStatus::Verified;
                            $isPending = $kycStatus === \App\Enums\KycStatus::Pending && ($this->hasGhanaCardDoc || $this->hasTinDoc);
                            $isRejected = $kycStatus === \App\Enums\KycStatus::NeedsResubmission;
                            $fieldsLocked = $isVerified || $isPending;
                        @endphp

                        <div class="flex items-center justify-between border-b border-base-content/5 pb-2.5 mb-4">
                            <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60">
                                {{ __('Identification (KYC)') }}
                            </h2>
                            @if ($isVerified)
                                <span class="inline-flex items-center gap-1 rounded-full bg-success/10 px-2.5 py-1 text-[11px] font-bold text-success border border-success/20 uppercase tracking-wide">
                                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
                                    Verified
                                </span>
                            @elseif ($isPending)
                                <span class="inline-flex items-center gap-1 rounded-full bg-info/10 px-2.5 py-1 text-[11px] font-bold text-info border border-info/20 uppercase tracking-wide">
                                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    Pending Review
                                </span>
                            @elseif ($isRejected)
                                <span class="inline-flex items-center gap-1 rounded-full bg-error/10 px-2.5 py-1 text-[11px] font-bold text-error border border-error/20 uppercase tracking-wide">
                                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                    Rejected — Re-upload Required
                                </span>
                            @endif
                        </div>

                        @if ($fieldsLocked)
                            <div class="flex items-center gap-2 mb-6 px-3 py-2 rounded-lg bg-base-200 border border-base-content/5">
                                <svg class="w-4 h-4 text-base-content/40 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                <p class="text-[12px] text-base-content/50">
                                    @if ($isVerified)
                                        {{ __('Your identity has been verified. These fields are locked.') }}
                                    @else
                                        {{ __('Your documents are under review. Fields are locked until review is complete.') }}
                                    @endif
                                </p>
                            </div>
                        @else
                            <p class="text-[11px] text-base-content/40 mb-6">{{ __('Provide either a Ghana Card or TIN.') }}</p>
                        @endif

                        <div class="space-y-6">
                            {{-- Ghana Card --}}
                            <div class="bg-base-200 p-6 rounded-xl border border-base-content/5 {{ $fieldsLocked ? 'opacity-80' : '' }}">
                                <div class="grid gap-6 sm:grid-cols-2 items-start">
                                    <div>
                                        <x-ui.input
                                            label="{{ __('Ghana Card Number') }}"
                                            type="text"
                                            id="ghana_card_number"
                                            wire:model="ghana_card_number"
                                            placeholder="GHA-XXXXXXXXX-X"
                                            pattern="GHA-[0-9]{9}-[0-9]"
                                            title="Format: GHA-123456789-1"
                                            error="{{ $errors->first('ghana_card_number') }}"
                                            :disabled="$fieldsLocked"
                                        />
                                        @if ($this->hasGhanaCardDoc && ! $isRejected)
                                            <div class="mt-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-success/5 border border-success/10">
                                                <svg class="w-4 h-4 text-success flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" /></svg>
                                                <span class="text-[12px] font-semibold text-success">{{ __('Document uploaded') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if (!$fieldsLocked)
                                            <label class="text-[13px] font-medium text-base-content block mb-1.5">{{ __('Upload Ghana Card') }}</label>
                                            <x-ui.filepond
                                                wire:model="ghana_card_file"
                                                accepts="image/jpeg, image/png, application/pdf"
                                                maxSize="5MB"
                                            />
                                            <p class="mt-2 text-[11px] text-base-content/40">{{ __('Max 5MB (JPG, PNG, PDF)') }}</p>
                                            @error('ghana_card_file') <span class="text-[11px] text-error flex items-center gap-1 mt-1.5">⚠ {{ $message }}</span> @enderror
                                        @else
                                            <label class="text-[13px] font-medium text-base-content/40 block mb-1.5">{{ __('Ghana Card Document') }}</label>
                                            <div class="flex items-center gap-3 px-4 py-3 bg-base-100 border border-base-content/5 rounded-lg">
                                                <svg class="w-5 h-5 text-base-content/30" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                                <div>
                                                    <p class="text-[13px] font-medium text-base-content/60">{{ $this->hasGhanaCardDoc ? __('File on record') : __('No file uploaded') }}</p>
                                                    <p class="text-[11px] text-base-content/30">{{ __('Locked during review') }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- OR Divider --}}
                            <div class="relative flex items-center py-2">
                                <div class="flex-grow border-t border-base-content/10"></div>
                                <span class="flex-shrink-0 mx-4 text-[10px] font-bold text-base-content/40 uppercase tracking-widest">{{ __('OR') }}</span>
                                <div class="flex-grow border-t border-base-content/10"></div>
                            </div>

                            {{-- TIN --}}
                            <div class="bg-base-200 p-6 rounded-xl border border-base-content/5 {{ $fieldsLocked ? 'opacity-80' : '' }}">
                                <div class="grid gap-6 sm:grid-cols-2 items-start">
                                    <div>
                                        <x-ui.input
                                            label="{{ __('Tax Identification Number (TIN)') }}"
                                            type="text"
                                            id="tin_number"
                                            wire:model="tin_number"
                                            placeholder="PXXXXXXXXXX"
                                            error="{{ $errors->first('tin_number') }}"
                                            :disabled="$fieldsLocked"
                                        />
                                        @if ($this->hasTinDoc && ! $isRejected)
                                            <div class="mt-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-success/5 border border-success/10">
                                                <svg class="w-4 h-4 text-success flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" /></svg>
                                                <span class="text-[12px] font-semibold text-success">{{ __('Document uploaded') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if (!$fieldsLocked)
                                            <label class="text-[13px] font-medium text-base-content block mb-1.5">{{ __('Upload TIN Document') }}</label>
                                            <x-ui.filepond
                                                wire:model="tin_file"
                                                accepts="image/jpeg, image/png, application/pdf"
                                                maxSize="5MB"
                                            />
                                            <p class="mt-2 text-[11px] text-base-content/40">{{ __('Max 5MB (JPG, PNG, PDF)') }}</p>
                                            @error('tin_file') <span class="text-[11px] text-error flex items-center gap-1 mt-1.5">⚠ {{ $message }}</span> @enderror
                                        @else
                                            <label class="text-[13px] font-medium text-base-content/40 block mb-1.5">{{ __('TIN Document') }}</label>
                                            <div class="flex items-center gap-3 px-4 py-3 bg-base-100 border border-base-content/5 rounded-lg">
                                                <svg class="w-5 h-5 text-base-content/30" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                                <div>
                                                    <p class="text-[13px] font-medium text-base-content/60">{{ $this->hasTinDoc ? __('File on record') : __('No file uploaded') }}</p>
                                                    <p class="text-[11px] text-base-content/30">{{ __('Locked during review') }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Footer --}}
                <div class="border-t border-base-content/5 bg-base-200/30 px-6 py-4 flex items-center justify-end gap-3">
                    <span wire:loading wire:target="updateProfile, ghana_card_file, tin_file" class="text-[12px] text-base-content/40 font-medium">
                        {{ __('Uploading / Processing...') }}
                    </span>
                    <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                        {{ __('Save Changes') }}
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Phone Verification Modal --}}
        @if ($showPhoneVerificationModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/40 backdrop-blur-[2px]" wire:click="$set('showPhoneVerificationModal', false)"></div>
                <div class="relative z-10 w-full max-w-sm rounded-2xl bg-base-100 p-6 shadow-2xl border border-base-content/10 text-center">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                    </div>
                    <h2 class="text-lg font-semibold text-base-content mb-1">{{ __('Verify Phone Number') }}</h2>
                    <p class="text-[13px] text-base-content/50 mb-6">{{ __('We sent a 6-digit code to') }} <span class="font-bold text-base-content">{{ $phone }}</span></p>
                    
                    <form wire:submit="verifyPhone">
                        <input
                            type="text"
                            wire:model="verificationCode"
                            inputmode="numeric"
                            maxlength="6"
                            pattern="[0-9]*"
                            autocomplete="one-time-code"
                            placeholder="000000"
                            class="w-full text-center px-4 py-3 bg-base-200 border border-base-content/10 rounded-xl text-2xl font-bold tracking-[0.4em] placeholder:tracking-[0.4em] placeholder:text-base-content/20 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-150 mb-2"
                            required
                        />
                        @error('verificationCode') <span class="text-[12px] text-error mb-4 block">{{ $message }}</span> @enderror
                        
                        <div class="flex flex-col gap-3 mt-6">
                            <x-ui.button type="submit" variant="primary" class="w-full justify-center">{{ __('Verify') }}</x-ui.button>
                            <button type="button" wire:click="sendPhoneVerificationCode" class="text-[12px] font-bold text-primary hover:underline">{{ __('Resend Code') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Delete Account Section --}}
        @if ($this->showDeleteUser)
            <div class="mt-8">
                <livewire:settings.delete-user-form />
            </div>
        @endif
    </div>
