<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Welcome back')" :description="__('Enter your email and password to log in')" />

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <x-ui.input
                label="Email address"
                type="email"
                id="email"
                name="email"
                :value="old('email')"
                :required="true"
                autofocus
                autocomplete="email"
                placeholder="you@example.com"
                :error="$errors->first('email')"
            />

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="text-[13px] font-medium text-base-content">
                        Password <span class="text-error ml-0.5">*</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate class="text-[12px] text-base-content/40 hover:text-base-content/70 font-medium transition-colors">Forgot password?</a>
                    @endif
                </div>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                        class="w-full px-[14px] py-[10px] text-[15px] bg-base-100 border rounded-lg transition-all duration-120 outline-none placeholder:text-base-content/40 pr-11 {{ $errors->has('password') ? 'border-error focus:ring-3 focus:ring-error/20' : 'border-base-content/10 focus:border-primary focus:ring-3 focus:ring-primary/20' }}"
                    >
                    <button type="button" onclick="const i=this.previousElementSibling;i.type=i.type==='password'?'text':'password'" class="absolute inset-y-0 right-0 pr-3 flex items-center text-base-content/40 hover:text-base-content/70">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="text-xs text-error flex items-center gap-1 mt-1"><span>⚠</span> {{ $message }}</span>
                @enderror
            </div>

            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox" name="remember" class="size-4 rounded border-base-content/20 accent-primary cursor-pointer" {{ old('remember') ? 'checked' : '' }}>
                <span class="text-[13px] text-base-content/50 font-medium group-hover:text-base-content transition-colors">Remember me</span>
            </label>

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center" data-test="login-button">
                {{ __('Log in') }}
            </x-ui.button>
        </form>

        <div class="flex items-center gap-3">
            <div class="flex-1 h-px bg-base-content/10"></div>
            <span class="text-[12px] text-base-content/40 font-medium">{{ __('or') }}</span>
            <div class="flex-1 h-px bg-base-content/10"></div>
        </div>

        <a href="{{ route('auth.google.redirect') }}" class="flex items-center justify-center gap-3 w-full rounded-xl border border-base-content/10 bg-base-100 py-[10px] text-[14px] font-semibold text-base-content hover:bg-base-200 transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.21.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
            {{ __('Continue with Google') }}
        </a>

        @if (Route::has('register'))
            <div class="text-center pt-6 border-t border-base-content/5">
                <span class="text-[13px] text-base-content/40 font-medium">Don't have an account?</span>
                <a href="{{ route('register') }}" wire:navigate class="text-primary font-semibold hover:text-primary/80 transition-colors ml-1">Sign up</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
