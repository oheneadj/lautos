<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-base-200 antialiased font-sans">

    <div class="min-h-screen flex items-center justify-center p-6 md:p-10">
        <div class="w-full max-w-[460px] flex flex-col gap-8">

            {{-- Logo + back link --}}
            <div class="flex flex-col items-center gap-3">
                <a href="{{ route('home') }}" wire:navigate class="hover:opacity-80 transition-opacity duration-200">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                            <circle cx="7.5" cy="14.5" r="1.5"/>
                            <circle cx="16.5" cy="14.5" r="1.5"/>
                        </svg>
                        <span class="text-[15px] font-semibold text-base-content">Livingston Autos</span>
                    </div>
                </a>
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-1 text-[12px] text-base-content/40 hover:text-base-content/70 font-medium transition-colors">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to home
                </a>
            </div>

            {{-- Card — same shell as the dashboard's cards (x-ui.card): rounded-xl, hairline border, no shadow --}}
            <div class="bg-base-100 rounded-xl border border-base-content/5 overflow-hidden">
                <div class="p-6 sm:p-10">
                    {{ $slot }}
                </div>
            </div>

            {{-- Copyright --}}
            <p class="text-[10px] text-base-content/30 font-medium uppercase tracking-[0.15em] text-center">
                &copy; {{ date('Y') }} Livingston Autos
            </p>

        </div>
    </div>

</body>
</html>
