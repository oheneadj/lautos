<x-layouts::auth :title="__('Create account')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create your account')" :description="__('Enter your details below to get started')" />

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <x-ui.input
                label="Full Name"
                type="text"
                id="name"
                name="name"
                :value="old('name')"
                :required="true"
                autofocus
                autocomplete="name"
                placeholder="John Mensah"
                :error="$errors->first('name')"
            />

            <x-ui.input
                label="Email address"
                type="email"
                id="email"
                name="email"
                :value="old('email')"
                :required="true"
                autocomplete="email"
                placeholder="you@example.com"
                :error="$errors->first('email')"
            />

            <x-ui.input
                label="Password"
                type="password"
                id="password"
                name="password"
                :required="true"
                autocomplete="new-password"
                placeholder="Password"
                :error="$errors->first('password')"
            />

            <x-ui.input
                label="Confirm Password"
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                :required="true"
                autocomplete="new-password"
                placeholder="Confirm password"
            />

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center" data-test="register-user-button">
                {{ __('Create account') }}
            </x-ui.button>
        </form>

        <div class="text-center pt-6 border-t border-base-content/5">
            <span class="text-[13px] text-base-content/40 font-medium">Already have an account?</span>
            <a href="{{ route('login') }}" wire:navigate class="text-primary font-semibold hover:text-primary/80 transition-colors ml-1">Log in</a>
        </div>
    </div>
</x-layouts::auth>
