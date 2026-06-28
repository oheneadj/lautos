{{--
    Shared card content for every error page — keeps the badge, heading, and
    CTA buttons consistent without repeating markup per status code.
--}}
@props([
    'code',
    'heading',
    'message',
    'illustration' => 'lost',
    'showHomeButton' => true,
    'showBrowseButton' => true,
])

<div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-10 text-center">
    <div class="mb-6 mx-auto w-32 h-24 rounded-2xl bg-primary/10 flex items-center justify-center overflow-hidden">
        <x-car-illustration :variant="$illustration" class="w-28" />
    </div>

    <p class="text-sm font-black tracking-widest text-primary uppercase mb-2">Error {{ $code }}</p>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $heading }}</h1>
    <p class="text-sm text-gray-500 mb-8">{{ $message }}</p>

    @if ($showHomeButton || $showBrowseButton)
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @if ($showHomeButton)
                <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 bg-primary text-white hover:bg-primary/90 px-6 py-3 text-sm">
                    Go Home
                </a>
            @endif
            @if ($showBrowseButton)
                <a href="{{ url('/cars') }}" class="w-full sm:w-auto inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 bg-gray-100 text-gray-900 hover:bg-gray-200 px-6 py-3 text-sm">
                    Browse Cars
                </a>
            @endif
        </div>
    @endif
</div>
