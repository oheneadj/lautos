<x-layouts::auth :title="__('Email verification')">
    <div class="mt-4 flex flex-col gap-6">
        <p class="text-center text-sm text-base-content/50">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="text-center text-sm font-medium text-success">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </p>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center">
                    {{ __('Resend verification email') }}
                </x-ui.button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button type="submit" variant="ghost" size="sm" data-test="logout-button">
                    {{ __('Log out') }}
                </x-ui.button>
            </form>
        </div>
    </div>
</x-layouts::auth>
