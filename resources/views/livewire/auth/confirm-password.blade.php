<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Confirm password')"
            :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-5">
            @csrf

            <x-ui.input
                label="Password"
                id="password"
                name="password"
                type="password"
                :required="true"
                autocomplete="current-password"
                placeholder="{{ __('Password') }}"
                :error="$errors->first('password')"
            />

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center" data-test="confirm-password-button">
                {{ __('Confirm') }}
            </x-ui.button>
        </form>
    </div>
</x-layouts::auth>
