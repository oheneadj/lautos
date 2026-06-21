<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Set new password')" :description="__('Please enter your new password below')" />

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <x-ui.input
                label="Email address"
                type="email"
                id="email"
                name="email"
                :value="request('email')"
                :required="true"
                autocomplete="email"
                placeholder="you@example.com"
                :error="$errors->first('email')"
            />

            <x-ui.input
                label="New Password"
                type="password"
                id="password"
                name="password"
                :required="true"
                autocomplete="new-password"
                placeholder="New password"
                :error="$errors->first('password')"
            />

            <x-ui.input
                label="Confirm New Password"
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                :required="true"
                autocomplete="new-password"
                placeholder="Confirm new password"
            />

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center" data-test="reset-password-button">
                {{ __('Reset password') }}
            </x-ui.button>
        </form>
    </div>
</x-layouts::auth>
