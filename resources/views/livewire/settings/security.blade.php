<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('Security settings') }}</h2>

    <x-settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <div class="space-y-2">
                <label for="current_password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Current password') }}</label>
                <input wire:model="current_password" id="current_password" type="password" required autocomplete="current-password" class="input input-bordered w-full" />
                @error('current_password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('New password') }}</label>
                <input wire:model="password" id="password" type="password" required autocomplete="new-password" class="input input-bordered w-full" />
                @error('password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Confirm password') }}</label>
                <input wire:model="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password" class="input input-bordered w-full" />
                @error('password_confirmation') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn btn-primary" data-test="update-password-button">{{ __('Save') }}</button>
            </div>
        </form>

        @if ($canManageTwoFactor)
            <section class="mt-12">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Two-factor authentication') }}</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Manage your two-factor authentication settings') }}</p>

                <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                    @if ($twoFactorEnabled)
                        <div class="space-y-4">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                            </p>

                            <div class="flex justify-start">
                                <button class="btn btn-error" wire:click="disable">
                                    {{ __('Disable 2FA') }}
                                </button>
                            </div>

                            <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>
                        </div>
                    @else
                        <div class="space-y-4">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                            </p>

                            <button class="btn btn-primary" wire:click="enable">
                                {{ __('Enable 2FA') }}
                            </button>
                        </div>
                    @endif
                </div>
            </section>

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

                <div class="relative z-10 w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-800 dark:border dark:border-zinc-700">
                    <div class="space-y-6">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="p-0.5 w-auto rounded-full border border-stone-100 dark:border-stone-600 bg-white dark:bg-stone-800 shadow-sm">
                                <div class="p-2.5 rounded-full border border-stone-200 dark:border-stone-600 overflow-hidden bg-stone-100 dark:bg-stone-200 relative">
                                    <svg class="size-6 relative z-20 text-zinc-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" /></svg>
                                </div>
                            </div>

                            <div class="space-y-2 text-center">
                                <h4 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->modalConfig['title'] }}</h4>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $this->modalConfig['description'] }}</p>
                            </div>
                        </div>

                        @if ($showVerificationStep)
                            <div class="space-y-6">
                                <div class="flex flex-col items-center space-y-3 justify-center" x-data x-init="$nextTick(() => $el.querySelector('input')?.focus())">
                                    <input
                                        type="text"
                                        name="code"
                                        wire:model="code"
                                        inputmode="numeric"
                                        maxlength="6"
                                        pattern="[0-9]*"
                                        autocomplete="one-time-code"
                                        placeholder="000000"
                                        class="input input-bordered w-48 text-center text-2xl font-bold tracking-[0.4em] placeholder:tracking-[0.4em] placeholder:text-zinc-300"
                                    />
                                </div>

                                <div class="flex items-center space-x-3">
                                    <button class="btn btn-outline flex-1" wire:click="resetVerification">{{ __('Back') }}</button>
                                    <button class="btn btn-primary flex-1" wire:click="confirmTwoFactor" x-bind:disabled="$wire.code.length < 6">{{ __('Confirm') }}</button>
                                </div>
                            </div>
                        @else
                            @error('setupData')
                                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="flex justify-center">
                                <div class="relative w-64 overflow-hidden border rounded-lg border-stone-200 dark:border-stone-700 aspect-square">
                                    @empty($qrCodeSvg)
                                        <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-stone-700 animate-pulse">
                                            <span class="loading loading-spinner loading-md"></span>
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
                                <button
                                    {{ $errors->has('setupData') ? 'disabled' : '' }}
                                    class="btn btn-primary w-full"
                                    wire:click="showVerificationIfNecessary"
                                >
                                    {{ $this->modalConfig['buttonText'] }}
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div class="relative flex items-center justify-center w-full">
                                    <div class="absolute inset-0 w-full h-px top-1/2 bg-stone-200 dark:bg-stone-600"></div>
                                    <span class="relative px-2 text-sm bg-white dark:bg-zinc-800 text-stone-600 dark:text-stone-400">
                                        {{ __('or, enter the code manually') }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2" x-data="{ copied: false, async copy() { try { await navigator.clipboard.writeText('{{ $manualSetupKey }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } catch (e) { console.warn('Could not copy'); } } }">
                                    <div class="flex items-stretch w-full border rounded-xl dark:border-stone-700">
                                        @empty($manualSetupKey)
                                            <div class="flex items-center justify-center w-full p-3 bg-stone-100 dark:bg-stone-700">
                                                <span class="loading loading-spinner loading-sm"></span>
                                            </div>
                                        @else
                                            <input type="text" readonly value="{{ $manualSetupKey }}" class="w-full p-3 bg-transparent outline-none text-stone-900 dark:text-stone-100" />
                                            <button @click="copy()" class="px-3 transition-colors border-l cursor-pointer border-stone-200 dark:border-stone-600">
                                                <svg x-show="!copied" class="size-5 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                                                <svg x-show="copied" class="size-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
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
    </x-settings.layout>
</section>
