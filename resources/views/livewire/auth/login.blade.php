<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-zinc-700">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="email@example.com" class="input input-bordered w-full" />
                @error('email') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div class="relative space-y-2">
                <label for="password" class="block text-sm font-medium text-zinc-700">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate class="absolute top-0 end-0 text-sm text-zinc-500 hover:text-zinc-700 transition-colors">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="{{ __('Password') }}" class="input input-bordered w-full" />
                @error('password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Remember Me -->
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="checkbox checkbox-sm" {{ old('remember') ? 'checked' : '' }}>
                <span class="text-sm text-zinc-600">{{ __('Remember me') }}</span>
            </label>

            <div class="flex items-center justify-end">
                <button type="submit" class="btn btn-primary w-full" data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600">
                <span>{{ __('Don\'t have an account?') }}</span>
                <a href="{{ route('register') }}" wire:navigate class="text-zinc-900 underline hover:no-underline">{{ __('Sign up') }}</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
