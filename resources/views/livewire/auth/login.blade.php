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

        @if (Route::has('register'))
            <div class="text-center pt-6 border-t border-base-content/5">
                <span class="text-[13px] text-base-content/40 font-medium">Don't have an account?</span>
                <a href="{{ route('register') }}" wire:navigate class="text-primary font-semibold hover:text-primary/80 transition-colors ml-1">Sign up</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
