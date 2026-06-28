<div class="space-y-6 max-w-4xl">
    {{-- Page Header --}}
    <div>
        <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('Security & 2FA') }}</h1>
        <p class="text-[14px] text-base-content/50 mt-1">{{ __('Manage your password and two-factor authentication settings.') }}</p>
    </div>

    {{-- Update Password Card --}}
    <x-ui.card class="overflow-hidden">
        <div class="p-6 md:p-8 space-y-6">
            <div class="border-b border-base-content/5 pb-2.5">
                <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60">
                    {{ __('Update Password') }}
                </h2>
            </div>
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <x-ui.input
                label="Current password"
                id="current_password"
                name="current_password"
                type="password"
                wire:model="current_password"
                :required="true"
                autocomplete="current-password"
                :error="$errors->first('current_password')"
            />
            <x-ui.input
                label="New password"
                id="password"
                name="password"
                type="password"
                wire:model="password"
                :required="true"
                autocomplete="new-password"
                :error="$errors->first('password')"
            />
            <x-ui.input
                label="Confirm password"
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                wire:model="password_confirmation"
                :required="true"
                autocomplete="new-password"
                :error="$errors->first('password_confirmation')"
            />

            <div class="flex items-center gap-4">
                <x-ui.button type="submit" variant="primary" data-test="update-password-button" wire:loading.attr="disabled">{{ __('Save Password') }}</x-ui.button>
                <span wire:loading wire:target="updatePassword" class="text-[12px] text-base-content/40 font-medium">Saving...</span>
            </div>
        </form>
        </div>
    </x-ui.card>

    {{-- Connected Accounts Card --}}
    <x-ui.card class="overflow-hidden">
        <div class="p-6 md:p-8 space-y-6">
            <div class="border-b border-base-content/5 pb-2.5">
                <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60">
                    {{ __('Connected Accounts') }}
                </h2>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-base-content">{{ __('Google') }}</p>
                    @if ($this->googleConnected)
                        <p class="text-[12px] text-success">{{ __('Connected') }}</p>
                    @else
                        <p class="text-[12px] text-base-content/50">{{ __('Not connected') }}</p>
                    @endif
                </div>

                @if ($this->googleConnected)
                    <x-ui.button variant="danger" wire:click="$set('showDisconnectGoogleForm', true)">
                        {{ __('Disconnect') }}
                    </x-ui.button>
                @else
                    <x-ui.button variant="outline" href="{{ route('auth.google.link') }}">
                        {{ __('Connect Google account') }}
                    </x-ui.button>
                @endif
            </div>

            @if ($showDisconnectGoogleForm)
                <form wire:submit="disconnectGoogle" class="space-y-4 rounded-lg border border-base-300 p-4">
                    <p class="text-sm text-base-content/60">
                        {{ __('Confirm your password to disconnect Google.') }}
                    </p>
                    <x-ui.input
                        label="Password"
                        id="disconnect_google_password"
                        name="disconnect_google_password"
                        type="password"
                        wire:model="disconnect_google_password"
                        :required="true"
                        autocomplete="current-password"
                        :error="$errors->first('disconnect_google_password')"
                    />
                    <div class="flex items-center gap-3">
                        <x-ui.button type="submit" variant="danger" wire:loading.attr="disabled">{{ __('Disconnect') }}</x-ui.button>
                        <x-ui.button type="button" variant="outline" wire:click="$set('showDisconnectGoogleForm', false)">{{ __('Cancel') }}</x-ui.button>
                    </div>
                </form>
            @endif

            @unless ($this->hasPassword)
                <div class="flex items-center justify-between rounded-lg border border-base-300 p-4">
                    <p class="text-sm text-base-content/60">
                        {{ __("You signed up with Google and don't have a password yet. Add one to also sign in with your email.") }}
                    </p>
                    <x-ui.button variant="outline" wire:click="sendPasswordSetupLink" wire:loading.attr="disabled">
                        {{ __('Add Password') }}
                    </x-ui.button>
                </div>
            @endunless
        </div>
    </x-ui.card>

    {{-- Active Sessions Card --}}
    <x-ui.card class="overflow-hidden">
        <div class="p-6 md:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-base-content/5 pb-2.5">
                <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60">
                    {{ __('Active Sessions') }}
                </h2>
                @if (count($this->sessions) > 1)
                    <x-ui.button variant="outline" wire:click="logoutOtherSessions" wire:loading.attr="disabled">
                        {{ __('Log out all other sessions') }}
                    </x-ui.button>
                @endif
            </div>

            <div class="space-y-3">
                @foreach ($this->sessions as $session)
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-base-300 p-3">
                        <div class="text-sm">
                            <p class="font-medium text-base-content">
                                {{ $session->user_agent ?: __('Unknown device') }}
                                @if ($session->isCurrent)
                                    <span class="text-[11px] font-semibold text-success">{{ __('This device') }}</span>
                                @endif
                            </p>
                            <p class="text-[12px] text-base-content/50">
                                {{ $session->ip_address }} &middot; {{ __('Last active') }} {{ $session->last_active->diffForHumans() }}
                            </p>
                        </div>
                        @unless ($session->isCurrent)
                            <x-ui.button variant="outline" wire:click="logoutSession('{{ $session->id }}')">
                                {{ __('Log out') }}
                            </x-ui.button>
                        @endunless
                    </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>

    {{-- 2FA Card --}}
    @if ($canManageTwoFactor)
        <x-ui.card class="overflow-hidden">
            <div class="p-6 md:p-8 space-y-6">
                <div class="border-b border-base-content/5 pb-2.5">
                    <h2 class="text-[11px] font-bold uppercase tracking-widest text-base-content/60">
                        {{ __('Two-factor authentication') }}
                    </h2>
                </div>

                <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                    @if ($twoFactorEnabled)
                        <div class="space-y-4">
                            <p class="text-sm text-base-content/60">
                                {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                            </p>

                            <div class="flex justify-start">
                                <x-ui.button variant="danger" wire:click="disable">
                                    {{ __('Disable 2FA') }}
                                </x-ui.button>
                            </div>

                            <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>
                        </div>
                    @else
                        <div class="space-y-4">
                            <p class="text-sm text-base-content/50">
                                {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                            </p>

                            <x-ui.button variant="primary" wire:click="enable">
                                {{ __('Enable 2FA') }}
                            </x-ui.button>
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.card>

            {{-- 2FA Setup Modal --}}
            <div
                x-data="{ show: @entangle('showModal') }"
                x-show="show"
                x-on:keydown.escape.window="show = false; @this.call('closeModal')"
                x-transition
                class="fixed inset-0 z-50 flex items-center justify-center"
                style="display: none;"
            >
                <div class="fixed inset-0 bg-black/40 backdrop-blur-[2px]" @click="show = false; @this.call('closeModal')"></div>

                <div class="relative z-10 w-full max-w-md rounded-2xl bg-base-100 border border-base-300 p-6 shadow-2xl">
                    <button @click="show = false; @this.call('closeModal')" class="absolute top-4 right-4 text-base-content/40 hover:text-base-content transition-colors outline-none">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <div class="space-y-6">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="p-0.5 w-auto rounded-full border border-base-300 bg-base-100 shadow-sm">
                                <div class="p-2.5 rounded-full border border-base-300 overflow-hidden bg-base-200 relative">
                                    <svg class="size-6 relative z-20 text-base-content" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" /></svg>
                                </div>
                            </div>

                            <div class="space-y-2 text-center">
                                <h4 class="text-lg font-semibold text-base-content">{{ $this->modalConfig['title'] }}</h4>
                                <p class="text-sm text-base-content/50">{{ $this->modalConfig['description'] }}</p>
                            </div>
                        </div>

                        @if ($showVerificationStep)
                            <div class="space-y-6">
                                <div class="flex flex-col items-center space-y-3 justify-center" x-data x-init="$nextTick(() => $el.querySelector('input')?.focus())">
                                    <div class="w-48">
                                        <x-ui.input
                                            type="text"
                                            name="code"
                                            wire:model="code"
                                            inputmode="numeric"
                                            maxlength="6"
                                            pattern="[0-9]*"
                                            autocomplete="one-time-code"
                                            placeholder="000000"
                                            class="text-center text-xl font-bold tracking-[0.3em] placeholder:tracking-[0.3em]"
                                        />
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <x-ui.button class="flex-1 justify-center" variant="outline" wire:click="resetVerification">{{ __('Back') }}</x-ui.button>
                                    <x-ui.button class="flex-1 justify-center" variant="primary" wire:click="confirmTwoFactor" x-bind:disabled="$wire.code.length < 6">{{ __('Confirm') }}</x-ui.button>
                                </div>
                            </div>
                        @else
                            @error('setupData')
                                <div class="rounded-lg border border-error/20 bg-error/5 p-4 text-sm text-error">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="flex justify-center">
                                <div class="relative w-64 overflow-hidden border rounded-lg border-base-300 aspect-square">
                                    @empty($qrCodeSvg)
                                        <div class="absolute inset-0 flex items-center justify-center bg-base-100 animate-pulse">
                                            <svg class="w-6 h-6 text-base-content/30 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center h-full p-4">
                                            <div class="bg-white p-3 rounded">
                                                {!! $qrCodeSvg !!}
                                            </div>
                                        </div>
                                    @endempty
                                </div>
                            </div>

                            <div>
                                {{-- I use :disabled instead of a bare {{ }} attribute — Blade's component-tag
                                     parser chokes on an unnamed expression attribute and produces a broken
                                     compiled template (a dangling endif) when one appears here. --}}
                                <x-ui.button
                                    :disabled="$errors->has('setupData')"
                                    class="w-full justify-center"
                                    variant="primary"
                                    wire:click="showVerificationIfNecessary"
                                >
                                    {{ $this->modalConfig['buttonText'] }}
                                </x-ui.button>
                            </div>

                            <div class="space-y-4">
                                <div class="relative flex items-center justify-center w-full">
                                    <div class="absolute inset-0 w-full h-px top-1/2 bg-base-300"></div>
                                    <span class="relative px-2 text-sm bg-base-100 text-base-content/50">
                                        {{ __('or, enter the code manually') }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2" x-data="{ copied: false, async copy() { try { await navigator.clipboard.writeText('{{ $manualSetupKey }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } catch (e) { console.warn('Could not copy'); } } }">
                                    <div class="flex items-stretch w-full border rounded-xl border-base-300">
                                        @empty($manualSetupKey)
                                            <div class="flex items-center justify-center w-full p-3 bg-base-200">
                                                <svg class="w-5 h-5 text-base-content/30 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <input type="text" readonly value="{{ $manualSetupKey }}" class="w-full p-3 bg-transparent outline-none text-base-content text-sm" />
                                            <button @click="copy()" class="px-3 transition-colors border-l cursor-pointer border-base-300 hover:bg-base-200">
                                                <svg x-show="!copied" class="size-5 text-base-content/50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                                                <svg x-show="copied" class="size-5 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            </button>
                                        @endempty
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    @endif
</div>
