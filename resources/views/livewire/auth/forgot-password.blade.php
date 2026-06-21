<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset your password')" :description="__('Enter your email and we\'ll send you a reset link')" />

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

            <x-ui.input
                label="Email address"
                type="email"
                id="email"
                name="email"
                :required="true"
                autofocus
                placeholder="you@example.com"
                :error="$errors->first('email')"
            />

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center" data-test="email-password-reset-link-button">
                {{ __('Send reset link') }}
            </x-ui.button>
        </form>

        <div class="text-center pt-6 border-t border-base-content/5">
            <span class="text-[13px] text-base-content/40 font-medium">Or, return to</span>
            <a href="{{ route('login') }}" wire:navigate class="text-primary font-semibold hover:text-primary/80 transition-colors ml-1">Log in</a>
        </div>
    </div>
</x-layouts::auth>
