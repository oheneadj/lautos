<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otpInput?.focus());
                },
                init() {
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;
                    this.code = '';
                    this.recovery_code = '';
                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Authentication code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Recovery code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center mt-6">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5">
                            <input
                                x-ref="otpInput"
                                x-model="code"
                                type="text"
                                name="code"
                                inputmode="numeric"
                                maxlength="6"
                                pattern="[0-9]*"
                                autocomplete="one-time-code"
                                placeholder="000000"
                                class="w-48 px-4 py-3 bg-base-100 border border-base-content/10 rounded-xl text-center text-2xl font-bold tracking-[0.4em] placeholder:tracking-[0.4em] placeholder:text-base-content/20 outline-none focus:border-primary focus:ring-3 focus:ring-primary/20 transition-all duration-150"
                            />
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                                class="w-full px-[14px] py-[10px] text-[15px] bg-base-100 border border-base-content/10 rounded-lg transition-all duration-150 outline-none placeholder:text-base-content/40 focus:border-primary focus:ring-3 focus:ring-primary/20"
                            />
                        </div>

                        @error('recovery_code')
                            <p class="text-sm text-error mb-3">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center">
                        {{ __('Continue') }}
                    </x-ui.button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                    <span class="opacity-50">{{ __('or you can') }}</span>
                    <div class="inline font-medium underline cursor-pointer opacity-80">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
